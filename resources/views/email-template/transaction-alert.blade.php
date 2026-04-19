@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    @php
        $isIncome = ($transactionType ?? 'expense') === 'income';
    @endphp

    <p class="message">
        We detected a <strong>{{ $isIncome ? 'new income' : 'new expense' }}</strong> on your linked account:
    </p>

    <div class="transaction-card">
        <div class="transaction-icon {{ $isIncome ? 'income' : 'expense' }}">
            {{ $isIncome ? '↓' : '↑' }}
        </div>

        <p class="transaction-type">{{ ucfirst($transactionType ?? 'Transaction') }}</p>

        <p class="transaction-amount {{ $isIncome ? 'income' : 'expense' }}">
            {{ $isIncome ? '+' : '-' }}{{ $currency ?? '₹' }}{{ number_format($amount ?? 0, 2) }}
        </p>

        <table class="data-table">
            @if(isset($description))
                <tr>
                    <td>📝 Description</td>
                    <td>{{ $description }}</td>
                </tr>
            @endif
            @if(isset($category))
                <tr>
                    <td>🏷️ Category</td>
                    <td>{{ $category }}</td>
                </tr>
            @endif
            @if(isset($accountName))
                <tr>
                    <td>🏦 Account</td>
                    <td>{{ $accountName }}</td>
                </tr>
            @endif
            @if(isset($transactionDate))
                <tr>
                    <td>📅 Date</td>
                    <td>{{ $transactionDate }}</td>
                </tr>
            @endif
            @if(isset($merchant))
                <tr>
                    <td>🏪 Merchant</td>
                    <td>{{ $merchant }}</td>
                </tr>
            @endif
            @if(isset($balance))
                <tr>
                    <td>💰 Balance</td>
                    <td style="color: #34d399;">{{ $currency ?? '₹' }}{{ number_format($balance, 2) }}</td>
                </tr>
            @endif
        </table>
    </div>

    @if(isset($isLargeTransaction) && $isLargeTransaction)
        <div class="alert-box">
            <p class="alert-title">💰 Large Transaction Alert</p>
            <p class="alert-message">
                This transaction exceeds your configured threshold for large transactions. If you didn't authorize this, please
                review your account immediately.
            </p>
        </div>
    @endif

    @if(isset($isSuspicious) && $isSuspicious)
        <div class="alert-box danger">
            <p class="alert-title">🚨 Unusual Activity Detected</p>
            <p class="alert-message">
                This transaction appears unusual based on your spending patterns. Please verify that you authorized this
                transaction. If not, contact your bank immediately.
            </p>
        </div>

        <div class="button-container">
            <a href="{{ config('app.url', 'https://trackflow.app') }}/transactions/{{ $transactionId ?? '' }}/dispute"
                class="button" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">Report Suspicious
                Activity</a>
        </div>
    @endif

    @if(isset($budgetImpact))
        <div class="alert-box info">
            <p class="alert-title">📊 Budget Impact</p>
            <p class="alert-message">
                {{ $budgetImpact }}
            </p>
        </div>
    @endif

    <div class="button-container">
        <a href="{{ config('app.url', 'https://trackflow.app') }}/transactions" class="button">View All Transactions</a>
    </div>

    <p class="message" style="text-align: center; color: #718096; font-size: 13px;">
        You can customize transaction alerts in your <a
            href="{{ config('app.url', 'https://trackflow.app') }}/settings/notifications"
            style="color: #0d9488;">notification settings</a>.
    </p>
@endsection
