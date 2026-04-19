<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = app(App\Services\CurrencyService::class);

echo "INR-based Currency Conversion Tests for Reports:\n";
echo "=================================================\n\n";

// Test 1: User adds 100 USD, display in INR
$usdAmount = 100;
$inr = $service->convert($usdAmount, 'USD', 'INR');
echo "1. 100 USD -> INR: " . number_format($inr, 2) . " INR\n";

// Test 2: User adds 1000 INR, display in USD
$inrAmount = 1000;
$usd = $service->convert($inrAmount, 'INR', 'USD');
echo "2. 1000 INR -> USD: " . number_format($usd, 2) . " USD\n";

// Test 3: User adds 100 EUR, display in INR
$eurAmount = 100;
$inr = $service->convert($eurAmount, 'EUR', 'INR');
echo "3. 100 EUR -> INR: " . number_format($inr, 2) . " INR\n";

// Test 4: User adds 1000 JPY, display in INR
$jpyAmount = 1000;
$inr = $service->convert($jpyAmount, 'JPY', 'INR');
echo "4. 1000 JPY -> INR: " . number_format($inr, 2) . " INR\n";

// Test 5: Cross-currency USD to EUR
$usdToEur = $service->convert(100, 'USD', 'EUR');
echo "5. 100 USD -> EUR: " . number_format($usdToEur, 2) . " EUR\n";

echo "\n";
echo "Base Currency: " . $service->getBaseCurrency() . "\n";
echo "All conversions pass through INR as intermediary.\n";

echo "\n=== Report Scenario Test ===\n";
echo "If a transaction is stored with amount=100 and currency=USD:\n";
$stored = 100;
$storedCurrency = 'USD';
$userCurrency = 'INR';
$converted = $service->convert($stored, $storedCurrency, $userCurrency);
echo "  - Stored: $storedCurrency $stored\n";
echo "  - Display (INR): ₹" . number_format($converted, 2) . "\n";
