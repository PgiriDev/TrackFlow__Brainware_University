<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$currencyService = app(App\Services\CurrencyService::class);

echo "=== Debug Report Currency Conversion ===\n\n";

// Get user 21's settings
$userId = 21;
$user = DB::table('users')->where('id', $userId)->first();
$userSetting = DB::table('user_settings')->where('user_id', $userId)->first();

echo "User: {$user->name}\n";
echo "User currency (users table): " . ($user->currency ?? 'NULL') . "\n";
echo "Display currency (user_settings): " . ($userSetting->display_currency ?? 'NULL') . "\n";

$userCurrency = strtoupper(trim($userSetting->display_currency ?? $user->currency ?? 'INR'));
echo "Resolved userCurrency: {$userCurrency}\n\n";

// Get the transaction
$tx = DB::table('transactions')->where('user_id', $userId)->first();
echo "=== Transaction ===\n";
echo "Amount: {$tx->amount}\n";
echo "Currency: " . ($tx->currency ?? 'NULL') . "\n";
echo "Description: {$tx->description}\n\n";

$storedCurrency = $tx->currency ?? 'INR';
echo "=== Conversion ===\n";
echo "From: {$storedCurrency}\n";
echo "To: {$userCurrency}\n";

$rates = config('currency.rates');
echo "Rate for {$storedCurrency}: " . ($rates[$storedCurrency] ?? 'NOT FOUND') . "\n";
echo "Rate for {$userCurrency}: " . ($rates[$userCurrency] ?? 'NOT FOUND') . "\n";

$converted = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
echo "\nConversion result:\n";
echo "  {$tx->amount} {$storedCurrency} => " . number_format($converted, 2) . " {$userCurrency}\n";

echo "\n=== Expected in Report ===\n";
$currencyConfig = config('currency.currencies');
$currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? '¥';
echo "Symbol: {$currencySymbol}\n";
echo "Formatted: {$currencySymbol}" . number_format($converted, 2) . "\n";
