@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    <div class="alert-box success">
        <p class="alert-title">🎯 New Goal Created!</p>
        <p class="alert-message">
            You've set a new savings goal: <strong>{{ $goalName ?? 'Your Goal' }}</strong>.
            Stay focused and track your progress regularly!
        </p>
    </div>

    <div class="progress-container" style="text-align: center;">
        <div style="font-size: 56px; margin-bottom: 16px;">{{ $goalIcon ?? '🎯' }}</div>

        <p style="color: #ffffff; font-size: 20px; font-weight: 700; margin-bottom: 4px;">{{ $goalName ?? 'Your Goal' }}</p>

        @if(isset($targetDate))
            <p style="color: #718096; font-size: 13px; margin-bottom: 24px;">Target Date: {{ $targetDate }}</p>
        @endif

        <div class="progress-bar" style="height: 16px; margin: 24px 0;">
            <div class="progress-fill" style="width: {{ $percentage ?? 0 }}%;"></div>
        </div>

        <p style="font-size: 36px; font-weight: 800; color: #5eead4; margin: 16px 0;">
            {{ number_format($percentage ?? 0, 1) }}%
        </p>
        <p style="color: #718096; font-size: 14px; margin-bottom: 24px;">
            Your journey begins!
        </p>

        <table class="data-table">
            <tr>
                <td>💰 Starting Amount</td>
                <td style="color: #34d399;">{{ $currency ?? '₹' }}{{ number_format($currentAmount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>🎯 Target Amount</td>
                <td>{{ $currency ?? '₹' }}{{ number_format($targetAmount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>📊 Remaining</td>
                <td style="color: #a78bfa;">
                    {{ $currency ?? '₹' }}{{ number_format(($targetAmount ?? 0) - ($currentAmount ?? 0), 2) }}</td>
            </tr>
            @if(isset($goalType))
                <tr>
                    <td>📂 Category</td>
                    <td>{{ ucfirst($goalType) }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="alert-box info">
        <p class="alert-title">💡 Tips for Success</p>
        <p class="alert-message">
            • Set up automatic contributions to stay consistent<br>
            • Review your progress weekly<br>
            • Break down your goal into monthly targets<br>
            • Celebrate milestones along the way!
        </p>
    </div>

    <div class="button-container">
        <a href="{{ config('app.url', 'https://trackflow.app') }}/goals" class="button">View My Goals 🚀</a>
    </div>

    <p class="footer-note">
        We'll keep you updated on your progress at 50% and when you reach your goal! 🎉
    </p>
@endsection