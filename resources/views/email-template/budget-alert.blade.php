@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    @php
        $percentage = $percentage ?? 0;
        $progressClass = $percentage >= 100 ? 'danger' : ($percentage >= 80 ? 'warning' : 'success');
        $alertClass = $percentage >= 100 ? 'danger' : ($percentage >= 80 ? '' : 'info');
        $alertIcon = $percentage >= 100 ? '🚨' : ($percentage >= 80 ? '⚠️' : '📊');
        $alertTitle = $percentage >= 100 ? 'Budget Exceeded!' : ($percentage >= 80 ? 'Budget Warning' : 'Budget Update');
    @endphp

    <div class="alert-box {{ $alertClass }}">
        <p class="alert-title">{{ $alertIcon }} {{ $alertTitle }}</p>
        <p class="alert-message">
            @if($percentage >= 100)
                You've exceeded your <strong>{{ $budgetName ?? 'budget' }}</strong> limit. Time to review your spending!
            @elseif($percentage >= 80)
                You're approaching your <strong>{{ $budgetName ?? 'budget' }}</strong> limit.
                {{ number_format(100 - $percentage, 1) }}% remaining.
            @else
                Here's an update on your <strong>{{ $budgetName ?? 'budget' }}</strong> progress.
            @endif
        </p>
    </div>

    <div class="progress-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <span class="progress-title">{{ $budgetName ?? 'Budget' }}</span>
            <span class="progress-value"
                style="color: {{ $percentage >= 100 ? '#f87171' : ($percentage >= 80 ? '#fbbf24' : '#34d399') }};">{{ number_format($percentage, 1) }}%</span>
        </div>

        <div class="progress-bar">
            <div class="progress-fill {{ $progressClass }}" style="width: {{ min($percentage, 100) }}%;"></div>
        </div>

        <table class="data-table">
            <tr>
                <td>💰 Budget Limit</td>
                <td>{{ $currency ?? '₹' }}{{ number_format($limit ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>💸 Amount Spent</td>
                <td style="color: {{ $percentage >= 80 ? '#f87171' : '#ffffff' }};">
                    {{ $currency ?? '₹' }}{{ number_format($spent ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>✨ Remaining</td>
                <td style="color: {{ ($remaining ?? 0) >= 0 ? '#34d399' : '#f87171' }};">
                    {{ $currency ?? '₹' }}{{ number_format($remaining ?? 0, 2) }}</td>
            </tr>
            @if(isset($daysLeft))
                <tr>
                    <td>📅 Days Left</td>
                    <td>{{ $daysLeft }} days</td>
                </tr>
            @endif
        </table>
    </div>

    @if($percentage >= 100)
        <div class="alert-box danger">
            <p class="alert-title">🎯 What You Can Do</p>
            <p class="alert-message">
                • Review recent transactions for unnecessary expenses<br>
                • Consider adjusting your budget for next month<br>
                • Set up spending alerts for earlier notifications<br>
                • Identify categories where you can cut back
            </p>
        </div>
    @elseif($percentage >= 80)
        <div class="alert-box">
            <p class="alert-title">💡 Tips to Stay on Track</p>
            <p class="alert-message">
                • You have {{ $currency ?? '₹' }}{{ number_format($remaining ?? 0, 2) }} left for this period<br>
                • Consider delaying non-essential purchases<br>
                • Check if any subscriptions can be paused
            </p>
        </div>
    @else
        <div class="alert-box success">
            <p class="alert-title">🌟 You're Doing Great!</p>
            <p class="alert-message">
                You're well within your budget. Keep up the excellent financial discipline!
            </p>
        </div>
    @endif

    <div class="button-container">
        <a href="{{ config('app.url', 'https://trackflow.app') }}/budgets" class="button">View All Budgets</a>
    </div>
@endsection
