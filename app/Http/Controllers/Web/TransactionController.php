<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TransactionController extends Controller
{
    public function index()
    {
        return view('transactions.index');
    }

    public function create()
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Use cached categories
        $categories = Category::getCachedForUser($userId)->values();

        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $validated = $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'transaction_date' => 'required|date',
            'merchant' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Use cached user settings
        $user = \App\Models\User::find($userId);
        $userSetting = app('user.settings');
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');

        // Convert input amount from user's currency to base currency for storage
        $currencyService = app(\App\Services\CurrencyService::class);
        $amountInBase = $currencyService->toBase($validated['amount'], $userCurrency);

        // Create transaction using direct DB insert to avoid model field mismatches
        $transaction = \App\Models\Transaction::create([
            'user_id' => $userId,
            'type' => $validated['type'],
            'amount' => round($amountInBase, 2),
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'date' => $validated['transaction_date'],
            'merchant' => $validated['merchant'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'completed',
            'currency' => $currencyService->getBaseCurrency(), // store in configured base currency
            'is_recurring' => false,
            'is_duplicate' => false,
        ]);

        // Create notifications
        $notificationService = app(\App\Services\NotificationService::class);

        // Always notify about transaction being added
        $notificationService->transactionAdded(
            $userId,
            $transaction,
            $validated['amount'], // Amount in user's currency
            $userCurrency
        );

        // Check if it's a large transaction and notify
        $threshold = $notificationService->getLargeTransactionThreshold($userId, $userCurrency);
        if ($validated['amount'] >= $threshold) {
            $notificationService->largeTransaction(
                $userId,
                $transaction,
                $validated['amount'], // Amount in user's currency
                $userCurrency,
                $threshold
            );
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction added successfully!');
    }

    public function edit($id)
    {
        return view('transactions.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Handled via API
        return redirect()->route('transactions.index');
    }

    public function destroy($id)
    {
        // Handled via API
        return redirect()->route('transactions.index');
    }

    public function deleteAjax(Request $request, $id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $transaction = \App\Models\Transaction::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        $transaction->delete();
        return response()->json(['success' => true, 'message' => 'Transaction deleted successfully']);
    }
}
