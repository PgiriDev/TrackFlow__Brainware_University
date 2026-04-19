<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Google OAuth Configuration ===\n";
echo "Client ID: " . config('services.google.client_id') . "\n";
echo "Client Secret: " . (config('services.google.client_secret') ? '[SET - ' . strlen(config('services.google.client_secret')) . ' chars]' : '[NOT SET]') . "\n";
echo "Redirect URI: " . config('services.google.redirect') . "\n";
echo "APP_URL: " . config('app.url') . "\n";
