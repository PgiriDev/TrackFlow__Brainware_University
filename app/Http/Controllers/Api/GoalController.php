<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GoalController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function index(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Get user's currency preference (prefer user_settings.display_currency)
        $user = \App\Models\User::find($userId);
        $userSetting = app('user.settings');
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');
        $currencyService = app(\App\Services\CurrencyService::class);

        $goals = Goal::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($goal) use ($currencyService, $userCurrency) {
                $storedCurrency = $goal->currency ?? 'INR';

                // Only convert if goal currency differs from user currency
                if ($storedCurrency === $userCurrency) {
                    // No conversion needed
                    $goal->display_currency = $userCurrency;
                    $goal->stored_currency = $storedCurrency;
                } else {
                    // Convert amounts to user's display currency
                    $goal->target_amount = round($currencyService->convert(
                        (float) $goal->target_amount,
                        $storedCurrency,
                        $userCurrency
                    ), 2);

                    $goal->current_amount = round($currencyService->convert(
                        (float) $goal->current_amount,
                        $storedCurrency,
                        $userCurrency
                    ), 2);

                    $goal->display_currency = $userCurrency;
                    $goal->stored_currency = $storedCurrency;
                }

                return $goal;
            });

        return response()->json([
            'success' => true,
            'data' => $goals,
            'meta' => [
                'currency' => $userCurrency
            ]
        ]);
    }

    public function store(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
            'status' => 'nullable|string|in:in_progress,completed,paused'
        ]);

        // Get user's currency and store amounts in user's currency (prefer user_settings.display_currency)
        $user = \App\Models\User::find($userId);
        $userSetting = app('user.settings');
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');

        $goal = Goal::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'target_amount' => $validated['target_amount'],
            'current_amount' => $validated['current_amount'] ?? 0,
            'currency' => $userCurrency,
            'target_date' => $validated['target_date'] ?? null,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? '🎯',
            'color' => $validated['color'] ?? '#3B82F6',
            'status' => $validated['status'] ?? 'in_progress',
        ]);

        // Send goal created notification and email
        $this->notificationService->goalCreated($userId, $goal);

        return response()->json([
            'success' => true,
            'message' => 'Goal created successfully',
            'data' => $goal
        ], 201);
    }

    public function show($id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $goal = Goal::where('user_id', $userId)->findOrFail($id);

        // Get user's currency and convert amounts for display (prefer user_settings.display_currency)
        $user = \App\Models\User::find($userId);
        $userSetting = app('user.settings');
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');
        $currencyService = app(\App\Services\CurrencyService::class);
        $storedCurrency = $goal->currency ?? 'INR';

        // Only convert if goal currency differs from user currency
        if ($storedCurrency !== $userCurrency) {
            $goal->target_amount = round($currencyService->convert(
                (float) $goal->target_amount,
                $storedCurrency,
                $userCurrency
            ), 2);

            $goal->current_amount = round($currencyService->convert(
                (float) $goal->current_amount,
                $storedCurrency,
                $userCurrency
            ), 2);
        }

        $goal->display_currency = $userCurrency;
        $goal->stored_currency = $storedCurrency;

        return response()->json([
            'success' => true,
            'data' => $goal
        ]);
    }

    public function update(Request $request, $id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $goal = Goal::where('user_id', $userId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:100',
            'target_amount' => 'sometimes|numeric|min:0.01',
            'current_amount' => 'sometimes|numeric|min:0',
            'target_date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
            'status' => 'sometimes|string|in:in_progress,completed,paused'
        ]);

        // Get user's currency - amounts stay in user's currency (prefer user_settings.display_currency)
        $user = \App\Models\User::find($userId);
        $userSetting = app('user.settings');
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');

        // Update currency to match user's current currency if amounts are being updated
        if (isset($validated['target_amount']) || isset($validated['current_amount'])) {
            $validated['currency'] = $userCurrency;
        }
        $goal->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Goal updated successfully',
            'data' => $goal
        ]);
    }

    public function destroy($id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $goal = Goal::where('user_id', $userId)->findOrFail($id);
        $goal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Goal deleted successfully'
        ]);
    }

    public function addContribution(Request $request, $id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $goal = Goal::where('user_id', $userId)->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500'
        ]);

        // Get user's currency (prefer user_settings.display_currency)
        $user = \App\Models\User::find($userId);
        $userSetting = app('user.settings');
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');
        $currencyService = app(\App\Services\CurrencyService::class);

        // Convert contribution to goal's currency if different
        $goalCurrency = $goal->currency ?? 'INR';
        if ($goalCurrency === $userCurrency) {
            $contributionAmount = $validated['amount'];
        } else {
            // Convert from user's currency to goal's currency
            $contributionAmount = round($currencyService->convert($validated['amount'], $userCurrency, $goalCurrency), 2);
        }

        // Store old values for milestone checking
        $oldAmount = $goal->current_amount;
        $oldPercentage = $goal->target_amount > 0 ? ($oldAmount / $goal->target_amount) * 100 : 0;

        $goal->current_amount += $contributionAmount;
        $goal->currency = $goalCurrency;

        // Calculate new percentage
        $newPercentage = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;

        // Check for milestone notifications (25%, 50%, 75%)
        $milestones = [25, 50, 75];
        foreach ($milestones as $milestone) {
            if ($oldPercentage < $milestone && $newPercentage >= $milestone && $newPercentage < 100) {
                $this->notificationService->goalMilestone($userId, $goal, $milestone);
            }
        }

        // Auto-complete if target reached
        $wasCompleted = $goal->status === 'completed';
        if ($goal->current_amount >= $goal->target_amount && !$wasCompleted) {
            $goal->status = 'completed';
            // Send goal completion notification
            $this->notificationService->goalCompleted($userId, $goal);
        }

        $goal->save();

        return response()->json([
            'success' => true,
            'message' => 'Contribution added successfully',
            'data' => $goal
        ]);
    }
}

