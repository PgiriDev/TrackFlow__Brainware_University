<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackFlow - {{ $reportTitle }} - {{ $user->name }}</title>
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
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 14px;
            color: #6b7280;
        }

        .summary-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .summary-section h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }

        .summary-item {
            background: rgba(255, 255, 255, 0.15);
            padding: 18px;
            border-radius: 10px;
        }

        .summary-item label {
            display: block;
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .summary-item value {
            display: block;
            font-size: 24px;
            font-weight: bold;
        }

        .section {
            margin-bottom: 35px;
        }

        .section h2 {
            color: #1e40af;
            font-size: 20px;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }

        .section h3 {
            color: #374151;
            font-size: 16px;
            margin: 20px 0 12px 0;
        }

        .table-wrapper {
            overflow-x: auto;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f3f4f6;
        }

        th {
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
            font-size: 13px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
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
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-income, .badge-good {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-expense, .badge-over {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background: #e5e7eb;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 5px;
        }

        .progress-good { background: #10b981; }
        .progress-warning { background: #f59e0b; }
        .progress-over { background: #ef4444; }

        .stat-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .stat-card-title {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .stat-card-value {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
        }

        .stat-card-change {
            font-size: 12px;
            margin-top: 5px;
        }

        .positive { color: #10b981; }
        .negative { color: #ef4444; }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .category-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .category-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .category-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .category-details h4 {
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .category-details span {
            font-size: 12px;
            color: #6b7280;
        }

        .category-amount {
            font-weight: 600;
            font-size: 16px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
            font-style: italic;
        }

        .print-button {
            text-align: center;
            margin-top: 30px;
        }

        .print-button button {
            padding: 14px 40px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
        }

        .print-button button:hover {
            background: #2563eb;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .container {
                box-shadow: none;
                padding: 15px;
            }

            .print-button {
                display: none !important;
            }

            .section {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .grid-2, .grid-3, .grid-4 {
                grid-template-columns: 1fr;
            }

            .header-info {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow Logo" class="header-logo">
            <h1>📊 {{ $reportTitle }}</h1>
            <div class="header-info">
                <div>
                    <strong>User:</strong> {{ $user->name ?? 'N/A' }}<br>
                    <strong>Email:</strong> {{ $user->email ?? 'N/A' }}
                </div>
                <div>
                    <strong>Generated:</strong> {{ $generatedDate }}<br>
                    <strong>Period:</strong> {{ $dateRange }}<br>
                    <strong>Currency:</strong> {{ $currencySymbol }}
                </div>
            </div>
        </div>

        {{-- ==================== INCOME VS EXPENSE REPORT ==================== --}}
        @if($reportType === 'income-expense')
            <div class="summary-section">
                <h2>Financial Summary</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <label>Total Income</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_income'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Total Expenses</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_expenses'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Net Balance</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['net_balance'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Savings Rate</label>
                        <value>{{ $data['summary']['savings_rate'] ?? 0 }}%</value>
                    </div>
                </div>
            </div>

            @if(!empty($data['daily_breakdown']))
                <div class="section">
                    <h2>Daily Breakdown</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-right">Income</th>
                                    <th class="text-right">Expenses</th>
                                    <th class="text-right">Net</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['daily_breakdown'] as $day)
                                    <tr>
                                        <td>{{ $day['date'] }}</td>
                                        <td class="text-right positive">{{ $currencySymbol }}{{ number_format($day['income'] ?? 0, 2) }}</td>
                                        <td class="text-right negative">{{ $currencySymbol }}{{ number_format($day['expenses'] ?? 0, 2) }}</td>
                                        <td class="text-right {{ ($day['net'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                            {{ $currencySymbol }}{{ number_format($day['net'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(!empty($data['top_income_sources']))
                <div class="section">
                    <h2>Top Income Sources</h2>
                    @foreach($data['top_income_sources'] as $source)
                        <div class="category-item">
                            <div class="category-info">
                                <div class="category-icon" style="background: #d1fae5; color: #065f46;">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <div class="category-details">
                                    <h4>{{ $source['category'] ?? 'Unknown' }}</h4>
                                    <span>{{ $source['count'] ?? 0 }} transactions</span>
                                </div>
                            </div>
                            <div class="category-amount positive">{{ $currencySymbol }}{{ number_format($source['amount'] ?? 0, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(!empty($data['top_expense_categories']))
                <div class="section">
                    <h2>Top Expenses</h2>
                    @foreach($data['top_expense_categories'] as $expense)
                        <div class="category-item">
                            <div class="category-info">
                                <div class="category-icon" style="background: #fee2e2; color: #991b1b;">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <div class="category-details">
                                    <h4>{{ $expense['category'] ?? 'Unknown' }}</h4>
                                    <span>{{ $expense['count'] ?? 0 }} transactions</span>
                                </div>
                            </div>
                            <div class="category-amount negative">{{ $currencySymbol }}{{ number_format($expense['amount'] ?? 0, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        {{-- ==================== CATEGORY ANALYSIS REPORT ==================== --}}
        @if($reportType === 'category')
            <div class="summary-section">
                <h2>Category Overview</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <label>Total Expenses</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_expenses'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Total Income</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_income'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Expense Categories</label>
                        <value>{{ $data['summary']['expense_categories_count'] ?? 0 }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Income Categories</label>
                        <value>{{ $data['summary']['income_categories_count'] ?? 0 }}</value>
                    </div>
                </div>
            </div>

            @if(!empty($data['income_categories']) && count($data['income_categories']) > 0)
                <div class="section">
                    <h2>Income by Category</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Transactions</th>
                                    <th class="text-right">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['income_categories'] as $cat)
                                    <tr>
                                        <td>{{ $cat['name'] ?? 'Unknown' }}</td>
                                        <td class="text-right positive">{{ $currencySymbol }}{{ number_format($cat['amount'] ?? 0, 2) }}</td>
                                        <td class="text-right">{{ $cat['count'] ?? 0 }}</td>
                                        <td class="text-right">{{ number_format($cat['percentage'] ?? 0, 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(!empty($data['expense_categories']) && count($data['expense_categories']) > 0)
                <div class="section">
                    <h2>Expenses by Category</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Transactions</th>
                                    <th class="text-right">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['expense_categories'] as $cat)
                                    <tr>
                                        <td>{{ $cat['name'] ?? 'Unknown' }}</td>
                                        <td class="text-right negative">{{ $currencySymbol }}{{ number_format($cat['amount'] ?? 0, 2) }}</td>
                                        <td class="text-right">{{ $cat['count'] ?? 0 }}</td>
                                        <td class="text-right">{{ number_format($cat['percentage'] ?? 0, 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif

        {{-- ==================== MONTHLY SUMMARY REPORT ==================== --}}
        @if($reportType === 'monthly')
            <div class="summary-section">
                <h2>Monthly Overview</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <label>Average Monthly Income</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['averages']['avg_income'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Average Monthly Expenses</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['averages']['avg_expenses'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Average Monthly Savings</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['averages']['avg_savings'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Total Months</label>
                        <value>{{ $data['highlights']['total_months'] ?? 0 }}</value>
                    </div>
                </div>
            </div>

            @if(!empty($data['monthly_breakdown']))
                <div class="section">
                    <h2>Monthly Breakdown</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-right">Income</th>
                                    <th class="text-right">Expenses</th>
                                    <th class="text-right">Savings</th>
                                    <th class="text-right">Savings Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['monthly_breakdown'] as $month)
                                    <tr>
                                        <td>{{ $month['month'] ?? '' }}</td>
                                        <td class="text-right positive">{{ $currencySymbol }}{{ number_format($month['income'] ?? 0, 2) }}</td>
                                        <td class="text-right negative">{{ $currencySymbol }}{{ number_format($month['expenses'] ?? 0, 2) }}</td>
                                        <td class="text-right {{ ($month['savings'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                            {{ $currencySymbol }}{{ number_format($month['savings'] ?? 0, 2) }}
                                        </td>
                                        <td class="text-right">{{ number_format($month['savings_rate'] ?? 0, 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(!empty($data['highlights']['best_month']))
                <div class="section">
                    <h2>Highlights</h2>
                    <div class="grid-2">
                        <div class="stat-card">
                            <div class="stat-card-title">Best Month</div>
                            <div class="stat-card-value positive">{{ $data['highlights']['best_month']['month'] ?? 'N/A' }}</div>
                            <div class="stat-card-change">Saved: {{ $currencySymbol }}{{ number_format($data['highlights']['best_month']['savings'] ?? 0, 2) }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-title">Worst Month</div>
                            <div class="stat-card-value negative">{{ $data['highlights']['worst_month']['month'] ?? 'N/A' }}</div>
                            <div class="stat-card-change">Saved: {{ $currencySymbol }}{{ number_format($data['highlights']['worst_month']['savings'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- ==================== CASH FLOW ANALYSIS REPORT ==================== --}}
        @if($reportType === 'cashflow')
            <div class="summary-section">
                <h2>Cash Flow Summary</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <label>Total Inflows</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_inflow'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Total Outflows</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_outflow'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Net Cash Flow</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['net_cash_flow'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Cash Flow Ratio</label>
                        <value>{{ $data['summary']['cash_flow_ratio'] ?? 0 }}</value>
                    </div>
                </div>
            </div>

            @if(!empty($data['weekly_cash_flow']) && count($data['weekly_cash_flow']) > 0)
                <div class="section">
                    <h2>Weekly Cash Flow</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Week Starting</th>
                                    <th class="text-right">Inflows</th>
                                    <th class="text-right">Outflows</th>
                                    <th class="text-right">Net Flow</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['weekly_cash_flow'] as $week)
                                    <tr>
                                        <td>{{ $week['week_start'] ?? '' }}</td>
                                        <td class="text-right positive">{{ $currencySymbol }}{{ number_format($week['inflow'] ?? 0, 2) }}</td>
                                        <td class="text-right negative">{{ $currencySymbol }}{{ number_format($week['outflow'] ?? 0, 2) }}</td>
                                        <td class="text-right {{ ($week['net'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                            {{ $currencySymbol }}{{ number_format($week['net'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(!empty($data['largest_inflows']) && count($data['largest_inflows']) > 0)
                <div class="section">
                    <h2>Largest Inflows</h2>
                    @foreach($data['largest_inflows'] as $inflow)
                        <div class="category-item">
                            <div class="category-info">
                                <div class="category-details">
                                    <h4>{{ $inflow['description'] ?? 'Unknown' }}</h4>
                                    <span>{{ $inflow['date'] ?? '' }}</span>
                                </div>
                            </div>
                            <div class="category-amount positive">{{ $currencySymbol }}{{ number_format($inflow['amount'] ?? 0, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(!empty($data['largest_outflows']) && count($data['largest_outflows']) > 0)
                <div class="section">
                    <h2>Largest Outflows</h2>
                    @foreach($data['largest_outflows'] as $outflow)
                        <div class="category-item">
                            <div class="category-info">
                                <div class="category-details">
                                    <h4>{{ $outflow['description'] ?? 'Unknown' }}</h4>
                                    <span>{{ $outflow['date'] ?? '' }}</span>
                                </div>
                            </div>
                            <div class="category-amount negative">{{ $currencySymbol }}{{ number_format($outflow['amount'] ?? 0, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        {{-- ==================== BUDGET PERFORMANCE REPORT ==================== --}}
        @if($reportType === 'budget')
            @if(isset($data['has_budgets']) && !$data['has_budgets'])
                <div class="no-data">
                    <p>{{ $data['message'] ?? 'No budgets found. Create budgets to track your spending.' }}</p>
                </div>
            @else
                <div class="summary-section">
                    <h2>Budget Performance Summary</h2>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <label>Total Budgeted</label>
                            <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_budgeted'] ?? 0, 2) }}</value>
                        </div>
                        <div class="summary-item">
                            <label>Total Spent</label>
                            <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_spent'] ?? 0, 2) }}</value>
                        </div>
                        <div class="summary-item">
                            <label>Remaining</label>
                            <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_remaining'] ?? 0, 2) }}</value>
                        </div>
                        <div class="summary-item">
                            <label>Overall Usage</label>
                            <value>{{ $data['summary']['overall_percentage'] ?? 0 }}%</value>
                        </div>
                    </div>
                </div>

                @if(!empty($data['categories']))
                    <div class="section">
                        <h2>Budget Details</h2>
                        @foreach($data['categories'] as $budget)
                            <div class="stat-card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                    <div>
                                        <h4 style="font-size: 16px; color: #1f2937; margin-bottom: 4px;">{{ $budget['budget_name'] ?? $budget['category'] }}</h4>
                                        <span style="font-size: 12px; color: #6b7280;">{{ $budget['category'] }} • {{ $budget['budget_period'] ?? '' }}</span>
                                    </div>
                                    <span class="badge {{ $budget['status'] === 'over' ? 'badge-over' : ($budget['status'] === 'warning' ? 'badge-warning' : 'badge-good') }}">
                                        {{ $budget['status'] === 'over' ? 'Over Budget' : ($budget['status'] === 'warning' ? 'Warning' : 'On Track') }}
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <span style="font-size: 13px; color: #6b7280;">Spent: {{ $currencySymbol }}{{ number_format($budget['spent'] ?? 0, 2) }} of {{ $currencySymbol }}{{ number_format($budget['budgeted'] ?? 0, 2) }}</span>
                                    <span style="font-size: 13px; font-weight: 600; color: {{ ($budget['remaining'] ?? 0) >= 0 ? '#10b981' : '#ef4444' }};">
                                        Remaining: {{ $currencySymbol }}{{ number_format($budget['remaining'] ?? 0, 2) }}
                                    </span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill {{ $budget['status'] === 'over' ? 'progress-over' : ($budget['status'] === 'warning' ? 'progress-warning' : 'progress-good') }}" 
                                         style="width: {{ min($budget['percentage'] ?? 0, 100) }}%"></div>
                                </div>
                                <p style="font-size: 12px; color: #6b7280; text-align: right; margin-top: 5px;">{{ $budget['percentage'] ?? 0 }}% used</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        @endif

        {{-- ==================== TAX SUMMARY REPORT ==================== --}}
        @if($reportType === 'tax')
            <div class="summary-section">
                <h2>Tax Summary ({{ $data['summary']['tax_year'] ?? date('Y') }})</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <label>Total Income</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_income'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Total Expenses</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_expenses'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Net Income</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['net_income'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Potentially Deductible</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['potentially_deductible'] ?? 0, 2) }}</value>
                    </div>
                </div>
            </div>

            @if(!empty($data['quarterly_breakdown']))
                <div class="section">
                    <h2>Quarterly Breakdown</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Quarter</th>
                                    <th>Period</th>
                                    <th class="text-right">Income</th>
                                    <th class="text-right">Expenses</th>
                                    <th class="text-right">Net</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['quarterly_breakdown'] as $quarter)
                                    <tr>
                                        <td>{{ $quarter['quarter'] ?? '' }}</td>
                                        <td>{{ $quarter['period'] ?? '' }}</td>
                                        <td class="text-right positive">{{ $currencySymbol }}{{ number_format($quarter['income'] ?? 0, 2) }}</td>
                                        <td class="text-right negative">{{ $currencySymbol }}{{ number_format($quarter['expenses'] ?? 0, 2) }}</td>
                                        <td class="text-right {{ ($quarter['net'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                            {{ $currencySymbol }}{{ number_format($quarter['net'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(!empty($data['income_sources']) && count($data['income_sources']) > 0)
                <div class="section">
                    <h2>Income Sources</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Source</th>
                                    <th class="text-right">Transactions</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['income_sources'] as $source)
                                    <tr>
                                        <td>{{ $source['source'] ?? 'Unknown' }}</td>
                                        <td class="text-right">{{ $source['count'] ?? 0 }}</td>
                                        <td class="text-right positive">{{ $currencySymbol }}{{ number_format($source['amount'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(!empty($data['expense_categories']) && count($data['expense_categories']) > 0)
                <div class="section">
                    <h2>Expense Categories</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-center">Deductible</th>
                                    <th class="text-right">Transactions</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['expense_categories'] as $expense)
                                    <tr>
                                        <td>{{ $expense['category'] ?? 'Unknown' }}</td>
                                        <td class="text-center">
                                            @if($expense['potentially_deductible'] ?? false)
                                                <span class="badge badge-good">Yes</span>
                                            @else
                                                <span class="badge badge-info">No</span>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ $expense['count'] ?? 0 }}</td>
                                        <td class="text-right">{{ $currencySymbol }}{{ number_format($expense['amount'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(!empty($data['large_transactions']) && count($data['large_transactions']) > 0)
                <div class="section">
                    <h2>Large Transactions (Over {{ $currencySymbol }}10,000)</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['large_transactions'] as $tx)
                                    <tr>
                                        <td>{{ $tx['date'] ?? '' }}</td>
                                        <td>{{ $tx['description'] ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $tx['type'] === 'credit' ? 'badge-income' : 'badge-expense' }}">
                                                {{ ucfirst($tx['type'] ?? '') }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ $currencySymbol }}{{ number_format($tx['amount'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif

        {{-- ==================== SAVINGS ANALYSIS REPORT ==================== --}}
        @if($reportType === 'savings')
            <div class="summary-section">
                <h2>Savings Analysis</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <label>Total Savings</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['total_savings'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Average Monthly Savings</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['avg_monthly_savings'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Savings Rate</label>
                        <value>{{ $data['summary']['savings_rate'] ?? 0 }}%</value>
                    </div>
                    <div class="summary-item">
                        <label>Best Savings Month</label>
                        <value>{{ $data['summary']['best_month'] ?? 'N/A' }}</value>
                    </div>
                </div>
            </div>

            @if(!empty($data['monthly_savings']))
                <div class="section">
                    <h2>Monthly Savings Trend</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-right">Income</th>
                                    <th class="text-right">Expenses</th>
                                    <th class="text-right">Savings</th>
                                    <th class="text-right">Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['monthly_savings'] as $month)
                                    <tr>
                                        <td>{{ $month['month'] ?? '' }}</td>
                                        <td class="text-right positive">{{ $currencySymbol }}{{ number_format($month['income'] ?? 0, 2) }}</td>
                                        <td class="text-right negative">{{ $currencySymbol }}{{ number_format($month['expenses'] ?? 0, 2) }}</td>
                                        <td class="text-right {{ ($month['savings'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                            {{ $currencySymbol }}{{ number_format($month['savings'] ?? 0, 2) }}
                                        </td>
                                        <td class="text-right">{{ number_format($month['rate'] ?? 0, 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(!empty($data['goals_progress']))
                <div class="section">
                    <h2>Savings Goals Progress</h2>
                    @foreach($data['goals_progress'] as $goal)
                        <div class="stat-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <h4 style="font-size: 16px; color: #1f2937;">{{ $goal['name'] ?? 'Goal' }}</h4>
                                <span style="font-size: 14px; font-weight: 600; color: #3b82f6;">{{ $goal['progress'] ?? 0 }}%</span>
                            </div>
                            <div class="progress-bar" style="height: 12px;">
                                <div class="progress-fill progress-good" style="width: {{ min($goal['progress'] ?? 0, 100) }}%"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 13px; color: #6b7280;">
                                <span>Current: {{ $currencySymbol }}{{ number_format($goal['current'] ?? 0, 2) }}</span>
                                <span>Target: {{ $currencySymbol }}{{ number_format($goal['target'] ?? 0, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        {{-- ==================== YEAR COMPARISON REPORT ==================== --}}
        @if($reportType === 'year-comparison')
            <div class="summary-section">
                <h2>Year-over-Year Comparison</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <label>Current Year Income</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['current_year_income'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Previous Year Income</label>
                        <value>{{ $currencySymbol }}{{ number_format($data['summary']['previous_year_income'] ?? 0, 2) }}</value>
                    </div>
                    <div class="summary-item">
                        <label>Income Change</label>
                        <value class="{{ ($data['summary']['income_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                            {{ ($data['summary']['income_change'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($data['summary']['income_change'] ?? 0, 1) }}%
                        </value>
                    </div>
                    <div class="summary-item">
                        <label>Expense Change</label>
                        <value class="{{ ($data['summary']['expense_change'] ?? 0) <= 0 ? 'positive' : 'negative' }}">
                            {{ ($data['summary']['expense_change'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($data['summary']['expense_change'] ?? 0, 1) }}%
                        </value>
                    </div>
                </div>
            </div>

            @if(!empty($data['monthly_comparison']))
                <div class="section">
                    <h2>Monthly Comparison</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-right">{{ $data['current_year'] ?? date('Y') }} Income</th>
                                    <th class="text-right">{{ $data['previous_year'] ?? (date('Y') - 1) }} Income</th>
                                    <th class="text-right">{{ $data['current_year'] ?? date('Y') }} Expenses</th>
                                    <th class="text-right">{{ $data['previous_year'] ?? (date('Y') - 1) }} Expenses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['monthly_comparison'] as $month)
                                    <tr>
                                        <td>{{ $month['month'] ?? '' }}</td>
                                        <td class="text-right positive">{{ $currencySymbol }}{{ number_format($month['current_income'] ?? 0, 2) }}</td>
                                        <td class="text-right" style="color: #6b7280;">{{ $currencySymbol }}{{ number_format($month['previous_income'] ?? 0, 2) }}</td>
                                        <td class="text-right negative">{{ $currencySymbol }}{{ number_format($month['current_expenses'] ?? 0, 2) }}</td>
                                        <td class="text-right" style="color: #6b7280;">{{ $currencySymbol }}{{ number_format($month['previous_expenses'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(!empty($data['category_comparison']))
                <div class="section">
                    <h2>Category Comparison</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-right">{{ $data['current_year'] ?? date('Y') }}</th>
                                    <th class="text-right">{{ $data['previous_year'] ?? (date('Y') - 1) }}</th>
                                    <th class="text-right">Change</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['category_comparison'] as $cat)
                                    <tr>
                                        <td>{{ $cat['category'] ?? 'Unknown' }}</td>
                                        <td class="text-right">{{ $currencySymbol }}{{ number_format($cat['current'] ?? 0, 2) }}</td>
                                        <td class="text-right" style="color: #6b7280;">{{ $currencySymbol }}{{ number_format($cat['previous'] ?? 0, 2) }}</td>
                                        <td class="text-right {{ ($cat['change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                            {{ ($cat['change'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($cat['change'] ?? 0, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>This report was generated by TrackFlow - Personal Finance Management System</p>
            <p style="margin-top: 5px;">© {{ date('Y') }} TrackFlow. All rights reserved.</p>
        </div>

        <!-- Print Button -->
        <div class="print-button">
            <button onclick="window.print()">
                🖨️ Print / Save as PDF
            </button>
        </div>
    </div>
</body>

</html>
