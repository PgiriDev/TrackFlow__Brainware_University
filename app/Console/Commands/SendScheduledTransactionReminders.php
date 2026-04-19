<?php

namespace App\Console\Commands;

use App\Models\ScheduledTransaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendScheduledTransactionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for scheduled transactions due today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting scheduled transaction reminder process...');

        // Get all pending scheduled transactions due today that haven't received reminders
        $scheduledTransactions = ScheduledTransaction::with(['user', 'category'])
            ->where('status', 'pending')
            ->where('reminder_sent', false)
            ->whereDate('scheduled_date', today())
            ->get();

        $this->info("Found {$scheduledTransactions->count()} scheduled transactions due today.");

        $sentCount = 0;
        $failedCount = 0;

        foreach ($scheduledTransactions as $scheduled) {
            try {
                $user = $scheduled->user;

                if (!$user || !$user->email) {
                    $this->warn("Skipping scheduled transaction #{$scheduled->id}: No user or email");
                    continue;
                }

                // Get currency symbol
                $currencyConfig = config('currency.currencies');
                $userCurrency = $user->currency ?? 'INR';
                $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? '₹';

                // Send reminder email
                Mail::send('emails.scheduled-transaction-reminder', [
                    'userName' => $user->name ?? 'User',
                    'description' => $scheduled->description,
                    'merchant' => $scheduled->merchant,
                    'amount' => $scheduled->amount,
                    'currency' => $currencySymbol,
                    'type' => $scheduled->type,
                    'category' => $scheduled->category->name ?? null,
                    'notes' => $scheduled->notes,
                    'scheduledDate' => $scheduled->scheduled_date,
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('⏰ Scheduled Transaction Reminder - TrackFlow');
                });

                // Mark reminder as sent
                $scheduled->update([
                    'reminder_sent' => true,
                    'reminder_sent_at' => now(),
                ]);

                $sentCount++;
                $this->info("Sent reminder for scheduled transaction #{$scheduled->id} to {$user->email}");

            } catch (\Exception $e) {
                $failedCount++;
                Log::error("Failed to send scheduled transaction reminder #{$scheduled->id}: " . $e->getMessage());
                $this->error("Failed to send reminder for #{$scheduled->id}: " . $e->getMessage());
            }
        }

        $this->info("Completed! Sent: {$sentCount}, Failed: {$failedCount}");

        return Command::SUCCESS;
    }
}
