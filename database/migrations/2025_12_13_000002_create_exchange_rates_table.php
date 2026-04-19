<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateExchangeRatesTable extends Migration
{
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('currency_code', 3)->unique();
            $table->decimal('rate_to_inr', 18, 8)->default(0); // 1 unit = X INR
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();
        });

        // Seed initial rates based on config (config.rates assumed relative to USD)
        $configRates = config('currency.rates', []);
        if (!empty($configRates) && isset($configRates['INR'])) {
            $inrPerUsd = $configRates['INR'];
            foreach ($configRates as $code => $val) {
                // val is relative to USD: 1 USD = val units of that currency
                // compute 1 unit = (1/val) USD; then convert to INR: (1/val) * inrPerUsd
                if ($val <= 0) {
                    continue;
                }
                $rateToInr = ($inrPerUsd / $val);
                DB::table('exchange_rates')->updateOrInsert([
                    'currency_code' => strtoupper($code)
                ], [
                    'rate_to_inr' => round($rateToInr, 8),
                    'fetched_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('exchange_rates');
    }
}
