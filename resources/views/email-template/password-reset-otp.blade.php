@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello 👋</p>

    <p class="message">
        We received a request to <strong>reset your password</strong> for your TrackFlow account. Use the OTP below to
        verify your identity and create a new password.
    </p>

    <div class="otp-container">
        <div class="otp-box">
            <p class="otp-label">🔓 Password Reset OTP</p>
            <p class="otp-code">{{ $otp }}</p>
            <p class="otp-expiry">Expires in <strong>15 minutes</strong></p>
        </div>
    </div>

    <div class="alert-box">
        <p class="alert-title">🔐 Didn't Request This?</p>
        <p class="alert-message">
            If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged
            and your account is secure.
        </p>
    </div>

    <div class="divider"></div>

    <p class="message" style="margin-bottom: 16px;">
        <strong style="color: #ffffff;">💡 Tips for a Strong Password:</strong>
    </p>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">📏</span>
        <div class="feature-content">
            <p class="feature-title">At Least 12 Characters</p>
            <p class="feature-desc">Longer passwords are significantly harder to crack</p>
        </div>
    </div>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #10b981, #059669);">🔤</span>
        <div class="feature-content">
            <p class="feature-title">Mix Character Types</p>
            <p class="feature-desc">Use uppercase, lowercase, numbers & symbols</p>
        </div>
    </div>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">🚫</span>
        <div class="feature-content">
            <p class="feature-title">Avoid Personal Info</p>
            <p class="feature-desc">Don't use names, birthdays, or common words</p>
        </div>
    </div>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">🔑</span>
        <div class="feature-content">
            <p class="feature-title">Unique Password</p>
            <p class="feature-desc">Don't reuse passwords from other accounts</p>
        </div>
    </div>

    <div class="alert-box info">
        <p class="alert-title">⏰ One-Time Use</p>
        <p class="alert-message">
            This OTP can only be used once. If it expires or you need a new one, simply request another password reset from
            the login page.
        </p>
    </div>
@endsection
