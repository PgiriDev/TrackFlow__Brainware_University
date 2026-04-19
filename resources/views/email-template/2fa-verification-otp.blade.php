@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    <p class="message">
        You're setting up <strong>Two-Factor Authentication (2FA)</strong> via email for your TrackFlow account. This adds a
        powerful extra layer of security to protect your financial data.
    </p>

    <div class="otp-container">
        <div class="otp-box">
            <p class="otp-label">🛡️ 2FA Verification Code</p>
            <p class="otp-code">{{ $otp }}</p>
            <p class="otp-expiry">Expires in <strong>10 minutes</strong></p>
        </div>
    </div>

    <div class="alert-box success">
        <p class="alert-title">✨ Benefits of 2FA</p>
        <p class="alert-message">
            With 2FA enabled, even if someone discovers your password, they won't be able to access your account without the
            verification code sent to your email.
        </p>
    </div>

    <div class="divider"></div>

    <p class="message" style="margin-bottom: 16px;">
        <strong style="color: #ffffff;">🔐 How Email 2FA Works:</strong>
    </p>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">1️⃣</span>
        <div class="feature-content">
            <p class="feature-title">Enter Your Password</p>
            <p class="feature-desc">Log in with your email and password as usual</p>
        </div>
    </div>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #10b981, #059669);">2️⃣</span>
        <div class="feature-content">
            <p class="feature-title">Receive Verification Code</p>
            <p class="feature-desc">We'll send a unique code to this email address</p>
        </div>
    </div>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">3️⃣</span>
        <div class="feature-content">
            <p class="feature-title">Enter the Code</p>
            <p class="feature-desc">Type the code on the verification screen to access your account</p>
        </div>
    </div>

    <div class="alert-box purple">
        <p class="alert-title">🔑 Recovery Codes</p>
        <p class="alert-message">
            After enabling 2FA, you'll receive recovery codes. <strong>Save these in a secure place!</strong> They're your
            backup way to access your account if you lose access to your email.
        </p>
    </div>

    <div class="alert-box info">
        <p class="alert-title">📧 Didn't Request This?</p>
        <p class="alert-message">
            If you didn't attempt to enable 2FA, please ignore this email and review your account security. Consider
            changing your password as a precaution.
        </p>
    </div>

    <div class="button-container">
        <a href="{{ config('app.url', 'https://trackflow.app') }}/settings/security" class="button">Security Settings</a>
    </div>
@endsection
