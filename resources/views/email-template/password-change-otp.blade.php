@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    <p class="message">
        You have requested to <strong>change your account password</strong>. Use the verification code below to confirm this
        change.
    </p>

    <div class="otp-container">
        <div class="otp-box">
            <p class="otp-label">🔐 Password Change OTP</p>
            <p class="otp-code">{{ $otp }}</p>
            <p class="otp-expiry">Expires in <strong>10 minutes</strong></p>
        </div>
    </div>

    <div class="alert-box">
        <p class="alert-title">⚠️ Security Notice</p>
        <p class="alert-message">
            If you didn't request this password change, please ignore this email and ensure your account is secure. Consider
            reviewing your recent activity.
        </p>
    </div>

    <div class="divider"></div>

    <p class="message" style="margin-bottom: 16px;">
        <strong style="color: #ffffff;">🛡️ Security Best Practices:</strong>
    </p>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">🔑</span>
        <div class="feature-content">
            <p class="feature-title">Use a Strong Password</p>
            <p class="feature-desc">Mix uppercase, lowercase, numbers & special characters</p>
        </div>
    </div>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #0891b2, #06b6d4);">🔒</span>
        <div class="feature-content">
            <p class="feature-title">Enable Two-Factor Authentication</p>
            <p class="feature-desc">Add an extra layer of security to your account</p>
        </div>
    </div>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">🚫</span>
        <div class="feature-content">
            <p class="feature-title">Never Share Your OTP</p>
            <p class="feature-desc">TrackFlow will never ask for your OTP via phone or chat</p>
        </div>
    </div>

    <div class="alert-box success">
        <p class="alert-title">💡 Pro Tip</p>
        <p class="alert-message">
            After changing your password, you'll need to log in again on all your devices. Consider using a password manager
            to securely store your credentials.
        </p>
    </div>
@endsection