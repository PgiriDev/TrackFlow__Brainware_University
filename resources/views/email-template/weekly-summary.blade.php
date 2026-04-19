@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    <p class="message">
        Here's your <strong>{{ $reportPeriod ?? 'weekly' }} financial summary</strong> from TrackFlow. Let's see how you're
        doing!
    </p>

    <!-- Stats Cards -->
    <div class="stats-row" style="text-align: center;">
        <div class="stat-card income" style="margin-right: 2%;">
            <p class="stat-label">💰 Income</p>
            <p class="stat-value">{{ $currency ?? '₹' }}{{ number_format($totalIncome ?? 0, 0) }}</p>
        </div>
        <div class="stat-card expense">
            <p class="stat-label">💸 Expenses</p>
            <p class="stat-value">{{ $currency ?? '₹' }}{{ number_format($totalExpenses ?? 0, 0) }}</p>
        </div>
    </div>

    <!-- Net Savings -->
    @php
        $netSavings = $netSavings ?? (($totalIncome ?? 0) - ($totalExpenses ?? 0));
        $isPositive = $netSavings >= 0;
    @endphp

    <div class="progress-container"
        style="text-align: center; {{ $isPositive ? 'border-color: rgba(16, 185, 129, 0.3);' : 'border-color: rgba(239, 68, 68, 0.3);' }}">
        <p style="color: #718096; font-size: 13px; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;">
            {{ $isPositive ? '🎉 Net Savings' : '📉 Net Loss' }}
        </p>
        <p style="font-size: 42px; font-weight: 800; color: {{ $isPositive ? '#34d399' : '#f87171' }}; margin: 0;">
            {{ $isPositive ? '+' : '' }}{{ $currency ?? '₹' }}{{ number_format($netSavings, 0) }}
        </p>
        @if(isset($savingsRate))
            <p style="color: #718096; font-size: 14px; margin-top: 12px;">
                Savings Rate: <strong
                    style="color: {{ $savingsRate >= 20 ? '#34d399' : ($savingsRate >= 10 ? '#fbbf24' : '#f87171') }}">{{ number_format($savingsRate, 1) }}%</strong>
            </p>
        @endif
    </div>

    @if($isPositive)
        <div class="alert-box success">
            <p class="alert-title">🌟 Great Job!</p>
            <p class="alert-message">
                You saved money this {{ $reportPeriod ?? 'week' }}! Keep up the excellent work on your financial journey.
            </p>
        </div>
    @else
        <div class="alert-box">
            <p class="alert-title">💡 Tip</p>
            <p class="alert-message">
                You spent more than you earned this {{ $reportPeriod ?? 'week' }}. Consider reviewing your expenses to find
                areas where you can cut back.
            </p>
        </div>
    @endif

    @if(isset($topCategories) && is_array($topCategories) && count($topCategories) > 0)
        <div class="divider"></div>

        <p class="message" style="margin-bottom: 20px;">
            <strong style="color: #ffffff; font-size: 16px;">📊 Top Spending Categories:</strong>
        </p>

        @foreach($topCategories as $index => $category)
            <div class="feature-card" style="margin-bottom: 12px;">
                <span class="feature-icon"
                    style="background: linear-gradient(135deg, {{ ['#0d9488, #14b8a6', '#10b981, #059669', '#f59e0b, #d97706', '#ec4899, #db2777', '#8b5cf6, #7c3aed'][$index % 5] }});">
                    {{ $category['icon'] ?? ['🛒', '🍽️', '🚗', '🎬', '📱'][$index % 5] }}
                </span>
                <div class="feature-content" style="display: inline-block; width: calc(100% - 100px);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <p class="feature-title" style="margin: 0;">{{ $category['name'] }}</p>
                        <p style="color: #f87171; font-weight: 700; margin: 0;">
                            {{ $currency ?? '₹' }}{{ number_format($category['amount'], 0) }}</p>
                    </div>
                    @if(isset($category['percentage']))
                        <div class="progress-bar" style="height: 6px; margin-top: 8px; background: rgba(255,255,255,0.1);">
                            <div class="progress-fill"
                                style="width: {{ min($category['percentage'], 100) }}%; background: linear-gradient(90deg, #0d9488, #5eead4);">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif

    @if(isset($transactionCount) || isset($avgDailySpend))
        <div class="divider"></div>

        <div class="progress-container">
            <p style="color: #ffffff; font-size: 16px; font-weight: 600; margin-bottom: 16px;">📈 Activity Summary</p>
            <table class="data-table">
                @if(isset($transactionCount))
                    <tr>
                        <td>🔢 Total Transactions</td>
                        <td>{{ $transactionCount }}</td>
                    </tr>
                @endif
                @if(isset($avgDailySpend))
                    <tr>
                        <td>📅 Avg. Daily Spending</td>
                        <td>{{ $currency ?? '₹' }}{{ number_format($avgDailySpend, 2) }}</td>
                    </tr>
                @endif
                @if(isset($largestExpense))
                    <tr>
                        <td>💰 Largest Expense</td>
                        <td style="color: #f87171;">{{ $currency ?? '₹' }}{{ number_format($largestExpense, 2) }}</td>
                    </tr>
                @endif
                @if(isset($mostFrequentCategory))
                    <tr>
                        <td>🏷️ Most Frequent</td>
                        <td>{{ $mostFrequentCategory }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    @if(isset($insight))
        <div class="alert-box purple">
            <p class="alert-title">💡 Weekly Insight</p>
            <p class="alert-message">{{ $insight }}</p>
        </div>
    @endif

    @if(isset($goals) && is_array($goals) && count($goals) > 0)
        <div class="divider"></div>

        <p class="message" style="margin-bottom: 16px;">
            <strong style="color: #ffffff; font-size: 16px;">🎯 Goals Progress:</strong>
        </p>

        @foreach($goals as $goal)
            <div class="feature-card">
                <span class="feature-icon"
                    style="background: linear-gradient(135deg, #10b981, #059669);">{{ $goal['icon'] ?? '🎯' }}</span>
                <div class="feature-content">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <p class="feature-title" style="margin: 0;">{{ $goal['name'] }}</p>
                        <span style="color: #34d399; font-weight: 600;">{{ number_format($goal['percentage'] ?? 0, 0) }}%</span>
                    </div>
                    <div class="progress-bar" style="height: 6px; margin: 0; background: rgba(255,255,255,0.1);">
                        <div class="progress-fill success" style="width: {{ min($goal['percentage'] ?? 0, 100) }}%;"></div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="button-container">
        <a href="{{ config('app.url', 'https://trackflow.app') }}/reports" class="button">View Full Report →</a>
    </div>

    <p class="message" style="text-align: center; color: #718096; font-size: 13px;">
        Customize your summary preferences in <a
            href="{{ config('app.url', 'https://trackflow.app') }}/settings/notifications"
            style="color: #0d9488;">notification settings</a>.
    </p>
@endsection
