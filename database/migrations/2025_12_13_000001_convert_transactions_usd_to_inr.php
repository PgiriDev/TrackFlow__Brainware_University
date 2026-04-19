<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert existing transactions stored in USD to INR.
     *
     * @return void
     */
    public function up()
    {
        // Use the application's CurrencyService to convert amounts
        $currencyService = app(\App\Services\CurrencyService::class);

        // Convert only rows that look like they are stored in USD (legacy)
        $txs = \DB::table('transactions')->whereNull('currency')->orWhere('currency', 'USD')->get();
        foreach ($txs as $tx) {
            $original = (float) $tx->amount;
            // Convert USD -> INR using the service
            $converted = $currencyService->convert($original, 'USD', 'INR');
            \DB::table('transactions')->where('id', $tx->id)->update([
                'amount' => round($converted, 2),
                'currency' => 'INR'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     * Convert back from INR to USD for rollback.
     *
     * @return void
     */
    public function down()
    {
        $currencyService = app(\App\Services\CurrencyService::class);
        $txs = \DB::table('transactions')->where('currency', 'INR')->get();
        foreach ($txs as $tx) {
            $original = (float) $tx->amount;
            $converted = $currencyService->convert($original, 'INR', 'USD');
            \DB::table('transactions')->where('id', $tx->id)->update([
                'amount' => round($converted, 2),
                'currency' => 'USD'
            ]);
        }
    }
};
