<?php

namespace App\Services;

class CurrencyService
{
    protected $rates;
    // Base currency for internal storage / conversions. All amounts are stored in INR.
    // Rates in config are relative to INR (INR=1.00).
    protected $baseCurrency = 'INR';

    public function __construct()
    {
        // Config rates are relative to INR (INR = 1.00, other currencies = how many units per 1 INR)
        // Example: USD = 0.011 means 1 INR = 0.011 USD
        $default = config('currency.rates', [
            'INR' => 1.00,
            'USD' => 0.011,
            'EUR' => 0.0095,
            'GBP' => 0.0083,
            'JPY' => 1.76,
        ]);

        // Optionally fetch live rates if enabled via env
        if (env('CURRENCY_USE_LIVE', false)) {
            $live = $this->fetchLiveRates(array_keys(config('currency.currencies')));
            if ($live && is_array($live)) {
                $this->rates = array_merge($default, $live);
                return;
            }
        }

        // Skip loading from exchange_rates table - use config rates for INR-based system
        // The database rates are stored in USD-based format which is incompatible
        // with the INR-based conversion logic

        // Use config rates directly - they are already relative to INR
        $this->rates = [];
        foreach ($default as $code => $val) {
            if ($val <= 0)
                continue;
            $this->rates[strtoupper($code)] = (float) $val;
        }
    }

    /**
     * Fetch live rates from a public API (exchangerate.host) with USD base.
     * Returns an associative array of rates keyed by currency code on success, or null on failure.
     */
    protected function fetchLiveRates(array $symbols = [])
    {
        try {
            $base = 'USD';
            $symbolsParam = implode(',', $symbols ?: array_keys(config('currency.currencies')));
            $url = env('CURRENCY_API_URL', 'https://api.exchangerate.host/latest') . '?base=' . $base . '&symbols=' . urlencode($symbolsParam);

            $ctx = stream_context_create(['http' => ['timeout' => 5]]);
            $response = @file_get_contents($url, false, $ctx);
            if (!$response) {
                return null;
            }

            $json = json_decode($response, true);
            if (!isset($json['rates']) || !is_array($json['rates'])) {
                return null;
            }

            return $json['rates'];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Convert amount from one currency to another
     *
     * Rates are stored relative to INR (1 INR = X units of currency)
     * Example: USD rate = 0.011 means 1 INR = 0.011 USD
     *
     * To convert FROM currency X TO INR: amount / rate[X]
     * To convert FROM INR TO currency X: amount * rate[X]
     * To convert FROM currency X TO currency Y: (amount / rate[X]) * rate[Y]
     */
    public function convert($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $from = strtoupper($fromCurrency);
        $to = strtoupper($toCurrency);

        $rateFrom = $this->rates[$from] ?? null; // 1 INR = rateFrom units of FROM currency
        $rateTo = $this->rates[$to] ?? null; // 1 INR = rateTo units of TO currency

        if (!$rateFrom || !$rateTo) {
            // fallback: no rate available, return original amount
            return $amount;
        }

        // Step 1: Convert from source currency to INR
        // If 1 INR = rateFrom units of FROM, then amount units of FROM = amount / rateFrom INR
        $amountInInr = $amount / $rateFrom;

        // Step 2: Convert from INR to target currency
        // If 1 INR = rateTo units of TO, then amountInInr INR = amountInInr * rateTo units of TO
        $result = $amountInInr * $rateTo;

        return $result;
    }

    /**
     * Convert to base currency (INR)
     */
    public function toBase($amount, $fromCurrency)
    {
        return $this->convert($amount, $fromCurrency, $this->baseCurrency);
    }

    /**
     * Convert from base currency (INR)
     */
    public function fromBase($amount, $toCurrency)
    {
        return $this->convert($amount, $this->baseCurrency, $toCurrency);
    }

    // Legacy helper methods - now use the main convert() function
    public function convertToINR($amount, $currency)
    {
        return $this->convert($amount, $currency, 'INR');
    }

    public function convertFromINR($amountInInr, $currency)
    {
        return $this->convert($amountInInr, 'INR', $currency);
    }

    /**
     * Convert amount to USD (convenience method)
     */
    public function convertToUSD($amount, $currency)
    {
        return $this->convert($amount, $currency, 'USD');
    }

    /**
     * Convert amount from USD (convenience method)
     */
    public function convertFromUSD($amountInUsd, $currency)
    {
        return $this->convert($amountInUsd, 'USD', $currency);
    }

    /**
     * Get exchange rate
     */
    public function getRate($currency)
    {
        return $this->rates[$currency] ?? 1.00;
    }

    /**
     * Return the configured base currency code
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * Get all rates
     */
    public function getAllRates()
    {
        return $this->rates;
    }
}
