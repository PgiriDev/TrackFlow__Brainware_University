<?php

namespace App\Jobs;

use App\Models\BankAccount;
use App\Services\SyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 90;
    public $backoff = [60, 300, 900];
    public $bankAccountId;

    public function __construct(int $bankAccountId)
    {
        $this->bankAccountId = $bankAccountId;
    }

    public function handle(SyncService $syncService): void
    {
        $account = BankAccount::find($this->bankAccountId);

        if (!$account) {
            Log::warning("Bank account not found for sync", ['id' => $this->bankAccountId]);
            return;
        }

        if ($account->status !== 'active') {
            Log::info("Skipping inactive account", ['bank_account_id' => $account->id]);
            return;
        }

        Log::info("Starting scheduled sync", ['bank_account_id' => $account->id]);

        $result = $syncService->syncAccount($account, false);

        if ($result['success']) {
            Log::info("Sync completed", [
                'bank_account_id' => $account->id,
                'stats' => $result['stats'],
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncTransactionsJob failed", [
            'bank_account_id' => $this->bankAccountId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Mark account as error after max retries
        if ($this->attempts() >= $this->tries) {
            $account = BankAccount::find($this->bankAccountId);
            if ($account) {
                $account->update(['status' => 'error']);
            }
        }
    }
}
