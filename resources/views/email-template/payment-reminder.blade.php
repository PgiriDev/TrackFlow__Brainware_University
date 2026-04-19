@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    @if(isset($payments) && is_array($payments) && count($payments) > 0)
        {{-- Multiple payments reminder --}}
        <p class="message">
            You have <strong>{{ count($payments) }}</strong> upcoming payment{{ count($payments) > 1 ? 's' : '' }} 
            totaling <strong>{{ $currency ?? '₹' }}{{ $total_amount ?? '0.00' }}</strong>:
        </p>

        @foreach($payments as $payment)
            @php
                $isOverdue = isset($payment['is_overdue']) && $payment['is_overdue'];
            @endphp
            <div class="progress-container" style="margin-bottom: 16px; {{ $isOverdue ? 'border-color: rgba(239, 68, 68, 0.3); background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(220, 38, 38, 0.03) 100%);' : '' }}">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="color: #ffffff; font-size: 16px; font-weight: 600; margin: 0;">
                            {{ $isOverdue ? '⏰' : '📅' }} {{ $payment['name'] ?? 'Payment' }}
                        </p>
                        <p style="color: #718096; font-size: 13px; margin: 4px 0 0 0;">
                            {{ $payment['category'] ?? '' }} • Due: {{ $payment['due_date'] ?? 'N/A' }}
                            @if(isset($payment['days_until']))
                                @if($payment['days_until'] < 0)
                                    <span style="color: #f87171;">({{ abs($payment['days_until']) }} days overdue)</span>
                                @elseif($payment['days_until'] == 0)
                                    <span style="color: #fbbf24;">(Due today!)</span>
                                @else
                                    <span style="color: #34d399;">(in {{ $payment['days_until'] }} day{{ $payment['days_until'] > 1 ? 's' : '' }})</span>
                                @endif
                            @endif
                        </p>
                    </div>
                    <p style="color: {{ $isOverdue ? '#f87171' : '#5eead4' }}; font-size: 20px; font-weight: 700; margin: 0;">
                        {{ $currency ?? '₹' }}{{ $payment['amount'] ?? '0.00' }}
                    </p>
                </div>
            </div>
        @endforeach

        <div class="alert-box info">
            <p class="alert-title">💡 Stay on Top of Your Finances</p>
            <p class="alert-message">
                Set up automatic payments to never miss a due date. You can manage your recurring transactions in the app.
            </p>
        </div>

    @else
        {{-- Single payment reminder (legacy) --}}
        <p class="message">
            This is a friendly reminder about an upcoming payment:
        </p>

        @php
            $isOverdue = isset($isOverdue) && $isOverdue;
        @endphp

        <div class="progress-container"
            style="{{ $isOverdue ? 'border-color: rgba(239, 68, 68, 0.3); background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(220, 38, 38, 0.03) 100%);' : '' }}">
            <div style="text-align: center; margin-bottom: 20px;">
                <span style="font-size: 48px;">{{ $isOverdue ? '⏰' : '📅' }}</span>
            </div>

            <p style="text-align: center; color: #ffffff; font-size: 18px; font-weight: 700; margin-bottom: 8px;">
                {{ $reminderTitle ?? 'Payment Reminder' }}
            </p>

            @if(isset($reminderMessage))
                <p style="text-align: center; color: #a0aec0; font-size: 14px; margin-bottom: 24px;">
                    {{ $reminderMessage }}
                </p>
            @endif

            <table class="data-table">
                @if(isset($category))
                    <tr>
                        <td>🏷️ Category</td>
                        <td>{{ $category }}</td>
                    </tr>
                @endif
                @if(isset($payee))
                    <tr>
                        <td>🏪 Payee</td>
                        <td>{{ $payee }}</td>
                    </tr>
                @endif
                @if(isset($dueDate))
                    <tr>
                        <td>📅 Due Date</td>
                        <td style="{{ $isOverdue ? 'color: #f87171;' : '' }}">{{ $dueDate }}</td>
                    </tr>
                @endif
                @if(isset($amount))
                    <tr>
                        <td>💰 Amount</td>
                        <td style="color: {{ $isOverdue ? '#f87171' : '#34d399' }}; font-size: 20px; font-weight: 700;">
                            {{ $currency ?? '₹' }}{{ number_format($amount, 2) }}
                        </td>
                    </tr>
                @endif
                @if(isset($frequency))
                    <tr>
                        <td>🔄 Frequency</td>
                        <td>{{ $frequency }}</td>
                    </tr>
                @endif
            </table>
        </div>

        @if($isOverdue)
            <div class="alert-box danger">
                <p class="alert-title">⚠️ Payment Overdue</p>
                <p class="alert-message">
                    This payment is past its due date. Please take action as soon as possible to avoid late fees or service
                    interruptions.
                </p>
            </div>
        @else
            <div class="alert-box info">
                <p class="alert-title">⏰ Reminder</p>
                <p class="alert-message">
                    @if(isset($daysUntilDue))
                        This payment is due in <strong>{{ $daysUntilDue }} day{{ $daysUntilDue > 1 ? 's' : '' }}</strong>. Make sure you
                        have sufficient funds available.
                    @else
                        Don't forget to make this payment on time to avoid any late fees.
                    @endif
                </p>
            </div>
        @endif
    @endif

    <div class="button-container">
        <a href="{{ config('app.url', 'https://trackflow.app') }}/recurring-transactions" class="button">View All Payments</a>
    </div>
@endsection
        <div class="button-container">
            <a href="{{ $actionUrl }}" class="button">{{ $actionText ?? 'Mark as Paid' }}</a>
        </div>
    @else
        <div class="button-container">
            <a href="{{ config('app.url', 'https://trackflow.app') }}/reminders" class="button">View All Reminders</a>
        </div>
    @endif

    <p class="message" style="text-align: center; color: #718096; font-size: 13px;">
        Manage your payment reminders in <a href="{{ config('app.url', 'https://trackflow.app') }}/settings/reminders"
            style="color: #0d9488;">settings</a>.
    </p>
@endsection
