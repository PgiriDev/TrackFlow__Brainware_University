<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\EmailNotificationService;

echo "Testing Goal/Budget Email Notifications...\n";
echo "==========================================\n\n";

// List all users first
echo "--- Available Users ---\n";
$users = User::all(['id', 'name', 'email']);
foreach ($users as $u) {
    echo $u->id . ' - ' . $u->name . ' - ' . $u->email . "\n";
}
echo "\n";

// Use first user found
$user = User::first();
if (!$user) {
    echo "ERROR: No users found in database!\n";
    exit(1);
}

$userId = $user->id;
echo "Testing with User ID: {$userId} ({$user->email})\n\n";

// Check notification preferences
$pref = DB::table('notification_preferences')->where('user_id', $userId)->first();
echo "Preferences exist: " . ($pref ? 'YES' : 'NO') . "\n";

// Check email_notifications column
if ($pref) {
    echo "Email Types: " . ($pref->email_notifications ?? 'NULL/EMPTY') . "\n";
    $types = json_decode($pref->email_notifications ?? '[]', true);
    echo "Parsed types: " . print_r($types, true) . "\n";
}

// Test sending email directly
echo "\n--- Testing Email Send ---\n";

$emailService = new EmailNotificationService();

// Try sending goal created email
try {
    $result = $emailService->sendGoalCreated($userId, [
        'goal_name' => 'Test Goal',
        'goal_icon' => '🎯',
        'goal_type' => 'savings',
        'target_amount' => 10000,
        'current_amount' => 0,
        'target_date' => '2026-12-31',
    ]);
    echo "Goal Created Email Result: " . ($result ? 'SENT' : 'NOT SENT (blocked by preferences)') . "\n";
} catch (Exception $e) {
    echo "Goal Created Email Error: " . $e->getMessage() . "\n";
}

// Try sending budget created email  
try {
    $result = $emailService->sendBudgetCreated($userId, [
        'budget_name' => 'Test Budget',
        'total_limit' => 50000,
        'month' => 1,
        'year' => 2026,
        'category_count' => 3,
        'categories' => [
            ['name' => 'Food', 'limit' => 15000],
            ['name' => 'Transport', 'limit' => 5000],
        ],
    ]);
    echo "Budget Created Email Result: " . ($result ? 'SENT' : 'NOT SENT (blocked by preferences)') . "\n";
} catch (Exception $e) {
    echo "Budget Created Email Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
