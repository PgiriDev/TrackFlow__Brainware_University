<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\NotificationPreference;
use App\Models\Transaction;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function index()
    {
        return view('budgets.index');
    }

    public function create()
    {
        return redirect()->route('budgets.index');
    }

    public function store(Request $request)
    {
        // Handled via API
        return redirect()->route('budgets.index')
            ->with('success', 'Created successfully');
    }

    public function show($id)
    {
        return view('budgets.show', compact('id'));
    }

    public function edit($id)
    {
        return view('budgets.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Handled via API
        return redirect()->route('budgets.index')
            ->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        // Handled via API
        return redirect()->route('budgets.index')
            ->with('success', 'Deleted successfully');
    }

    // AJAX Methods for session-based auth
    public function listAjax(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $budgets = Budget::where('user_id', $userId)
            ->with('items.category')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Convert budget amounts from base (INR) to user's display currency for UI
        $currencyService = app(\App\Services\CurrencyService::class);
        $userSetting = app('user.settings');
        $displayCurrency = $userSetting->display_currency ?? \App\Models\User::find($userId)->currency ?? config('currency.default', 'INR');

        $converted = $budgets->map(function ($b) use ($currencyService, $displayCurrency) {
            $limit = (float) $b->total_limit;
            $spent = (float) $b->getTotalSpentAttribute();
            $remaining = $limit - $spent;

            return [
                'id' => $b->id,
                'user_id' => $b->user_id,
                'name' => $b->name,
                'month' => $b->month,
                'year' => $b->year,
                // display amounts converted from INR -> displayCurrency
                'total_limit' => $currencyService->convertFromINR($limit, $displayCurrency),
                'total_limit_display' => ($currencyService ? ($currencyService->getAllRates() ? null : null) : null),
                'total_limit_formatted' => ($currencyService ? ($displayCurrency . ' ' . number_format($currencyService->convertFromINR($limit, $displayCurrency), 2)) : number_format($limit, 2)),
                'spent' => $currencyService->convertFromINR($spent, $displayCurrency),
                'remaining' => $currencyService->convertFromINR($remaining, $displayCurrency),
                'percentage_used' => $b->getPercentageUsedAttribute(),
                'items' => $b->items->map(function ($item) use ($currencyService, $displayCurrency) {
                    return [
                        'id' => $item->id,
                        'category_id' => $item->category_id,
                        'category' => $item->category ? $item->category->name : null,
                        'limit_amount' => $currencyService->convertFromINR((float) $item->limit_amount, $displayCurrency),
                        'spent_amount' => $currencyService->convertFromINR((float) $item->spent_amount, $displayCurrency),
                        'remaining' => $currencyService->convertFromINR((float) ($item->limit_amount - $item->spent_amount), $displayCurrency),
                        'percentage_used' => $item->getPercentageUsedAttribute()
                    ];
                })
            ];
        });

        return response()->json(['success' => true, 'data' => $converted]);
    }

    public function storeAjax(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
            'total_limit' => 'required|numeric|min:0',
            'name' => 'nullable|string|max:255',
            'items' => 'required|array',
            'items.*.category_id' => 'required|exists:categories,id',
            'items.*.limit_amount' => 'required|numeric|min:0',
        ]);

        // Check if budget already exists for this user/month/year
        $existingBudget = Budget::where('user_id', $userId)
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();

        if ($existingBudget) {
            $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return response()->json([
                'success' => false,
                'message' => "A budget already exists for {$monthNames[$validated['month']]} {$validated['year']}. Please edit or delete the existing budget first."
            ], 422);
        }

        // Convert incoming amounts (entered in user's display currency) to base (INR) for storage
        $currencyService = app(\App\Services\CurrencyService::class);
        $userSetting = app('user.settings');
        $enteredCurrency = $userSetting->display_currency ?? \App\Models\User::find($userId)->currency ?? config('currency.default', 'INR');

        $totalLimitInBase = $currencyService->toBase((float) $validated['total_limit'], $enteredCurrency);

        $budget = Budget::create([
            'user_id' => $userId,
            'name' => $validated['name'] ?? null,
            'month' => $validated['month'],
            'year' => $validated['year'],
            'total_limit' => $totalLimitInBase,
        ]);

        $categories = [];
        foreach ($validated['items'] as $item) {
            $limitInBase = $currencyService->toBase((float) $item['limit_amount'], $enteredCurrency);
            $budgetItem = BudgetItem::create([
                'budget_id' => $budget->id,
                'category_id' => $item['category_id'],
                'limit_amount' => $limitInBase,
            ]);
            // Get category name for email
            $category = \App\Models\Category::find($item['category_id']);
            if ($category) {
                $categories[] = [
                    'name' => $category->name,
                    'limit' => $item['limit_amount'], // Use display currency amount for email
                ];
            }
        }

        // Send budget created notification and email
        $this->notificationService->budgetCreated($userId, $budget, $categories);

        return response()->json(['success' => true, 'data' => $budget->load('items.category')], 201);
    }

    public function destroyAjax(Request $request, $id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $budget = Budget::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$budget) {
            return response()->json(['success' => false, 'message' => 'Budget not found'], 404);
        }

        $budget->delete();
        return response()->json(['success' => true, 'message' => 'Budget deleted successfully']);
    }

    public function addSpentAjax(Request $request, $budgetId, $itemId)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'date' => 'required|date',
        ]);

        // Verify the budget belongs to the user
        $budget = Budget::where('user_id', $userId)
            ->where('id', $budgetId)
            ->first();

        if (!$budget) {
            return response()->json(['success' => false, 'message' => 'Budget not found'], 404);
        }

        // Verify the budget item belongs to this budget
        $budgetItem = BudgetItem::where('budget_id', $budgetId)
            ->where('id', $itemId)
            ->first();

        if (!$budgetItem) {
            return response()->json(['success' => false, 'message' => 'Budget item not found'], 404);
        }

        // Get user's display currency
        $user = \App\Models\User::find($userId);
        $userCurrency = $user->currency ?? config('currency.default', 'INR');

        // User enters amount in their display currency
        $enteredAmount = (float) $validated['amount'];

        // Convert to base currency (INR) for storage - budgets are stored in base currency
        $currencyService = app(\App\Services\CurrencyService::class);
        $amountInBase = $currencyService->toBase($enteredAmount, $userCurrency);

        // Update budget item spent_amount in base currency
        $oldSpent = $budgetItem->spent_amount ?? 0;
        $budgetItem->spent_amount = $oldSpent + $amountInBase;
        $budgetItem->save();

        // Create a transaction record for this spent amount
        $transaction = Transaction::create([
            'user_id' => $userId,
            'category_id' => $budgetItem->category_id,
            'budget_id' => $budget->id,
            'budget_item_id' => $budgetItem->id,
            'amount' => $amountInBase,
            'entered_amount' => $enteredAmount,
            'entered_currency' => $userCurrency,
            'type' => 'debit',
            'description' => $validated['description'] ?: 'Budget expense',
            'date' => $validated['date'],
            'merchant' => $budget->name ?? 'Budget Expense',
            'source' => 'manual',
        ]);

        // Check for budget notifications (pass old spent for milestone detection)
        $this->checkBudgetNotifications($userId, $budget, $budgetItem, $oldSpent);

        // Check for negative balance
        $this->checkNegativeBalance($userId);

        return response()->json([
            'success' => true,
            'message' => 'Spent amount added successfully',
            'data' => $budgetItem->load('category')
        ]);
    }

    private function checkBudgetNotifications($userId, $budget, $budgetItem, $oldSpent = 0)
    {
        $preferences = NotificationPreference::where('user_id', $userId)->first();
        $threshold = $preferences ? $preferences->budget_threshold_percentage : 80;

        $spentAmount = $budgetItem->spent_amount ?? 0;
        $limitAmount = $budgetItem->limit_amount ?? 0;

        if ($limitAmount <= 0) {
            return;
        }

        $oldPercentage = ($oldSpent / $limitAmount) * 100;
        $percentage = ($spentAmount / $limitAmount) * 100;

        // Check for 50% milestone (only if just crossed)
        if ($oldPercentage < 50 && $percentage >= 50 && $percentage < 100) {
            $this->notificationService->budgetHalfway($userId, $budget, $spentAmount, $limitAmount);
        }

        // Check for 100% milestone (budget complete - only if just crossed)
        if ($oldPercentage < 100 && $percentage >= 100 && $spentAmount <= $limitAmount) {
            $this->notificationService->budgetComplete($userId, $budget, $spentAmount, $limitAmount);
        }

        // Check for overspending (over 100%)
        if ($spentAmount > $limitAmount) {
            $this->notificationService->budgetOverspend($userId, $budget, $spentAmount, $limitAmount);

            // Also check category overspend
            if ($budgetItem->category) {
                $this->notificationService->categoryOverspend(
                    $userId,
                    $budgetItem->category,
                    $budget,
                    $spentAmount,
                    $limitAmount
                );
            }
        }
        // Check for threshold warning (typically 80%)
        elseif ($percentage >= $threshold && $percentage < 100 && $oldPercentage < $threshold) {
            $this->notificationService->budgetThreshold(
                $userId,
                $budget,
                $spentAmount,
                $limitAmount,
                round($percentage)
            );
        }
    }

    private function checkNegativeBalance($userId)
    {
        // Calculate total income (credit transactions)
        $totalIncome = \App\Models\Transaction::where('user_id', $userId)
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->sum('amount');

        // Calculate total expenses (debit transactions)
        $totalExpenses = \App\Models\Transaction::where('user_id', $userId)
            ->where('type', 'debit')
            ->where('status', 'completed')
            ->sum('amount');

        // Calculate total balance
        $totalBalance = $totalIncome - $totalExpenses;

        // Check if balance is negative
        if ($totalBalance < 0) {
            // Check if notification was already sent recently (within last 24 hours)
            $recentNotification = \App\Models\Notification::where('user_id', $userId)
                ->where('type', 'negative_balance')
                ->where('created_at', '>', now()->subHours(24))
                ->first();

            // Only send notification if no recent notification exists
            if (!$recentNotification) {
                $this->notificationService->negativeBalance(
                    $userId,
                    $totalBalance,
                    $totalIncome,
                    $totalExpenses
                );
            }
        }
    }
}