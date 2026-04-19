<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\RecurringTransaction;
use App\Services\EmailNotificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendPaymentRemindersCommand extends Command
{
    protected $signature = 'notifications:payment-reminders
                            {--days=3 : Days before due date to send reminder}';

    protected $description = 'Send payment reminder emails for upcoming recurring transactions';

    protected $emailService;

    public function __construct()
    {
        parent::__construct();
        $this->emailService = new EmailNotificationService();
    }

    public function handle(): int
    {
        $this->info('Sending payment reminders...');

        $daysAhead = (int) $this->option('days');
        $targetDate = Carbon::now()->addDays($daysAhead)->startOfDay();
        $remindersSent = 0;

        // Get all users with recurring transactions due in the next X days
        $users = User::whereHas('recurringTransactions', function ($query) use ($targetDate) {
            $query->where('is_active', true)
                ->whereDate('next_occurrence', '<=', $targetDate);
        })->with([
                    'recurringTransactions' => function ($query) use ($targetDate) {
                        $query->where('is_active', true)
                            ->whereDate('next_occurrence', '<=', $targetDate)
                            ->orderBy('next_occurrence');
                    }
                ])->get();

        foreach ($users as $user) {
            $upcomingPayments = [];
            $totalAmount = 0;

            foreach ($user->recurringTransactions as $transaction) {
                $daysUntilDue = Carbon::now()->startOfDay()->diffInDays($transaction->next_occurrence, false);

                $upcomingPayments[] = [
                    'name' => $transaction->description ?? $transaction->category->name ?? 'Payment',
                    'amount' => number_format($transaction->amount, 2),
                    'due_date' => Carbon::parse($transaction->next_occurrence)->format('M d, Y'),
                    'category' => $transaction->category->name ?? 'Other',
                    'days_until' => $daysUntilDue,
                    'is_overdue' => $daysUntilDue < 0,
                ];

                $totalAmount += $transaction->amount;
            }

            if (count($upcomingPayments) > 0) {
                try {
                    $this->emailService->sendPaymentReminder($user->id, [
                        'payments' => $upcomingPayments,
                        'total_amount' => number_format($totalAmount, 2),
                        'payment_count' => count($upcomingPayments),
                        'days_ahead' => $daysAhead,
                    ]);

                    $remindersSent++;
                    $this->info("Sent reminder to {$user->email} for " . count($upcomingPayments) . " upcoming payments");
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder to {$user->email}: " . $e->getMessage());
                }
            }
        }

        $this->info("Payment reminders sent: {$remindersSent}");

        return Command::SUCCESS;
    }
}
