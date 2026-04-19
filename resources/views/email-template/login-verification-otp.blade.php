@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    <p class="message">
        We detected a <strong>new login attempt</strong> to your TrackFlow account. To verify your identity, please enter
        the verification code below.
    </p>

    <div class="otp-container">
        <div class="otp-box">
            <p class="otp-label">🔐 Login Verification Code</p>
            <p class="otp-code">{{ $otp }}</p>
            <p class="otp-expiry">Expires in <strong>10 minutes</strong></p>
        </div>
    </div>

    <div class="alert-box danger">
        <p class="alert-title">🚨 Not You?</p>
        <p class="alert-message">
            If you didn't attempt to login, someone may be trying to access your account. We recommend changing your
            password immediately and reviewing your security settings.
        </p>
    </div>

    @if(isset($ipAddress) || isset($browser) || isset($location))
        <div class="divider"></div>

        <p class="message" style="margin-bottom: 16px;">
            <strong style="color: #ffffff;">📍 Login Attempt Details:</strong>
        </p>

        <div class="progress-container">
            <table class="data-table">
                @if(isset($ipAddress))
                    <tr>
                        <td>🌐 IP Address</td>
                        <td>{{ $ipAddress }}</td>
                    </tr>
                @endif
                @if(isset($browser))
                    <tr>
                        <td>💻 Browser</td>
                        <td>{{ $browser }}</td>
                    </tr>
                @endif
                @if(isset($location))
                    <tr>
                        <td>📍 Location</td>
                        <td>{{ $location }}</td>
                    </tr>
                @endif
                <tr>
                    <td>🕐 Time</td>
                    <td>{{ now()->format('M d, Y h:i A') }} (UTC)</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="alert-box info">
        <p class="alert-title">🛡️ Keep Your Account Safe</p>
        <p class="alert-message">
            Never share this code with anyone. TrackFlow staff will never ask for your verification code via phone, email,
            or chat.
        </p>
    </div>

    <div class="button-container">
        <a href="{{ config('app.url', 'https://trackflow.app') }}/settings/security" class="button">Review Security
            Settings</a>
    </div>
@endsection
