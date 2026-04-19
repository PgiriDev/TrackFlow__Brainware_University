@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    <p class="message">
        You have requested to <strong>permanently delete</strong> your TrackFlow account. This is a critical action that
        <strong>cannot be undone</strong>.
    </p>

    <div class="alert-box danger">
        <p class="alert-title">🚨 Warning: Permanent Deletion</p>
        <p class="alert-message">
            Once deleted, ALL your data will be permanently removed from our servers and cannot be recovered. This includes:
        </p>
    </div>

    <div class="progress-container" style="border-color: rgba(239, 68, 68, 0.3);">
        <table class="data-table">
            <tr>
                <td>👤 Profile & Personal Information</td>
                <td style="color: #f87171;">Will be deleted</td>
            </tr>
            <tr>
                <td>💳 Transaction History & Records</td>
                <td style="color: #f87171;">Will be deleted</td>
            </tr>
            <tr>
                <td>📊 Budgets, Goals & Reports</td>
                <td style="color: #f87171;">Will be deleted</td>
            </tr>
            <tr>
                <td>⚙️ Settings & Preferences</td>
                <td style="color: #f87171;">Will be deleted</td>
            </tr>
        </table>
    </div>

    <p class="message">
        To confirm account deletion, enter this verification code:
    </p>

    <div class="otp-container">
        <div class="otp-box"
            style="border-color: rgba(239, 68, 68, 0.3); background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.05) 100%);">
            <p class="otp-label" style="color: #ef4444;">⚠️ Account Deletion OTP</p>
            <p class="otp-code" style="color: #f87171;">{{ $otp }}</p>
            <p class="otp-expiry">Expires in <strong style="color: #ef4444;">10 minutes</strong></p>
        </div>
    </div>

    <div class="alert-box">
        <p class="alert-title">💾 Export Your Data First</p>
        <p class="alert-message">
            Before deleting your account, we recommend exporting your financial data and transaction history. You can do
            this from Settings → Data Export.
        </p>
    </div>

    <div class="alert-box info">
        <p class="alert-title">🤔 Changed Your Mind?</p>
        <p class="alert-message">
            Simply ignore this email if you no longer wish to delete your account. Your data will remain safe and your
            account will continue working normally.
        </p>
    </div>

    <div class="divider"></div>

    <p class="message" style="color: #718096; font-size: 14px; text-align: center;">
        If you didn't request account deletion, please secure your account immediately by changing your password and
        enabling 2FA.
    </p>
@endsection