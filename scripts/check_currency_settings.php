<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Users Table ===\n";
$users = DB::table('users')->select('id', 'name', 'currency')->get();
foreach ($users as $u) {
    echo "  ID: {$u->id}, Name: {$u->name}, Currency: " . ($u->currency ?? 'NULL') . "\n";
}

echo "\n=== User Settings Table ===\n";
$settings = DB::table('user_settings')->get();
if ($settings->isEmpty()) {
    echo "  No records in user_settings table\n";
} else {
    foreach ($settings as $s) {
        echo "  User ID: {$s->user_id}, Display Currency: " . ($s->display_currency ?? 'NULL') . "\n";
    }
}

echo "\n=== Transactions ===\n";
$transactions = DB::table('transactions')->select('id', 'amount', 'currency', 'description', 'user_id')->limit(5)->get();
foreach ($transactions as $tx) {
    echo "  ID: {$tx->id}, Amount: {$tx->amount}, Currency: " . ($tx->currency ?? 'NULL') . ", Desc: {$tx->description}\n";
}
