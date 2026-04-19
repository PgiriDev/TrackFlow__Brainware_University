@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    <div class="alert-box success">
        <p class="alert-title">📊 New Budget Created!</p>
        <p class="alert-message">
            You've set up a new budget: <strong>{{ $budgetName ?? 'Monthly Budget' }}</strong> for
            {{ $monthName ?? 'this month' }} {{ $year ?? date('Y') }}.
            Great job taking control of your finances!
        </p>
    </div>

    <div class="progress-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <span class="progress-title">{{ $budgetName ?? 'Budget' }}</span>
            <span class="progress-value" style="color: #34d399;">0%</span>
        </div>

        <div class="progress-bar">
            <div class="progress-fill success" style="width: 0%;"></div>
        </div>

        <table class="data-table">
            <tr>
                <td>💰 Total Budget</td>
                <td style="color: #34d399;">{{ $currency ?? '₹' }}{{ number_format($totalLimit ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>📅 Period</td>
                <td>{{ $monthName ?? 'Month' }} {{ $year ?? date('Y') }}</td>
            </tr>
            <tr>
                <td>📂 Categories</td>
                <td>{{ $categoryCount ?? 0 }} categories</td>
            </tr>
        </table>
    </div>

    @if(isset($categories) && count($categories) > 0)
        <div class="progress-container">
            <p style="color: #ffffff; font-size: 16px; font-weight: 600; margin-bottom: 16px;">Budget Categories</p>
            <table class="data-table">
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $category['name'] ?? 'Category' }}</td>
                        <td>{{ $currency ?? '₹' }}{{ number_format($category['limit'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="alert-box info">
        <p class="alert-title">💡 Budget Tips</p>
        <p class="alert-message">
            • Track every expense to stay within limits<br>
            • Review spending patterns weekly<br>
            • Adjust categories as needed<br>
            • We'll alert you at 50% and when approaching limits!
        </p>
    </div>

    <div class="button-container">
        <a href="{{ config('app.url', 'https://trackflow.app') }}/budgets" class="button">View My Budgets 📊</a>
    </div>

    <p class="footer-note">
        We'll notify you when you reach 50% and 100% of your budget. Stay on track! 💪
    </p>
@endsection