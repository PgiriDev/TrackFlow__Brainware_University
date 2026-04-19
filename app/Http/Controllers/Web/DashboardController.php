<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $userId = session('user_id');

        // Check negative balance whenever dashboard is loaded
        if ($userId) {
            $this->checkNegativeBalance($userId);
        }

        return view('dashboard');
    }

    /**
     * Check if user has negative balance and send notification
     * Optimized: Single query with conditional sums
     */
    private function checkNegativeBalance(int $userId): void
    {
        // Use cache to avoid repeated balance checks within short time
        $cacheKey = "user_balance_check:{$userId}";

        if (Cache::has($cacheKey)) {
            return; // Skip check if recently performed
        }

        // Single optimized query instead of two separate queries
        $totals = Transaction::where('user_id', $userId)
            ->where('status', 'completed')
            ->selectRaw("
                SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as total_expenses
            ")
            ->first();

        $totalIncome = (float) ($totals->total_income ?? 0);
        $totalExpenses = (float) ($totals->total_expenses ?? 0);
        $totalBalance = $totalIncome - $totalExpenses;

        // Cache the check for 5 minutes
        Cache::put($cacheKey, true, 300);

        // Check if balance is negative
        if ($totalBalance < 0) {
            // Check if notification was already sent recently (within last 24 hours)
            $recentNotification = Notification::where('user_id', $userId)
                ->where('type', 'negative_balance')
                ->where('created_at', '>', now()->subHours(24))
                ->exists(); // Use exists() instead of first() for better performance

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
