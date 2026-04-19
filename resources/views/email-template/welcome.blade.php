@extends('email-template.layout')

@section('content')
    <p class="greeting">Welcome to TrackFlow, {{ $userName ?? 'User' }}! 🎉</p>

    <p class="message">
        You've taken the first step towards <strong>financial freedom</strong>. TrackFlow is here to help you track, manage,
        and grow your money smarter.
    </p>

    <div class="alert-box success">
        <p class="alert-title">✅ Account Created Successfully!</p>
        <p class="alert-message">
            Your TrackFlow account is all set up and ready to go. Start exploring features and take control of your finances
            today!
        </p>
    </div>

    <div class="divider"></div>

    <p class="message" style="margin-bottom: 24px;">
        <strong style="color: #ffffff; font-size: 18px;">🚀 Get Started with TrackFlow:</strong>
    </p>

    <!-- <div class="feature-card">
            <span class="feature-icon" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">🏦</span>
            <div class="feature-content">
                <p class="feature-title">Link Your Bank Accounts</p>
                <p class="feature-desc">Connect your accounts for automatic transaction syncing and real-time balance updates
                </p>
            </div>
        </div> -->

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #10b981, #059669);">📊</span>
        <div class="feature-content">
            <p class="feature-title">Create Smart Budgets</p>
            <p class="feature-desc">Set spending limits by category and get alerts before you overspend</p>
        </div>
    </div>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">🎯</span>
        <div class="feature-content">
            <p class="feature-title">Set Savings Goals</p>
            <p class="feature-desc">Plan for vacations, emergencies, or big purchases with visual progress tracking</p>
        </div>
    </div>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">📈</span>
        <div class="feature-content">
            <p class="feature-title">Analyze Your Spending</p>
            <p class="feature-desc">Beautiful charts and insights show you exactly where your money goes</p>
        </div>
    </div>

    <div class="feature-card">
        <span class="feature-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">🔔</span>
        <div class="feature-content">
            <p class="feature-title">Smart Notifications</p>
            <p class="feature-desc">Get alerts for large transactions, budget limits, and bill reminders</p>
        </div>
    </div>

    <div class="button-container">
        <a href="{{ config('app.url', 'https://trackflow.app') }}/dashboard" class="button">Go to Dashboard →</a>
    </div>

    <div class="divider"></div>

    <div class="alert-box purple">
        <p class="alert-title">🛡️ Secure Your Account</p>
        <p class="alert-message">
            We recommend enabling Two-Factor Authentication (2FA) for extra security. Your financial data is precious –
            let's keep it safe together!
        </p>
    </div>

    <p class="message" style="text-align: center; color: #718096;">
        Questions? Our support team is always here to help.<br>
        Reply to this email or visit our <a href="{{ config('app.url', 'https://trackflow.app') }}/help"
            style="color: #0d9488; text-decoration: none;">Help Center</a>.
    </p>
@endsection