<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\CategorizationService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    protected $categorizationService;
    protected $notificationService;

    public function __construct(
        CategorizationService $categorizationService,
        NotificationService $notificationService
    ) {
        $this->categorizationService = $categorizationService;
        $this->notificationService = $notificationService;
    }

    /**
     * List all transactions with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::where('user_id', $request->user()->id)
            ->with(['category', 'bankAccount'])
            ->excludeDuplicates();

        // Filters
        if ($request->has('from')) {
            $query->where('date', '>=', $request->input('from'));
        }

        if ($request->has('to')) {
            $query->where('date', '<=', $request->input('to'));
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('bank_account')) {
            $query->where('bank_account_id', $request->input('bank_account'));
        }

        if ($request->has('merchant')) {
            $query->where('merchant', 'LIKE', '%' . $request->input('merchant') . '%');
        }

        if ($request->has('min_amount')) {
            $query->where('amount', '>=', $request->input('min_amount'));
        }

        if ($request->has('max_amount')) {
            $query->where('amount', '<=', $request->input('max_amount'));
        }

        if ($request->has('uncategorized') && $request->input('uncategorized')) {
            $query->uncategorized();
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'date');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = min($request->input('per_page', 50), 100);
        $transactions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * Get single transaction
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $transaction = Transaction::where('user_id', $request->user()->id)
            ->with(['category', 'bankAccount'])
            ->findOrFail($id);

        // Treat DB `amount` as canonical base (INR). Convert from INR -> user's display currency
        // for the edit form so the user sees the expected value. When saving, the update
        // endpoint will convert the incoming display amount back to INR for storage.
        $currencyService = app(\App\Services\CurrencyService::class);
        $user = $request->user();
        $userSetting = app('user.settings');
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');

        // DB `amount` is stored in base currency (INR) after normalization migrations.
        $amountInInr = (float) $transaction->amount;

        try {
            $amountDisplay = $currencyService->convertFromINR($amountInInr, $userCurrency);
        } catch (\Exception $e) {
            $amountDisplay = $amountInInr;
        }

        $payload = $transaction->toArray();
        $payload['amount'] = round($amountDisplay, 2); // amount for form inputs (display currency)
        $payload['amount_display_currency'] = $userCurrency;
        $payload['stored_amount_in_inr'] = $amountInInr;
        $payload['stored_currency'] = $currencyService->getBaseCurrency();

        return response()->json([
            'success' => true,
            'data' => $payload,
        ]);
    }

    /**
     * Create manual transaction
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'merchant' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'type' => 'required|in:credit,debit',
            'category_id' => 'nullable|exists:categories,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $transaction = Transaction::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => 'completed',
        ]);

        // Auto-categorize if not provided
        if (!$transaction->category_id) {
            $this->categorizationService->categorizeTransaction($transaction);
            $transaction->refresh();
        }

        // Get user's currency (prefer user_settings.display_currency)
        $user = $request->user();
        $userSetting = app('user.settings');
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');

        // Convert amount to user's currency for notification
        $currencyService = app(\App\Services\CurrencyService::class);
        $amountInUserCurrency = $currencyService->convert(
            $transaction->amount,
            $transaction->currency,
            $userCurrency
        );

        // Always notify about transaction being added
        $this->notificationService->transactionAdded(
            $user->id,
            $transaction,
            $amountInUserCurrency,
            $userCurrency
        );

        // Check for large transaction notification
        $threshold = $this->notificationService->getLargeTransactionThreshold($user->id, $userCurrency);
        if ($amountInUserCurrency >= $threshold) {
            $this->notificationService->largeTransaction(
                $user->id,
                $transaction,
                $amountInUserCurrency,
                $userCurrency,
                $threshold
            );
        }

        // Check for negative balance after transaction
        $this->checkNegativeBalance($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => $transaction->load(['category', 'bankAccount']),
        ], 201);
    }

    /**
     * Update transaction
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $transaction = Transaction::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'date' => 'sometimes|date',
            'description' => 'sometimes|string|max:255',
            'merchant' => 'nullable|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|in:credit,debit',
            'category_id' => 'nullable|exists:categories,id',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:pending,completed',
        ]);

        // Convert incoming amount (which is in user's display currency) to base (INR) for storage.
        if (isset($validated['amount'])) {
            $currencyService = app(\App\Services\CurrencyService::class);
            $userSetting = app('user.settings');
            $userCurrency = $userSetting->display_currency ?? $request->user()->currency ?? config('currency.default', 'INR');

            try {
                // Convert from display currency -> INR (base)
                $convertedToInr = $currencyService->convertToINR((float) $validated['amount'], $userCurrency);
            } catch (\Exception $e) {
                $convertedToInr = (float) $validated['amount'];
            }

            $validated['amount'] = round($convertedToInr, 2);
            // Ensure stored currency is base (INR)
            $validated['currency'] = $currencyService->getBaseCurrency();
        }

        $transaction->update($validated);

        // Check for negative balance after update
        $this->checkNegativeBalance($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'data' => $transaction->load(['category', 'bankAccount']),
        ]);
    }

    /**
     * Delete transaction
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $userId = $request->user() ? $request->user()->id : session('user_id');

        $transaction = Transaction::where('user_id', $userId)
            ->findOrFail($id);

        $transaction->delete();

        // Check for negative balance after deletion
        $this->checkNegativeBalance($userId);

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully',
        ]);
    }

    /**
     * Bulk categorize transactions
     */
    public function bulkCategorize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ]);

        $updated = Transaction::where('user_id', $request->user()->id)
            ->whereIn('id', $validated['transaction_ids'])
            ->update(['category_id' => $validated['category_id']]);

        return response()->json([
            'success' => true,
            'message' => "Updated {$updated} transactions",
            'data' => ['updated_count' => $updated],
        ]);
    }

    /**
     * Import transactions from CSV
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');

        $imported = 0;
        $skipped = 0;
        $errors = [];

        // Skip header row
        fgetcsv($handle);

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                try {
                    // Expected CSV format: date, description, merchant, amount, type, category
                    $transaction = Transaction::create([
                        'user_id' => $request->user()->id,
                        'date' => $row[0],
                        'description' => $row[1],
                        'merchant' => $row[2] ?? null,
                        'amount' => abs((float) $row[3]),
                        'currency' => $request->user()->currency ?? 'INR',
                        'type' => $row[4] ?? 'debit',
                        'status' => 'completed',
                    ]);

                    // Auto-categorize
                    $this->categorizationService->categorizeTransaction($transaction);

                    $imported++;

                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Row skipped: " . implode(',', $row);
                }
            }

            fclose($handle);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Imported {$imported} transactions, skipped {$skipped}",
                'data' => [
                    'imported' => $imported,
                    'skipped' => $skipped,
                    'errors' => $errors,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);

            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category suggestions for transaction
     */
    public function suggestCategory(Request $request, int $id): JsonResponse
    {
        $transaction = Transaction::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $suggestions = $this->categorizationService->suggestCategory($transaction);

        return response()->json([
            'success' => true,
            'data' => $suggestions,
        ]);
    }

    /**
     * Check if user has negative balance and send notification
     */
    private function checkNegativeBalance(int $userId): void
    {
        // Calculate total income (credit transactions)
        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->sum('amount');

        // Calculate total expenses (debit transactions)
        $totalExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'debit')
            ->where('status', 'completed')
            ->sum('amount');

        // Calculate total balance
        $totalBalance = $totalIncome - $totalExpenses;

        // Check if balance is negative
        if ($totalBalance < 0) {
            // Check if notification was already sent recently (within last 24 hours)
            $recentNotification = \App\Models\Notification::where('user_id', $userId)
                ->where('type', 'negative_balance')
                ->where('created_at', '>', now()->subHours(24))
                ->first();

            // Only send notification if no recent notification exists
            if (!$recentNotification) {
                $this->notificationService->negativeBalance(
                    $userId,
                    $totalBalance,
                    $totalIncome,
                    $totalExpenses
                );
            }
        }
    }
}

