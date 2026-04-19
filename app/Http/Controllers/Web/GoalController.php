<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $user = \App\Models\User::find($userId);

        // Get currency configuration - use cached singleton
        $currencyConfig = config('currency.currencies');
        $currencyRates = config('currency.rates');
        $userSetting = app('user.settings');
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');
        $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? $currencyConfig[config('currency.default')]['symbol'] ?? '₹';

        return view('goals.index', compact('currencyConfig', 'currencyRates', 'userCurrency', 'currencySymbol'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        // Handled via API
        return redirect()->route('goals.index')
            ->with('success', 'Created successfully');
    }

    public function edit($id)
    {
        return view('goals.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Handled via API
        return redirect()->route('goals.index')
            ->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        // Handled via API
        return redirect()->route('goals.index')
            ->with('success', 'Deleted successfully');
    }

    public function listAjax(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $goals = \App\Models\Goal::where('user_id', $userId)
            ->whereIn('status', ['in_progress', 'paused'])
            ->orderBy('target_date', 'asc')
            ->get();

        return response()->json(['success' => true, 'data' => $goals]);
    }
}