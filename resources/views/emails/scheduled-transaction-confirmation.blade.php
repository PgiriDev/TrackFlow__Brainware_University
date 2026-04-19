<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Scheduled - TrackFlow</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 28px;
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

        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .transaction-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
        }

        .transaction-type {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .type-expense {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .type-income {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .transaction-amount {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0;
        }

        .amount-expense {
            color: #dc2626;
        }

        .amount-income {
            color: #16a34a;
        }

        .transaction-details {
            margin-top: 20px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-size: 14px;
        }

        .detail-value {
            color: #1f2937;
            font-weight: 500;
            font-size: 14px;
        }

        .scheduled-badge {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .scheduled-badge .date {
            font-size: 20px;
            font-weight: 700;
        }

        .scheduled-badge .label {
            font-size: 12px;
            opacity: 0.9;
            margin-top: 5px;
        }

        .info-text {
            color: #6b7280;
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
        }

        .button:hover {
            opacity: 0.9;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .footer a {
            color: #6366f1;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">📅</div>
            <h1>Transaction Scheduled!</h1>
            <p>Your transaction has been scheduled successfully</p>
        </div>

        <div class="content">
            <p class="greeting">Hi {{ $userName ?? 'there' }},</p>

            <p>Great news! Your transaction has been scheduled and will be recorded on the specified date.</p>

            <div class="transaction-card">
                <span class="transaction-type {{ $type === 'debit' ? 'type-expense' : 'type-income' }}">
                    {{ $type === 'debit' ? '💸 Expense' : '💰 Income' }}
                </span>

                <div class="transaction-amount {{ $type === 'debit' ? 'amount-expense' : 'amount-income' }}">
                    {{ $type === 'debit' ? '-' : '+' }}{{ $currency }}{{ number_format($amount, 2) }}
                </div>

                <div class="transaction-details">
                    <div class="detail-row">
                        <span class="detail-label">Description: </span>
                        <span class="detail-value">{{ $description }}</span>
                    </div>
                    @if($merchant)
                        <div class="detail-row">
                            <span class="detail-label">Merchant: </span>
                            <span class="detail-value">{{ $merchant }}</span>
                        </div>
                    @endif
                    @if($category)
                        <div class="detail-row">
                            <span class="detail-label">Category: </span>
                            <span class="detail-value">{{ $category }}</span>
                        </div>
                    @endif
                    @if($notes)
                        <div class="detail-row">
                            <span class="detail-label">Notes: </span>
                            <span class="detail-value">{{ $notes }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="scheduled-badge">
                <div class="label">SCHEDULED FOR</div>
                <div class="date">{{ \Carbon\Carbon::parse($scheduledDate)->format('F d, Y') }}</div>
            </div>

            <p class="info-text">
                You will receive a reminder email on the scheduled date at 12:00 AM.
            </p>

            <div style="text-align: center;">
                <a href="{{ url('/transactions') }}" class="button">View Transactions</a>
            </div>
        </div>

        <div class="footer">
            <p>This email was sent from <a href="{{ url('/') }}">TrackFlow</a></p>
            <p>© {{ date('Y') }} TrackFlow. All rights reserved.</p>
        </div>
    </div>
</body>

</html>