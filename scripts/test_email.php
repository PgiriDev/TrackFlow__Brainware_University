<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Testing Advanced Email Templates...\n";
echo "===================================\n\n";

$config = config('mail');
echo "Mail Driver: " . $config['default'] . "\n\n";

try {
    // Test Password Change OTP Template
    Mail::send('email-template.password-change-otp', [
        'userName' => 'John Doe',
        'otp' => '847291',
        'title' => 'Password Change Verification'
    ], function ($message) {
        $message->to(config('mail.from.address'))
            ->subject('🔐 Password Change OTP - TrackFlow');
    });

    echo "✅ Password Change OTP email sent!\n";
    echo "📧 Check your inbox at " . config('mail.from.address') . "\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
