<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Verify CurrencyService Fix ===\n\n";

// Check if CurrencyService is now bound
$isBound = app()->bound(\App\Services\CurrencyService::class);
echo "CurrencyService is bound: " . ($isBound ? 'YES' : 'NO') . "\n\n";

// Get the service
$currencyService = app(\App\Services\CurrencyService::class);
echo "CurrencyService instance created: " . ($currencyService ? 'YES' : 'NO') . "\n\n";

// Test conversion
echo "=== Test Conversion ===\n";
$result = $currencyService->convert(13363.64, 'INR', 'JPY');
echo "13363.64 INR -> JPY = ¥" . number_format($result, 2) . "\n";

echo "\n=== This should now show in the report! ===\n";
