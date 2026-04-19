<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\API\GoalController;
use App\Jobs\ProcessProviderWebhookJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API v1 routes
Route::prefix('v1')->group(function () {

    // Public authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {


        // Authentication - Protected
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('logout-all', [AuthController::class, 'logoutAll']);
            Route::get('profile', [AuthController::class, 'profile']);
            Route::put('profile', [AuthController::class, 'updateProfile']);
            Route::post('change-password', [AuthController::class, 'changePassword']);

            // 2FA routes
            Route::post('2fa/enable', [AuthController::class, 'enable2FA']);
            Route::post('2fa/disable', [AuthController::class, 'disable2FA']);
            Route::post('2fa/verify', [AuthController::class, 'verify2FA']);
        });

        // Transactions
        Route::prefix('transactions')->group(function () {
            Route::get('/', [TransactionController::class, 'index']);
            Route::post('/', [TransactionController::class, 'store']);
            Route::get('/{id}', [TransactionController::class, 'show']);
            Route::put('/{id}', [TransactionController::class, 'update']);
            Route::delete('/{id}', [TransactionController::class, 'destroy']);
            Route::post('/bulk-categorize', [TransactionController::class, 'bulkCategorize']);
            Route::post('/import', [TransactionController::class, 'import']);
            Route::get('/{id}/suggest-category', [TransactionController::class, 'suggestCategory']);
        });

        // Categories
        Route::prefix('categories')->group(function () {
            Route::get('/', function (Request $request) {
                $userId = $request->user() ? $request->user()->id : session('user_id');
                $categories = \App\Models\Category::where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                        ->orWhere('is_system', true);
                })
                    ->orderBy('name')
                    ->get();
                return response()->json(['success' => true, 'data' => $categories]);
            });

            Route::post('/', function (Request $request) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'type' => 'required|in:credit,debit',
                    'color' => 'required|string',
                    'icon' => 'nullable|string',
                    'description' => 'nullable|string|max:500',
                ]);

                $userId = $request->user() ? $request->user()->id : session('user_id');

                // Map type from credit/debit to income/expense
                $typeMapping = [
                    'credit' => 'income',
                    'debit' => 'expense'
                ];

                $category = \App\Models\Category::create([
                    'user_id' => $userId,
                    'name' => $validated['name'],
                    'type' => $typeMapping[$validated['type']],
                    'color' => $validated['color'],
                    'icon' => $validated['icon'] ?? 'fa-tag',
                ]);

                return response()->json(['success' => true, 'data' => $category], 201);
            });

            Route::delete('/{id}', function (Request $request, $id) {
                $userId = $request->user() ? $request->user()->id : session('user_id');
                $category = \App\Models\Category::where('user_id', $userId)
                    ->where('id', $id)
                    ->first();

                if (!$category) {
                    return response()->json(['success' => false, 'message' => 'Category not found'], 404);
                }

                $category->delete();
                return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
            });
        });

        // Budgets
        Route::prefix('budgets')->group(function () {
            Route::get('/', function (Request $request) {
                $budgets = \App\Models\Budget::where('user_id', $request->user()->id)
                    ->with('items.category')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get();
                return response()->json(['success' => true, 'data' => $budgets]);
            });

            Route::post('/', function (Request $request) {
                $validated = $request->validate([
                    'month' => 'required|integer|between:1,12',
                    'year' => 'required|integer|min:2020',
                    'total_limit' => 'required|numeric|min:0',
                    'name' => 'nullable|string|max:255',
                    'items' => 'required|array',
                    'items.*.category_id' => 'required|exists:categories,id',
                    'items.*.limit_amount' => 'required|numeric|min:0',
                ]);

                $budget = \App\Models\Budget::create([
                    'user_id' => $request->user()->id,
                    'name' => $validated['name'] ?? null,
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'total_limit' => $validated['total_limit'],
                ]);

                foreach ($validated['items'] as $item) {
                    \App\Models\BudgetItem::create([
                        'budget_id' => $budget->id,
                        'category_id' => $item['category_id'],
                        'limit_amount' => $item['limit_amount'],
                    ]);
                }

                return response()->json(['success' => true, 'data' => $budget->load('items.category')], 201);
            });

            Route::delete('/{id}', function (Request $request, $id) {
                $budget = \App\Models\Budget::where('user_id', $request->user()->id)
                    ->where('id', $id)
                    ->first();

                if (!$budget) {
                    return response()->json(['success' => false, 'message' => 'Budget not found'], 404);
                }

                $budget->delete();
                return response()->json(['success' => true, 'message' => 'Budget deleted successfully']);
            });
        });

        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('/', function (Request $request) {
                $reports = \App\Models\Report::where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                return response()->json(['success' => true, 'data' => $reports]);
            });

            Route::post('/generate', function (Request $request) {
                $validated = $request->validate([
                    'type' => 'required|string',
                    'params' => 'required|array',
                    'format' => 'required|in:pdf,csv,excel',
                ]);

                $report = \App\Models\Report::create([
                    'user_id' => $request->user()->id,
                    'report_type' => $validated['type'],
                    'parameters' => $validated['params'],
                    'format' => $validated['format'],
                    'status' => 'pending',
                ]);

                // Dispatch job to generate report
                // \App\Jobs\GenerateReportJob::dispatch($report->id);

                return response()->json(['success' => true, 'data' => $report], 201);
            });
        });

        // Dashboard Analytics
        Route::get('/dashboard/stats', function (Request $request) {
            $user = $request->user();

            $stats = [
                'total_balance' => 0,
                'monthly_expenses' => \App\Models\Transaction::where('user_id', $user->id)
                    ->where('type', 'debit')
                    ->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month)
                    ->sum('amount'),
                'monthly_income' => \App\Models\Transaction::where('user_id', $user->id)
                    ->where('type', 'credit')
                    ->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month)
                    ->sum('amount'),
                'linked_accounts' => 0,
            ];

            return response()->json(['success' => true, 'data' => $stats]);
        });
    });

    // Goals (Session-based auth)
    Route::prefix('goals')->group(function () {
        Route::get('/', [GoalController::class, 'index']);
        Route::post('/', [GoalController::class, 'store']);
        Route::get('/{id}', [GoalController::class, 'show']);
        Route::put('/{id}', [GoalController::class, 'update']);
        Route::delete('/{id}', [GoalController::class, 'destroy']);
        Route::post('/{id}/contribute', [GoalController::class, 'addContribution']);
    });

    // Webhook receiver (no auth - signature verification in job)
    Route::post('webhooks/provider', function (Request $request) {
        // TODO: Verify webhook signature
        ProcessProviderWebhookJob::dispatch($request->all());

        return response()->json(['success' => true, 'message' => 'Webhook received']);
    });

});
