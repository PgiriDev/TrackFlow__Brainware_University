<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

$currencyService = app(App\Services\CurrencyService::class);

echo "=== Simulating Report Generation ===\n\n";

// Get user 21's settings (same as ReportController does)
$userId = 21;
$user = DB::table('users')->where('id', $userId)->first();
$userSetting = DB::table('user_settings')->where('user_id', $userId)->first();

$userCurrency = strtoupper(trim($userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR')));
echo "User Currency: {$userCurrency}\n\n";

// Get transactions like gatherConsolidatedData does
$transactions = Transaction::where('user_id', $userId)
    ->with('category')
    ->orderBy('date', 'desc')
    ->get();

echo "=== Transactions ===\n";
foreach ($transactions as $tx) {
    $storedCurrency = $tx->currency ?? ($currencyService ? $currencyService->getBaseCurrency() : 'INR');

    echo "ID: {$tx->id}\n";
    echo "  Raw Amount: {$tx->amount}\n";
    echo "  Stored Currency: {$storedCurrency}\n";
    echo "  User Display Currency: {$userCurrency}\n";

    if ($currencyService) {
        try {
            $converted = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
            echo "  Converted Amount: " . number_format($converted, 2) . " {$userCurrency}\n";
        } catch (\Exception $e) {
            echo "  ERROR: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
}

// Get currency symbol
$currencyConfig = config('currency.currencies');
$currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? '¥';

echo "=== Expected in Report ===\n";
echo "Currency Symbol: {$currencySymbol}\n";

// Calculate totals like the report does
$totalIncome = 0;
$totalExpenses = 0;

foreach ($transactions as $tx) {
    $storedCurrency = $tx->currency ?? 'INR';
    $converted = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);

    if ($tx->type === 'credit') {
        $totalIncome += $converted;
    } else {
        $totalExpenses += $converted;
    }
}

echo "Total Income: {$currencySymbol}" . number_format($totalIncome, 2) . "\n";
echo "Total Expenses: {$currencySymbol}" . number_format($totalExpenses, 2) . "\n";
echo "Net Balance: {$currencySymbol}" . number_format($totalIncome - $totalExpenses, 2) . "\n";
