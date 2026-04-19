@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    @php
        $alertClass = ($alertType ?? 'info') === 'danger' ? 'danger' : (($alertType ?? 'info') === 'success' ? 'success' : 'info');
        $alertIcon = ($alertType ?? 'info') === 'danger' ? '🚨' : (($alertType ?? 'info') === 'success' ? '✅' : '⚠️');
    @endphp

    <div class="alert-box {{ $alertClass }}">
        <p class="alert-title">{{ $alertIcon }} {{ $alertTitle ?? 'Security Alert' }}</p>
        <p class="alert-message">
            {{ $alertMessage ?? 'We detected important activity on your TrackFlow account.' }}
        </p>
    </div>

    @if(isset($description))
        <p class="message">
            {{ $description }}
        </p>
    @endif

    @if(isset($details) && is_array($details) && count($details) > 0)
        <div class="divider"></div>

        <p class="message" style="margin-bottom: 16px;">
            <strong style="color: #ffffff;">📋 Event Details:</strong>
        </p>

        <div class="progress-container">
            <table class="data-table">
                @foreach($details as $key => $value)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    @if(isset($actionRequired) && $actionRequired)
        <div class="alert-box danger">
            <p class="alert-title">⚡ Immediate Action Required</p>
            <p class="alert-message">
                {{ $actionMessage ?? 'Please review this activity and take necessary action to secure your account.' }}
            </p>
        </div>
    @endif

    @if(isset($recommendations) && is_array($recommendations))
        <div class="divider"></div>

        <p class="message" style="margin-bottom: 16px;">
            <strong style="color: #ffffff;">🛡️ Recommended Actions:</strong>
        </p>

        @foreach($recommendations as $index => $recommendation)
            <div class="feature-card">
                <span class="feature-icon" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">{{ $index + 1 }}</span>
                <div class="feature-content">
                    <p class="feature-title">{{ $recommendation['title'] ?? 'Action Item' }}</p>
                    <p class="feature-desc">{{ $recommendation['description'] ?? '' }}</p>
                </div>
            </div>
        @endforeach
    @endif

    @if(isset($actionUrl) && isset($actionText))
        <div class="button-container">
            <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
        </div>
    @else
        <div class="button-container">
            <a href="{{ config('app.url', 'https://trackflow.app') }}/settings/security" class="button">Review Security
                Settings</a>
        </div>
    @endif

    <div class="alert-box info">
        <p class="alert-title">💬 Need Help?</p>
        <p class="alert-message">
            If you have any concerns about your account security, please contact our support team immediately. We're here to
            help 24/7.
        </p>
    </div>
@endsection
