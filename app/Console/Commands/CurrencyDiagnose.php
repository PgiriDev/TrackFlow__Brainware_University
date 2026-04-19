<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class CurrencyDiagnose extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --migrate : run the conversion migration
     * --rollback : rollback the last migration step
     */
    protected $signature = 'currency:diagnose {--migrate} {--rollback} {--sample=20}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnostic and normalization helper for currency and transactions (INR base)';

    public function handle()
    {
        $this->info('Currency diagnosis starting...');

        $base = config('currency.default', 'INR');

        $total = DB::table('transactions')->count();
        $nonBase = DB::table('transactions')->where('currency', '!=', $base)->count();

        $this->line("Total transactions: {$total}");
        $this->line("Transactions not in base ({$base}): {$nonBase}");

        $this->info('Top currencies:');
        $top = DB::table('transactions')
            ->select('currency', DB::raw('count(*) as cnt'))
            ->groupBy('currency')
            ->orderByDesc('cnt')
            ->get();

        foreach ($top as $row) {
            $this->line(" - {$row->currency}: {$row->cnt}");
        }

        $sample = (int) $this->option('sample');
        $this->info("Showing sample of {$sample} transactions not in {$base} (id, amount, currency, original_amount, original_currency, date):");
        $rows = DB::table('transactions')
            ->where('currency', '!=', $base)
            ->orWhereNotNull('original_currency')
            ->orderByDesc('id')
            ->limit($sample)
            ->get();

        if ($rows->isEmpty()) {
            $this->line('No suspect rows found.');
        } else {
            $headers = ['id', 'amount', 'currency', 'original_amount', 'original_currency', 'date'];
            $data = [];
            foreach ($rows as $r) {
                $data[] = [
                    $r->id,
                    $r->amount,
                    $r->currency,
                    $r->original_amount,
                    $r->original_currency,
                    $r->date
                ];
            }
            $this->table($headers, $data);
        }

        if ($this->option('migrate')) {
            $this->warn('Running migrations now (this will modify data). Make sure you have a DB backup.');
            if ($this->confirm('Proceed running migrations?')) {
                Artisan::call('migrate', ['--force' => true]);
                $this->info('Migrations executed (see output above).');
            } else {
                $this->info('Migration aborted.');
            }
        }

        if ($this->option('rollback')) {
            $this->warn('Rolling back last migration step (this will modify data).');
            if ($this->confirm('Proceed with rollback?')) {
                Artisan::call('migrate:rollback', ['--step' => 1, '--force' => true]);
                $this->info('Rollback executed.');
            } else {
                $this->info('Rollback aborted.');
            }
        }

        $this->info('Diagnosis complete.');
        return 0;
    }
}
