<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Services\EmailNotificationService;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    // Notification Types
    const TYPE_BUDGET_OVERSPEND = 'budget_overspend';
    const TYPE_BUDGET_THRESHOLD = 'budget_threshold';
    const TYPE_BUDGET_CREATED = 'budget_created';
    const TYPE_BUDGET_HALFWAY = 'budget_halfway';
    const TYPE_BUDGET_COMPLETE = 'budget_complete';
    const TYPE_GOAL_CREATED = 'goal_created';
    const TYPE_GOAL_COMPLETED = 'goal_completed';
    const TYPE_GOAL_MILESTONE = 'goal_milestone';
    const TYPE_GROUP_MEMBER_ADDED = 'group_member_added';
    const TYPE_GROUP_EXPENSE_ADDED = 'group_expense_added';
    const TYPE_GROUP_SETTLEMENT = 'group_settlement';
    const TYPE_TRANSACTION_LARGE = 'transaction_large';
    const TYPE_TRANSACTION_ADDED = 'transaction_added';
    const TYPE_CATEGORY_OVERSPEND = 'category_overspend';
    const TYPE_FEATURE_AVAILABLE = 'feature_available';
    const TYPE_NEGATIVE_BALANCE = 'negative_balance';

    protected $emailService;

    public function __construct()
    {
        $this->emailService = new EmailNotificationService();
    }

    public function create(int $userId, string $type, string $title, string $message, array $options = [])
    {
        $preferences = $this->getUserPreferences($userId);

        // Check if user wants this type of notification
        if (!$this->shouldNotify($preferences, $type)) {
            return null;
        }

        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $options['icon'] ?? $this->getDefaultIcon($type),
            'color' => $options['color'] ?? $this->getDefaultColor($type),
            'data' => $options['data'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'priority' => $options['priority'] ?? 'medium',
        ]);

        return $notification;
    }

    public function budgetOverspend(int $userId, $budget, float $spentAmount, float $limitAmount)
    {
        $overspend = $spentAmount - $limitAmount;
        $percentage = round(($spentAmount / $limitAmount) * 100, 1);

        // Send email notification
        $this->emailService->sendBudgetAlert($userId, [
            'budget_name' => $budget->name,
            'budget_limit' => $limitAmount,
            'spent_amount' => $spentAmount,
            'remaining_amount' => max(0, $limitAmount - $spentAmount),
            'overspend_amount' => $overspend,
            'percentage' => $percentage,
            'alert_type' => 'overspent',
            'categories' => $budget->items->pluck('category.name')->filter()->toArray(),
        ]);

        return $this->create(
            $userId,
            self::TYPE_BUDGET_OVERSPEND,
            'Budget Overspent!',
            "You've exceeded your budget for {$budget->name} by ₹" . number_format($overspend, 2) . " ({$percentage}%)",
            [
                'icon' => 'fa-exclamation-triangle',
                'color' => 'red',
                'priority' => 'high',
                'action_url' => '/budgets',
                'data' => [
                    'budget_id' => $budget->id,
                    'spent_amount' => $spentAmount,
                    'limit_amount' => $limitAmount,
                    'overspend_amount' => $overspend,
                ]
            ]
        );
    }

    public function budgetThreshold(int $userId, $budget, float $spentAmount, float $limitAmount, int $percentage)
    {
        // Send email notification
        $this->emailService->sendBudgetAlert($userId, [
            'budget_name' => $budget->name,
            'budget_limit' => $limitAmount,
            'spent_amount' => $spentAmount,
            'remaining_amount' => max(0, $limitAmount - $spentAmount),
            'percentage' => $percentage,
            'alert_type' => 'threshold',
            'categories' => $budget->items->pluck('category.name')->filter()->toArray(),
        ]);

        return $this->create(
            $userId,
            self::TYPE_BUDGET_THRESHOLD,
            'Budget Alert',
            "You've spent {$percentage}% of your {$budget->name} budget (₹" . number_format($spentAmount, 2) . " of ₹" . number_format($limitAmount, 2) . ")",
            [
                'icon' => 'fa-exclamation-circle',
                'color' => 'yellow',
                'priority' => 'medium',
                'action_url' => '/budgets',
                'data' => [
                    'budget_id' => $budget->id,
                    'spent_amount' => $spentAmount,
                    'limit_amount' => $limitAmount,
                    'percentage' => $percentage,
                ]
            ]
        );
    }

    public function goalCompleted(int $userId, $goal)
    {
        // Send email notification
        $this->emailService->sendGoalProgress($userId, [
            'goal_name' => $goal->name,
            'target_amount' => $goal->target_amount,
            'current_amount' => $goal->current_amount,
            'percentage' => 100,
            'remaining_amount' => 0,
            'deadline' => $goal->deadline ?? null,
            'is_completed' => true,
        ]);

        return $this->create(
            $userId,
            self::TYPE_GOAL_COMPLETED,
            'Goal Achieved! 🎉',
            "Congratulations! You've reached your goal: {$goal->name}",
            [
                'icon' => 'fa-trophy',
                'color' => 'green',
                'priority' => 'high',
                'action_url' => '/goals',
                'data' => [
                    'goal_id' => $goal->id,
                    'target_amount' => $goal->target_amount,
                ]
            ]
        );
    }

    public function goalCreated(int $userId, $goal)
    {
        // Send email notification
        $this->emailService->sendGoalCreated($userId, [
            'goal_name' => $goal->name,
            'goal_icon' => $goal->icon ?? '🎯',
            'goal_type' => $goal->type ?? null,
            'target_amount' => $goal->target_amount,
            'current_amount' => $goal->current_amount ?? 0,
            'target_date' => $goal->target_date ? $goal->target_date->format('M d, Y') : null,
        ]);

        return $this->create(
            $userId,
            self::TYPE_GOAL_CREATED,
            'New Goal Created! 🎯',
            "You've set a new goal: {$goal->name} with target of ₹" . number_format($goal->target_amount, 2),
            [
                'icon' => 'fa-bullseye',
                'color' => 'blue',
                'priority' => 'medium',
                'action_url' => '/goals',
                'data' => [
                    'goal_id' => $goal->id,
                    'goal_name' => $goal->name,
                    'target_amount' => $goal->target_amount,
                ]
            ]
        );
    }

    public function goalMilestone(int $userId, $goal, int $percentage)
    {
        // Send email notification for major milestones (25%, 50%, 75%, 90%)
        if (in_array($percentage, [25, 50, 75, 90])) {
            $this->emailService->sendGoalProgress($userId, [
                'goal_name' => $goal->name,
                'target_amount' => $goal->target_amount,
                'current_amount' => $goal->current_amount,
                'percentage' => $percentage,
                'remaining_amount' => $goal->target_amount - $goal->current_amount,
                'deadline' => $goal->deadline ?? null,
                'is_completed' => false,
            ]);
        }

        return $this->create(
            $userId,
            self::TYPE_GOAL_MILESTONE,
            'Goal Progress',
            "You've reached {$percentage}% of your goal: {$goal->name}",
            [
                'icon' => 'fa-bullseye',
                'color' => 'blue',
                'priority' => 'medium',
                'action_url' => '/goals',
                'data' => [
                    'goal_id' => $goal->id,
                    'percentage' => $percentage,
                    'current_amount' => $goal->current_amount,
                    'target_amount' => $goal->target_amount,
                ]
            ]
        );
    }

    public function budgetCreated(int $userId, $budget, array $categories = [])
    {
        $months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $monthName = $months[$budget->month] ?? 'Month';

        // Send email notification
        $this->emailService->sendBudgetCreated($userId, [
            'budget_name' => $budget->name ?? "{$monthName} {$budget->year} Budget",
            'total_limit' => $budget->total_limit,
            'month' => $budget->month,
            'year' => $budget->year,
            'category_count' => count($categories),
            'categories' => $categories,
        ]);

        return $this->create(
            $userId,
            self::TYPE_BUDGET_CREATED,
            'New Budget Created! 📊',
            "You've set up a budget for {$monthName} {$budget->year} with a limit of ₹" . number_format($budget->total_limit, 2),
            [
                'icon' => 'fa-wallet',
                'color' => 'green',
                'priority' => 'medium',
                'action_url' => '/budgets',
                'data' => [
                    'budget_id' => $budget->id,
                    'budget_name' => $budget->name,
                    'total_limit' => $budget->total_limit,
                ]
            ]
        );
    }

    public function budgetHalfway(int $userId, $budget, float $spentAmount, float $limitAmount)
    {
        // Send email notification
        $this->emailService->sendBudgetHalfway($userId, [
            'budget_name' => $budget->name,
            'budget_limit' => $limitAmount,
            'spent_amount' => $spentAmount,
        ]);

        return $this->create(
            $userId,
            self::TYPE_BUDGET_HALFWAY,
            'Budget 50% Used',
            "You've used 50% of your {$budget->name} budget (₹" . number_format($spentAmount, 2) . " of ₹" . number_format($limitAmount, 2) . ")",
            [
                'icon' => 'fa-chart-pie',
                'color' => 'yellow',
                'priority' => 'medium',
                'action_url' => '/budgets',
                'data' => [
                    'budget_id' => $budget->id,
                    'spent_amount' => $spentAmount,
                    'limit_amount' => $limitAmount,
                    'percentage' => 50,
                ]
            ]
        );
    }

    public function budgetComplete(int $userId, $budget, float $spentAmount, float $limitAmount)
    {
        // Send email notification
        $this->emailService->sendBudgetComplete($userId, [
            'budget_name' => $budget->name,
            'budget_limit' => $limitAmount,
            'spent_amount' => $spentAmount,
        ]);

        return $this->create(
            $userId,
            self::TYPE_BUDGET_COMPLETE,
            'Budget Fully Used! 🎯',
            "You've used 100% of your {$budget->name} budget (₹" . number_format($spentAmount, 2) . ")",
            [
                'icon' => 'fa-check-circle',
                'color' => 'green',
                'priority' => 'high',
                'action_url' => '/budgets',
                'data' => [
                    'budget_id' => $budget->id,
                    'spent_amount' => $spentAmount,
                    'limit_amount' => $limitAmount,
                    'percentage' => 100,
                ]
            ]
        );
    }

    public function groupMemberAdded(int $userId, $group, $memberName)
    {
        return $this->create(
            $userId,
            self::TYPE_GROUP_MEMBER_ADDED,
            'New Group Member',
            "{$memberName} joined the group: {$group->name}",
            [
                'icon' => 'fa-user-plus',
                'color' => 'blue',
                'priority' => 'medium',
                'action_url' => "/group-expense/{$group->id}",
                'data' => [
                    'group_id' => $group->id,
                    'member_name' => $memberName,
                ]
            ]
        );
    }

    public function groupExpenseAdded(int $userId, $group, $expense, $addedBy)
    {
        return $this->create(
            $userId,
            self::TYPE_GROUP_EXPENSE_ADDED,
            'New Group Expense',
            "{$addedBy} added an expense of ₹" . number_format($expense->amount, 2) . " in {$group->name}",
            [
                'icon' => 'fa-receipt',
                'color' => 'purple',
                'priority' => 'medium',
                'action_url' => "/group-expense/{$group->id}",
                'data' => [
                    'group_id' => $group->id,
                    'expense_id' => $expense->id,
                    'amount' => $expense->amount,
                    'added_by' => $addedBy,
                ]
            ]
        );
    }

    public function groupSettlement(int $userId, $group, float $amount, $settledWith)
    {
        return $this->create(
            $userId,
            self::TYPE_GROUP_SETTLEMENT,
            'Settlement Recorded',
            "Settlement of ₹" . number_format($amount, 2) . " with {$settledWith} in {$group->name}",
            [
                'icon' => 'fa-handshake',
                'color' => 'green',
                'priority' => 'medium',
                'action_url' => "/group-expense/{$group->id}",
                'data' => [
                    'group_id' => $group->id,
                    'amount' => $amount,
                    'settled_with' => $settledWith,
                ]
            ]
        );
    }

    public function transactionAdded(int $userId, $transaction, $amountInUserCurrency, $currency)
    {
        $type = $transaction->type === 'credit' ? 'Income' : 'Expense';
        $icon = $transaction->type === 'credit' ? 'fa-arrow-up' : 'fa-arrow-down';
        $color = $transaction->type === 'credit' ? 'green' : 'red';
        $description = $transaction->description ?? 'transaction';

        // Format currency
        $currencyConfig = config('currency.currencies');
        $symbol = $currencyConfig[$currency]['symbol'] ?? '$';
        $formattedAmount = $symbol . number_format($amountInUserCurrency, 2);

        return $this->create(
            $userId,
            'transaction_added',
            "{$type} Added",
            "New {$type}: {$formattedAmount} - {$description}",
            [
                'icon' => $icon,
                'color' => $color,
                'priority' => 'low',
                'action_url' => '/transactions',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'amount' => $amountInUserCurrency,
                    'currency' => $currency,
                    'type' => $transaction->type,
                    'description' => $description,
                ]
            ]
        );
    }

    public function largeTransaction(int $userId, $transaction, $amountInUserCurrency, $currency, $threshold)
    {
        $type = $transaction->type === 'credit' ? 'Income' : 'Expense';
        $emailType = $transaction->type === 'credit' ? 'income' : 'expense';
        $description = $transaction->description ?? 'transaction';

        // Format currency
        $currencyConfig = config('currency.currencies');
        $symbol = $currencyConfig[$currency]['symbol'] ?? '$';
        $formattedAmount = $symbol . number_format($amountInUserCurrency, 2);
        $formattedThreshold = $symbol . number_format($threshold, 2);

        // Send email notification for large transactions
        $this->emailService->sendTransactionAlert($userId, [
            'type' => $emailType,
            'amount' => $amountInUserCurrency,
            'description' => $description,
            'merchant' => $transaction->merchant ?? null,
            'category' => $transaction->category->name ?? 'Uncategorized',
            'date' => $transaction->date ?? now()->format('M d, Y'),
            'account_name' => 'Manual',
            'is_large' => true,
            'threshold' => $threshold,
        ]);

        return $this->create(
            $userId,
            self::TYPE_TRANSACTION_LARGE,
            "Large {$type} Alert!",
            "Large {$type} detected: {$formattedAmount} (exceeds threshold of {$formattedThreshold})",
            [
                'icon' => 'fa-exclamation-circle',
                'color' => 'yellow',
                'priority' => 'high',
                'action_url' => '/transactions',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'amount' => $amountInUserCurrency,
                    'currency' => $currency,
                    'threshold' => $threshold,
                    'type' => $transaction->type,
                    'description' => $description,
                ]
            ]
        );
    }

    public function categoryOverspend(int $userId, $category, $budget, float $spent, float $limit)
    {
        $overspend = $spent - $limit;

        return $this->create(
            $userId,
            self::TYPE_CATEGORY_OVERSPEND,
            'Category Budget Exceeded',
            "You've exceeded the {$category->name} budget by ₹" . number_format($overspend, 2),
            [
                'icon' => 'fa-tags',
                'color' => 'red',
                'priority' => 'high',
                'action_url' => '/budgets',
                'data' => [
                    'category_id' => $category->id,
                    'budget_id' => $budget->id,
                    'spent_amount' => $spent,
                    'limit_amount' => $limit,
                ]
            ]
        );
    }

    public function featureAvailable(int $userId, $feature)
    {
        return $this->create(
            $userId,
            self::TYPE_FEATURE_AVAILABLE,
            'New Feature Available! 🎉',
            "The feature you requested is now available: {$feature->feature_name}",
            [
                'icon' => 'fa-star',
                'color' => 'purple',
                'priority' => 'medium',
                'action_url' => '/coming-soon',
                'data' => [
                    'feature_id' => $feature->id,
                    'feature_name' => $feature->feature_name,
                ]
            ]
        );
    }

    public function negativeBalance(int $userId, float $totalBalance, float $totalIncome, float $totalExpenses)
    {
        $deficit = abs($totalBalance);

        // Send email notification for negative balance (financial security alert)
        $this->emailService->sendSecurityAlert($userId, [
            'alert_type' => 'negative_balance',
            'title' => 'Negative Balance Alert',
            'message' => "Your expenses exceed your income by ₹" . number_format($deficit, 2),
            'details' => [
                'Total Balance' => '-₹' . number_format($deficit, 2),
                'Total Income' => '₹' . number_format($totalIncome, 2),
                'Total Expenses' => '₹' . number_format($totalExpenses, 2),
                'Deficit' => '₹' . number_format($deficit, 2),
            ],
            'action_required' => true,
            'action_text' => 'Review Your Finances',
            'action_url' => url('/dashboard'),
        ]);

        return $this->create(
            $userId,
            self::TYPE_NEGATIVE_BALANCE,
            'Negative Balance Alert! ⚠️',
            "Your expenses exceed your income by ₹" . number_format($deficit, 2) . ". Your total balance is -₹" . number_format($deficit, 2),
            [
                'icon' => 'fa-exclamation-triangle',
                'color' => 'red',
                'priority' => 'high',
                'action_url' => '/dashboard',
                'data' => [
                    'total_balance' => $totalBalance,
                    'total_income' => $totalIncome,
                    'total_expenses' => $totalExpenses,
                    'deficit' => $deficit,
                ]
            ]
        );
    }

    /**
     * Send a security alert email (for login, password change, etc.)
     */
    public function securityAlert(int $userId, array $data)
    {
        $this->emailService->sendSecurityAlert($userId, $data);

        return $this->create(
            $userId,
            'security_alert',
            $data['title'] ?? 'Security Alert',
            $data['message'] ?? 'A security event has occurred on your account.',
            [
                'icon' => 'fa-shield-alt',
                'color' => 'red',
                'priority' => 'high',
                'action_url' => '/settings/security',
                'data' => $data['details'] ?? [],
            ]
        );
    }

    private function getUserPreferences(int $userId)
    {
        // Read from user_preferences table (where settings UI saves)
        $prefs = DB::table('user_preferences')->where('user_id', $userId)->first();

        if (!$prefs) {
            // Create default preferences if not exist
            DB::table('user_preferences')->insert([
                'user_id' => $userId,
                'budget_alerts' => true,
                'goal_progress' => true,
                'group_expense' => true,
                'transaction_alerts' => true,
                'push_notifications' => true,
                'large_transaction_threshold' => 333.87,
                'large_transaction_alerts' => true,
                'weekly_summary' => true,
                'login_alerts' => true,
                'new_device_alerts' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $prefs = DB::table('user_preferences')->where('user_id', $userId)->first();
        }

        return $prefs;
    }

    private function shouldNotify($preferences, string $type): bool
    {
        $typeMapping = [
            self::TYPE_BUDGET_OVERSPEND => 'budget_alerts',
            self::TYPE_BUDGET_THRESHOLD => 'budget_alerts',
            self::TYPE_BUDGET_CREATED => 'budget_alerts',
            self::TYPE_GOAL_COMPLETED => 'goal_progress',
            self::TYPE_GOAL_MILESTONE => 'goal_progress',
            self::TYPE_GROUP_MEMBER_ADDED => 'group_expense',
            self::TYPE_GROUP_EXPENSE_ADDED => 'group_expense',
            self::TYPE_GROUP_SETTLEMENT => 'group_expense',
            self::TYPE_TRANSACTION_LARGE => 'large_transaction_alerts',
            self::TYPE_TRANSACTION_ADDED => 'transaction_alerts',
            self::TYPE_CATEGORY_OVERSPEND => 'budget_alerts',
            self::TYPE_FEATURE_AVAILABLE => 'push_notifications',
            self::TYPE_NEGATIVE_BALANCE => 'budget_alerts',
        ];

        $preferenceKey = $typeMapping[$type] ?? null;

        if (!$preferenceKey) {
            return true;
        }

        return isset($preferences->{$preferenceKey}) ? (bool) $preferences->{$preferenceKey} : true;
    }

    /**
     * Get large transaction threshold for user (in user's currency)
     * The threshold is stored directly in user's local currency
     */
    public function getLargeTransactionThreshold($userId, $currency)
    {
        $prefs = $this->getUserPreferences($userId);
        // Threshold is stored in user's local currency, no conversion needed
        return $prefs->large_transaction_threshold ?? 1000;
    }
    private function getDefaultIcon(string $type): string
    {
        $icons = [
            self::TYPE_BUDGET_OVERSPEND => 'fa-exclamation-triangle',
            self::TYPE_BUDGET_THRESHOLD => 'fa-exclamation-circle',
            self::TYPE_BUDGET_CREATED => 'fa-wallet',
            self::TYPE_GOAL_COMPLETED => 'fa-trophy',
            self::TYPE_GOAL_MILESTONE => 'fa-bullseye',
            self::TYPE_GROUP_MEMBER_ADDED => 'fa-user-plus',
            self::TYPE_GROUP_EXPENSE_ADDED => 'fa-receipt',
            self::TYPE_GROUP_SETTLEMENT => 'fa-handshake',
            self::TYPE_TRANSACTION_LARGE => 'fa-money-bill-wave',
            self::TYPE_CATEGORY_OVERSPEND => 'fa-tags',
            self::TYPE_FEATURE_AVAILABLE => 'fa-rocket',
            self::TYPE_NEGATIVE_BALANCE => 'fa-exclamation-triangle',
        ];

        return $icons[$type] ?? 'fa-bell';
    }

    private function getDefaultColor(string $type): string
    {
        $colors = [
            self::TYPE_BUDGET_OVERSPEND => 'red',
            self::TYPE_BUDGET_THRESHOLD => 'yellow',
            self::TYPE_BUDGET_CREATED => 'blue',
            self::TYPE_GOAL_COMPLETED => 'green',
            self::TYPE_GOAL_MILESTONE => 'blue',
            self::TYPE_GROUP_MEMBER_ADDED => 'blue',
            self::TYPE_GROUP_EXPENSE_ADDED => 'purple',
            self::TYPE_GROUP_SETTLEMENT => 'green',
            self::TYPE_TRANSACTION_LARGE => 'red',
            self::TYPE_CATEGORY_OVERSPEND => 'red',
            self::TYPE_FEATURE_AVAILABLE => 'purple',
            self::TYPE_NEGATIVE_BALANCE => 'red',
        ];

        return $colors[$type] ?? 'blue';
    }
}
