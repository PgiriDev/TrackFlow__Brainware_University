<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $subject ?? 'TrackFlow' }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset & Base */
        * {
            box-sizing: border-box;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            background-color: #0f0f23;
            -webkit-font-smoothing: antialiased;
        }

        /* Wrapper */
        .email-wrapper {
            max-width: 640px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Main Container */
        .email-container {
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 50%, #5eead4 100%);
            padding: 48px 40px;
            text-align: center;
            position: relative;
        }

        .logo-img {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .logo {
            display: block;
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: -1px;
            margin-top: 12px;
        }

        .logo-icon {
            display: none;
        }

        .email-title {
            color: #ffffff;
            font-size: 18px;
            font-weight: 500;
            margin-top: 20px;
            margin-bottom: 0;
            opacity: 0.95;
        }

        /* Body */
        .email-body {
            padding: 48px 40px;
        }

        .greeting {
            font-size: 22px;
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .message {
            color: #a0aec0;
            font-size: 16px;
            margin-bottom: 24px;
            line-height: 1.7;
        }

        .message strong {
            color: #ffffff;
        }

        /* OTP Box - Glass Morphism */
        .otp-container {
            text-align: center;
            margin: 40px 0;
        }

        .otp-box {
            display: inline-block;
            background: linear-gradient(135deg, rgba(13, 148, 136, 0.15) 0%, rgba(94, 234, 212, 0.15) 100%);
            border: 1px solid rgba(20, 184, 166, 0.3);
            border-radius: 20px;
            padding: 32px 56px;
        }

        .otp-label {
            color: #14b8a6;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 16px;
        }

        .otp-code {
            font-size: 42px;
            font-weight: 800;
            color: #5eead4;
            letter-spacing: 12px;
            font-family: 'SF Mono', 'Courier New', monospace;
            margin: 0;
        }

        .otp-expiry {
            color: #718096;
            font-size: 13px;
            margin-top: 16px;
        }

        .otp-expiry strong {
            color: #14b8a6;
            font-weight: 600;
        }

        /* Alert Boxes */
        .alert-box {
            background: rgba(251, 191, 36, 0.1);
            border-left: 4px solid #fbbf24;
            padding: 20px 24px;
            border-radius: 0 16px 16px 0;
            margin: 28px 0;
        }

        .alert-box.danger {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: #ef4444;
        }

        .alert-box.info {
            background: rgba(59, 130, 246, 0.1);
            border-left-color: #3b82f6;
        }

        .alert-box.success {
            background: rgba(16, 185, 129, 0.1);
            border-left-color: #10b981;
        }

        .alert-box.purple {
            background: rgba(139, 92, 246, 0.1);
            border-left-color: #8b5cf6;
        }

        .alert-title {
            font-weight: 700;
            color: #fbbf24;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .alert-box.danger .alert-title {
            color: #ef4444;
        }

        .alert-box.info .alert-title {
            color: #60a5fa;
        }

        .alert-box.success .alert-title {
            color: #34d399;
        }

        .alert-box.purple .alert-title {
            color: #a78bfa;
        }

        .alert-message {
            color: #d4a017;
            font-size: 14px;
            line-height: 1.6;
        }

        .alert-box.danger .alert-message {
            color: #fca5a5;
        }

        .alert-box.info .alert-message {
            color: #93c5fd;
        }

        .alert-box.success .alert-message {
            color: #6ee7b7;
        }

        .alert-box.purple .alert-message {
            color: #c4b5fd;
        }

        /* Button */
        .button-container {
            text-align: center;
            margin: 40px 0;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 30px -5px rgba(20, 184, 166, 0.4);
        }

        /* Feature Cards */
        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 16px;
        }

        .feature-icon {
            display: inline-block;
            width: 56px;
            height: 56px;
            border-radius: 14px;
            text-align: center;
            line-height: 56px;
            font-size: 24px;
            vertical-align: top;
            margin-right: 16px;
        }

        .feature-content {
            display: inline-block;
            vertical-align: top;
            max-width: calc(100% - 80px);
        }

        .feature-title {
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .feature-desc {
            color: #718096;
            font-size: 13px;
            margin: 0;
        }

        /* Stats Cards */
        .stats-row {
            margin: 32px 0;
        }

        .stat-card {
            display: inline-block;
            width: 48%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            vertical-align: top;
        }

        .stat-card.income {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.08) 100%);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .stat-card.expense {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.08) 100%);
            border-color: rgba(239, 68, 68, 0.3);
        }

        .stat-label {
            color: #718096;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
        }

        .stat-card.income .stat-value {
            color: #34d399;
        }

        .stat-card.expense .stat-value {
            color: #f87171;
        }

        /* Progress Container */
        .progress-container {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 28px;
            margin: 32px 0;
        }

        .progress-title {
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .progress-bar {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
            margin: 16px 0;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #0d9488 0%, #14b8a6 50%, #5eead4 100%);
        }

        .progress-fill.success {
            background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        }

        .progress-fill.warning {
            background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
        }

        .progress-fill.danger {
            background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .data-table tr:last-child {
            border-bottom: none;
        }

        .data-table td {
            padding: 14px 0;
            font-size: 14px;
        }

        .data-table td:first-child {
            color: #718096;
        }

        .data-table td:last-child {
            color: #ffffff;
            font-weight: 600;
            text-align: right;
        }

        /* Transaction Card */
        .transaction-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 32px;
            margin: 32px 0;
            text-align: center;
        }

        .transaction-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            line-height: 72px;
            text-align: center;
            font-size: 32px;
            margin: 0 auto 20px;
        }

        .transaction-icon.income {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.1) 100%);
            border: 2px solid rgba(16, 185, 129, 0.4);
        }

        .transaction-icon.expense {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.1) 100%);
            border: 2px solid rgba(239, 68, 68, 0.4);
        }

        .transaction-type {
            color: #718096;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .transaction-amount {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 24px;
        }

        .transaction-amount.income {
            color: #34d399;
        }

        .transaction-amount.expense {
            color: #f87171;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            margin: 32px 0;
        }

        /* Footer */
        .email-footer {
            background: rgba(0, 0, 0, 0.2);
            padding: 32px 40px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .footer-logo {
            font-size: 20px;
            font-weight: 700;
            color: #14b8a6;
            margin-bottom: 16px;
        }

        .footer-text {
            color: #4a5568;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .footer-links {
            margin-bottom: 24px;
        }

        .footer-link {
            color: #14b8a6;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            margin: 0 16px;
        }

        .copyright {
            color: #2d3748;
            font-size: 12px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 12px;
            }

            .email-header,
            .email-body,
            .email-footer {
                padding: 32px 24px;
            }

            .logo {
                font-size: 26px;
            }

            .greeting {
                font-size: 20px;
            }

            .otp-code {
                font-size: 32px;
                letter-spacing: 8px;
            }

            .otp-box {
                padding: 28px 40px;
            }

            .stat-card {
                display: block;
                width: 100%;
                margin-bottom: 12px;
            }

            .feature-content {
                display: block;
                max-width: 100%;
                margin-top: 12px;
            }

            .footer-link {
                display: block;
                margin: 8px 0;
            }
        }
    </style>
