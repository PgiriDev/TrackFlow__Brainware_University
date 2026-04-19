<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Goal;
use App\Services\EmailNotificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendWeeklySummaryCommand extends Command
{
    protected $signature = 'notifications:weekly-summary';

    protected $description = 'Send weekly financial summary emails to all users';

    protected $emailService;

    public function __construct()
    {
        parent::__construct();
        $this->emailService = new EmailNotificationService();
    }

    public function handle(): int
    {
        $this->info('Sending weekly summaries...');

        $summariesSent = 0;
        $startOfWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfWeek = Carbon::now()->subWeek()->endOfWeek();

        // Get all users with verified email
        $users = User::whereNotNull('email_verified_at')
            ->get();

        foreach ($users as $user) {
            try {
                // Get user's transactions for the week
                $transactions = Transaction::where('user_id', $user->id)
                    ->whereBetween('date', [$startOfWeek, $endOfWeek])
                    ->get();

                $totalIncome = $transactions->where('type', 'credit')->sum('amount');
                $totalExpenses = $transactions->where('type', 'debit')->sum('amount');
                $netSavings = $totalIncome - $totalExpenses;
                $savingsRate = $totalIncome > 0 ? round(($netSavings / $totalIncome) * 100, 1) : 0;

                // Get top spending categories
                $topCategories = $transactions->where('type', 'debit')
                    ->groupBy('category_id')
                    ->map(function ($group) {
                        return [
                            'name' => $group->first()->category->name ?? 'Uncategorized',
                            'amount' => $group->sum('amount'),
                            'count' => $group->count(),
                        ];
                    })
                    ->sortByDesc('amount')
                    ->take(5)
                    ->values()
                    ->toArray();

                // Format top categories
                foreach ($topCategories as &$cat) {
                    $cat['formatted_amount'] = '₹' . number_format($cat['amount'], 2);
                }

                // Get budget status
                $budgets = Budget::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->get();

                $budgetSummary = [];
                foreach ($budgets as $budget) {
                    $spent = $budget->spent_amount ?? 0;
                    $limit = $budget->total_limit ?? $budget->amount ?? 0;
                    $percentage = $limit > 0 ? round(($spent / $limit) * 100, 1) : 0;

                    $budgetSummary[] = [
                        'name' => $budget->name,
                        'spent' => '₹' . number_format($spent, 2),
                        'limit' => '₹' . number_format($limit, 2),
                        'percentage' => $percentage,
                        'status' => $percentage >= 100 ? 'overspent' : ($percentage >= 80 ? 'warning' : 'good'),
                    ];
                }

                // Get goal progress
                $goals = Goal::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->get();

                $goalSummary = [];
                foreach ($goals as $goal) {
                    $currentAmount = (float) ($goal->current_amount ?? 0);
                    $targetAmount = (float) ($goal->target_amount ?? 0);
                    $percentage = $targetAmount > 0
                        ? round(($currentAmount / $targetAmount) * 100, 1)
                        : 0;

                    $goalSummary[] = [
                        'name' => $goal->name,
                        'current' => '₹' . number_format($currentAmount, 2),
                        'target' => '₹' . number_format($targetAmount, 2),
                        'percentage' => $percentage,
                        'deadline' => $goal->deadline ? Carbon::parse($goal->deadline)->format('M d, Y') : null,
                    ];
                }

                // Send the weekly summary email
                $this->emailService->sendWeeklySummary($user->id, [
                    'week_start' => $startOfWeek->format('M d'),
                    'week_end' => $endOfWeek->format('M d, Y'),
                    'total_income' => '₹' . number_format($totalIncome, 2),
                    'total_expenses' => '₹' . number_format($totalExpenses, 2),
                    'net_savings' => '₹' . number_format($netSavings, 2),
                    'savings_rate' => $savingsRate,
                    'transaction_count' => $transactions->count(),
                    'top_categories' => $topCategories,
                    'budgets' => $budgetSummary,
                    'goals' => $goalSummary,
                    'income_trend' => $netSavings >= 0 ? 'positive' : 'negative',
                ]);

                $summariesSent++;
                $this->info("Sent weekly summary to {$user->email}");

            } catch (\Exception $e) {
                $this->error("Failed to send summary to {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Weekly summaries sent: {$summariesSent}");

        return Command::SUCCESS;
    }
}
