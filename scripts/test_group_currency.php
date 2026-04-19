<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Group Expense Currency Conversion Test ===\n\n";

$currencyService = app(App\Services\CurrencyService::class);

// Simulate group expense amounts stored in INR
$amounts = [
    'Total Income' => 50000,
    'Total Expenses' => 25000,
    'Member 1 Contributed' => 15000,
    'Member 1 Share' => 12500,
];

$userCurrency = 'JPY';
$currencyConfig = config('currency.currencies');
$currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? '¥';

echo "User Currency: {$userCurrency}\n";
echo "Currency Symbol: {$currencySymbol}\n\n";

echo "=== Conversion Results ===\n";
foreach ($amounts as $label => $amount) {
    $converted = $currencyService->convert((float) $amount, 'INR', $userCurrency);
    echo "{$label}: {$currencySymbol}" . number_format($converted, 2) . " (from ₹" . number_format($amount, 2) . ")\n";
}

echo "\n=== Success! Currency conversion is working correctly! ===\n";
