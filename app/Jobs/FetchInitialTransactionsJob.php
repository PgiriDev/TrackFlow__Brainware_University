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

class FetchInitialTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $bankAccountId;

    public function __construct(int $bankAccountId)
    {
        $this->bankAccountId = $bankAccountId;
    }

    public function handle(SyncService $syncService): void
    {
        $account = BankAccount::find($this->bankAccountId);

        if (!$account) {
            Log::error("Bank account not found", ['id' => $this->bankAccountId]);
            return;
        }

        Log::info("Fetching initial transactions", ['bank_account_id' => $account->id]);

        $result = $syncService->syncAccount($account, true);

        if ($result['success']) {
            Log::info("Initial fetch completed", [
                'bank_account_id' => $account->id,
                'stats' => $result['stats'],
            ]);
        } else {
            Log::error("Initial fetch failed", [
                'bank_account_id' => $account->id,
                'message' => $result['message'],
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("FetchInitialTransactionsJob failed", [
            'bank_account_id' => $this->bankAccountId,
            'error' => $exception->getMessage(),
        ]);
    }
}