</head>

<body style="background-color: #0f0f23;">
    @php
        $logoUrl = config('mail.logo_url') ?: env('MAIL_LOGO_URL');
        $useExternalLogo = !empty($logoUrl);
    @endphp
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <a href="{{ config('app.url', 'https://trackflow.app') }}" style="text-decoration: none;">
                    @if($useExternalLogo)
                        <img src="{{ $logoUrl }}" alt="TrackFlow" class="logo-img" style="width: 64px; height: 64px; border-radius: 16px;">
                    @else
                        {{-- Inline SVG Logo - Works in all email clients --}}
                        <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto 16px;">
                            <tr>
                                <td style="width: 64px; height: 64px; background: linear-gradient(135deg, #0d9488, #14b8a6); border-radius: 16px; text-align: center; vertical-align: middle; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);">
                                    <!--[if mso]>
                                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="height:64px;v-text-anchor:middle;width:64px;" arcsize="25%" fillcolor="#14b8a6">
                                    <w:anchorlock/>
                                    <center style="color:#ffffff;font-family:sans-serif;font-size:28px;font-weight:bold;">TF</center>
                                    </v:roundrect>
                                    <![endif]-->
                                    <!--[if !mso]><!-->
                                    <span style="color: #ffffff; font-size: 28px; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 64px;">TF</span>
                                    <!--<![endif]-->
                                </td>
                            </tr>
                        </table>
                    @endif
                </a>
                <a href="{{ config('app.url', 'https://trackflow.app') }}" class="logo">
                    TrackFlow
                </a>
                @if(isset($title))
                    <p class="email-title">{{ $title }}</p>
                @endif
            </div>

            <!-- Body -->
            <div class="email-body">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="email-footer">
                @if($useExternalLogo)
                    <img src="{{ $logoUrl }}" alt="TrackFlow" style="width: 40px; height: 40px; border-radius: 10px; margin-bottom: 12px;">
                @else
                    {{-- Inline Text Logo for Footer --}}
                    <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto 12px;">
                        <tr>
                            <td style="width: 40px; height: 40px; background: linear-gradient(135deg, #0d9488, #14b8a6); border-radius: 10px; text-align: center; vertical-align: middle;">
                                <span style="color: #ffffff; font-size: 18px; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 40px;">TF</span>
                            </td>
                        </tr>
                    </table>
                @endif
                <p class="footer-logo">TrackFlow</p>
                <p class="footer-text">
                    Your personal finance management companion.<br>
                    Take control of your money, one transaction at a time.
                </p>
                <div class="footer-links">
                    <a href="{{ config('app.url', 'https://trackflow.app') }}" class="footer-link">Website</a>
                    <a href="{{ config('app.url', 'https://trackflow.app') }}/settings" class="footer-link">Settings</a>
                    <a href="{{ config('app.url', 'https://trackflow.app') }}/contact" class="footer-link">Support</a>
                </div>
                <p class="copyright">
                    &copy; {{ date('Y') }} TrackFlow. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
