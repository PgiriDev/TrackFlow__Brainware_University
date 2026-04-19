<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$logoPath = storage_path('logo_base64.txt');
echo "Storage path: " . $logoPath . PHP_EOL;
echo "File exists: " . (file_exists($logoPath) ? 'YES' : 'NO') . PHP_EOL;

if (file_exists($logoPath)) {
    $content = trim(file_get_contents($logoPath));
    echo "Content length: " . strlen($content) . " chars" . PHP_EOL;
    echo "Starts with: " . substr($content, 0, 30) . "..." . PHP_EOL;
} else {
    echo "Logo file not found!" . PHP_EOL;
}
