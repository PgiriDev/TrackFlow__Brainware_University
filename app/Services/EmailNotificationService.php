<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EmailNotificationService
{
    /**
     * Check if user has email notifications enabled for a specific type
     */
    protected function shouldSendEmail(int $userId, string $type): bool
    {
        $user = User::find($userId);
        if (!$user || !$user->email) {
            return false;
        }

        // Check user's notification preferences from user_preferences table
        $preference = DB::table('user_preferences')
            ->where('user_id', $userId)
            ->first();

        if (!$preference) {
            return true; // Default to sending emails if no preferences set
        }

        // Map email types to preference columns
        $typeMapping = [
            'budget_alert' => 'budget_alerts',
            'budget_created' => 'budget_alerts',
            'goal_progress' => 'goal_progress',
            'goal_created' => 'goal_progress',
            'transaction_alert' => 'transaction_alerts',
            'large_transaction' => 'large_transaction_alerts',
            'group_expense' => 'group_expense',
            'login_alert' => 'login_alerts',
            'new_device' => 'new_device_alerts',
            'weekly_summary' => 'weekly_summary',
            'payment_reminder' => 'budget_alerts', // Map payment reminders to budget alerts
        ];

        $preferenceColumn = $typeMapping[$type] ?? null;

        if (!$preferenceColumn) {
            return true; // Unknown type, default to send
        }

        // Check if the specific notification type is enabled
        $isEnabled = $preference->{$preferenceColumn} ?? true;

        return (bool) $isEnabled;
    }

    /**
     * Get user's preferred currency symbol
     */
    protected function getCurrencySymbol(int $userId): string
    {
        $user = User::find($userId);
        $currency = $user->currency ?? 'INR';
        $currencyConfig = config('currency.currencies');
        return $currencyConfig[$currency]['symbol'] ?? '₹';
    }

    /**
     * Send Budget Alert Email
     */
    public function sendBudgetAlert(int $userId, array $data): bool
    {
        if (!$this->shouldSendEmail($userId, 'budget_alert')) {
            return false;
        }

        $user = User::find($userId);
        if (!$user)
            return false;

        try {
            $spent = $data['spent_amount'] ?? $data['spent'] ?? 0;
            $limit = $data['budget_limit'] ?? $data['limit'] ?? 0;
            $percentage = $data['percentage'] ?? ($limit > 0 ? round(($spent / $limit) * 100, 1) : 0);
            $remaining = max(0, $limit - $spent);

            Mail::send('email-template.budget-alert', [
                'userName' => $user->name ?? 'User',
                'title' => 'Budget Alert',
                'budgetName' => $data['budget_name'] ?? 'Budget',
                'percentage' => $percentage,
                'spent' => $spent,
                'limit' => $limit,
                'remaining' => $remaining,
                'currency' => $this->getCurrencySymbol($userId),
                'daysLeft' => $data['days_left'] ?? null,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('📊 Budget Alert - TrackFlow');
            });

            Log::info("Budget alert email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send budget alert email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Goal Progress Email
     */
    public function sendGoalProgress(int $userId, array $data): bool
    {
        if (!$this->shouldSendEmail($userId, 'goal_progress')) {
            return false;
        }

        $user = User::find($userId);
        if (!$user)
            return false;

        try {
            $saved = $data['current_amount'] ?? $data['saved'] ?? 0;
            $target = $data['target_amount'] ?? $data['target'] ?? 0;
            $percentage = $data['percentage'] ?? ($target > 0 ? round(($saved / $target) * 100, 1) : 0);
            $remaining = $target - $saved;

            Mail::send('email-template.goal-progress', [
                'userName' => $user->name ?? 'User',
                'title' => 'Goal Progress Update',
                'goalName' => $data['goal_name'] ?? 'Goal',
                'goalIcon' => $data['goal_icon'] ?? '🎯',
                'percentage' => $percentage,
                'saved' => $saved,
                'target' => $target,
                'remaining' => max(0, $remaining),
                'currency' => $this->getCurrencySymbol($userId),
                'targetDate' => $data['deadline'] ?? $data['target_date'] ?? null,
                'daysLeft' => $data['days_left'] ?? null,
            ], function ($message) use ($user, $data) {
                $percentage = $data['percentage'] ?? 0;
                $emoji = $percentage >= 100 ? '🎉' : '📈';
                $message->to($user->email)
                    ->subject("{$emoji} Goal Progress - TrackFlow");
            });

            Log::info("Goal progress email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send goal progress email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Transaction Alert Email
     */
    public function sendTransactionAlert(int $userId, array $data): bool
    {
        if (!$this->shouldSendEmail($userId, 'transaction_alert')) {
            return false;
        }

        $user = User::find($userId);
        if (!$user)
            return false;

        try {
            $transactionType = $data['type'] ?? $data['transaction_type'] ?? 'expense';
            $amount = $data['amount'] ?? 0;
            $currency = $this->getCurrencySymbol($userId);

            Mail::send('email-template.transaction-alert', [
                'userName' => $user->name ?? 'User',
                'title' => 'Transaction Alert',
                'transactionType' => strtolower($transactionType),
                'amount' => $amount,
                'currency' => $currency,
                'description' => $data['description'] ?? null,
                'category' => $data['category'] ?? null,
                'accountName' => $data['account_name'] ?? $data['accountName'] ?? null,
                'transactionDate' => $data['date'] ?? $data['transaction_date'] ?? now()->format('M d, Y'),
                'merchant' => $data['merchant'] ?? null,
                'balance' => $data['balance'] ?? null,
                'isLargeTransaction' => $data['is_large'] ?? false,
                'isSuspicious' => $data['is_suspicious'] ?? false,
                'budgetImpact' => $data['budget_impact'] ?? null,
            ], function ($message) use ($user, $transactionType) {
                $emoji = strtolower($transactionType) === 'income' ? '💰' : '💸';
                $message->to($user->email)
                    ->subject("{$emoji} Transaction Alert - TrackFlow");
            });

            Log::info("Transaction alert email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send transaction alert email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Security Alert Email
     */
    public function sendSecurityAlert(int $userId, array $data): bool
    {
        $user = User::find($userId);
        if (!$user)
            return false;

        // Check alert type and respect user preferences
        $alertType = $data['alert_type'] ?? 'info';

        // Password change alerts are always sent (critical security)
        // But login and new device alerts respect user preferences
        if ($alertType === 'login' || $alertType === 'new_login') {
            if (!$this->shouldSendEmail($userId, 'login_alert')) {
                return false;
            }
        } elseif ($alertType === 'new_device') {
            if (!$this->shouldSendEmail($userId, 'new_device')) {
                return false;
            }
        }
        // Password change and other security alerts are always sent

        try {
            Mail::send('email-template.security-alert', [
                'userName' => $user->name ?? 'User',
                'title' => 'Security Alert',
                'alertType' => $data['alert_type'] ?? 'info',
                'alertTitle' => $data['alert_title'] ?? 'Security Notice',
                'alertMessage' => $data['alert_message'] ?? 'Important security activity on your account.',
                'description' => $data['description'] ?? null,
                'details' => $data['details'] ?? [],
                'actionRequired' => $data['action_required'] ?? false,
                'actionMessage' => $data['action_message'] ?? null,
                'actionUrl' => $data['action_url'] ?? null,
                'actionText' => $data['action_text'] ?? 'Review Activity',
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('🚨 Security Alert - TrackFlow');
            });

            Log::info("Security alert email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send security alert email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Payment Reminder Email
     */
    public function sendPaymentReminder(int $userId, array $data): bool
    {
        if (!$this->shouldSendEmail($userId, 'payment_reminder')) {
            return false;
        }

        $user = User::find($userId);
        if (!$user)
            return false;

        try {
            $currency = $this->getCurrencySymbol($userId);

            Mail::send('email-template.payment-reminder', [
                'userName' => $user->name ?? 'User',
                'title' => 'Payment Reminder',
                'payments' => $data['payments'] ?? null,
                'total_amount' => $data['total_amount'] ?? null,
                'payment_count' => $data['payment_count'] ?? null,
                'currency' => $currency,
                // Legacy single payment support
                'reminderTitle' => $data['reminder_title'] ?? 'Upcoming Payment',
                'reminderMessage' => $data['reminder_message'] ?? null,
                'category' => $data['category'] ?? null,
                'payee' => $data['payee'] ?? null,
                'dueDate' => $data['due_date'] ?? null,
                'amount' => $data['amount'] ?? null,
                'frequency' => $data['frequency'] ?? null,
                'isOverdue' => $data['is_overdue'] ?? false,
                'daysUntilDue' => $data['days_until_due'] ?? null,
                'accountBalance' => $data['account_balance'] ?? null,
            ], function ($message) use ($user, $data) {
                $emoji = ($data['is_overdue'] ?? false) ? '⚠️' : '📅';
                $count = $data['payment_count'] ?? 1;
                $subject = $count > 1
                    ? "{$emoji} {$count} Upcoming Payments - TrackFlow"
                    : "{$emoji} Payment Reminder - TrackFlow";
                $message->to($user->email)->subject($subject);
            });

            Log::info("Payment reminder email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send payment reminder email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Weekly Summary Email
     */
    public function sendWeeklySummary(int $userId, array $data): bool
    {
        if (!$this->shouldSendEmail($userId, 'weekly_summary')) {
            return false;
        }

        $user = User::find($userId);
        if (!$user)
            return false;

        try {
            $currency = $this->getCurrencySymbol($userId);

            // Parse currency formatted strings back to numbers
            $totalIncome = $this->parseCurrencyString($data['total_income'] ?? 0);
            $totalExpenses = $this->parseCurrencyString($data['total_expenses'] ?? 0);
            $netSavings = $this->parseCurrencyString($data['net_savings'] ?? ($totalIncome - $totalExpenses));

            Mail::send('email-template.weekly-summary', [
                'userName' => $user->name ?? 'User',
                'title' => 'Weekly Financial Summary',
                'reportPeriod' => 'week (' . ($data['week_start'] ?? '') . ' - ' . ($data['week_end'] ?? '') . ')',
                'totalIncome' => $totalIncome,
                'totalExpenses' => $totalExpenses,
                'netSavings' => $netSavings,
                'currency' => $currency,
                'savingsRate' => $data['savings_rate'] ?? null,
                'topCategories' => $data['top_categories'] ?? [],
                'transactionCount' => $data['transaction_count'] ?? null,
                'avgDailySpend' => $data['avg_daily_spend'] ?? null,
                'largestExpense' => $data['largest_expense'] ?? null,
                'mostFrequentCategory' => $data['most_frequent_category'] ?? null,
                'insight' => $data['insight'] ?? null,
                'goals' => $data['goals'] ?? [],
                'budgets' => $data['budgets'] ?? [],
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('📊 Your Weekly Summary - TrackFlow');
            });

            Log::info("Weekly summary email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send weekly summary email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse currency formatted string to float
     */
    protected function parseCurrencyString($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        // Remove currency symbols and formatting
        $cleaned = preg_replace('/[^0-9.-]/', '', $value);
        return (float) $cleaned;
    }

    /**
     * Send Welcome Email
     */
    public function sendWelcomeEmail(int $userId): bool
    {
        $user = User::find($userId);
        if (!$user)
            return false;

        try {
            Mail::send('email-template.welcome', [
                'userName' => $user->name ?? 'User',
                'title' => 'Welcome to TrackFlow!',
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('🎉 Welcome to TrackFlow!');
            });

            Log::info("Welcome email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Group Created Email to Leader
     */
    public function sendGroupCreatedEmail(int $userId, array $data): bool
    {
        if (!$this->shouldSendEmail($userId, 'group_expense')) {
            return false;
        }

        $user = User::find($userId);
        if (!$user || !$user->email) {
            return false;
        }

        try {
            Mail::send('email-template.group-expense', [
                'userName' => $user->name ?? 'User',
                'actionType' => 'group_created',
                'groupName' => $data['group_name'] ?? 'Group',
                'groupId' => $data['group_id'] ?? null,
                'groupDescription' => $data['group_description'] ?? null,
                'groupCode' => $data['group_code'] ?? null,
            ], function ($message) use ($user, $data) {
                $message->to($user->email)
                    ->subject('👑 You created a new group: ' . ($data['group_name'] ?? 'Group') . ' - TrackFlow');
            });

            Log::info("Group created email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send group created email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Member Added Email to the new member
     */
    public function sendMemberAddedEmail(string $email, array $data): bool
    {
        if (!$email) {
            return false;
        }

        // Check if the recipient has group_expense notifications enabled
        $user = User::where('email', $email)->first();
        if ($user && !$this->shouldSendEmail($user->id, 'group_expense')) {
            return false;
        }

        try {
            Mail::send('email-template.group-expense', [
                'userName' => $data['member_name'] ?? 'User',
                'actionType' => 'member_added',
                'groupName' => $data['group_name'] ?? 'Group',
                'groupId' => $data['group_id'] ?? null,
                'groupDescription' => $data['group_description'] ?? null,
                'addedBy' => $data['added_by'] ?? 'the group leader',
                'totalMembers' => $data['total_members'] ?? null,
                'groupLeader' => $data['group_leader'] ?? null,
            ], function ($message) use ($email, $data) {
                $message->to($email)
                    ->subject('👥 You were added to group: ' . ($data['group_name'] ?? 'Group') . ' - TrackFlow');
            });

            Log::info("Member added email sent to {$email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send member added email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send New Member Notification to existing members
     */
    public function sendNewMemberNotificationEmail(string $email, array $data): bool
    {
        if (!$email) {
            return false;
        }

        // Check if the recipient has group_expense notifications enabled
        $user = User::where('email', $email)->first();
        if ($user && !$this->shouldSendEmail($user->id, 'group_expense')) {
            return false;
        }

        try {
            Mail::send('email-template.group-expense', [
                'userName' => $data['recipient_name'] ?? 'User',
                'actionType' => 'new_member_joined',
                'groupName' => $data['group_name'] ?? 'Group',
                'groupId' => $data['group_id'] ?? null,
                'newMemberName' => $data['new_member_name'] ?? 'A new member',
                'totalMembers' => $data['total_members'] ?? null,
                'addedBy' => $data['added_by'] ?? 'the group leader',
            ], function ($message) use ($email, $data) {
                $message->to($email)
                    ->subject('👤 New member joined: ' . ($data['group_name'] ?? 'Group') . ' - TrackFlow');
            });

            Log::info("New member notification email sent to {$email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send new member notification email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Goal Created Email
     */
    public function sendGoalCreated(int $userId, array $data): bool
    {
        if (!$this->shouldSendEmail($userId, 'goal_created')) {
            return false;
        }

        $user = User::find($userId);
        if (!$user)
            return false;

        try {
            $targetAmount = $data['target_amount'] ?? 0;
            $currentAmount = $data['current_amount'] ?? 0;
            $percentage = $targetAmount > 0 ? round(($currentAmount / $targetAmount) * 100, 1) : 0;

            Mail::send('email-template.goal-created', [
                'userName' => $user->name ?? 'User',
                'goalName' => $data['goal_name'] ?? 'Goal',
                'goalIcon' => $data['goal_icon'] ?? '🎯',
                'goalType' => $data['goal_type'] ?? null,
                'targetAmount' => $targetAmount,
                'currentAmount' => $currentAmount,
                'percentage' => $percentage,
                'currency' => $this->getCurrencySymbol($userId),
                'targetDate' => $data['target_date'] ?? null,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('🎯 New Goal Created - TrackFlow');
            });

            Log::info("Goal created email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send goal created email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Budget Created Email
     */
    public function sendBudgetCreated(int $userId, array $data): bool
    {
        if (!$this->shouldSendEmail($userId, 'budget_created')) {
            return false;
        }

        $user = User::find($userId);
        if (!$user)
            return false;

        try {
            $months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $monthName = $months[$data['month'] ?? date('n')] ?? 'Month';

            Mail::send('email-template.budget-created', [
                'userName' => $user->name ?? 'User',
                'budgetName' => $data['budget_name'] ?? 'Budget',
                'totalLimit' => $data['total_limit'] ?? 0,
                'month' => $data['month'] ?? date('n'),
                'monthName' => $monthName,
                'year' => $data['year'] ?? date('Y'),
                'categoryCount' => $data['category_count'] ?? 0,
                'categories' => $data['categories'] ?? [],
                'currency' => $this->getCurrencySymbol($userId),
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('📊 New Budget Created - TrackFlow');
            });

            Log::info("Budget created email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send budget created email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Budget 50% Used Email
     */
    public function sendBudgetHalfway(int $userId, array $data): bool
    {
        if (!$this->shouldSendEmail($userId, 'budget_alert')) {
            return false;
        }

        $user = User::find($userId);
        if (!$user)
            return false;

        try {
            Mail::send('email-template.budget-alert', [
                'userName' => $user->name ?? 'User',
                'title' => 'Budget 50% Used',
                'budgetName' => $data['budget_name'] ?? 'Budget',
                'percentage' => 50,
                'spent' => $data['spent_amount'] ?? 0,
                'limit' => $data['budget_limit'] ?? 0,
                'remaining' => ($data['budget_limit'] ?? 0) - ($data['spent_amount'] ?? 0),
                'currency' => $this->getCurrencySymbol($userId),
                'daysLeft' => $data['days_left'] ?? null,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('📊 Budget 50% Used - TrackFlow');
            });

            Log::info("Budget halfway email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send budget halfway email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Budget Complete (100%) Email
     */
    public function sendBudgetComplete(int $userId, array $data): bool
    {
        if (!$this->shouldSendEmail($userId, 'budget_alert')) {
            return false;
        }

        $user = User::find($userId);
        if (!$user)
            return false;

        try {
            Mail::send('email-template.budget-alert', [
                'userName' => $user->name ?? 'User',
                'title' => 'Budget Fully Used',
                'budgetName' => $data['budget_name'] ?? 'Budget',
                'percentage' => 100,
                'spent' => $data['spent_amount'] ?? 0,
                'limit' => $data['budget_limit'] ?? 0,
                'remaining' => 0,
                'currency' => $this->getCurrencySymbol($userId),
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('🚨 Budget 100% Used - TrackFlow');
            });

            Log::info("Budget complete email sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send budget complete email: " . $e->getMessage());
            return false;
        }
    }
}

