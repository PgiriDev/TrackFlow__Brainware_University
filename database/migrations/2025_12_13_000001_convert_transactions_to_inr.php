<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ConvertTransactionsToInr extends Migration
{
    /**
     * Run the migrations.
     * This migration will add `original_amount` and `original_currency` columns
     * and convert existing transaction `amount` values into the project's base currency (INR).
     * It preserves original values so the change is reversible.
     */
    public function up()
    {
        // Add columns to preserve original values
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'original_amount')) {
                $table->decimal('original_amount', 14, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('transactions', 'original_currency')) {
                $table->string('original_currency', 3)->nullable()->after('original_amount');
            }
        });

        // Perform conversion in chunks to avoid memory issues
        $currencyService = app(\App\Services\CurrencyService::class);
        $base = $currencyService->getBaseCurrency();

        DB::table('transactions')->select('id', 'amount', 'currency')->orderBy('id')->chunkById(200, function ($rows) use ($currencyService, $base) {
            foreach ($rows as $row) {
                $currentCurrency = $row->currency ?? $base;
                // If already in base, just ensure original fields are set
                if ($currentCurrency === $base) {
                    DB::table('transactions')->where('id', $row->id)->update([
                        'original_amount' => $row->amount,
                        'original_currency' => $currentCurrency,
                    ]);
                    continue;
                }

                // Convert amount from stored currency to base currency
                try {
                    $converted = $currencyService->convert((float) $row->amount, $currentCurrency, $base);
                } catch (\Exception $e) {
                    // On failure, skip conversion and only set original fields
                    DB::table('transactions')->where('id', $row->id)->update([
                        'original_amount' => $row->amount,
                        'original_currency' => $currentCurrency,
                    ]);
                    continue;
                }

                DB::table('transactions')->where('id', $row->id)->update([
                    'original_amount' => $row->amount,
                    'original_currency' => $currentCurrency,
                    'amount' => round($converted, 2),
                    'currency' => $base,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     * This will restore `amount` and `currency` from the preserved original columns and then drop them.
     */
    public function down()
    {
        // Restore original amounts if present
        DB::table('transactions')->select('id', 'original_amount', 'original_currency')->whereNotNull('original_currency')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('transactions')->where('id', $row->id)->update([
                    'amount' => $row->original_amount,
                    'currency' => $row->original_currency,
                ]);
            }
        });

        // Drop the preserving columns
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'original_amount')) {
                $table->dropColumn('original_amount');
            }
            if (Schema::hasColumn('transactions', 'original_currency')) {
                $table->dropColumn('original_currency');
            }
        });
    }
}
