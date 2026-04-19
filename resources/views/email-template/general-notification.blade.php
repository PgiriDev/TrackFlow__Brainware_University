@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    <p class="message">
        {{ $message ?? 'You have a new notification from TrackFlow.' }}
    </p>

    @if(isset($highlightText))
        <div class="progress-container" style="text-align: center;">
            <p style="color: #718096; font-size: 13px; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;">
                {{ $highlightLabel ?? 'Important' }}
            </p>
            <p style="font-size: 28px; font-weight: 800; color: #5eead4; margin: 0;">
                {{ $highlightText }}
            </p>
        </div>
    @endif

    @if(isset($details) && is_array($details) && count($details) > 0)
        <div class="divider"></div>

        <p class="message" style="margin-bottom: 16px;">
            <strong style="color: #ffffff;">📋 Details:</strong>
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

    @if(isset($features) && is_array($features))
        <div class="divider"></div>

        @foreach($features as $feature)
            <div class="feature-card">
                <span class="feature-icon"
                    style="background: linear-gradient(135deg, {{ $feature['gradient'] ?? '#0d9488, #14b8a6' }});">{{ $feature['icon'] ?? '✨' }}</span>
                <div class="feature-content">
                    <p class="feature-title">{{ $feature['title'] ?? '' }}</p>
                    <p class="feature-desc">{{ $feature['description'] ?? '' }}</p>
                </div>
            </div>
        @endforeach
    @endif

    @if(isset($alertMessage))
        <div class="alert-box {{ $alertType ?? 'info' }}">
            <p class="alert-title">{{ $alertIcon ?? '💡' }} {{ $alertTitle ?? 'Notice' }}</p>
            <p class="alert-message">{{ $alertMessage }}</p>
        </div>
    @endif

    @if(isset($actionUrl) && isset($actionText))
        <div class="button-container">
            <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
        </div>
    @endif

    <p class="message" style="color: #718096;">
        Thank you for using TrackFlow to manage your finances. If you have any questions, our support team is always here to
        help.
    </p>
@endsection
