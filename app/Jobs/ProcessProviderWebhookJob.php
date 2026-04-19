<?php

namespace App\Jobs;

use App\Models\SyncLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessProviderWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function handle(): void
    {
        Log::info("Processing provider webhook", ['payload' => $this->payload]);

        // Extract relevant data from webhook
        $eventType = $this->payload['event_type'] ?? null;
        $accountId = $this->payload['account_id'] ?? null;

        if (!$eventType || !$accountId) {
            Log::warning("Invalid webhook payload", ['payload' => $this->payload]);
            return;
        }

        // Handle different event types
        switch ($eventType) {
            case 'transaction.created':
                $this->handleNewTransaction();
                break;
            case 'transaction.updated':
                $this->handleUpdatedTransaction();
                break;
            case 'account.updated':
                $this->handleAccountUpdate();
                break;
            case 'consent.revoked':
                $this->handleConsentRevoked();
                break;
            default:
                Log::info("Unhandled webhook event", ['type' => $eventType]);
                break;
        }
    }

    protected function handleNewTransaction(): void
    {
        // Trigger sync for the specific account
        $bankAccount = \App\Models\BankAccount::where('provider_account_id', $this->payload['account_id'])->first();
        
        if ($bankAccount) {
            SyncTransactionsJob::dispatch($bankAccount->id);
        }
    }

    protected function handleUpdatedTransaction(): void
    {
        // Similar to new transaction
        $this->handleNewTransaction();
    }

    protected function handleAccountUpdate(): void
    {
        $bankAccount = \App\Models\BankAccount::where('provider_account_id', $this->payload['account_id'])->first();
        
        if ($bankAccount && isset($this->payload['balance'])) {
            $bankAccount->update(['balance' => $this->payload['balance']]);
        }
    }

    protected function handleConsentRevoked(): void
    {
        $bankAccount = \App\Models\BankAccount::where('provider_account_id', $this->payload['account_id'])->first();
        
        if ($bankAccount) {
            $bankAccount->update(['status' => 'inactive']);
            
            SyncLog::logSync(
                $bankAccount->user_id,
                $bankAccount->id,
                'consent_revoked',
                'failed',
                'User revoked access from bank provider'
            );
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessProviderWebhookJob failed", [
            'payload' => $this->payload,
            'error' => $exception->getMessage(),
        ]);
    }
}
