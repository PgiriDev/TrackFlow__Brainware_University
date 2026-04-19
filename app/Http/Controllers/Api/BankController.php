<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\FetchInitialTransactionsJob;
use App\Jobs\SyncTransactionsJob;
use App\Models\AccountToken;
use App\Models\BankAccount;
use App\Services\Providers\FinvuProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    protected $provider;

    public function __construct(FinvuProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * List all bank accounts for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $accounts = BankAccount::where('user_id', $request->user()->id)
            ->with('accountToken')
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'provider' => $account->provider,
                    'bank_name' => $account->bank_name,
                    'account_mask' => $account->account_number_masked,
                    'account_type' => $account->account_type,
                    'balance' => $account->balance,
                    'currency' => $account->currency,
                    'status' => $account->status,
                    'last_synced_at' => $account->last_synced_at?->toISOString(),
                    'created_at' => $account->created_at->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);
    }

    /**
     * Get single bank account
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $account = BankAccount::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $account,
        ]);
    }

    /**
     * Initialize bank linking session
     */
    public function linkInit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => 'required|string|in:finvu,saltedge,yodlee',
            'country' => 'nullable|string|size:2',
        ]);

        try {
            $session = $this->provider->createLinkSession(
                $request->user()->id,
                ['country' => $validated['country'] ?? 'IN']
            );

            return response()->json([
                'success' => true,
                'data' => $session,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to create link session", [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize bank link session',
            ], 500);
        }
    }

    /**
     * Handle callback from bank provider
     */
    public function callback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consent_id' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validated['status'] !== 'success') {
            return response()->json([
                'success' => false,
                'message' => 'Bank linking was not completed',
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Exchange consent for access token
            $tokenData = $this->provider->exchangeToken($validated['consent_id']);

            // Fetch account details
            $accounts = $this->provider->fetchAccounts($tokenData['access_token']);

            if (empty($accounts)) {
                throw new \Exception("No accounts found");
            }

            $accountData = $accounts[0]; // Take first account for now

            // Create bank account record
            $bankAccount = BankAccount::create([
                'user_id' => $request->user()->id,
                'bank_name' => $accountData['bank_name'],
                'account_number_masked' => $accountData['account_number_masked'],
                'account_type' => $accountData['account_type'],
                'balance' => $accountData['balance'],
                'currency' => $accountData['currency'],
                'provider' => 'finvu',
                'provider_account_id' => $accountData['provider_account_id'],
                'status' => 'active',
            ]);

            // Store encrypted tokens
            AccountToken::create([
                'bank_account_id' => $bankAccount->id,
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_at' => now()->addSeconds($tokenData['expires_in']),
            ]);

            DB::commit();

            // Dispatch job to fetch initial transactions
            FetchInitialTransactionsJob::dispatch($bankAccount->id);

            return response()->json([
                'success' => true,
                'message' => 'Bank account linked successfully',
                'data' => [
                    'account_id' => $bankAccount->id,
                    'bank_name' => $bankAccount->bank_name,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Failed to complete bank link", [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to link bank account',
            ], 500);
        }
    }

    /**
     * Trigger manual sync for an account
     */
    public function sync(Request $request, int $id): JsonResponse
    {
        $account = BankAccount::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($account->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot sync inactive account',
            ], 400);
        }

        // Dispatch sync job
        SyncTransactionsJob::dispatch($account->id);

        return response()->json([
            'success' => true,
            'message' => 'Sync initiated. This may take a few moments.',
        ]);
    }

    /**
     * Unlink/remove a bank account
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $account = BankAccount::where('user_id', $request->user()->id)
            ->findOrFail($id);

        DB::beginTransaction();

        try {
            // Try to revoke access at provider
            if ($account->accountToken) {
                try {
                    $this->provider->revokeAccess($account->accountToken->access_token);
                } catch (\Exception $e) {
                    Log::warning("Failed to revoke provider access", [
                        'account_id' => $account->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Mark account as inactive instead of deleting
            $account->update(['status' => 'inactive']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bank account unlinked successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Failed to unlink account", [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to unlink bank account',
            ], 500);
        }
    }
}
