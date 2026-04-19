<?php

namespace App\Console\Commands;

use App\Services\SyncService;
use Illuminate\Console\Command;

class SyncAllAccountsCommand extends Command
{
    protected $signature = 'sync:accounts
                            {--force : Force sync even if not due}';

    protected $description = 'Sync all active bank accounts';

    public function handle(SyncService $syncService): int
    {
        $this->info('Starting sync for all accounts...');

        $syncService->syncAllAccounts();

        $this->info('Sync completed!');

        return Command::SUCCESS;
    }
}
