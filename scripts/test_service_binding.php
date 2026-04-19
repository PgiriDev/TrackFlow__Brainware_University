<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=== Testing CurrencyService Binding ===\n\n";

// Check if CurrencyService is bound
$isBound = app()->bound(\App\Services\CurrencyService::class);
echo "CurrencyService is bound: " . ($isBound ? 'YES' : 'NO') . "\n\n";

if (!$isBound) {
    echo "Trying to create manually...\n";
    $currencyService = new \App\Services\CurrencyService();
} else {
    $currencyService = app(\App\Services\CurrencyService::class);
}

echo "CurrencyService instance: " . ($currencyService ? 'Created' : 'NULL') . "\n\n";

// Check what rates are loaded
echo "=== Rates in CurrencyService ===\n";
$rates = $currencyService->getRates();
print_r($rates);

echo "\n=== Base Currency ===\n";
echo $currencyService->getBaseCurrency() . "\n";

// Test conversion
echo "\n=== Test Conversion ===\n";
$result = $currencyService->convert(13363.64, 'INR', 'JPY');
echo "13363.64 INR -> JPY = " . number_format($result, 2) . "\n";
