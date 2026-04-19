<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\CategorizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CategorizeTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $userId;
    public $transactionIds;

    public function __construct(int $userId, array $transactionIds)
    {
        $this->userId = $userId;
        $this->transactionIds = $transactionIds;
    }

    public function handle(CategorizationService $categorizationService): void
    {
        Log::info("Starting categorization", [
            'user_id' => $this->userId,
            'transaction_count' => count($this->transactionIds),
        ]);

        $result = $categorizationService->categorizeMultiple($this->userId, $this->transactionIds);

        Log::info("Categorization completed", [
            'user_id' => $this->userId,
            'result' => $result,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("CategorizeTransactionsJob failed", [
            'user_id' => $this->userId,
            'transaction_ids' => $this->transactionIds,
            'error' => $exception->getMessage(),
        ]);
    }
}
