<?php

namespace App\Services;

use App\Jobs\CategorizeTransactionsJob;
use App\Models\BankAccount;
use App\Models\SyncLog;
use App\Models\Transaction;
use App\Services\Providers\FinvuProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncService
{
    protected $provider;
    protected $categorizationService;

    public function __construct(CategorizationService $categorizationService)
    {
        $this->categorizationService = $categorizationService;
    }

    public function syncAccount(BankAccount $account, bool $fullSync = false): array
    {
        $lockKey = "sync:bank_account:{$account->id}";
        
        // Acquire lock to prevent concurrent syncs
        $lock = Cache::lock($lockKey, 300); // 5 minutes

        if (!$lock->get()) {
            Log::warning("Sync already in progress", ['bank_account_id' => $account->id]);
            return [
                'success' => false,
                'message' => 'Sync already in progress',
            ];
        }

        try {
            $this->provider = $this->getProvider($account->provider);
            
            // Refresh token if needed
            $token = $account->accountToken;
            if (!$token) {
                throw new \Exception("No access token found for account");
            }

            $token = $this->provider->refreshIfNeeded($token);

            // Determine date range
            $fromDate = $fullSync 
                ? now()->subDays(90) 
                : ($account->last_synced_at ?? now()->subDays(90));

            $toDate = now();

            // Fetch transactions from provider
            $providerTransactions = $this->provider->fetchTransactions(
                $token->access_token,
                $account->provider_account_id,
                $fromDate,
                $toDate
            );

            $stats = $this->processTransactions($account, $providerTransactions);

            // Update account
            $account->update([
                'last_synced_at' => now(),
                'status' => 'active',
            ]);

            // Log success
            SyncLog::logSync(
                $account->user_id,
                $account->id,
                'sync_transactions',
                'success',
                "Synced {$stats['imported']} transactions",
                $stats['imported']
            );

            $lock->release();

            return [
                'success' => true,
                'stats' => $stats,
            ];

        } catch (\Exception $e) {
            Log::error("Sync failed", [
                'bank_account_id' => $account->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Log failure
            SyncLog::logSync(
                $account->user_id,
                $account->id,
                'sync_transactions',
                'failed',
                $e->getMessage(),
                0,
                ['exception' => $e->getMessage()]
            );

            $account->update(['status' => 'error']);

            $lock->release();

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function processTransactions(BankAccount $account, array $providerTransactions): array
    {
        $imported = 0;
        $duplicates = 0;
        $newTransactionIds = [];

        DB::beginTransaction();

        try {
            foreach ($providerTransactions as $txData) {
                // Check for existing transaction by provider_tx_id
                if ($txData['provider_tx_id']) {
                    $existing = Transaction::where('provider_tx_id', $txData['provider_tx_id'])->first();
                    if ($existing) {
                        $duplicates++;
                        continue;
                    }
                }

                // Create transaction
                $transaction = Transaction::create([
                    'user_id' => $account->user_id,
                    'bank_account_id' => $account->id,
                    'date' => $txData['date'],
                    'description' => $txData['description'],
                    'merchant' => $txData['merchant'],
                    'amount' => $txData['amount'],
                    'currency' => $txData['currency'],
                    'type' => $txData['type'],
                    'status' => 'completed',
                    'provider_tx_id' => $txData['provider_tx_id'] ?? null,
                    'raw' => $txData['raw'] ?? $txData['raw_data'] ?? null,
                ]);

                // Check for duplicates by hash
                $transactionHash = $transaction->generateHash();
                $duplicate = Transaction::where('user_id', $account->user_id)
                    ->where('bank_account_id', $account->id)
                    ->where('date', $txData['date'])
                    ->where('amount', $txData['amount'])
                    ->where('id', '!=', $transaction->id)
                    ->get()
                    ->first(function($tx) use ($transactionHash) {
                        return $tx->generateHash() === $transactionHash;
                    });

                if ($duplicate) {
                    $transaction->update([
                        'is_duplicate' => true,
                        'duplicate_of_id' => $duplicate->id,
                    ]);
                    $duplicates++;
                } else {
                    $newTransactionIds[] = $transaction->id;
                    $imported++;
                }
            }

            DB::commit();

            // Dispatch categorization job for new transactions
            if (count($newTransactionIds) > 0) {
                CategorizeTransactionsJob::dispatch($account->user_id, $newTransactionIds);
            }

            return [
                'imported' => $imported,
                'duplicates' => $duplicates,
                'total' => count($providerTransactions),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function getProvider(string $providerName): BankProviderService
    {
        switch ($providerName) {
            case 'finvu':
                return app(FinvuProvider::class);
            default:
                throw new \Exception("Unsupported provider: {$providerName}");
        }
    }

    public function syncAllAccounts(): void
    {
        $accounts = BankAccount::where('status', 'active')
            ->whereHas('accountToken')
            ->get();

        foreach ($accounts as $account) {
            if ($account->needsSync()) {
                Log::info("Syncing account", ['bank_account_id' => $account->id]);
                $this->syncAccount($account);
            }
        }
    }
}
