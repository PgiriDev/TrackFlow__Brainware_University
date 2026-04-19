<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get cached category by ID - prevents N+1 queries
     */
    private function getCategoryById($categoryId, $userId = null)
    {
        if (!$categoryId)
            return null;
        $userId = $userId ?? session('user_id');
        $categories = \App\Models\Category::getCachedForUser($userId);
        return $categories->firstWhere('id', $categoryId);
    }

    /**
     * Format date based on user preference
     */
    private function formatDate($date, $userId = null)
    {
        $userPrefs = app('user.preferences');
        $dateFormat = $userPrefs->date_format ?? 'Y-m-d';

        return Carbon::parse($date)->format($dateFormat);
    }

    /**
     * Get user's date format
     */
    private function getUserDateFormat($userId = null)
    {
        $userPrefs = app('user.preferences');
        return $userPrefs->date_format ?? 'Y-m-d';
    }

    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        // Generate report via API
        return redirect()->route('reports.index')
            ->with('success', 'Report generated successfully');
    }

    public function download($id)
    {
        // Download report via API
        return response()->download(storage_path("app/reports/{$id}.pdf"));
    }

    public function getStats()
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            // Get user's currency preference (prefer user_settings.display_currency)
            $user = User::find($userId);
            $userSetting = app('user.settings');
            $userCurrency = strtoupper(trim($userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR')));
            $currencyService = app(\App\Services\CurrencyService::class);

            // Get currency symbol - ensure we have a valid currency code
            $currencyConfig = config('currency.currencies');
            $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? $currencyConfig[config('currency.default', 'INR')]['symbol'] ?? '₹';

            // Get all transactions for the user
            $transactions = Transaction::where('user_id', $userId)->get();

            // Convert transaction amounts to user's currency
            $convertedTransactions = $transactions->map(function ($tx) use ($currencyService, $userCurrency) {
                $storedCurrency = $tx->currency ?? 'INR';
                $tx->converted_amount = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
                return $tx;
            });

            // Current month calculations
            $currentMonth = now()->format('Y-m');
            $currentMonthTransactions = $convertedTransactions->filter(function ($tx) use ($currentMonth) {
                return Carbon::parse($tx->date)->format('Y-m') === $currentMonth;
            });

            $thisMonthTotal = $currentMonthTransactions->where('type', 'debit')->sum('converted_amount');
            $thisMonthCount = $currentMonthTransactions->count();

            // Last month calculations
            $lastMonth = now()->subMonth()->format('Y-m');
            $lastMonthTransactions = $convertedTransactions->filter(function ($tx) use ($lastMonth) {
                return Carbon::parse($tx->date)->format('Y-m') === $lastMonth;
            });

            $lastMonthTotal = $lastMonthTransactions->where('type', 'debit')->sum('converted_amount');

            // Average daily calculation (current month)
            $daysInCurrentMonth = now()->day; // Days elapsed in current month
            $avgDaily = $daysInCurrentMonth > 0 ? $thisMonthTotal / $daysInCurrentMonth : 0;

            // Total transactions count (all time)
            $totalTransactions = $convertedTransactions->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'this_month' => round($thisMonthTotal, 2),
                    'last_month' => round($lastMonthTotal, 2),
                    'avg_daily' => round($avgDaily, 2),
                    'total_transactions' => $totalTransactions,
                    'currency_symbol' => $currencySymbol,
                    'currency_code' => $userCurrency,
                    'this_month_count' => $thisMonthCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportData(Request $request)
    {
        try {
            $userId = session('user_id');
            $format = $request->input('format', 'csv');

            if (!$userId) {
                // Redirect to login if not authenticated
                return redirect()->route('login')->with('error', 'Please login to export data');
            }

            // Get user's currency preference (prefer user_settings.display_currency)
            $user = User::find($userId);

            if (!$user) {
                return redirect()->route('login')->with('error', 'User not found');
            }

            $userSetting = app('user.settings');
            $userCurrency = strtoupper(trim($userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR')));

            // Check if CurrencyService exists
            if (!app()->bound(\App\Services\CurrencyService::class)) {
                // Fallback if service is not available
                $currencyService = null;
            } else {
                $currencyService = app(\App\Services\CurrencyService::class);
            }

            // Get currency symbol - ensure we have a valid currency code
            $currencyConfig = config('currency.currencies');
            $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? $currencyConfig[config('currency.default', 'INR')]['symbol'] ?? '₹';

            // Get all transactions for the user with eager loaded categories
            $transactions = Transaction::where('user_id', $userId)
                ->with('category')
                ->orderBy('date', 'desc')
                ->get();

            // Convert transaction amounts to user's currency
            $convertedTransactions = $transactions->map(function ($tx) use ($currencyService, $userCurrency, $currencySymbol, $userId) {
                $storedCurrency = $tx->currency ?? 'INR';

                // Convert amount if service is available, otherwise use as-is
                if ($currencyService) {
                    try {
                        $convertedAmount = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
                    } catch (\Exception $e) {
                        $convertedAmount = (float) $tx->amount;
                    }
                } else {
                    $convertedAmount = (float) $tx->amount;
                }

                // Get category name - check relationship first, then extract from notes if needed
                $categoryName = 'Uncategorized';
                if ($tx->category) {
                    $categoryName = $tx->category->name;
                } elseif ($tx->notes && preg_match('/\[Category:\s*(.+?)\]/', $tx->notes, $matches)) {
                    // Extract category from notes field (for legacy transactions)
                    $categoryIdentifier = trim($matches[1]);
                    if (is_numeric($categoryIdentifier)) {
                        // It's a category ID - use cached lookup
                        $category = $this->getCategoryById($categoryIdentifier, $userId);
                        $categoryName = $category ? $category->name : $categoryIdentifier;
                    } else {
                        // It's a category slug/name
                        $categoryName = ucwords(str_replace('-', ' ', $categoryIdentifier));
                    }
                }

                $storedSymbol = $currencyConfig[$storedCurrency]['symbol'] ?? $storedCurrency;

                return [
                    'Date' => $this->formatDate($tx->date, $userId),
                    'Description' => $tx->description ?? 'N/A',
                    'Type' => ucfirst($tx->type ?? 'debit'),
                    'Amount' => $currencySymbol . number_format($convertedAmount, 2),
                    'OriginalAmount' => $storedSymbol . number_format((float) $tx->amount, 2),
                    'Category' => $categoryName,
                    'Status' => ucfirst($tx->status ?? 'pending'),
                    'Merchant' => $tx->merchant ?? '-',
                    'Notes' => $tx->notes ?? '-'
                ];
            });

            $filename = 'transactions_export_' . date('Y-m-d_His');

            switch ($format) {
                case 'csv':
                    return $this->exportCSV($convertedTransactions, $filename);

                case 'excel':
                    return $this->exportExcel($convertedTransactions, $filename);

                case 'pdf':
                    return $this->exportPDF($convertedTransactions, $filename, $user, $currencySymbol);

                default:
                    return redirect()->back()->with('error', 'Invalid export format');
            }

        } catch (\Exception $e) {
            \Log::error('Export failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    private function exportCSV($data, $filename)
    {
        try {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');

                // Add BOM for Excel UTF-8 support
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Add headers
                if ($data->isNotEmpty()) {
                    fputcsv($file, array_keys($data->first()));
                }

                // Add data
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('CSV Export Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function exportExcel($data, $filename)
    {
        try {
            // Simple Excel XML format (compatible with Excel and LibreOffice)
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function () use ($data) {
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
                echo 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
                echo '<Worksheet ss:Name="Transactions"><Table>';

                // Add headers
                if ($data->isNotEmpty()) {
                    echo '<Row>';
                    foreach (array_keys($data->first()) as $header) {
                        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>';
                    }
                    echo '</Row>';
                }

                // Add data
                foreach ($data as $row) {
                    echo '<Row>';
                    foreach ($row as $cell) {
                        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($cell) . '</Data></Cell>';
                    }
                    echo '</Row>';
                }

                echo '</Table></Worksheet></Workbook>';
            };

            return Response::stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Excel Export Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function exportPDF($data, $filename, $user, $currencySymbol)
    {
        // Generate HTML content for PDF
        $html = view('reports.pdf-export', [
            'transactions' => $data,
            'user' => $user,
            'userId' => $user->id,
            'currencySymbol' => $currencySymbol,
            'generatedDate' => now()->format('F d, Y')
        ])->render();

        // For now, return HTML as PDF (browser will handle print to PDF)
        // In production, you would use a library like dompdf or wkhtmltopdf
        $headers = [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "inline; filename=\"{$filename}.html\"",
        ];

        return Response::make($html, 200, $headers);
    }

    /**
     * Export specific report type as PDF
     */
    public function exportReportPDF(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to export data');
        }

        try {
            $reportType = $request->input('type', 'income-expense');
            $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

            // Get user's currency preference
            $user = User::find($userId);
            $userSetting = app('user.settings');
            $userCurrency = strtoupper(trim($userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR')));

            $currencyService = app()->bound(\App\Services\CurrencyService::class)
                ? app(\App\Services\CurrencyService::class)
                : null;

            $currencyConfig = config('currency.currencies');
            $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? $currencyConfig[config('currency.default', 'INR')]['symbol'] ?? '₹';

            // Get transactions within date range
            $transactions = Transaction::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->get();

            // Convert amounts
            $convertedTransactions = $transactions->map(function ($tx) use ($currencyService, $userCurrency) {
                $storedCurrency = $tx->currency ?? 'INR';
                if ($currencyService) {
                    try {
                        $tx->converted_amount = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
                    } catch (\Exception $e) {
                        $tx->converted_amount = (float) $tx->amount;
                    }
                } else {
                    $tx->converted_amount = (float) $tx->amount;
                }
                return $tx;
            });

            // Generate report data based on type
            switch ($reportType) {
                case 'income-expense':
                    $reportData = $this->generateIncomeExpenseReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId);
                    $reportTitle = 'Income vs Expenses Report';
                    break;
                case 'category':
                    $reportData = $this->generateCategoryReport($convertedTransactions, $currencySymbol, $userId);
                    $reportTitle = 'Category Analysis Report';
                    break;
                case 'monthly':
                    $reportData = $this->generateMonthlyReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId);
                    $reportTitle = 'Monthly Summary Report';
                    break;
                case 'cashflow':
                    $reportData = $this->generateCashFlowReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId);
                    $reportTitle = 'Cash Flow Analysis Report';
                    break;
                case 'budget':
                    $reportData = $this->generateBudgetReport($userId, $startDate, $endDate, $currencySymbol, $currencyService, $userCurrency);
                    $reportTitle = 'Budget Performance Report';
                    break;
                case 'tax':
                    $reportData = $this->generateTaxReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId);
                    $reportTitle = 'Tax Summary Report';
                    break;
                case 'savings':
                    $reportData = $this->generateSavingsReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId, $currencyService, $userCurrency);
                    $reportTitle = 'Savings Analysis Report';
                    break;
                case 'year-comparison':
                    $reportData = $this->generateYearComparisonReport($userId, $startDate, $endDate, $currencySymbol, $currencyService, $userCurrency);
                    $reportTitle = 'Year-over-Year Comparison Report';
                    break;
                default:
                    $reportData = $this->generateIncomeExpenseReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId);
                    $reportTitle = 'Financial Report';
            }

            // Generate date range string
            $dateRange = Carbon::parse($startDate)->format('M d, Y') . ' - ' . Carbon::parse($endDate)->format('M d, Y');

            // Render PDF view
            $html = view('reports.report-pdf-export', [
                'reportType' => $reportType,
                'reportTitle' => $reportTitle,
                'data' => $reportData,
                'user' => $user,
                'currencySymbol' => $currencySymbol,
                'generatedDate' => now()->format('F d, Y H:i'),
                'dateRange' => $dateRange
            ])->render();

            $headers = [
                'Content-Type' => 'text/html',
                'Content-Disposition' => "inline; filename=\"{$reportType}_report.html\"",
            ];

            return Response::make($html, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('PDF Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate Savings Analysis Report
     */
    private function generateSavingsReport($transactions, $startDate, $endDate, $currencySymbol, $userId, $currencyService = null, $userCurrency = 'INR')
    {
        $totalIncome = $transactions->where('type', 'credit')->sum('converted_amount');
        $totalExpenses = $transactions->where('type', 'debit')->sum('converted_amount');
        $totalSavings = $totalIncome - $totalExpenses;
        $savingsRate = $totalIncome > 0 ? round(($totalSavings / $totalIncome) * 100, 1) : 0;

        // Monthly savings breakdown
        $monthlySavings = [];
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfMonth();
        $bestMonth = null;
        $bestSavings = PHP_INT_MIN;

        while ($start->lte($end)) {
            $monthKey = $start->format('Y-m');
            $monthTransactions = $transactions->filter(function ($tx) use ($monthKey) {
                return Carbon::parse($tx->date)->format('Y-m') === $monthKey;
            });

            $income = $monthTransactions->where('type', 'credit')->sum('converted_amount');
            $expenses = $monthTransactions->where('type', 'debit')->sum('converted_amount');
            $savings = $income - $expenses;
            $rate = $income > 0 ? round(($savings / $income) * 100, 1) : 0;

            $monthlySavings[] = [
                'month' => $start->format('F Y'),
                'income' => $income,
                'expenses' => $expenses,
                'savings' => $savings,
                'rate' => $rate
            ];

            if ($savings > $bestSavings) {
                $bestSavings = $savings;
                $bestMonth = $start->format('F Y');
            }

            $start->addMonth();
        }

        $avgMonthlySavings = count($monthlySavings) > 0 ? collect($monthlySavings)->avg('savings') : 0;

        // Get goals progress
        $goalsProgress = [];
        $goals = \App\Models\Goal::where('user_id', $userId)->get();
        foreach ($goals as $goal) {
            $current = (float) $goal->current_amount;
            $target = (float) $goal->target_amount;

            // Convert if needed
            if ($currencyService && $userCurrency !== 'INR') {
                try {
                    $current = $currencyService->convertFromINR($current, $userCurrency);
                    $target = $currencyService->convertFromINR($target, $userCurrency);
                } catch (\Exception $e) {
                    // Keep original
                }
            }

            $goalsProgress[] = [
                'name' => $goal->name,
                'current' => $current,
                'target' => $target,
                'progress' => $target > 0 ? round(($current / $target) * 100, 1) : 0
            ];
        }

        return [
            'summary' => [
                'total_savings' => $totalSavings,
                'avg_monthly_savings' => $avgMonthlySavings,
                'savings_rate' => $savingsRate,
                'best_month' => $bestMonth ?? 'N/A'
            ],
            'monthly_savings' => $monthlySavings,
            'goals_progress' => $goalsProgress
        ];
    }

    /**
     * Generate Year-over-Year Comparison Report
     */
    private function generateYearComparisonReport($userId, $startDate, $endDate, $currencySymbol, $currencyService = null, $userCurrency = 'INR')
    {
        $currentYear = Carbon::parse($endDate)->year;
        $previousYear = $currentYear - 1;

        // Get transactions for both years
        $currentYearStart = Carbon::create($currentYear, 1, 1)->toDateString();
        $currentYearEnd = Carbon::create($currentYear, 12, 31)->toDateString();
        $previousYearStart = Carbon::create($previousYear, 1, 1)->toDateString();
        $previousYearEnd = Carbon::create($previousYear, 12, 31)->toDateString();

        $currentYearTx = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$currentYearStart, $currentYearEnd])
            ->get();

        $previousYearTx = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$previousYearStart, $previousYearEnd])
            ->get();

        // Convert amounts
        $convertAmount = function ($tx) use ($currencyService, $userCurrency) {
            $storedCurrency = $tx->currency ?? 'INR';
            if ($currencyService) {
                try {
                    return $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
                } catch (\Exception $e) {
                    return (float) $tx->amount;
                }
            }
            return (float) $tx->amount;
        };

        $currentYearTx = $currentYearTx->map(function ($tx) use ($convertAmount) {
            $tx->converted_amount = $convertAmount($tx);
            return $tx;
        });

        $previousYearTx = $previousYearTx->map(function ($tx) use ($convertAmount) {
            $tx->converted_amount = $convertAmount($tx);
            return $tx;
        });

        // Calculate totals
        $currentIncome = $currentYearTx->where('type', 'credit')->sum('converted_amount');
        $currentExpenses = $currentYearTx->where('type', 'debit')->sum('converted_amount');
        $previousIncome = $previousYearTx->where('type', 'credit')->sum('converted_amount');
        $previousExpenses = $previousYearTx->where('type', 'debit')->sum('converted_amount');

        $incomeChange = $previousIncome > 0 ? round((($currentIncome - $previousIncome) / $previousIncome) * 100, 1) : 0;
        $expenseChange = $previousExpenses > 0 ? round((($currentExpenses - $previousExpenses) / $previousExpenses) * 100, 1) : 0;

        // Monthly comparison
        $monthlyComparison = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthName = Carbon::create(null, $m, 1)->format('F');
            $monthKey = sprintf('%02d', $m);

            $currentMonthTx = $currentYearTx->filter(fn($tx) => Carbon::parse($tx->date)->format('m') === $monthKey);
            $previousMonthTx = $previousYearTx->filter(fn($tx) => Carbon::parse($tx->date)->format('m') === $monthKey);

            $monthlyComparison[] = [
                'month' => $monthName,
                'current_income' => $currentMonthTx->where('type', 'credit')->sum('converted_amount'),
                'previous_income' => $previousMonthTx->where('type', 'credit')->sum('converted_amount'),
                'current_expenses' => $currentMonthTx->where('type', 'debit')->sum('converted_amount'),
                'previous_expenses' => $previousMonthTx->where('type', 'debit')->sum('converted_amount')
            ];
        }

        // Category comparison - use cached categories
        $categoryComparison = [];
        $allCategories = $currentYearTx->pluck('category_id')
            ->merge($previousYearTx->pluck('category_id'))
            ->unique()
            ->filter();

        foreach ($allCategories as $categoryId) {
            $category = $this->getCategoryById($categoryId, $userId);
            $categoryName = $category ? $category->name : 'Uncategorized';

            $currentCatAmount = $currentYearTx->where('category_id', $categoryId)->where('type', 'debit')->sum('converted_amount');
            $previousCatAmount = $previousYearTx->where('category_id', $categoryId)->where('type', 'debit')->sum('converted_amount');
            $change = $previousCatAmount > 0 ? round((($currentCatAmount - $previousCatAmount) / $previousCatAmount) * 100, 1) : ($currentCatAmount > 0 ? 100 : 0);

            $categoryComparison[] = [
                'category' => $categoryName,
                'current' => $currentCatAmount,
                'previous' => $previousCatAmount,
                'change' => $change
            ];
        }

        // Sort by current amount
        usort($categoryComparison, fn($a, $b) => $b['current'] <=> $a['current']);

        return [
            'current_year' => $currentYear,
            'previous_year' => $previousYear,
            'summary' => [
                'current_year_income' => $currentIncome,
                'previous_year_income' => $previousIncome,
                'current_year_expenses' => $currentExpenses,
                'previous_year_expenses' => $previousExpenses,
                'income_change' => $incomeChange,
                'expense_change' => $expenseChange
            ],
            'monthly_comparison' => $monthlyComparison,
            'category_comparison' => array_slice($categoryComparison, 0, 10)
        ];
    }

    public function getReportData(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            $reportType = $request->input('type', 'income-expense');
            $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

            // Get user's currency preference (prefer user_settings.display_currency)
            $user = User::find($userId);
            $userSetting = app('user.settings');
            $userCurrency = strtoupper(trim($userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR')));

            // Check if CurrencyService exists
            if (!app()->bound(\App\Services\CurrencyService::class)) {
                $currencyService = null;
            } else {
                $currencyService = app(\App\Services\CurrencyService::class);
            }

            // Get currency symbol - ensure we have a valid currency code
            $currencyConfig = config('currency.currencies');
            $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? $currencyConfig[config('currency.default', 'INR')]['symbol'] ?? '₹';

            // Get transactions within date range
            $transactions = Transaction::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->get();

            // Convert amounts to user currency
            $convertedTransactions = $transactions->map(function ($tx) use ($currencyService, $userCurrency) {
                $storedCurrency = $tx->currency ?? 'INR';

                if ($currencyService) {
                    try {
                        $convertedAmount = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
                    } catch (\Exception $e) {
                        $convertedAmount = (float) $tx->amount;
                    }
                } else {
                    $convertedAmount = (float) $tx->amount;
                }

                $tx->converted_amount = $convertedAmount;
                return $tx;
            });

            // Calculate totals
            $totalIncome = $convertedTransactions->where('type', 'credit')->sum('converted_amount');
            $totalExpenses = $convertedTransactions->where('type', 'debit')->sum('converted_amount');
            $netBalance = $totalIncome - $totalExpenses;

            // Get category breakdown
            $categoryData = $convertedTransactions->where('type', 'debit')
                ->groupBy('category_id')
                ->map(function ($group) {
                    return [
                        'category' => $group->first()->category_id ?? 'Uncategorized',
                        'total' => $group->sum('converted_amount'),
                        'count' => $group->count()
                    ];
                })->values();

            // Get monthly trend data
            $monthlyData = $convertedTransactions->groupBy(function ($tx) {
                return Carbon::parse($tx->date)->format('Y-m');
            })->map(function ($group, $month) {
                return [
                    'month' => $month,
                    'income' => $group->where('type', 'credit')->sum('converted_amount'),
                    'expenses' => $group->where('type', 'debit')->sum('converted_amount')
                ];
            })->values();

            // Get daily average
            $daysDiff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $avgDailyExpense = $daysDiff > 0 ? $totalExpenses / $daysDiff : 0;
            $avgDailyIncome = $daysDiff > 0 ? $totalIncome / $daysDiff : 0;

            // Top spending categories
            $topCategories = $categoryData->sortByDesc('total')->take(5)->values();

            // Recent transactions
            $recentTransactions = $convertedTransactions->take(10)->map(function ($tx) use ($currencySymbol, $userId) {
                return [
                    'id' => $tx->id,
                    'date' => $this->formatDate($tx->date, $userId),
                    'description' => $tx->description ?? 'N/A',
                    'category' => $tx->category_id ?? 'Uncategorized',
                    'amount' => $tx->converted_amount,
                    'formatted_amount' => $currencySymbol . number_format($tx->converted_amount, 2),
                    'type' => $tx->type,
                    'merchant' => $tx->merchant ?? '-'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'report_type' => $reportType,
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ],
                    'currency_symbol' => $currencySymbol,
                    'summary' => [
                        'total_income' => $totalIncome,
                        'total_expenses' => $totalExpenses,
                        'net_balance' => $netBalance,
                        'transaction_count' => $convertedTransactions->count(),
                        'avg_daily_expense' => $avgDailyExpense,
                        'avg_daily_income' => $avgDailyIncome
                    ],
                    'monthly_trend' => $monthlyData,
                    'categories' => $categoryData,
                    'top_categories' => $topCategories,
                    'recent_transactions' => $recentTransactions
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load report data: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load report data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific report data based on report type
     */
    public function getSpecificReport(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            $reportType = $request->input('type', 'income-expense');
            $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

            // Get user's currency preference
            $user = User::find($userId);
            $userSetting = app('user.settings');
            $userCurrency = strtoupper(trim($userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR')));

            $currencyService = app()->bound(\App\Services\CurrencyService::class)
                ? app(\App\Services\CurrencyService::class)
                : null;

            $currencyConfig = config('currency.currencies');
            $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? $currencyConfig[config('currency.default', 'INR')]['symbol'] ?? '₹';

            // Get transactions within date range
            $transactions = Transaction::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->get();

            // Convert amounts
            $convertedTransactions = $transactions->map(function ($tx) use ($currencyService, $userCurrency) {
                $storedCurrency = $tx->currency ?? 'INR';
                if ($currencyService) {
                    try {
                        $tx->converted_amount = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
                    } catch (\Exception $e) {
                        $tx->converted_amount = (float) $tx->amount;
                    }
                } else {
                    $tx->converted_amount = (float) $tx->amount;
                }
                return $tx;
            });

            // Generate report based on type
            switch ($reportType) {
                case 'income-expense':
                    $reportData = $this->generateIncomeExpenseReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId);
                    break;
                case 'category':
                    $reportData = $this->generateCategoryReport($convertedTransactions, $currencySymbol, $userId);
                    break;
                case 'monthly':
                    $reportData = $this->generateMonthlyReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId);
                    break;
                case 'cashflow':
                    $reportData = $this->generateCashFlowReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId);
                    break;
                case 'budget':
                    $reportData = $this->generateBudgetReport($userId, $startDate, $endDate, $currencySymbol, $currencyService, $userCurrency);
                    break;
                case 'tax':
                    $reportData = $this->generateTaxReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId);
                    break;
                default:
                    $reportData = $this->generateIncomeExpenseReport($convertedTransactions, $startDate, $endDate, $currencySymbol, $userId);
            }

            return response()->json([
                'success' => true,
                'report_type' => $reportType,
                'currency_symbol' => $currencySymbol,
                'date_range' => ['start' => $startDate, 'end' => $endDate],
                'data' => $reportData
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to generate specific report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Income vs Expenses Report
     */
    private function generateIncomeExpenseReport($transactions, $startDate, $endDate, $currencySymbol, $userId)
    {
        $totalIncome = $transactions->where('type', 'credit')->sum('converted_amount');
        $totalExpenses = $transactions->where('type', 'debit')->sum('converted_amount');
        $netBalance = $totalIncome - $totalExpenses;
        $savingsRate = $totalIncome > 0 ? round(($netBalance / $totalIncome) * 100, 1) : 0;

        // Daily breakdown
        $dailyData = $transactions->groupBy(function ($tx) {
            return Carbon::parse($tx->date)->format('Y-m-d');
        })->map(function ($group, $date) {
            return [
                'date' => $date,
                'income' => $group->where('type', 'credit')->sum('converted_amount'),
                'expenses' => $group->where('type', 'debit')->sum('converted_amount'),
                'net' => $group->where('type', 'credit')->sum('converted_amount') - $group->where('type', 'debit')->sum('converted_amount')
            ];
        })->values();

        // Weekly breakdown
        $weeklyData = $transactions->groupBy(function ($tx) {
            return Carbon::parse($tx->date)->startOfWeek()->format('Y-m-d');
        })->map(function ($group, $weekStart) {
            return [
                'week_start' => $weekStart,
                'income' => $group->where('type', 'credit')->sum('converted_amount'),
                'expenses' => $group->where('type', 'debit')->sum('converted_amount')
            ];
        })->values();

        // Get cached categories for efficient lookup
        $cachedCategories = \App\Models\Category::getCachedForUser($userId);

        // Top income sources - use cached category lookup
        $topIncome = $transactions->where('type', 'credit')
            ->groupBy('category_id')
            ->map(function ($group) use ($cachedCategories) {
                $cat = $group->first()->category_id ? $cachedCategories->firstWhere('id', $group->first()->category_id) : null;
                return [
                    'category' => $cat ? $cat->name : 'Other Income',
                    'amount' => $group->sum('converted_amount'),
                    'count' => $group->count()
                ];
            })->sortByDesc('amount')->values()->take(5);

        // Top expense categories - use cached category lookup
        $topExpenses = $transactions->where('type', 'debit')
            ->groupBy('category_id')
            ->map(function ($group) use ($cachedCategories) {
                $cat = $group->first()->category_id ? $cachedCategories->firstWhere('id', $group->first()->category_id) : null;
                return [
                    'category' => $cat ? $cat->name : 'Uncategorized',
                    'amount' => $group->sum('converted_amount'),
                    'count' => $group->count()
                ];
            })->sortByDesc('amount')->values()->take(5);

        // Recent transactions - use cached category lookup
        $recentTransactions = $transactions->take(20)->map(function ($tx) use ($userId, $cachedCategories) {
            $cat = $tx->category_id ? $cachedCategories->firstWhere('id', $tx->category_id) : null;
            return [
                'date' => $this->formatDate($tx->date, $userId),
                'description' => $tx->description ?? 'N/A',
                'category' => $cat ? $cat->name : 'Uncategorized',
                'amount' => $tx->converted_amount,
                'type' => $tx->type
            ];
        });

        return [
            'summary' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'net_balance' => $netBalance,
                'savings_rate' => $savingsRate,
                'transaction_count' => $transactions->count(),
                'income_count' => $transactions->where('type', 'credit')->count(),
                'expense_count' => $transactions->where('type', 'debit')->count()
            ],
            'daily_breakdown' => $dailyData,
            'weekly_breakdown' => $weeklyData,
            'top_income_sources' => $topIncome,
            'top_expense_categories' => $topExpenses,
            'transactions' => $recentTransactions
        ];
    }

    /**
     * Generate Category Analysis Report
     */
    private function generateCategoryReport($transactions, $currencySymbol, $userId)
    {
        $totalExpenses = $transactions->where('type', 'debit')->sum('converted_amount');
        $totalIncome = $transactions->where('type', 'credit')->sum('converted_amount');

        // Get cached categories for efficient lookup
        $cachedCategories = \App\Models\Category::getCachedForUser(session('user_id'));

        // Expense categories breakdown - use cached category lookup
        $expenseCategories = $transactions->where('type', 'debit')
            ->groupBy('category_id')
            ->map(function ($group) use ($totalExpenses, $cachedCategories) {
                $cat = $group->first()->category_id ? $cachedCategories->firstWhere('id', $group->first()->category_id) : null;
                $amount = $group->sum('converted_amount');
                return [
                    'category_id' => $group->first()->category_id,
                    'name' => $cat ? $cat->name : 'Uncategorized',
                    'icon' => $cat ? $cat->icon : 'fa-question',
                    'color' => $cat ? $cat->color : '#6B7280',
                    'amount' => $amount,
                    'percentage' => $totalExpenses > 0 ? round(($amount / $totalExpenses) * 100, 1) : 0,
                    'count' => $group->count(),
                    'avg_transaction' => $group->count() > 0 ? $amount / $group->count() : 0
                ];
            })->sortByDesc('amount')->values();

        // Income categories breakdown - use cached category lookup
        $incomeCategories = $transactions->where('type', 'credit')
            ->groupBy('category_id')
            ->map(function ($group) use ($totalIncome, $cachedCategories) {
                $cat = $group->first()->category_id ? $cachedCategories->firstWhere('id', $group->first()->category_id) : null;
                $amount = $group->sum('converted_amount');
                return [
                    'category_id' => $group->first()->category_id,
                    'name' => $cat ? $cat->name : 'Other Income',
                    'icon' => $cat ? $cat->icon : 'fa-coins',
                    'color' => $cat ? $cat->color : '#10B981',
                    'amount' => $amount,
                    'percentage' => $totalIncome > 0 ? round(($amount / $totalIncome) * 100, 1) : 0,
                    'count' => $group->count()
                ];
            })->sortByDesc('amount')->values();

        // Category trends (by month) - use cached category lookup
        $categoryTrends = $transactions->where('type', 'debit')
            ->groupBy(function ($tx) {
                return Carbon::parse($tx->date)->format('Y-m');
            })->map(function ($monthGroup, $month) use ($cachedCategories) {
                return [
                    'month' => $month,
                    'categories' => $monthGroup->groupBy('category_id')->map(function ($catGroup) use ($cachedCategories) {
                        $cat = $catGroup->first()->category_id ? $cachedCategories->firstWhere('id', $catGroup->first()->category_id) : null;
                        return [
                            'name' => $cat ? $cat->name : 'Uncategorized',
                            'amount' => $catGroup->sum('converted_amount')
                        ];
                    })->values()
                ];
            })->values();

        // Uncategorized transactions
        $uncategorized = $transactions->whereNull('category_id')->count();

        return [
            'summary' => [
                'total_expenses' => $totalExpenses,
                'total_income' => $totalIncome,
                'expense_categories_count' => $expenseCategories->count(),
                'income_categories_count' => $incomeCategories->count(),
                'uncategorized_count' => $uncategorized
            ],
            'expense_categories' => $expenseCategories,
            'income_categories' => $incomeCategories,
            'category_trends' => $categoryTrends,
            'top_category' => $expenseCategories->first()
        ];
    }

    /**
     * Generate Monthly Summary Report
     */
    private function generateMonthlyReport($transactions, $startDate, $endDate, $currencySymbol, $userId)
    {
        $monthlyData = [];
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfMonth();

        while ($start->lte($end)) {
            $monthKey = $start->format('Y-m');
            $monthTransactions = $transactions->filter(function ($tx) use ($monthKey) {
                return Carbon::parse($tx->date)->format('Y-m') === $monthKey;
            });

            $income = $monthTransactions->where('type', 'credit')->sum('converted_amount');
            $expenses = $monthTransactions->where('type', 'debit')->sum('converted_amount');

            $monthlyData[] = [
                'month' => $start->format('F Y'),
                'month_key' => $monthKey,
                'income' => $income,
                'expenses' => $expenses,
                'savings' => $income - $expenses,
                'savings_rate' => $income > 0 ? round((($income - $expenses) / $income) * 100, 1) : 0,
                'transaction_count' => $monthTransactions->count(),
                'avg_daily_expense' => $start->daysInMonth > 0 ? $expenses / $start->daysInMonth : 0
            ];

            $start->addMonth();
        }

        // Calculate averages
        $avgIncome = collect($monthlyData)->avg('income');
        $avgExpenses = collect($monthlyData)->avg('expenses');
        $avgSavings = collect($monthlyData)->avg('savings');

        // Best and worst months
        $bestMonth = collect($monthlyData)->sortByDesc('savings')->first();
        $worstMonth = collect($monthlyData)->sortBy('savings')->first();

        return [
            'monthly_breakdown' => $monthlyData,
            'averages' => [
                'avg_income' => $avgIncome,
                'avg_expenses' => $avgExpenses,
                'avg_savings' => $avgSavings
            ],
            'highlights' => [
                'best_month' => $bestMonth,
                'worst_month' => $worstMonth,
                'total_months' => count($monthlyData)
            ]
        ];
    }

    /**
     * Generate Cash Flow Report
     */
    private function generateCashFlowReport($transactions, $startDate, $endDate, $currencySymbol, $userId)
    {
        $totalInflow = $transactions->where('type', 'credit')->sum('converted_amount');
        $totalOutflow = $transactions->where('type', 'debit')->sum('converted_amount');
        $netCashFlow = $totalInflow - $totalOutflow;

        // Daily cash flow
        $dailyCashFlow = [];
        $runningBalance = 0;
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($start->lte($end)) {
            $dateKey = $start->format('Y-m-d');
            $dayTransactions = $transactions->filter(function ($tx) use ($dateKey) {
                return Carbon::parse($tx->date)->format('Y-m-d') === $dateKey;
            });

            $inflow = $dayTransactions->where('type', 'credit')->sum('converted_amount');
            $outflow = $dayTransactions->where('type', 'debit')->sum('converted_amount');
            $runningBalance += ($inflow - $outflow);

            if ($dayTransactions->count() > 0) {
                $dailyCashFlow[] = [
                    'date' => $this->formatDate($dateKey, $userId),
                    'date_raw' => $dateKey,
                    'inflow' => $inflow,
                    'outflow' => $outflow,
                    'net' => $inflow - $outflow,
                    'running_balance' => $runningBalance
                ];
            }

            $start->addDay();
        }

        // Weekly cash flow
        $weeklyCashFlow = $transactions->groupBy(function ($tx) {
            return Carbon::parse($tx->date)->startOfWeek()->format('Y-m-d');
        })->map(function ($group, $weekStart) {
            return [
                'week_start' => $weekStart,
                'inflow' => $group->where('type', 'credit')->sum('converted_amount'),
                'outflow' => $group->where('type', 'debit')->sum('converted_amount'),
                'net' => $group->where('type', 'credit')->sum('converted_amount') - $group->where('type', 'debit')->sum('converted_amount')
            ];
        })->values();

        // Cash flow by day of week
        $dayOfWeekFlow = $transactions->groupBy(function ($tx) {
            return Carbon::parse($tx->date)->format('l');
        })->map(function ($group, $dayName) {
            return [
                'day' => $dayName,
                'avg_inflow' => $group->where('type', 'credit')->avg('converted_amount') ?? 0,
                'avg_outflow' => $group->where('type', 'debit')->avg('converted_amount') ?? 0,
                'transaction_count' => $group->count()
            ];
        });

        // Largest inflows and outflows
        $largestInflows = $transactions->where('type', 'credit')
            ->sortByDesc('converted_amount')
            ->take(5)
            ->map(function ($tx) use ($userId) {
                return [
                    'date' => $this->formatDate($tx->date, $userId),
                    'description' => $tx->description ?? 'N/A',
                    'amount' => $tx->converted_amount
                ];
            })->values();

        $largestOutflows = $transactions->where('type', 'debit')
            ->sortByDesc('converted_amount')
            ->take(5)
            ->map(function ($tx) use ($userId) {
                return [
                    'date' => $this->formatDate($tx->date, $userId),
                    'description' => $tx->description ?? 'N/A',
                    'amount' => $tx->converted_amount
                ];
            })->values();

        return [
            'summary' => [
                'total_inflow' => $totalInflow,
                'total_outflow' => $totalOutflow,
                'net_cash_flow' => $netCashFlow,
                'cash_flow_ratio' => $totalOutflow > 0 ? round($totalInflow / $totalOutflow, 2) : 0,
                'is_positive' => $netCashFlow >= 0
            ],
            'daily_cash_flow' => $dailyCashFlow,
            'weekly_cash_flow' => $weeklyCashFlow,
            'day_of_week_analysis' => $dayOfWeekFlow,
            'largest_inflows' => $largestInflows,
            'largest_outflows' => $largestOutflows
        ];
    }

    /**
     * Generate Budget Performance Report
     */
    private function generateBudgetReport($userId, $startDate, $endDate, $currencySymbol, $currencyService, $userCurrency)
    {
        // Get user's budgets with their items
        $budgets = \App\Models\Budget::where('user_id', $userId)
            ->with('items.category')
            ->get();

        if ($budgets->isEmpty()) {
            return [
                'has_budgets' => false,
                'message' => 'No budgets found. Create budgets to track your spending against limits.'
            ];
        }

        $budgetPerformance = [];
        $totalBudgeted = 0;
        $totalSpent = 0;

        foreach ($budgets as $budget) {
            // Get budget items with category relationship already loaded
            $budgetItems = $budget->items;

            foreach ($budgetItems as $item) {
                $categoryId = $item->category_id;
                $budgetedAmount = (float) $item->limit_amount; // Fixed: use limit_amount instead of amount
                $spentFromBudget = (float) $item->spent_amount; // Use spent_amount from budget item

                // Convert budget amount if needed (budgets are stored in INR base)
                if ($currencyService && $userCurrency !== 'INR') {
                    try {
                        $budgetedAmount = $currencyService->convertFromINR($budgetedAmount, $userCurrency);
                        $spentFromBudget = $currencyService->convertFromINR($spentFromBudget, $userCurrency);
                    } catch (\Exception $e) {
                        // Keep original amount
                    }
                }

                $category = $item->category;
                $percentage = $budgetedAmount > 0 ? round(($spentFromBudget / $budgetedAmount) * 100, 1) : 0;
                $remaining = $budgetedAmount - $spentFromBudget;

                $budgetPerformance[] = [
                    'budget_name' => $budget->name,
                    'budget_period' => date('F Y', mktime(0, 0, 0, $budget->month, 1, $budget->year)),
                    'category' => $category ? $category->name : 'Unknown',
                    'icon' => $category ? $category->icon : 'fa-tag',
                    'color' => $category ? $category->color : '#6B7280',
                    'budgeted' => $budgetedAmount,
                    'spent' => $spentFromBudget,
                    'remaining' => $remaining,
                    'percentage' => $percentage,
                    'status' => $percentage > 100 ? 'over' : ($percentage > 80 ? 'warning' : 'good')
                ];

                $totalBudgeted += $budgetedAmount;
                $totalSpent += $spentFromBudget;
            }
        }

        // Sort by percentage (highest first to show overspending)
        usort($budgetPerformance, function ($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        $overBudgetCount = count(array_filter($budgetPerformance, fn($b) => $b['percentage'] > 100));
        $warningCount = count(array_filter($budgetPerformance, fn($b) => $b['percentage'] > 80 && $b['percentage'] <= 100));

        return [
            'has_budgets' => true,
            'summary' => [
                'total_budgeted' => $totalBudgeted,
                'total_spent' => $totalSpent,
                'total_remaining' => $totalBudgeted - $totalSpent,
                'overall_percentage' => $totalBudgeted > 0 ? round(($totalSpent / $totalBudgeted) * 100, 1) : 0,
                'budget_count' => count($budgetPerformance),
                'over_budget_count' => $overBudgetCount,
                'warning_count' => $warningCount
            ],
            'categories' => $budgetPerformance
        ];
    }

    /**
     * Generate Tax Summary Report
     */
    private function generateTaxReport($transactions, $startDate, $endDate, $currencySymbol, $userId)
    {
        $totalIncome = $transactions->where('type', 'credit')->sum('converted_amount');
        $totalExpenses = $transactions->where('type', 'debit')->sum('converted_amount');

        // Define tax-deductible categories (common ones)
        $deductibleCategories = ['Business', 'Medical', 'Education', 'Charity', 'Home Office', 'Professional Services'];

        // Get cached categories for efficient lookup
        $cachedCategories = \App\Models\Category::getCachedForUser($userId);

        // Get expense breakdown - use cached category lookup
        $expensesByCategory = $transactions->where('type', 'debit')
            ->groupBy('category_id')
            ->map(function ($group) use ($deductibleCategories, $cachedCategories) {
                $cat = $group->first()->category_id ? $cachedCategories->firstWhere('id', $group->first()->category_id) : null;
                $categoryName = $cat ? $cat->name : 'Uncategorized';
                $isPotentiallyDeductible = in_array($categoryName, is_array($deductibleCategories) ? $deductibleCategories : []);

                return [
                    'category' => $categoryName,
                    'amount' => $group->sum('converted_amount'),
                    'count' => $group->count(),
                    'potentially_deductible' => $isPotentiallyDeductible
                ];
            })->sortByDesc('amount')->values();

        // Income sources - use cached category lookup
        $incomeSources = $transactions->where('type', 'credit')
            ->groupBy('category_id')
            ->map(function ($group) use ($cachedCategories) {
                $cat = $group->first()->category_id ? $cachedCategories->firstWhere('id', $group->first()->category_id) : null;
                return [
                    'source' => $cat ? $cat->name : 'Other Income',
                    'amount' => $group->sum('converted_amount'),
                    'count' => $group->count()
                ];
            })->sortByDesc('amount')->values();

        // Quarterly breakdown
        $quarterlyData = [];
        $year = Carbon::parse($startDate)->year;
        for ($q = 1; $q <= 4; $q++) {
            $quarterStart = Carbon::create($year, ($q - 1) * 3 + 1, 1)->startOfDay();
            $quarterEnd = $quarterStart->copy()->addMonths(3)->subDay()->endOfDay();

            $quarterTransactions = $transactions->filter(function ($tx) use ($quarterStart, $quarterEnd) {
                $date = Carbon::parse($tx->date);
                return $date->gte($quarterStart) && $date->lte($quarterEnd);
            });

            $quarterlyData[] = [
                'quarter' => 'Q' . $q,
                'period' => $quarterStart->format('M') . ' - ' . $quarterEnd->format('M Y'),
                'income' => $quarterTransactions->where('type', 'credit')->sum('converted_amount'),
                'expenses' => $quarterTransactions->where('type', 'debit')->sum('converted_amount'),
                'net' => $quarterTransactions->where('type', 'credit')->sum('converted_amount') -
                    $quarterTransactions->where('type', 'debit')->sum('converted_amount')
            ];
        }

        // Large transactions (potential audit flags)
        $largeTransactions = $transactions->filter(function ($tx) {
            return $tx->converted_amount >= 10000; // Threshold for large transactions
        })->map(function ($tx) use ($userId) {
            return [
                'date' => $this->formatDate($tx->date, $userId),
                'description' => $tx->description ?? 'N/A',
                'type' => $tx->type,
                'amount' => $tx->converted_amount
            ];
        })->values();

        $potentiallyDeductible = $expensesByCategory->where('potentially_deductible', true)->sum('amount');

        return [
            'summary' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'net_income' => $totalIncome - $totalExpenses,
                'potentially_deductible' => $potentiallyDeductible,
                'tax_year' => $year
            ],
            'income_sources' => $incomeSources,
            'expense_categories' => $expensesByCategory,
            'quarterly_breakdown' => $quarterlyData,
            'large_transactions' => $largeTransactions
        ];
    }

    public function exportConsolidated(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to export data');
        }

        try {
            $format = $request->input('format', 'csv');
            $startDate = $request->input('start_date', now()->subMonth()->toDateString());
            $endDate = $request->input('end_date', now()->toDateString());

            // Get user info
            $user = User::find($userId);
            if (!$user) {
                return redirect()->route('login')->with('error', 'User not found');
            }

            $userSetting = app('user.settings');
            $userCurrency = strtoupper(trim($userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR')));

            // Check if CurrencyService exists
            if (!app()->bound(\App\Services\CurrencyService::class)) {
                $currencyService = null;
            } else {
                $currencyService = app(\App\Services\CurrencyService::class);
            }

            // Get currency symbol - ensure we have a valid currency code
            $currencyConfig = config('currency.currencies');
            $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? $currencyConfig[config('currency.default', 'INR')]['symbol'] ?? '₹';

            // Gather all data
            $consolidatedData = $this->gatherConsolidatedData($userId, $startDate, $endDate, $currencyService, $userCurrency, $currencySymbol);

            $filename = 'consolidated_report_' . date('Y-m-d_His');

            switch ($format) {
                case 'csv':
                    return $this->exportConsolidatedCSV($consolidatedData, $filename);

                case 'excel':
                    return $this->exportConsolidatedExcel($consolidatedData, $filename);

                case 'pdf':
                    return $this->exportConsolidatedPDF($consolidatedData, $filename, $user, $currencySymbol);

                default:
                    return redirect()->back()->with('error', 'Invalid export format');
            }

        } catch (\Exception $e) {
            \Log::error('Consolidated export failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to export consolidated report: ' . $e->getMessage());
        }
    }

    private function gatherConsolidatedData($userId, $startDate, $endDate, $currencyService, $userCurrency, $currencySymbol)
    {
        $data = [];

        // 1. Transactions
        $transactions = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('category')
            ->orderBy('date', 'desc')
            ->get();

        // Convert amounts once and keep a numeric collection for consistent totals.
        // Also preserve the stored currency and original amount so the PDF can show amounts in the
        // same currency the transaction was added in.
        $currencyConfig = config('currency.currencies');

        $convertedCollection = $transactions->map(function ($tx) use ($currencyService, $userCurrency) {
            $storedCurrency = $tx->currency ?? ($currencyService ? $currencyService->getBaseCurrency() : 'INR');
            if ($currencyService) {
                try {
                    $converted = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
                } catch (\Exception $e) {
                    $converted = (float) $tx->amount;
                }
            } else {
                $converted = (float) $tx->amount;
            }

            return (object) [
                'id' => $tx->id,
                'date' => $tx->date,
                'description' => $tx->description,
                'type' => $tx->type,
                // original stored amount and currency
                'amount_raw' => (float) $tx->amount,
                'currency' => $storedCurrency,
                // numeric converted amount into user's currency for cross-currency totals
                'converted_amount' => $converted,
                'category' => $tx->category,
                'merchant' => $tx->merchant,
                'notes' => $tx->notes
            ];
        });

        // Get cached categories for efficient lookup
        $cachedCategories = \App\Models\Category::getCachedForUser($userId);

        $data['transactions'] = $convertedCollection->map(function ($tx) use ($currencyConfig, $userId, $currencySymbol, $cachedCategories) {
            // Resolve category name - use cached lookup
            $categoryName = 'Uncategorized';
            if (!empty($tx->category) && isset($tx->category->name)) {
                $categoryName = $tx->category->name;
            } elseif (!empty($tx->notes) && preg_match('/\[Category:\s*(.+?)\]/', $tx->notes, $matches)) {
                $categoryIdentifier = trim($matches[1]);
                if (is_numeric($categoryIdentifier)) {
                    $category = $cachedCategories->firstWhere('id', $categoryIdentifier);
                    $categoryName = $category ? $category->name : $categoryIdentifier;
                } else {
                    $categoryName = ucwords(str_replace('-', ' ', $categoryIdentifier));
                }
            }

            // Determine symbol for the original stored currency (fallback to user's symbol)
            $storedSymbol = $currencyConfig[$tx->currency]['symbol'] ?? $currencySymbol;

            return [
                'date' => $this->formatDate($tx->date, $userId),
                'description' => $tx->description ?? 'N/A',
                'type' => ucfirst($tx->type ?? 'debit'),
                // show transaction amount in the currency it was stored in
                'amount' => $storedSymbol . number_format($tx->amount_raw, 2),
                'amount_raw' => $tx->amount_raw,
                'currency' => $tx->currency,
                'category' => $categoryName,
                'merchant' => $tx->merchant ?? '-',
                // also include converted amount into user's display currency for summaries/preferred display
                'converted_amount' => $tx->converted_amount,
                'converted_amount_display' => $currencySymbol . number_format($tx->converted_amount, 2),
            ];
        });

        // 2. Budgets (if exists)
        if (class_exists(\App\Models\Budget::class)) {
            $budgets = \App\Models\Budget::where('user_id', $userId)
                ->with('items.category')
                ->get();

            $data['budgets'] = $budgets->map(function ($budget) use ($currencySymbol, $currencyService, $userCurrency) {
                $totalLimit = (float) ($budget->total_limit ?? 0);
                $totalSpent = (float) $budget->total_spent;

                // Convert budget amounts from INR (base currency) to user's display currency
                if ($currencyService && $userCurrency !== 'INR') {
                    try {
                        $totalLimit = $currencyService->convertFromINR($totalLimit, $userCurrency);
                        $totalSpent = $currencyService->convertFromINR($totalSpent, $userCurrency);
                    } catch (\Exception $e) {
                        // Keep original amounts if conversion fails
                    }
                }

                $remaining = $totalLimit - $totalSpent;

                return [
                    'name' => $budget->name ?? 'N/A',
                    'amount' => $currencySymbol . number_format($totalLimit, 2),
                    'spent' => $currencySymbol . number_format($totalSpent, 2),
                    'remaining' => $currencySymbol . number_format($remaining, 2),
                ];
            });
        } else {
            $data['budgets'] = collect([]);
        }

        // 3. Goals (if exists)
        if (class_exists(\App\Models\Goal::class)) {
            $goals = \App\Models\Goal::where('user_id', $userId)->get();
            $data['goals'] = $goals->map(function ($goal) use ($currencySymbol, $currencyService, $userCurrency) {
                $targetAmount = (float) ($goal->target_amount ?? 0);
                $currentAmount = (float) ($goal->current_amount ?? 0);

                // Convert goal amounts from INR (base currency) to user's display currency
                if ($currencyService && $userCurrency !== 'INR') {
                    try {
                        $targetAmount = $currencyService->convertFromINR($targetAmount, $userCurrency);
                        $currentAmount = $currencyService->convertFromINR($currentAmount, $userCurrency);
                    } catch (\Exception $e) {
                        // Keep original amounts if conversion fails
                    }
                }

                return [
                    'name' => $goal->name ?? 'N/A',
                    'target' => $currencySymbol . number_format($targetAmount, 2),
                    'current' => $currencySymbol . number_format($currentAmount, 2),
                    'progress' => round(($currentAmount / ($targetAmount ?: 1)) * 100, 2) . '%',
                ];
            });
        } else {
            $data['goals'] = collect([]);
        }

        // 4. Bank Accounts (removed)
        $data['bank_accounts'] = collect([]);

        // 5. Summary Statistics - always show totals in the user's display currency
        // This ensures consistent reporting regardless of transaction currencies
        $totalIncome = $convertedCollection->where('type', 'credit')->sum('converted_amount');
        $totalExpenses = $convertedCollection->where('type', 'debit')->sum('converted_amount');

        $netBalance = $totalIncome - $totalExpenses;

        $data['summary'] = [
            'total_income' => $currencySymbol . number_format($totalIncome, 2),
            'total_income_raw' => $totalIncome,
            'total_expenses' => $currencySymbol . number_format($totalExpenses, 2),
            'total_expenses_raw' => $totalExpenses,
            'net_balance' => $currencySymbol . number_format($netBalance, 2),
            'net_balance_raw' => $netBalance,
            'summary_currency' => $userCurrency,
            'transaction_count' => $convertedCollection->count(),
            'date_range' => Carbon::parse($startDate)->format('M d, Y') . ' - ' . Carbon::parse($endDate)->format('M d, Y'),
        ];

        return $data;
    }

    private function exportConsolidatedCSV($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // Summary Section
            fputcsv($file, ['CONSOLIDATED FINANCIAL REPORT']);
            fputcsv($file, ['Generated', date('Y-m-d H:i:s')]);
            fputcsv($file, ['Date Range', $data['summary']['date_range']]);
            fputcsv($file, []);

            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Income', $data['summary']['total_income']]);
            fputcsv($file, ['Total Expenses', $data['summary']['total_expenses']]);
            fputcsv($file, ['Net Balance', $data['summary']['net_balance']]);
            fputcsv($file, ['Total Transactions', $data['summary']['transaction_count']]);
            fputcsv($file, []);

            // Transactions
            fputcsv($file, ['TRANSACTIONS']);
            // Use converted/display amount as primary Amount column and include OriginalAmount for audit
            fputcsv($file, ['Date', 'Description', 'Type', 'Amount', 'OriginalAmount', 'Category', 'Merchant']);
            foreach ($data['transactions'] as $tx) {
                // Ensure CSV columns match header: Date, Description, Type, Amount (display), OriginalAmount, Category, Merchant
                fputcsv($file, [
                    $tx['date'] ?? '',
                    $tx['description'] ?? '',
                    $tx['type'] ?? '',
                    $tx['converted_amount_display'] ?? ($tx['amount'] ?? ''),
                    $tx['amount'] ?? '',
                    $tx['category'] ?? '',
                    $tx['merchant'] ?? ''
                ]);
            }
            fputcsv($file, []);

            // Budgets
            if ($data['budgets']->isNotEmpty()) {
                fputcsv($file, ['BUDGETS']);
                fputcsv($file, ['Name', 'Budget Amount', 'Spent', 'Remaining']);
                foreach ($data['budgets'] as $budget) {
                    fputcsv($file, array_values($budget));
                }
                fputcsv($file, []);
            }

            // Goals
            if ($data['goals']->isNotEmpty()) {
                fputcsv($file, ['GOALS']);
                fputcsv($file, ['Name', 'Target', 'Current', 'Progress']);
                foreach ($data['goals'] as $goal) {
                    fputcsv($file, array_values($goal));
                }
                fputcsv($file, []);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportConsolidatedExcel($data, $filename)
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($data) {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
            echo 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';

            // Summary Sheet
            echo '<Worksheet ss:Name="Summary"><Table>';
            echo '<Row><Cell><Data ss:Type="String">CONSOLIDATED FINANCIAL REPORT</Data></Cell></Row>';
            echo '<Row><Cell><Data ss:Type="String">Generated: ' . date('Y-m-d H:i:s') . '</Data></Cell></Row>';
            echo '<Row><Cell><Data ss:Type="String">Date Range: ' . htmlspecialchars($data['summary']['date_range']) . '</Data></Cell></Row>';
            echo '<Row></Row>';
            echo '<Row><Cell><Data ss:Type="String">Total Income</Data></Cell><Cell><Data ss:Type="String">' . htmlspecialchars($data['summary']['total_income']) . '</Data></Cell></Row>';
            echo '<Row><Cell><Data ss:Type="String">Total Expenses</Data></Cell><Cell><Data ss:Type="String">' . htmlspecialchars($data['summary']['total_expenses']) . '</Data></Cell></Row>';
            echo '<Row><Cell><Data ss:Type="String">Net Balance</Data></Cell><Cell><Data ss:Type="String">' . htmlspecialchars($data['summary']['net_balance']) . '</Data></Cell></Row>';
            echo '<Row><Cell><Data ss:Type="String">Total Transactions</Data></Cell><Cell><Data ss:Type="String">' . htmlspecialchars($data['summary']['transaction_count']) . '</Data></Cell></Row>';
            echo '</Table></Worksheet>';

            // Transactions Sheet
            echo '<Worksheet ss:Name="Transactions"><Table>';
            echo '<Row>';
            foreach (['Date', 'Description', 'Type', 'Amount', 'Original Amount', 'Category', 'Merchant'] as $header) {
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>';
            }
            echo '</Row>';
            foreach ($data['transactions'] as $tx) {
                echo '<Row>';
                // Ensure order matches header: Date, Description, Type, Amount (display), Original Amount, Category, Merchant
                $cells = [
                    $tx['date'] ?? '',
                    $tx['description'] ?? '',
                    $tx['type'] ?? '',
                    $tx['converted_amount_display'] ?? ($tx['amount'] ?? ''),
                    $tx['amount'] ?? '',
                    $tx['category'] ?? '',
                    $tx['merchant'] ?? ''
                ];
                foreach ($cells as $cell) {
                    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($cell) . '</Data></Cell>';
                }
                echo '</Row>';
            }
            echo '</Table></Worksheet>';

            // Budgets Sheet
            if ($data['budgets']->isNotEmpty()) {
                echo '<Worksheet ss:Name="Budgets"><Table>';
                echo '<Row>';
                foreach (['Name', 'Budget Amount', 'Spent', 'Remaining'] as $header) {
                    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>';
                }
                echo '</Row>';
                foreach ($data['budgets'] as $budget) {
                    echo '<Row>';
                    foreach ($budget as $cell) {
                        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($cell) . '</Data></Cell>';
                    }
                    echo '</Row>';
                }
                echo '</Table></Worksheet>';
            }

            // Goals Sheet
            if ($data['goals']->isNotEmpty()) {
                echo '<Worksheet ss:Name="Goals"><Table>';
                echo '<Row>';
                foreach (['Name', 'Target', 'Current', 'Progress'] as $header) {
                    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>';
                }
                echo '</Row>';
                foreach ($data['goals'] as $goal) {
                    echo '<Row>';
                    foreach ($goal as $cell) {
                        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($cell) . '</Data></Cell>';
                    }
                    echo '</Row>';
                }
                echo '</Table></Worksheet>';
            }

            echo '</Workbook>';
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportConsolidatedPDF($data, $filename, $user, $currencySymbol)
    {
        $html = view('reports.consolidated-pdf-export', [
            'data' => $data,
            'user' => $user,
            'userId' => $user->id,
            'currencySymbol' => $currencySymbol,
            'generatedDate' => now()->format('F d, Y H:i:s')
        ])->render();

        $headers = [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "inline; filename=\"{$filename}.html\"",
        ];

        return Response::make($html, 200, $headers);
    }

    /**
     * Get Financial Health Score and Advanced Analytics
     */
    public function getFinancialHealth()
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            // Get user's currency preference
            $user = User::find($userId);
            $userSetting = app('user.settings');
            $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');

            $currencyService = app()->bound(\App\Services\CurrencyService::class)
                ? app(\App\Services\CurrencyService::class)
                : null;

            // Get last 6 months of transactions
            $sixMonthsAgo = now()->subMonths(6)->startOfMonth();
            $transactions = Transaction::where('user_id', $userId)
                ->where('date', '>=', $sixMonthsAgo)
                ->get();

            // Convert amounts
            $convertedTransactions = $transactions->map(function ($tx) use ($currencyService, $userCurrency) {
                $storedCurrency = $tx->currency ?? 'INR';
                if ($currencyService) {
                    try {
                        $tx->converted_amount = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
                    } catch (\Exception $e) {
                        $tx->converted_amount = (float) $tx->amount;
                    }
                } else {
                    $tx->converted_amount = (float) $tx->amount;
                }
                return $tx;
            });

            // Calculate monthly totals
            $monthlyData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthKey = $month->format('Y-m');
                $monthTransactions = $convertedTransactions->filter(function ($tx) use ($monthKey) {
                    return Carbon::parse($tx->date)->format('Y-m') === $monthKey;
                });

                $monthlyData[$monthKey] = [
                    'income' => $monthTransactions->where('type', 'credit')->sum('converted_amount'),
                    'expenses' => $monthTransactions->where('type', 'debit')->sum('converted_amount'),
                    'savings' => 0
                ];
                $monthlyData[$monthKey]['savings'] = $monthlyData[$monthKey]['income'] - $monthlyData[$monthKey]['expenses'];
            }

            // Calculate Savings Rate (current month)
            $currentMonthKey = now()->format('Y-m');
            $currentMonthIncome = $monthlyData[$currentMonthKey]['income'] ?? 0;
            $currentMonthExpenses = $monthlyData[$currentMonthKey]['expenses'] ?? 0;
            $savingsRate = $currentMonthIncome > 0
                ? round((($currentMonthIncome - $currentMonthExpenses) / $currentMonthIncome) * 100, 1)
                : 0;

            // Calculate Budget Adherence (based on spending consistency)
            $avgMonthlyExpenses = collect($monthlyData)->avg('expenses');
            $budgetAdherence = 100;
            if ($avgMonthlyExpenses > 0 && $currentMonthExpenses > 0) {
                $variance = abs($currentMonthExpenses - $avgMonthlyExpenses) / $avgMonthlyExpenses;
                $budgetAdherence = max(0, round(100 - ($variance * 50), 1)); // Less variance = better adherence
            }

            // Calculate Expense Trend (comparing last 3 months)
            $recentMonths = array_slice(array_values($monthlyData), -3);
            $expenseTrend = 'Stable';
            if (count($recentMonths) >= 3) {
                $first = $recentMonths[0]['expenses'];
                $last = $recentMonths[2]['expenses'];
                if ($last > $first * 1.1) {
                    $expenseTrend = 'Increasing';
                } elseif ($last < $first * 0.9) {
                    $expenseTrend = 'Decreasing';
                }
            }

            // Calculate Consistency Score (how regular are transactions)
            $transactionCounts = [];
            foreach ($monthlyData as $key => $data) {
                $transactionCounts[] = $convertedTransactions->filter(function ($tx) use ($key) {
                    return Carbon::parse($tx->date)->format('Y-m') === $key;
                })->count();
            }
            $avgCount = collect($transactionCounts)->avg();
            $stdDev = $this->calculateStdDev($transactionCounts);
            $consistencyScore = $avgCount > 0 ? max(0, round(100 - ($stdDev / $avgCount * 50), 1)) : 50;

            // Calculate Overall Financial Health Score
            $healthScore = $this->calculateHealthScore($savingsRate, $budgetAdherence, $expenseTrend, $consistencyScore);

            // Get spending insights
            $insights = $this->generateSpendingInsights($convertedTransactions, $monthlyData);

            // Get top spending categories
            $categoryBreakdown = $this->getCategoryBreakdown($convertedTransactions->where('type', 'debit'));

            // Get merchant analysis
            $merchantAnalysis = $this->getMerchantAnalysis($convertedTransactions->where('type', 'debit'));

            return response()->json([
                'success' => true,
                'data' => [
                    'health_score' => $healthScore,
                    'savings_rate' => $savingsRate,
                    'budget_adherence' => $budgetAdherence,
                    'expense_trend' => $expenseTrend,
                    'consistency_score' => $consistencyScore,
                    'monthly_data' => $monthlyData,
                    'insights' => $insights,
                    'category_breakdown' => $categoryBreakdown,
                    'merchant_analysis' => $merchantAnalysis,
                    'score_label' => $this->getScoreLabel($healthScore)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Financial health calculation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate financial health: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate standard deviation
     */
    private function calculateStdDev(array $values)
    {
        $n = count($values);
        if ($n === 0)
            return 0;

        $mean = array_sum($values) / $n;
        $variance = array_sum(array_map(function ($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / $n;

        return sqrt($variance);
    }

    /**
     * Calculate overall health score
     */
    private function calculateHealthScore($savingsRate, $budgetAdherence, $expenseTrend, $consistencyScore)
    {
        // Weights for each component
        $weights = [
            'savings' => 0.35,
            'budget' => 0.25,
            'trend' => 0.20,
            'consistency' => 0.20
        ];

        // Convert savings rate to score (0-100)
        $savingsScore = min(100, max(0, $savingsRate * 2)); // 50% savings = 100 score

        // Convert expense trend to score
        switch ($expenseTrend) {
            case 'Decreasing':
                $trendScore = 100;
                break;
            case 'Stable':
                $trendScore = 75;
                break;
            case 'Increasing':
                $trendScore = 40;
                break;
            default:
                $trendScore = 50;
        }

        $totalScore =
            ($savingsScore * $weights['savings']) +
            ($budgetAdherence * $weights['budget']) +
            ($trendScore * $weights['trend']) +
            ($consistencyScore * $weights['consistency']);

        return round($totalScore);
    }

    /**
     * Get score label based on score value
     */
    private function getScoreLabel($score)
    {
        if ($score >= 85)
            return 'Excellent';
        if ($score >= 70)
            return 'Good';
        if ($score >= 50)
            return 'Fair';
        if ($score >= 30)
            return 'Needs Work';
        return 'Critical';
    }

    /**
     * Generate spending insights
     */
    private function generateSpendingInsights($transactions, $monthlyData)
    {
        $insights = [];

        // Insight 1: Spending trend
        $monthlyExpenses = array_column(array_values($monthlyData), 'expenses');
        if (count($monthlyExpenses) >= 2) {
            $lastMonth = $monthlyExpenses[count($monthlyExpenses) - 1];
            $prevMonth = $monthlyExpenses[count($monthlyExpenses) - 2];

            if ($prevMonth > 0) {
                $change = (($lastMonth - $prevMonth) / $prevMonth) * 100;
                if ($change > 10) {
                    $insights[] = [
                        'type' => 'warning',
                        'icon' => 'fa-arrow-trend-up',
                        'title' => 'Spending Increased',
                        'message' => 'Your spending increased by ' . round($change, 1) . '% compared to last month.'
                    ];
                } elseif ($change < -10) {
                    $insights[] = [
                        'type' => 'success',
                        'icon' => 'fa-arrow-trend-down',
                        'title' => 'Great Job!',
                        'message' => 'You reduced spending by ' . abs(round($change, 1)) . '% compared to last month.'
                    ];
                }
            }
        }

        // Insight 2: Savings rate
        $currentMonth = array_values($monthlyData)[count($monthlyData) - 1];
        if ($currentMonth['income'] > 0) {
            $savingsRate = (($currentMonth['income'] - $currentMonth['expenses']) / $currentMonth['income']) * 100;
            if ($savingsRate >= 20) {
                $insights[] = [
                    'type' => 'success',
                    'icon' => 'fa-piggy-bank',
                    'title' => 'Healthy Savings',
                    'message' => 'You\'re saving ' . round($savingsRate, 1) . '% of your income. Keep it up!'
                ];
            } elseif ($savingsRate < 10 && $savingsRate >= 0) {
                $insights[] = [
                    'type' => 'warning',
                    'icon' => 'fa-piggy-bank',
                    'title' => 'Low Savings Rate',
                    'message' => 'Try to increase your savings rate above 10% for better financial security.'
                ];
            } elseif ($savingsRate < 0) {
                $insights[] = [
                    'type' => 'danger',
                    'icon' => 'fa-exclamation-triangle',
                    'title' => 'Overspending Alert',
                    'message' => 'You\'re spending more than you earn. Review your expenses.'
                ];
            }
        }

        // Get cached categories for efficient lookup
        $cachedCategories = \App\Models\Category::getCachedForUser(session('user_id'));

        // Insight 3: Largest expense category
        $categoryTotals = $transactions->where('type', 'debit')
            ->filter(function ($tx) {
                return Carbon::parse($tx->date)->isCurrentMonth();
            })
            ->groupBy('category_id')
            ->map(function ($group) {
                return $group->sum('converted_amount');
            })
            ->sortDesc();

        if ($categoryTotals->isNotEmpty()) {
            $topCategory = $categoryTotals->keys()->first();
            $topAmount = $categoryTotals->first();
            $totalExpenses = $categoryTotals->sum();

            if ($totalExpenses > 0) {
                $percentage = ($topAmount / $totalExpenses) * 100;
                if ($percentage > 40) {
                    $catObj = $topCategory ? $cachedCategories->firstWhere('id', $topCategory) : null;
                    $categoryName = $catObj ? $catObj->name : 'Uncategorized';
                    $insights[] = [
                        'type' => 'info',
                        'icon' => 'fa-chart-pie',
                        'title' => 'Spending Concentration',
                        'message' => round($percentage, 0) . '% of your expenses are in ' . $categoryName . '. Consider diversifying.'
                    ];
                }
            }
        }

        // Insight 4: Weekend vs Weekday spending
        $currentMonthTx = $transactions->where('type', 'debit')
            ->filter(function ($tx) {
                return Carbon::parse($tx->date)->isCurrentMonth();
            });

        $weekendSpending = $currentMonthTx->filter(function ($tx) {
            return Carbon::parse($tx->date)->isWeekend();
        })->sum('converted_amount');

        $weekdaySpending = $currentMonthTx->filter(function ($tx) {
            return !Carbon::parse($tx->date)->isWeekend();
        })->sum('converted_amount');

        if ($weekendSpending > 0 && $weekdaySpending > 0) {
            $weekendDays = 8; // ~8 weekend days in a month
            $weekdayDays = 22; // ~22 weekdays in a month
            $avgWeekend = $weekendSpending / $weekendDays;
            $avgWeekday = $weekdaySpending / $weekdayDays;

            if ($avgWeekend > $avgWeekday * 1.5) {
                $insights[] = [
                    'type' => 'info',
                    'icon' => 'fa-calendar-week',
                    'title' => 'Weekend Spender',
                    'message' => 'You spend significantly more on weekends. Consider planning weekend activities.'
                ];
            }
        }

        return array_slice($insights, 0, 4); // Return max 4 insights
    }

    /**
     * Get category breakdown - use cached categories
     */
    private function getCategoryBreakdown($transactions)
    {
        $cachedCategories = \App\Models\Category::getCachedForUser(session('user_id'));

        $breakdown = $transactions->groupBy('category_id')
            ->map(function ($group) use ($cachedCategories) {
                $categoryId = $group->first()->category_id;
                $category = $categoryId ? $cachedCategories->firstWhere('id', $categoryId) : null;

                return [
                    'category_id' => $categoryId,
                    'name' => $category ? $category->name : 'Uncategorized',
                    'icon' => $category ? $category->icon : 'fa-question',
                    'color' => $category ? $category->color : '#6B7280',
                    'total' => $group->sum('converted_amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->take(10);

        $grandTotal = $breakdown->sum('total');

        return $breakdown->map(function ($item) use ($grandTotal) {
            $item['percentage'] = $grandTotal > 0 ? round(($item['total'] / $grandTotal) * 100, 1) : 0;
            return $item;
        });
    }

    /**
     * Get merchant analysis
     */
    private function getMerchantAnalysis($transactions)
    {
        return $transactions->filter(function ($tx) {
            return !empty($tx->merchant);
        })
            ->groupBy('merchant')
            ->map(function ($group, $merchant) {
                return [
                    'merchant' => $merchant,
                    'total' => $group->sum('converted_amount'),
                    'count' => $group->count(),
                    'avg_transaction' => $group->avg('converted_amount')
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->take(10);
    }

    /**
     * Get Year-over-Year Comparison
     */
    public function getYearComparison()
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            $user = User::find($userId);
            $userSetting = app('user.settings');
            $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');

            $currencyService = app()->bound(\App\Services\CurrencyService::class)
                ? app(\App\Services\CurrencyService::class)
                : null;

            $currentYear = now()->year;
            $lastYear = $currentYear - 1;

            // Get transactions for both years
            $transactions = Transaction::where('user_id', $userId)
                ->whereYear('date', '>=', $lastYear)
                ->get();

            // Convert amounts
            $convertedTransactions = $transactions->map(function ($tx) use ($currencyService, $userCurrency) {
                $storedCurrency = $tx->currency ?? 'INR';
                if ($currencyService) {
                    try {
                        $tx->converted_amount = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
                    } catch (\Exception $e) {
                        $tx->converted_amount = (float) $tx->amount;
                    }
                } else {
                    $tx->converted_amount = (float) $tx->amount;
                }
                return $tx;
            });

            // Current year data
            $currentYearTx = $convertedTransactions->filter(function ($tx) use ($currentYear) {
                return Carbon::parse($tx->date)->year === $currentYear;
            });

            // Last year data
            $lastYearTx = $convertedTransactions->filter(function ($tx) use ($lastYear) {
                return Carbon::parse($tx->date)->year === $lastYear;
            });

            // Monthly comparison
            $monthlyComparison = [];
            for ($month = 1; $month <= 12; $month++) {
                $currentMonthTx = $currentYearTx->filter(function ($tx) use ($month) {
                    return Carbon::parse($tx->date)->month === $month;
                });
                $lastMonthTx = $lastYearTx->filter(function ($tx) use ($month) {
                    return Carbon::parse($tx->date)->month === $month;
                });

                $monthlyComparison[] = [
                    'month' => Carbon::create()->month($month)->format('M'),
                    'current_income' => $currentMonthTx->where('type', 'credit')->sum('converted_amount'),
                    'current_expenses' => $currentMonthTx->where('type', 'debit')->sum('converted_amount'),
                    'last_income' => $lastMonthTx->where('type', 'credit')->sum('converted_amount'),
                    'last_expenses' => $lastMonthTx->where('type', 'debit')->sum('converted_amount')
                ];
            }

            // Annual totals
            $currentYearIncome = $currentYearTx->where('type', 'credit')->sum('converted_amount');
            $currentYearExpenses = $currentYearTx->where('type', 'debit')->sum('converted_amount');
            $lastYearIncome = $lastYearTx->where('type', 'credit')->sum('converted_amount');
            $lastYearExpenses = $lastYearTx->where('type', 'debit')->sum('converted_amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'current_year' => $currentYear,
                    'last_year' => $lastYear,
                    'monthly_comparison' => $monthlyComparison,
                    'annual_summary' => [
                        'current' => [
                            'income' => $currentYearIncome,
                            'expenses' => $currentYearExpenses,
                            'savings' => $currentYearIncome - $currentYearExpenses
                        ],
                        'last' => [
                            'income' => $lastYearIncome,
                            'expenses' => $lastYearExpenses,
                            'savings' => $lastYearIncome - $lastYearExpenses
                        ],
                        'change' => [
                            'income' => $lastYearIncome > 0 ? round((($currentYearIncome - $lastYearIncome) / $lastYearIncome) * 100, 1) : 0,
                            'expenses' => $lastYearExpenses > 0 ? round((($currentYearExpenses - $lastYearExpenses) / $lastYearExpenses) * 100, 1) : 0
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Year comparison failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load year comparison: ' . $e->getMessage()
            ], 500);
        }
    }
}


