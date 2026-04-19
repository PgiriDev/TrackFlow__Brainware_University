<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackFlow - Consolidated Financial Report - {{ $user->name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('trackflow-main/fav-icon.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: #f9fafb;
            color: #1f2937;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .header {
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
            position: relative;
        }

        .header-logo {
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: auto;
        }

        .header h1 {
            color: #1e40af;
            font-size: clamp(20px, 5vw, 32px);
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .header-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
            font-size: clamp(12px, 2vw, 14px);
            color: #6b7280;
        }

        .header-info>div {
            min-width: 0;
        }

        .summary-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .summary-section h2 {
            font-size: clamp(16px, 3vw, 20px);
            margin-bottom: 15px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
        }

        .summary-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            min-width: 0;
        }

        .summary-item label {
            display: block;
            font-size: clamp(11px, 2vw, 13px);
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .summary-item value {
            display: block;
            font-size: clamp(18px, 4vw, 24px);
            font-weight: bold;
            word-wrap: break-word;
        }

        .section {
            margin-bottom: 40px;
        }

        .section h2 {
            color: #1e40af;
            font-size: clamp(16px, 3vw, 20px);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }

        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-top: 15px;
        }

        table {
            width: 100%;
            min-width: 600px;
            border-collapse: collapse;
        }

        thead {
            background: #f3f4f6;
        }

        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
            font-size: clamp(11px, 2vw, 13px);
            white-space: nowrap;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: clamp(11px, 2vw, 13px);
            word-wrap: break-word;
            max-width: 200px;
        }

        tr:hover {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: clamp(10px, 1.5vw, 11px);
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-income {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-expense {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: clamp(10px, 2vw, 12px);
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-style: italic;
            font-size: clamp(12px, 2vw, 14px);
        }

        .print-button button {
            padding: 12px 30px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: clamp(14px, 2.5vw, 16px);
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
            width: auto;
            min-width: 200px;
        }

        .print-button button:hover {
            background: #2563eb;
        }

        .print-button button:active {
            transform: scale(0.98);
        }

        /* Tablet Styles */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .container {
                padding: 20px;
            }

            .header h1 {
                text-align: center;
            }

            .header-info {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            table {
                min-width: 500px;
            }

            th,
            td {
                padding: 8px 6px;
            }
        }

        /* Mobile Styles */
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
                border-radius: 0;
            }

            .header {
                padding-bottom: 15px;
                margin-bottom: 20px;
            }

            .summary-section {
                padding: 15px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .summary-item {
                padding: 12px;
            }

            .section {
                margin-bottom: 30px;
            }

            table {
                min-width: 100%;
                font-size: 11px;
            }

            th,
            td {
                padding: 6px 4px;
                font-size: 10px;
            }

            td {
                max-width: 120px;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .badge {
                padding: 3px 6px;
                font-size: 9px;
            }

            .print-button button {
                width: 100%;
                min-width: unset;
            }

            .footer {
                font-size: 10px;
            }
        }

        /* Print Styles */
        @media print {
            body {
                padding: 0;
                background: white;
            }

            .container {
                box-shadow: none;
                padding: 20px;
                max-width: 100%;
            }

            .print-button {
                display: none !important;
            }

            table {
                page-break-inside: avoid;
            }

            tr {
                page-break-inside: avoid;
            }

            .section {
                page-break-inside: avoid;
            }

            .header,
            .summary-section {
                page-break-after: avoid;
            }
        }

        /* Small Desktop / Large Tablet */
        @media (min-width: 769px) and (max-width: 1024px) {
            .container {
                padding: 25px;
            }

            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow Logo" class="header-logo">
            <h1>📊 Consolidated Financial Report</h1>
            <div class="header-info">
                <div>
                    <strong>User:</strong> {{ $user->name ?? 'N/A' }}<br>
                    <strong>Email:</strong> {{ $user->email ?? 'N/A' }}
                </div>
                <div>
                    <strong>Generated:</strong> {{ $generatedDate }}<br>
                    <strong>Period:</strong> {{ $data['summary']['date_range'] }}<br>
                    <strong>Currency:</strong> {{ $currencySymbol }}
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <h2>Financial Summary</h2>
            <div class="summary-grid">
                <div class="summary-item">
                    <label>Total Income</label>
                    <value>{{ $data['summary']['total_income'] }}</value>
                </div>
                <div class="summary-item">
                    <label>Total Expenses</label>
                    <value>{{ $data['summary']['total_expenses'] }}</value>
                </div>
                <div class="summary-item">
                    <label>Net Balance</label>
                    <value>{{ $data['summary']['net_balance'] }}</value>
                </div>
                <div class="summary-item">
                    <label>Total Transactions</label>
                    <value>{{ $data['summary']['transaction_count'] }}</value>
                </div>
            </div>
        </div>

        <!-- Transactions Section -->
        <div class="section">
            <h2>Transactions ({{ $data['transactions']->count() }})</h2>
            @if($data['transactions']->isNotEmpty())
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Merchant</th>
                                <th class="text-center">Type</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['transactions'] as $tx)
                                <tr>
                                    <td>{{ $tx['date'] }}</td>
                                    <td>{{ $tx['description'] }}</td>
                                    <td>{{ $tx['category'] }}</td>
                                    <td>{{ $tx['merchant'] }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge {{ strtolower($tx['type']) === 'credit' ? 'badge-income' : 'badge-expense' }}">
                                            {{ $tx['type'] }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <strong>{{ $currencySymbol }}{{ number_format($tx['converted_amount'] ?? 0, 2) }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="no-data">No transactions found for this period</div>
            @endif
        </div>

        <!-- Budgets Section -->
        @if($data['budgets']->isNotEmpty())
            <div class="section">
                <h2>Budget Summary ({{ $data['budgets']->count() }})</h2>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Budget Name</th>
                                <th class="text-right">Budget Amount</th>
                                <th class="text-right">Spent</th>
                                <th class="text-right">Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['budgets'] as $budget)
                                <tr>
                                    <td>{{ $budget['name'] }}</td>
                                    <td class="text-right">{{ $budget['amount'] }}</td>
                                    <td class="text-right">{{ $budget['spent'] }}</td>
                                    <td class="text-right"><strong>{{ $budget['remaining'] }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Goals Section -->
        @if($data['goals']->isNotEmpty())
            <div class="section">
                <h2>Goals Progress ({{ $data['goals']->count() }})</h2>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Goal Name</th>
                                <th class="text-right">Target Amount</th>
                                <th class="text-right">Current Amount</th>
                                <th class="text-center">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['goals'] as $goal)
                                <tr>
                                    <td>{{ $goal['name'] }}</td>
                                    <td class="text-right">{{ $goal['target'] }}</td>
                                    <td class="text-right">{{ $goal['current'] }}</td>
                                    <td class="text-center"><strong>{{ $goal['progress'] }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>This is a system-generated report from TrackFlow Financial Management System</p>
            <p style="margin-top: 5px;">© {{ date('Y') }} TrackFlow. All rights reserved.</p>
        </div>

        <!-- Print Button (hidden when printing) -->
        <div class="print-button" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()">
                🖨️ Print / Save as PDF
            </button>
        </div>
    </div>
</body>

</html>