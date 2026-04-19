@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    @php
        $percentage = $percentage ?? 0;
        $isComplete = $percentage >= 100;
    @endphp

    <p class="message">
        @if($isComplete)
            <strong>Congratulations!</strong> 🎉 You've reached your savings goal!
        @else
            Great progress on your savings goal! Here's your latest update:
        @endif
    </p>

    <div class="progress-container"
        style="text-align: center; {{ $isComplete ? 'border-color: rgba(16, 185, 129, 0.3); background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(5, 150, 105, 0.03) 100%);' : '' }}">
        <div style="font-size: 56px; margin-bottom: 16px;">{{ $goalIcon ?? '🎯' }}</div>

        <p style="color: #ffffff; font-size: 20px; font-weight: 700; margin-bottom: 4px;">{{ $goalName ?? 'Your Goal' }}</p>

        @if(isset($targetDate))
            <p style="color: #718096; font-size: 13px; margin-bottom: 24px;">Target: {{ $targetDate }}</p>
        @endif

        <div class="progress-bar" style="height: 16px; margin: 24px 0;">
            <div class="progress-fill {{ $isComplete ? 'success' : '' }}" style="width: {{ min($percentage, 100) }}%;">
            </div>
        </div>

        <p style="font-size: 36px; font-weight: 800; color: {{ $isComplete ? '#34d399' : '#5eead4' }}; margin: 16px 0;">
            {{ number_format($percentage, 1) }}%
        </p>
        <p style="color: #718096; font-size: 14px; margin-bottom: 24px;">
            @if($isComplete)
                Goal Achieved! 🏆
            @else
                {{ number_format(100 - $percentage, 1) }}% to go
            @endif
        </p>

        <table class="data-table">
            <tr>
                <td>💰 Saved</td>
                <td style="color: #34d399;">{{ $currency ?? '₹' }}{{ number_format($saved ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>🎯 Target</td>
                <td>{{ $currency ?? '₹' }}{{ number_format($target ?? 0, 2) }}</td>
            </tr>
            @if(!$isComplete)
                <tr>
                    <td>📊 Remaining</td>
                    <td style="color: #a78bfa;">{{ $currency ?? '₹' }}{{ number_format($remaining ?? 0, 2) }}</td>
                </tr>
            @endif
            @if(isset($monthlyTarget) && !$isComplete)
                <tr>
                    <td>📅 Monthly Target</td>
                    <td>{{ $currency ?? '₹' }}{{ number_format($monthlyTarget, 2) }}/month</td>
                </tr>
            @endif
        </table>
    </div>

    @if($isComplete)
        <div class="alert-box success">
            <p class="alert-title">🎉 Amazing Achievement!</p>
            <p class="alert-message">
                You've successfully saved {{ $currency ?? '₹' }}{{ number_format($saved ?? 0, 2) }} for
                <strong>{{ $goalName ?? 'your goal' }}</strong>!
                This is a fantastic accomplishment. Ready to set your next savings goal?
            </p>
        </div>

        <div class="button-container">
            <a href="{{ config('app.url', 'https://trackflow.app') }}/goals/new" class="button">Create New Goal 🚀</a>
        </div>
    @else
        <div class="alert-box purple">
            <p class="alert-title">💡 Keep Going!</p>
            <p class="alert-message">
                @if(isset($daysLeft) && $daysLeft > 0)
                    You have {{ $daysLeft }} days left to reach your goal.
                @endif
                Every small contribution adds up. Stay consistent and you'll get there!
            </p>
        </div>

        @if(isset($tips) && is_array($tips))
            <div class="divider"></div>
            <p class="message" style="margin-bottom: 16px;">
                <strong style="color: #ffffff;">💰 Tips to Save Faster:</strong>
            </p>
            @foreach($tips as $tip)
                <div class="feature-card">
                    <span class="feature-icon" style="background: linear-gradient(135deg, #10b981, #059669);">✓</span>
                    <div class="feature-content">
                        <p class="feature-desc" style="color: #a0aec0;">{{ $tip }}</p>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="button-container">
            <a href="{{ config('app.url', 'https://trackflow.app') }}/goals" class="button">View Goal Details</a>
        </div>
    @endif
@endsection
