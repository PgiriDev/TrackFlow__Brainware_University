<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support Message</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            display: block;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #6366f1;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }

        .info-box label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-box p {
            margin: 0;
            font-size: 16px;
            color: #1f2937;
            font-weight: 500;
        }

        .message-box {
            background-color: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .message-box h3 {
            margin: 0 0 15px;
            font-size: 16px;
            color: #374151;
        }

        .message-box p {
            margin: 0;
            white-space: pre-wrap;
            color: #4b5563;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-subject {
            background-color: #ede9fe;
            color: #7c3aed;
        }

        .attachment-notice {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .attachment-notice i {
            color: #f59e0b;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if(config('mail.logo_url'))
                <img src="{{ config('mail.logo_url') }}" alt="TrackFlow Logo" class="logo" />
            @endif
            <h1>📬 New Support Message</h1>
            <p>A user has submitted a contact form on TrackFlow</p>
        </div>

        <div class="content">
            <div class="info-box">
                <label>From</label>
                <p>{{ $contactData['name'] }}</p>
            </div>

            <div class="info-box">
                <label>Email</label>
                <p><a href="mailto:{{ $contactData['email'] }}"
                        style="color: #6366f1; text-decoration: none;">{{ $contactData['email'] }}</a></p>
            </div>

            <div class="info-box">
                <label>Subject</label>
                <p>
                    <span class="badge badge-subject">{{ ucfirst($contactData['subject']) }}</span>
                </p>
            </div>

            <div class="message-box">
                <h3>📝 Message</h3>
                <p>{{ $contactData['message'] }}</p>
            </div>

            @if(!empty($contactData['attachments_count']) && $contactData['attachments_count'] > 0)
                <div class="attachment-notice">
                    <strong>📎 Attachments:</strong> This message includes {{ $contactData['attachments_count'] }}
                    attachment(s). They are attached to this email.
                </div>
            @endif
        </div>

        <div class="footer">
            <p>This message was sent from TrackFlow Help Center</p>
            <p>© {{ date('Y') }} TrackFlow. All rights reserved.</p>
        </div>
    </div>
</body>

</html>