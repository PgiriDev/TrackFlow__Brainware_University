@extends('layouts.app')

@section('title', 'Reports')
@section('breadcrumb', 'Reports')

@push('styles')
    <style>
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .animate-modal-in {
            animation: modalFadeIn 0.2s ease-out forwards;
        }
    </style>
@endpush

@section('content')
    <!-- Colorful Glassmorphism Page Background - Slate/Blue Theme -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-slate-100 via-gray-50 to-blue-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-slate-300/40 to-gray-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-slate-600/10 dark:to-gray-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-blue-300/40 to-indigo-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-blue-600/10 dark:to-indigo-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-gray-300/30 to-slate-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-gray-600/10 dark:to-slate-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-sky-300/30 to-blue-400/30 rounded-full blur-3xl dark:from-sky-600/10 dark:to-blue-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-slate-300/30 to-zinc-400/30 rounded-full blur-3xl dark:from-slate-600/10 dark:to-zinc-700/10">
        </div>
    </div>

    <div class="animate-fade-in relative">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Financial Reports</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Analyze your financial data with detailed reports</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <button onclick="generateReport()"
                    class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Generate Report
                </button>
            </div>
        </div>

        <!-- Report Types Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Income vs Expenses Report -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow hover:shadow-lg transition-shadow cursor-pointer"
                onclick="showReportModal('income-expense')">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-blue-500/10 dark:bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-3xl text-blue-500"></i>
                    </div>
                    <span
                        class="px-3 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full">
                        Popular
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Income vs Expenses</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Compare your income and expenses over time with detailed breakdowns.
                </p>
                <div class="flex items-center text-sm text-blue-600 dark:text-blue-400 font-medium">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>

            <!-- Category Analysis -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow hover:shadow-lg transition-shadow cursor-pointer"
                onclick="showReportModal('category')">
                <div class="flex items-start justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-purple-500/10 dark:bg-purple-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-pie text-3xl text-purple-500"></i>
                    </div>
                    <span
                        class="px-3 py-1 text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full">
                        Popular
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Category Analysis</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    See where your money goes with detailed spending by category.
                </p>
                <div class="flex items-center text-sm text-purple-600 dark:text-purple-400 font-medium">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>

            <!-- Monthly Summary -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow hover:shadow-lg transition-shadow cursor-pointer"
                onclick="showReportModal('monthly')">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-green-500/10 dark:bg-green-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-3xl text-green-500"></i>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Monthly Summary</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Get a comprehensive overview of your monthly financial activity.
                </p>
                <div class="flex items-center text-sm text-green-600 dark:text-green-400 font-medium">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>

            <!-- Cash Flow Analysis -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow hover:shadow-lg transition-shadow cursor-pointer"
                onclick="showReportModal('cashflow')">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-teal-500/10 dark:bg-teal-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-3xl text-teal-500"></i>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Cash Flow Analysis</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Track the flow of money in and out of your accounts.
                </p>
                <div class="flex items-center text-sm text-teal-600 dark:text-teal-400 font-medium">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>

            <!-- Budget Performance -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow hover:shadow-lg transition-shadow cursor-pointer"
                onclick="showReportModal('budget')">
                <div class="flex items-start justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-orange-500/10 dark:bg-orange-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bullseye text-3xl text-orange-500"></i>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Budget Performance</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Analyze how well you're sticking to your budgets.
                </p>
                <div class="flex items-center text-sm text-orange-600 dark:text-orange-400 font-medium">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>

            <!-- Tax Summary -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow hover:shadow-lg transition-shadow cursor-pointer"
                onclick="showReportModal('tax')">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-red-500/10 dark:bg-red-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-3xl text-red-500"></i>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Tax Summary</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Prepare for tax season with organized financial summaries.
                </p>
                <div class="flex items-center text-sm text-red-600 dark:text-red-400 font-medium">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>

            <!-- Spending Insights -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow hover:shadow-lg transition-shadow cursor-pointer"
                onclick="showReportModal('insights')">
                <div class="flex items-start justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-brain text-3xl text-indigo-500"></i>
                    </div>
                    <span
                        class="px-3 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                        AI Insights
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Spending Insights</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Get AI-powered insights about your spending patterns and trends.
                </p>
                <div class="flex items-center text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                    <span>View Insights</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>

            <!-- Savings Analysis -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow hover:shadow-lg transition-shadow cursor-pointer"
                onclick="showReportModal('savings')">
                <div class="flex items-start justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-piggy-bank text-3xl text-emerald-500"></i>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Savings Analysis</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Track your savings rate and identify opportunities to save more.
                </p>
                <div class="flex items-center text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                    <span>View Analysis</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>

            <!-- Year Comparison -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow hover:shadow-lg transition-shadow cursor-pointer"
                onclick="showReportModal('comparison')">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-cyan-500/10 dark:bg-cyan-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-balance-scale-left text-3xl text-cyan-500"></i>
                    </div>
                    <span
                        class="px-3 py-1 text-xs font-medium bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 rounded-full">
                        New
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Year Comparison</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Compare your financial performance year over year.
                </p>
                <div class="flex items-center text-sm text-cyan-600 dark:text-cyan-400 font-medium">
                    <span>View Comparison</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>
        </div>

        <!-- Financial Health Score Card -->
        <div class="bg-gradient-to-br from-primary-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-8 text-white"
            id="financialHealthCard">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heartbeat text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">Financial Health Score</h2>
                            <p class="text-white/80 text-sm">Based on your spending patterns and savings</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="bg-white/10 rounded-lg p-3">
                            <p class="text-white/70 text-xs mb-1">Savings Rate</p>
                            <p class="text-lg font-bold" id="healthSavingsRate">---%</p>
                        </div>
                        <div class="bg-white/10 rounded-lg p-3">
                            <p class="text-white/70 text-xs mb-1">Budget Adherence</p>
                            <p class="text-lg font-bold" id="healthBudgetAdherence">---%</p>
                        </div>
                        <div class="bg-white/10 rounded-lg p-3">
                            <p class="text-white/70 text-xs mb-1">Expense Trend</p>
                            <p class="text-lg font-bold" id="healthExpenseTrend">---</p>
                        </div>
                        <div class="bg-white/10 rounded-lg p-3">
                            <p class="text-white/70 text-xs mb-1">Consistency</p>
                            <p class="text-lg font-bold" id="healthConsistency">---</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="relative w-32 h-32">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="8" />
                            <circle id="healthScoreCircle" cx="50" cy="50" r="45" fill="none" stroke="white"
                                stroke-width="8" stroke-dasharray="283" stroke-dashoffset="283" stroke-linecap="round"
                                style="transition: stroke-dashoffset 1s ease-out" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                            <span class="text-3xl font-bold" id="healthScoreValue">--</span>
                            <span class="text-xs text-white/70">out of 100</span>
                        </div>
                    </div>
                    <p class="text-sm font-medium mt-2" id="healthScoreLabel">Calculating...</p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 card-shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Quick Statistics</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- This Month -->
                <div
                    class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center text-sm text-blue-700 dark:text-blue-300 font-medium mb-2">
                        <i class="fas fa-calendar-week mr-2"></i>
                        <span>This Month</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                        <span class="text-lg" id="currencySymbol1">₹</span><span id="thisMonthTotal">0.00</span>
                    </p>
                </div>

                <!-- Last Month -->
                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
                    <div class="flex items-center text-sm text-purple-700 dark:text-purple-300 font-medium mb-2">
                        <i class="fas fa-calendar mr-2"></i>
                        <span>Last Month</span>
                    </div>
                    <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                        <span class="text-lg" id="currencySymbol2">₹</span><span id="lastMonthTotal">0.00</span>
                    </p>
                </div>

                <!-- Average Daily -->
                <div
                    class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                    <div class="flex items-center text-sm text-green-700 dark:text-green-300 font-medium mb-2">
                        <i class="fas fa-chart-line mr-2"></i>
                        <span>Average Daily</span>
                    </div>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                        <span class="text-lg" id="currencySymbol3">₹</span><span id="avgDaily">0.00</span>
                    </p>
                </div>

                <!-- Total Transactions -->
                <div
                    class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl p-4 border border-orange-200 dark:border-orange-800">
                    <div class="flex items-center text-sm text-orange-700 dark:text-orange-300 font-medium mb-2">
                        <i class="fas fa-receipt mr-2"></i>
                        <span>Total Transactions</span>
                    </div>
                    <p class="text-2xl font-bold text-orange-900 dark:text-orange-100" id="totalTransactions">0</p>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 card-shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Export Data</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Download your financial data in various formats</p>
            <div class="flex flex-wrap gap-3">
                <button onclick="exportData('pdf')"
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export as PDF
                </button>
                <button onclick="exportData('excel')"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export as Excel
                </button>
                <button onclick="exportData('csv')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-file-csv mr-2"></i>
                    Export as CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4"
        style="z-index: 9999;" x-data="reportData()" @keydown.escape.window="closeReportModal()"
        @click.self="closeReportModal()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-6xl w-full max-h-[95vh] overflow-y-auto animate-modal-in"
            @click.stop>
            <!-- Modal Header -->
            <div
                class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white" id="reportModalTitle">Report</h2>
                <div class="flex items-center gap-3">
                    <button @click="exportReport('pdf')"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>PDF
                    </button>
                    <button @click="exportReport('excel')"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>Excel
                    </button>
                    <button @click="exportReport('csv')"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-file-csv mr-2"></i>CSV
                    </button>
                    <button onclick="closeReportModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 ml-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <!-- Date Range Filters -->
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start
                                Date</label>
                            <input type="date" x-model="filters.startDate" @change="loadReportData()"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                            <input type="date" x-model="filters.endDate" @change="loadReportData()"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quick
                                Range</label>
                            <select @change="applyQuickRange($event.target.value)"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                                <option value="">Custom</option>
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="this_quarter">This Quarter</option>
                                <option value="this_year">This Year</option>
                                <option value="last_30">Last 30 Days</option>
                                <option value="last_90">Last 90 Days</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button @click="loadReportData()"
                                class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                <i class="fas fa-sync mr-2"></i>Update Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Report Content -->
                <div id="reportContent">
                    <!-- Loading State -->
                    <div x-show="loading" class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">Loading report data...</p>
                    </div>

                    <!-- Dynamic Report Content Based on Type -->
                    <div x-show="!loading && reportLoaded" x-cloak>
                        <!-- Report content will be rendered dynamically -->
                        <div id="dynamicReportContent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Consolidated Report Modal -->
    <div id="generateReportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4"
        style="z-index: 9999; display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Generate Consolidated Report</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Export all your financial data in one
                        comprehensive report</p>
                </div>
                <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Report Includes -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">This report includes:</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>All Transactions</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Budget Summary</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Goals Progress</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Group Expenses</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Bank Transactions</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Financial Statistics</span>
                    </div>
                </div>
            </div>

            <!-- Date Range Selection -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Select Date Range:</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Start Date</label>
                        <input type="date" id="consolidatedStartDate"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">End Date</label>
                        <input type="date" id="consolidatedEndDate"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                    </div>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Choose Export Format:</h3>
                <div class="grid grid-cols-3 gap-4">
                    <button onclick="exportConsolidatedReport('pdf')"
                        class="flex flex-col items-center justify-center gap-3 p-6 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 border-2 border-red-200 dark:border-red-800 rounded-xl transition-all">
                        <i class="fas fa-file-pdf text-4xl text-red-600"></i>
                        <span class="font-semibold text-red-700 dark:text-red-300">PDF</span>
                    </button>
                    <button onclick="exportConsolidatedReport('excel')"
                        class="flex flex-col items-center justify-center gap-3 p-6 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 border-2 border-green-200 dark:border-green-800 rounded-xl transition-all">
                        <i class="fas fa-file-excel text-4xl text-green-600"></i>
                        <span class="font-semibold text-green-700 dark:text-green-300">Excel</span>
                    </button>
                    <button onclick="exportConsolidatedReport('csv')"
                        class="flex flex-col items-center justify-center gap-3 p-6 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 border-2 border-blue-200 dark:border-blue-800 rounded-xl transition-all">
                        <i class="fas fa-file-csv text-4xl text-blue-600"></i>
                        <span class="font-semibold text-blue-700 dark:text-blue-300">CSV</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Global variables
        let currentReportType = '';
        let reportCharts = {};

        // Alpine.js component function - must be defined before page load
        function reportData() {
            return {
                loading: false,
                reportLoaded: false,
                filters: {
                    startDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
                    endDate: new Date().toISOString().split('T')[0]
                },
                reportData: null,
                currencySymbol: '₹',

                async loadReportData() {
                    this.loading = true;
                    this.destroyAllCharts();

                    try {
                        const response = await fetch(`/reports/specific?type=${currentReportType}&start_date=${this.filters.startDate}&end_date=${this.filters.endDate}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const result = await response.json();
                            if (result.success) {
                                this.reportData = result.data;
                                this.currencySymbol = result.currency_symbol || '₹';
                                this.reportLoaded = true;

                                await this.$nextTick();
                                this.renderReportContent(currentReportType, result.data);
                            } else {
                                this.showError(result.message || 'Failed to load report');
                            }
                        } else {
                            this.showError('Failed to fetch report data');
                        }
                    } catch (error) {
                        console.error('Error loading report data:', error);
                        this.showError('An error occurred while loading the report');
                    } finally {
                        this.loading = false;
                    }
                },

                destroyAllCharts() {
                    Object.values(reportCharts).forEach(chart => {
                        if (chart) chart.destroy();
                    });
                    reportCharts = {};
                },

                showError(message) {
                    const container = document.getElementById('dynamicReportContent');
                    if (container) {
                        container.innerHTML = `
                                        <div class="text-center py-12">
                                            <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                                            <p class="text-gray-500 dark:text-gray-400">${message}</p>
                                        </div>
                                    `;
                    }
                    this.reportLoaded = true;
                },

                renderReportContent(type, data) {
                    const container = document.getElementById('dynamicReportContent');
                    if (!container) return;

                    switch (type) {
                        case 'income-expense':
                            container.innerHTML = this.renderIncomeExpenseReport(data);
                            this.initIncomeExpenseCharts(data);
                            break;
                        case 'category':
                            container.innerHTML = this.renderCategoryReport(data);
                            this.initCategoryCharts(data);
                            break;
                        case 'monthly':
                            container.innerHTML = this.renderMonthlyReport(data);
                            this.initMonthlyCharts(data);
                            break;
                        case 'cashflow':
                            container.innerHTML = this.renderCashFlowReport(data);
                            this.initCashFlowCharts(data);
                            break;
                        case 'budget':
                            container.innerHTML = this.renderBudgetReport(data);
                            break;
                        case 'tax':
                            container.innerHTML = this.renderTaxReport(data);
                            this.initTaxCharts(data);
                            break;
                        default:
                            container.innerHTML = this.renderIncomeExpenseReport(data);
                            this.initIncomeExpenseCharts(data);
                    }
                },

                // ==================== INCOME VS EXPENSES REPORT ====================
                renderIncomeExpenseReport(data) {
                    const s = data.summary || {};
                    return `
                                    <!-- Summary Cards -->
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-5 border-2 border-green-200 dark:border-green-800">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Income</span>
                                                <i class="fas fa-arrow-up text-green-600"></i>
                                            </div>
                                            <p class="text-2xl font-bold text-green-600">${this.currencySymbol}${this.formatNum(s.total_income)}</p>
                                            <p class="text-xs text-gray-500 mt-1">${s.income_count || 0} transactions</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 rounded-lg p-5 border-2 border-red-200 dark:border-red-800">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Expenses</span>
                                                <i class="fas fa-arrow-down text-red-600"></i>
                                            </div>
                                            <p class="text-2xl font-bold text-red-600">${this.currencySymbol}${this.formatNum(s.total_expenses)}</p>
                                            <p class="text-xs text-gray-500 mt-1">${s.expense_count || 0} transactions</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-5 border-2 border-blue-200 dark:border-blue-800">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Net Balance</span>
                                                <i class="fas fa-balance-scale text-blue-600"></i>
                                            </div>
                                            <p class="text-2xl font-bold ${s.net_balance >= 0 ? 'text-green-600' : 'text-red-600'}">${this.currencySymbol}${this.formatNum(s.net_balance)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg p-5 border-2 border-purple-200 dark:border-purple-800">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Savings Rate</span>
                                                <i class="fas fa-piggy-bank text-purple-600"></i>
                                            </div>
                                            <p class="text-2xl font-bold text-purple-600">${s.savings_rate || 0}%</p>
                                        </div>
                                    </div>

                                    <!-- Charts -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Income vs Expenses</h4>
                                            <div style="height: 280px; position: relative;"><canvas id="dailyTrendChart"></canvas></div>
                                        </div>
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Expense Categories</h4>
                                            <div style="height: 280px; position: relative;"><canvas id="topExpensesChart"></canvas></div>
                                        </div>
                                    </div>

                                    <!-- Top Sources -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><i class="fas fa-arrow-up text-green-500 mr-2"></i>Top Income Sources</h4>
                                            ${this.renderTopList(data.top_income_sources, 'credit')}
                                        </div>
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><i class="fas fa-arrow-down text-red-500 mr-2"></i>Top Expense Categories</h4>
                                            ${this.renderTopList(data.top_expense_categories, 'debit')}
                                        </div>
                                    </div>

                                    <!-- Transaction Table -->
                                    ${this.renderTransactionTable(data.transactions)}
                                `;
                },

                initIncomeExpenseCharts(data) {
                    // Daily Trend Chart
                    const dailyCtx = document.getElementById('dailyTrendChart');
                    if (dailyCtx && data.daily_breakdown) {
                        const labels = data.daily_breakdown.map(d => d.date);
                        reportCharts.dailyTrend = new Chart(dailyCtx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    { label: 'Income', data: data.daily_breakdown.map(d => d.income), backgroundColor: 'rgba(34, 197, 94, 0.8)' },
                                    { label: 'Expenses', data: data.daily_breakdown.map(d => d.expenses), backgroundColor: 'rgba(239, 68, 68, 0.8)' }
                                ]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                        });
                    }

                    // Top Expenses Pie Chart
                    const expCtx = document.getElementById('topExpensesChart');
                    if (expCtx && data.top_expense_categories) {
                        const colors = ['#3B82F6', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981', '#EF4444'];
                        reportCharts.topExpenses = new Chart(expCtx, {
                            type: 'doughnut',
                            data: {
                                labels: data.top_expense_categories.map(c => c.category),
                                datasets: [{ data: data.top_expense_categories.map(c => c.amount), backgroundColor: colors }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
                        });
                    }
                },

                // ==================== CATEGORY ANALYSIS REPORT ====================
                renderCategoryReport(data) {
                    const s = data.summary || {};
                    return `
                                    <!-- Summary Cards -->
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                        <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 rounded-lg p-5 border border-red-200 dark:border-red-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Expenses</p>
                                            <p class="text-2xl font-bold text-red-600">${this.currencySymbol}${this.formatNum(s.total_expenses)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-5 border border-green-200 dark:border-green-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Income</p>
                                            <p class="text-2xl font-bold text-green-600">${this.currencySymbol}${this.formatNum(s.total_income)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg p-5 border border-purple-200 dark:border-purple-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Expense Categories</p>
                                            <p class="text-2xl font-bold text-purple-600">${s.expense_categories_count || 0}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-lg p-5 border border-orange-200 dark:border-orange-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Uncategorized</p>
                                            <p class="text-2xl font-bold text-orange-600">${s.uncategorized_count || 0}</p>
                                        </div>
                                    </div>

                                    <!-- Charts -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Expense Distribution</h4>
                                            <div style="height: 320px; position: relative;"><canvas id="expenseDistChart"></canvas></div>
                                        </div>
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Income Distribution</h4>
                                            <div style="height: 320px; position: relative;"><canvas id="incomeDistChart"></canvas></div>
                                        </div>
                                    </div>

                                    <!-- Category Breakdown -->
                                    <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Expense Categories Breakdown</h4>
                                        <div class="space-y-4">
                                            ${(data.expense_categories || []).map(cat => `
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: ${cat.color}20">
                                                        <i class="fas ${cat.icon || 'fa-tag'}" style="color: ${cat.color}"></i>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex justify-between mb-1">
                                                            <span class="font-medium text-gray-900 dark:text-white">${cat.name}</span>
                                                            <span class="font-semibold text-gray-900 dark:text-white">${this.currencySymbol}${this.formatNum(cat.amount)} <span class="text-xs text-gray-500">(${cat.percentage}%)</span></span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                            <div class="h-2 rounded-full" style="width: ${cat.percentage}%; background-color: ${cat.color}"></div>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">${cat.count} transactions • Avg: ${this.currencySymbol}${this.formatNum(cat.avg_transaction)}</p>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                `;
                },

                initCategoryCharts(data) {
                    const colors = ['#3B82F6', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981', '#EF4444', '#14B8A6', '#F97316'];

                    const expCtx = document.getElementById('expenseDistChart');
                    if (expCtx && data.expense_categories) {
                        reportCharts.expenseDist = new Chart(expCtx, {
                            type: 'pie',
                            data: {
                                labels: data.expense_categories.map(c => c.name),
                                datasets: [{ data: data.expense_categories.map(c => c.amount), backgroundColor: data.expense_categories.map(c => c.color || colors[0]) }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                        });
                    }

                    const incCtx = document.getElementById('incomeDistChart');
                    if (incCtx && data.income_categories) {
                        reportCharts.incomeDist = new Chart(incCtx, {
                            type: 'pie',
                            data: {
                                labels: data.income_categories.map(c => c.name),
                                datasets: [{ data: data.income_categories.map(c => c.amount), backgroundColor: data.income_categories.map(c => c.color || colors[0]) }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                        });
                    }
                },

                // ==================== MONTHLY SUMMARY REPORT ====================
                renderMonthlyReport(data) {
                    const avg = data.averages || {};
                    const highlights = data.highlights || {};
                    return `
                                    <!-- Averages -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-5 border border-green-200 dark:border-green-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Average Monthly Income</p>
                                            <p class="text-2xl font-bold text-green-600">${this.currencySymbol}${this.formatNum(avg.avg_income)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 rounded-lg p-5 border border-red-200 dark:border-red-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Average Monthly Expenses</p>
                                            <p class="text-2xl font-bold text-red-600">${this.currencySymbol}${this.formatNum(avg.avg_expenses)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-5 border border-blue-200 dark:border-blue-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Average Monthly Savings</p>
                                            <p class="text-2xl font-bold ${avg.avg_savings >= 0 ? 'text-green-600' : 'text-red-600'}">${this.currencySymbol}${this.formatNum(avg.avg_savings)}</p>
                                        </div>
                                    </div>

                                    <!-- Chart -->
                                    <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600 mb-6">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Overview</h4>
                                        <div style="height: 300px; position: relative;"><canvas id="monthlyOverviewChart"></canvas></div>
                                    </div>

                                    <!-- Monthly Breakdown Table -->
                                    <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Breakdown</h4>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead class="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Month</th>
                                                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Income</th>
                                                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Expenses</th>
                                                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Savings</th>
                                                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Rate</th>
                                                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Transactions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${(data.monthly_breakdown || []).map(m => `
                                                        <tr class="border-t border-gray-200 dark:border-gray-600">
                                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">${m.month}</td>
                                                            <td class="px-4 py-3 text-right text-green-600">${this.currencySymbol}${this.formatNum(m.income)}</td>
                                                            <td class="px-4 py-3 text-right text-red-600">${this.currencySymbol}${this.formatNum(m.expenses)}</td>
                                                            <td class="px-4 py-3 text-right font-semibold ${m.savings >= 0 ? 'text-green-600' : 'text-red-600'}">${this.currencySymbol}${this.formatNum(m.savings)}</td>
                                                            <td class="px-4 py-3 text-right">${m.savings_rate}%</td>
                                                            <td class="px-4 py-3 text-center">${m.transaction_count}</td>
                                                        </tr>
                                                    `).join('')}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                `;
                },

                initMonthlyCharts(data) {
                    const ctx = document.getElementById('monthlyOverviewChart');
                    if (ctx && data.monthly_breakdown) {
                        reportCharts.monthlyOverview = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.monthly_breakdown.map(m => m.month),
                                datasets: [
                                    { label: 'Income', data: data.monthly_breakdown.map(m => m.income), backgroundColor: 'rgba(34, 197, 94, 0.8)' },
                                    { label: 'Expenses', data: data.monthly_breakdown.map(m => m.expenses), backgroundColor: 'rgba(239, 68, 68, 0.8)' },
                                    { label: 'Savings', data: data.monthly_breakdown.map(m => m.savings), backgroundColor: 'rgba(59, 130, 246, 0.8)' }
                                ]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                        });
                    }
                },

                // ==================== CASH FLOW REPORT ====================
                renderCashFlowReport(data) {
                    const s = data.summary || {};
                    return `
                                    <!-- Summary -->
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-5 border border-green-200 dark:border-green-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Inflow</p>
                                            <p class="text-2xl font-bold text-green-600">${this.currencySymbol}${this.formatNum(s.total_inflow)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 rounded-lg p-5 border border-red-200 dark:border-red-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Outflow</p>
                                            <p class="text-2xl font-bold text-red-600">${this.currencySymbol}${this.formatNum(s.total_outflow)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-5 border border-blue-200 dark:border-blue-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Net Cash Flow</p>
                                            <p class="text-2xl font-bold ${s.net_cash_flow >= 0 ? 'text-green-600' : 'text-red-600'}">${this.currencySymbol}${this.formatNum(s.net_cash_flow)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg p-5 border border-purple-200 dark:border-purple-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Cash Flow Ratio</p>
                                            <p class="text-2xl font-bold text-purple-600">${s.cash_flow_ratio || 0}x</p>
                                        </div>
                                    </div>

                                    <!-- Chart -->
                                    <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600 mb-6">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cash Flow Over Time</h4>
                                        <div style="height: 300px; position: relative;"><canvas id="cashFlowChart"></canvas></div>
                                    </div>

                                    <!-- Largest Transactions -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><i class="fas fa-arrow-up text-green-500 mr-2"></i>Largest Inflows</h4>
                                            ${this.renderLargeTransactions(data.largest_inflows, 'credit')}
                                        </div>
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><i class="fas fa-arrow-down text-red-500 mr-2"></i>Largest Outflows</h4>
                                            ${this.renderLargeTransactions(data.largest_outflows, 'debit')}
                                        </div>
                                    </div>
                                `;
                },

                initCashFlowCharts(data) {
                    const ctx = document.getElementById('cashFlowChart');
                    if (ctx && data.daily_cash_flow) {
                        reportCharts.cashFlow = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.daily_cash_flow.map(d => d.date),
                                datasets: [{
                                    label: 'Running Balance',
                                    data: data.daily_cash_flow.map(d => d.running_balance),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: false } } }
                        });
                    }
                },

                // ==================== BUDGET PERFORMANCE REPORT ====================
                renderBudgetReport(data) {
                    if (!data.has_budgets) {
                        return `
                                        <div class="text-center py-12">
                                            <i class="fas fa-bullseye text-6xl text-gray-300 mb-4"></i>
                                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Budgets Found</h3>
                                            <p class="text-gray-500 dark:text-gray-400 mb-4">${data.message || 'Create budgets to track your spending against limits.'}</p>
                                            <a href="/budgets" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">
                                                <i class="fas fa-plus mr-2"></i>Create Budget
                                            </a>
                                        </div>
                                    `;
                    }

                    const s = data.summary || {};
                    return `
                                    <!-- Summary -->
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-5 border border-blue-200 dark:border-blue-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Budgeted</p>
                                            <p class="text-2xl font-bold text-blue-600">${this.currencySymbol}${this.formatNum(s.total_budgeted)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-lg p-5 border border-orange-200 dark:border-orange-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Spent</p>
                                            <p class="text-2xl font-bold text-orange-600">${this.currencySymbol}${this.formatNum(s.total_spent)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-5 border border-green-200 dark:border-green-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Remaining</p>
                                            <p class="text-2xl font-bold ${s.total_remaining >= 0 ? 'text-green-600' : 'text-red-600'}">${this.currencySymbol}${this.formatNum(s.total_remaining)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg p-5 border border-purple-200 dark:border-purple-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Overall Usage</p>
                                            <p class="text-2xl font-bold ${s.overall_percentage > 100 ? 'text-red-600' : s.overall_percentage > 80 ? 'text-orange-600' : 'text-green-600'}">${s.overall_percentage}%</p>
                                        </div>
                                    </div>

                                    <!-- Budget Categories -->
                                    <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Budget Details</h4>
                                        <div class="space-y-4">
                                            ${(data.categories || []).map(cat => `
                                                <div class="p-4 rounded-lg ${cat.status === 'over' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : cat.status === 'warning' ? 'bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800' : 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800'}">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: ${cat.color}20">
                                                                <i class="fas ${cat.icon || 'fa-tag'}" style="color: ${cat.color}"></i>
                                                            </div>
                                                            <div>
                                                                <span class="font-semibold text-gray-900 dark:text-white">${cat.budget_name || cat.category}</span>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">${cat.category}${cat.budget_period ? ' • ' + cat.budget_period : ''}</p>
                                                            </div>
                                                        </div>
                                                        <span class="px-3 py-1 text-xs font-medium rounded-full ${cat.status === 'over' ? 'bg-red-100 text-red-700' : cat.status === 'warning' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700'}">
                                                            ${cat.status === 'over' ? 'Over Budget' : cat.status === 'warning' ? 'Warning' : 'On Track'}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between text-sm mb-2">
                                                        <span class="text-gray-600 dark:text-gray-400">Spent: ${this.currencySymbol}${this.formatNum(cat.spent)} of ${this.currencySymbol}${this.formatNum(cat.budgeted)}</span>
                                                        <span class="font-semibold ${cat.remaining >= 0 ? 'text-green-600' : 'text-red-600'}">Remaining: ${this.currencySymbol}${this.formatNum(cat.remaining)}</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                                                        <div class="h-3 rounded-full ${cat.status === 'over' ? 'bg-red-500' : cat.status === 'warning' ? 'bg-orange-500' : 'bg-green-500'}" style="width: ${Math.min(cat.percentage, 100)}%"></div>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1 text-right">${cat.percentage}% used</p>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                `;
                },

                // ==================== TAX SUMMARY REPORT ====================
                renderTaxReport(data) {
                    const s = data.summary || {};
                    return `
                                    <!-- Summary -->
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-5 border border-green-200 dark:border-green-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Income (${s.tax_year})</p>
                                            <p class="text-2xl font-bold text-green-600">${this.currencySymbol}${this.formatNum(s.total_income)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 rounded-lg p-5 border border-red-200 dark:border-red-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Expenses</p>
                                            <p class="text-2xl font-bold text-red-600">${this.currencySymbol}${this.formatNum(s.total_expenses)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-5 border border-blue-200 dark:border-blue-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Net Income</p>
                                            <p class="text-2xl font-bold text-blue-600">${this.currencySymbol}${this.formatNum(s.net_income)}</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg p-5 border border-purple-200 dark:border-purple-800">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Potentially Deductible</p>
                                            <p class="text-2xl font-bold text-purple-600">${this.currencySymbol}${this.formatNum(s.potentially_deductible)}</p>
                                        </div>
                                    </div>

                                    <!-- Quarterly Chart -->
                                    <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600 mb-6">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quarterly Breakdown</h4>
                                        <div style="height: 280px; position: relative;"><canvas id="quarterlyChart"></canvas></div>
                                    </div>

                                    <!-- Income Sources & Expense Categories -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Income Sources</h4>
                                            ${(data.income_sources || []).map(src => `
                                                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-600 last:border-0">
                                                    <span class="text-gray-700 dark:text-gray-300">${src.source}</span>
                                                    <span class="font-semibold text-green-600">${this.currencySymbol}${this.formatNum(src.amount)}</span>
                                                </div>
                                            `).join('')}
                                        </div>
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Expense Categories</h4>
                                            ${(data.expense_categories || []).slice(0, 8).map(cat => `
                                                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-600 last:border-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-gray-700 dark:text-gray-300">${cat.category}</span>
                                                        ${cat.potentially_deductible ? '<span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded">Deductible</span>' : ''}
                                                    </div>
                                                    <span class="font-semibold text-red-600">${this.currencySymbol}${this.formatNum(cat.amount)}</span>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>

                                    <!-- Large Transactions -->
                                    ${data.large_transactions && data.large_transactions.length > 0 ? `
                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>Large Transactions (≥ ${this.currencySymbol}10,000)</h4>
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-sm">
                                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                                        <tr>
                                                            <th class="px-4 py-2 text-left">Date</th>
                                                            <th class="px-4 py-2 text-left">Description</th>
                                                            <th class="px-4 py-2 text-center">Type</th>
                                                            <th class="px-4 py-2 text-right">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${data.large_transactions.map(tx => `
                                                            <tr class="border-t border-gray-200 dark:border-gray-600">
                                                                <td class="px-4 py-2">${tx.date}</td>
                                                                <td class="px-4 py-2">${tx.description}</td>
                                                                <td class="px-4 py-2 text-center"><span class="px-2 py-1 text-xs rounded-full ${tx.type === 'credit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${tx.type === 'credit' ? 'Income' : 'Expense'}</span></td>
                                                                <td class="px-4 py-2 text-right font-semibold ${tx.type === 'credit' ? 'text-green-600' : 'text-red-600'}">${this.currencySymbol}${this.formatNum(tx.amount)}</td>
                                                            </tr>
                                                        `).join('')}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    ` : ''}
                                `;
                },

                initTaxCharts(data) {
                    const ctx = document.getElementById('quarterlyChart');
                    if (ctx && data.quarterly_breakdown) {
                        reportCharts.quarterly = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.quarterly_breakdown.map(q => q.quarter + ' (' + q.period + ')'),
                                datasets: [
                                    { label: 'Income', data: data.quarterly_breakdown.map(q => q.income), backgroundColor: 'rgba(34, 197, 94, 0.8)' },
                                    { label: 'Expenses', data: data.quarterly_breakdown.map(q => q.expenses), backgroundColor: 'rgba(239, 68, 68, 0.8)' }
                                ]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                        });
                    }
                },

                // ==================== HELPER METHODS ====================
                renderTopList(items, type) {
                    if (!items || items.length === 0) {
                        return '<p class="text-gray-500 text-center py-4">No data available</p>';
                    }
                    return `
                                    <div class="space-y-3">
                                        ${items.map(item => `
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-600 last:border-0">
                                                <span class="text-gray-700 dark:text-gray-300">${item.category}</span>
                                                <div class="text-right">
                                                    <span class="font-semibold ${type === 'credit' ? 'text-green-600' : 'text-red-600'}">${this.currencySymbol}${this.formatNum(item.amount)}</span>
                                                    <span class="text-xs text-gray-500 ml-2">(${item.count} txns)</span>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                `;
                },

                renderLargeTransactions(items, type) {
                    if (!items || items.length === 0) {
                        return '<p class="text-gray-500 text-center py-4">No large transactions</p>';
                    }
                    return `
                                    <div class="space-y-3">
                                        ${items.map(item => `
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-600 last:border-0">
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">${item.description}</p>
                                                    <p class="text-xs text-gray-500">${item.date}</p>
                                                </div>
                                                <span class="font-semibold ${type === 'credit' ? 'text-green-600' : 'text-red-600'}">${this.currencySymbol}${this.formatNum(item.amount)}</span>
                                            </div>
                                        `).join('')}
                                    </div>
                                `;
                },

                renderTransactionTable(transactions) {
                    if (!transactions || transactions.length === 0) {
                        return '<div class="text-center py-8 text-gray-500">No transactions found</div>';
                    }
                    return `
                                    <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600 mt-6">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Transactions</h4>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead class="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Description</th>
                                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Category</th>
                                                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Amount</th>
                                                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Type</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${transactions.map(tx => `
                                                        <tr class="border-t border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800">
                                                            <td class="px-4 py-3 text-gray-900 dark:text-white">${tx.date}</td>
                                                            <td class="px-4 py-3 text-gray-900 dark:text-white">${tx.description}</td>
                                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">${tx.category}</td>
                                                            <td class="px-4 py-3 text-right font-semibold ${tx.type === 'credit' ? 'text-green-600' : 'text-red-600'}">${this.currencySymbol}${this.formatNum(tx.amount)}</td>
                                                            <td class="px-4 py-3 text-center">
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full ${tx.type === 'credit' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'}">
                                                                    ${tx.type === 'credit' ? 'Income' : 'Expense'}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    `).join('')}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                `;
                },

                formatNum(num) {
                    if (num === null || num === undefined) return '0.00';
                    return parseFloat(num).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
                },

                applyQuickRange(range) {
                    const now = new Date();
                    let start, end;

                    switch (range) {
                        case 'this_month':
                            start = new Date(now.getFullYear(), now.getMonth(), 1);
                            end = now;
                            break;
                        case 'last_month':
                            start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                            end = new Date(now.getFullYear(), now.getMonth(), 0);
                            break;
                        case 'this_quarter':
                            const quarter = Math.floor(now.getMonth() / 3);
                            start = new Date(now.getFullYear(), quarter * 3, 1);
                            end = now;
                            break;
                        case 'this_year':
                            start = new Date(now.getFullYear(), 0, 1);
                            end = now;
                            break;
                        case 'last_30':
                            start = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                            end = now;
                            break;
                        case 'last_90':
                            start = new Date(now.getTime() - 90 * 24 * 60 * 60 * 1000);
                            end = now;
                            break;
                        default:
                            return;
                    }

                    this.filters.startDate = start.toISOString().split('T')[0];
                    this.filters.endDate = end.toISOString().split('T')[0];
                    this.loadReportData();
                },

                async exportReport(format) {
                    const params = new URLSearchParams({
                        format: format,
                        type: currentReportType,
                        start_date: this.filters.startDate,
                        end_date: this.filters.endDate
                    });

                    if (format === 'pdf') {
                        // Use the new report-specific PDF export endpoint
                        window.open(`/reports/export-pdf?${params.toString()}`, '_blank');
                    } else {
                        // For Excel/CSV, add report type to the existing export endpoint
                        window.location.href = `/reports/export?${params.toString()}`;
                    }
                }
            };
        }

        // Standalone utility functions (outside Alpine component)
        async function loadQuickStats() {
            try {
                // Show loading state
                document.getElementById('thisMonthTotal').textContent = '...';
                document.getElementById('lastMonthTotal').textContent = '...';
                document.getElementById('avgDaily').textContent = '...';
                document.getElementById('totalTransactions').textContent = '...';

                const response = await fetch('/reports/stats', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        const data = result.data;

                        // Update currency symbols
                        const currencySymbol = data.currency_symbol || '₹';
                        document.getElementById('currencySymbol1').textContent = currencySymbol;
                        document.getElementById('currencySymbol2').textContent = currencySymbol;
                        document.getElementById('currencySymbol3').textContent = currencySymbol;

                        // Update values with proper formatting
                        document.getElementById('thisMonthTotal').textContent = formatNumber(data.this_month);
                        document.getElementById('lastMonthTotal').textContent = formatNumber(data.last_month);
                        document.getElementById('avgDaily').textContent = formatNumber(data.avg_daily);
                        document.getElementById('totalTransactions').textContent = data.total_transactions;
                    } else {
                        setZeroValues();
                    }
                } else {
                    console.error('Failed to fetch stats:', response.status);
                    setZeroValues();
                }
            } catch (error) {
                console.error('Error loading quick stats:', error);
                setZeroValues();
            }
        }

        function setZeroValues() {
            document.getElementById('thisMonthTotal').textContent = '0.00';
            document.getElementById('lastMonthTotal').textContent = '0.00';
            document.getElementById('avgDaily').textContent = '0.00';
            document.getElementById('totalTransactions').textContent = '0';
        }

        function showReportModal(type) {
            console.log('showReportModal called with type:', type);
            currentReportType = type;

            const titles = {
                'income-expense': 'Income vs Expenses Report',
                'category': 'Category Analysis Report',
                'monthly': 'Monthly Summary Report',
                'cashflow': 'Cash Flow Analysis Report',
                'budget': 'Budget Performance Report',
                'tax': 'Tax Summary Report',
                'insights': 'AI Spending Insights',
                'savings': 'Savings Analysis Report',
                'comparison': 'Year-over-Year Comparison'
            };

            // Handle special report types
            if (type === 'insights' || type === 'savings') {
                showInsightsModal(type);
                return;
            }

            if (type === 'comparison') {
                showYearComparisonModal();
                return;
            }

            const modal = document.getElementById('reportModal');
            if (!modal) {
                console.error('Modal element not found!');
                return;
            }

            const titleElement = document.getElementById('reportModalTitle');
            if (titleElement) {
                titleElement.textContent = titles[type] || 'Financial Report';
            }

            // Clear previous content
            const dynamicContent = document.getElementById('dynamicReportContent');
            if (dynamicContent) {
                dynamicContent.innerHTML = '';
            }

            console.log('Opening modal...');
            // Show modal - remove hidden and set display flex
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';

            // Wait for modal to be visible, then load data
            setTimeout(() => {
                try {
                    if (typeof Alpine !== 'undefined') {
                        const alpineComponent = Alpine.$data(modal);
                        if (alpineComponent && alpineComponent.loadReportData) {
                            console.log('Loading report data...');
                            alpineComponent.loadReportData();
                        } else {
                            console.warn('Alpine component or loadReportData not found');
                        }
                    } else {
                        console.error('Alpine.js not loaded');
                    }
                } catch (error) {
                    console.error('Error loading report data:', error);
                }
            }, 100);
        }

        // Show Insights Modal for AI Insights and Savings Analysis
        function showInsightsModal(type) {
            const modalId = 'insightsModal';
            let modal = document.getElementById(modalId);

            if (!modal) {
                modal = createInsightsModal();
                document.body.appendChild(modal);
            }

            const title = type === 'insights' ? 'AI Spending Insights' : 'Savings Analysis';
            modal.querySelector('.modal-title').textContent = title;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Load insights data
            loadInsightsData(type);
        }

        function createInsightsModal() {
            const modal = document.createElement('div');
            modal.id = 'insightsModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4';
            modal.style.zIndex = '9999';
            modal.innerHTML = `
                                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[95vh] overflow-y-auto">
                                            <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                                <h2 class="modal-title text-xl font-bold text-gray-900 dark:text-white">Insights</h2>
                                                <button onclick="closeInsightsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <i class="fas fa-times text-xl"></i>
                                                </button>
                                            </div>
                                            <div class="p-6" id="insightsContent">
                                                <div class="text-center py-12">
                                                    <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                                                    <p class="text-gray-500 dark:text-gray-400">Loading insights...</p>
                                                </div>
                                            </div>
                                        </div>
                                    `;
            return modal;
        }

        async function loadInsightsData(type) {
            const contentDiv = document.getElementById('insightsContent');

            try {
                const response = await fetch('/reports/financial-health', {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || 'Failed to load data');
                }

                const data = result.data;

                if (type === 'insights') {
                    contentDiv.innerHTML = renderInsightsContent(data);
                } else {
                    contentDiv.innerHTML = renderSavingsContent(data);
                }
            } catch (error) {
                console.error('Error loading insights:', error);
                contentDiv.innerHTML = `
                                            <div class="text-center py-12">
                                                <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                                                <p class="text-gray-500 dark:text-gray-400">Failed to load insights. Please try again.</p>
                                            </div>
                                        `;
            }
        }

        function renderInsightsContent(data) {
            const insightsHtml = data.insights && data.insights.length > 0
                ? data.insights.map(insight => `
                                            <div class="flex items-start gap-4 p-4 rounded-lg ${insight.type === 'success' ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' :
                        insight.type === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' :
                            insight.type === 'danger' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' :
                                'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800'
                    }">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center ${insight.type === 'success' ? 'bg-green-100 dark:bg-green-800' :
                        insight.type === 'warning' ? 'bg-yellow-100 dark:bg-yellow-800' :
                            insight.type === 'danger' ? 'bg-red-100 dark:bg-red-800' :
                                'bg-blue-100 dark:bg-blue-800'
                    }">
                                                    <i class="fas ${insight.icon} ${insight.type === 'success' ? 'text-green-600 dark:text-green-400' :
                        insight.type === 'warning' ? 'text-yellow-600 dark:text-yellow-400' :
                            insight.type === 'danger' ? 'text-red-600 dark:text-red-400' :
                                'text-blue-600 dark:text-blue-400'
                    }"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-900 dark:text-white">${insight.title}</h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${insight.message}</p>
                                                </div>
                                            </div>
                                        `).join('')
                : '<div class="text-center py-8 text-gray-500 dark:text-gray-400">No insights available yet. Keep tracking your transactions!</div>';

            const categoriesHtml = data.category_breakdown && data.category_breakdown.length > 0
                ? data.category_breakdown.slice(0, 5).map(cat => `
                                            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: ${cat.color}20">
                                                        <i class="fas ${cat.icon || 'fa-tag'}" style="color: ${cat.color}"></i>
                                                    </div>
                                                    <span class="font-medium text-gray-900 dark:text-white">${cat.name}</span>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-semibold text-gray-900 dark:text-white">₹${formatNumber(cat.total)}</p>
                                                    <p class="text-xs text-gray-500">${cat.percentage}%</p>
                                                </div>
                                            </div>
                                        `).join('')
                : '<div class="text-center py-4 text-gray-500">No category data available</div>';

            return `
                                        <div class="space-y-6">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                                    <i class="fas fa-brain text-indigo-500"></i>
                                                    AI-Powered Insights
                                                </h3>
                                                <div class="space-y-4">${insightsHtml}</div>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                                    <i class="fas fa-chart-pie text-purple-500"></i>
                                                    Top Spending Categories
                                                </h3>
                                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">${categoriesHtml}</div>
                                            </div>
                                            ${data.merchant_analysis && data.merchant_analysis.length > 0 ? `
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                                        <i class="fas fa-store text-teal-500"></i>
                                                        Top Merchants
                                                    </h3>
                                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                                        ${data.merchant_analysis.slice(0, 5).map(m => `
                                                            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                                                <span class="font-medium text-gray-900 dark:text-white">${m.merchant}</span>
                                                                <div class="text-right">
                                                                    <p class="font-semibold text-gray-900 dark:text-white">₹${formatNumber(m.total)}</p>
                                                                    <p class="text-xs text-gray-500">${m.count} transactions</p>
                                                                </div>
                                                            </div>
                                                        `).join('')}
                                                    </div>
                                                </div>
                                            ` : ''}
                                        </div>
                                    `;
        }

        function renderSavingsContent(data) {
            const monthlyData = Object.entries(data.monthly_data || {});

            return `
                                        <div class="space-y-6">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-5 border border-green-200 dark:border-green-800">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <i class="fas fa-percentage text-green-600"></i>
                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Savings Rate</span>
                                                    </div>
                                                    <p class="text-3xl font-bold text-green-600">${data.savings_rate}%</p>
                                                </div>
                                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-5 border border-blue-200 dark:border-blue-800">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <i class="fas fa-bullseye text-blue-600"></i>
                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Budget Adherence</span>
                                                    </div>
                                                    <p class="text-3xl font-bold text-blue-600">${data.budget_adherence}%</p>
                                                </div>
                                                <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg p-5 border border-purple-200 dark:border-purple-800">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <i class="fas fa-chart-line text-purple-600"></i>
                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Expense Trend</span>
                                                    </div>
                                                    <p class="text-3xl font-bold text-purple-600">${data.expense_trend}</p>
                                                </div>
                                            </div>

                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Savings Overview</h3>
                                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                                    <div class="space-y-4">
                                                        ${monthlyData.map(([month, values]) => {
                const savingsPercent = values.income > 0 ? ((values.savings / values.income) * 100).toFixed(1) : 0;
                const barWidth = Math.max(0, Math.min(100, savingsPercent * 2));
                return `
                                                                <div>
                                                                    <div class="flex justify-between text-sm mb-1">
                                                                        <span class="font-medium text-gray-700 dark:text-gray-300">${month}</span>
                                                                        <span class="${values.savings >= 0 ? 'text-green-600' : 'text-red-600'} font-semibold">
                                                                            ${values.savings >= 0 ? '+' : ''}₹${formatNumber(values.savings)}
                                                                        </span>
                                                                    </div>
                                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                                        <div class="h-2 rounded-full ${values.savings >= 0 ? 'bg-green-500' : 'bg-red-500'}" 
                                                                            style="width: ${barWidth}%"></div>
                                                                    </div>
                                                                </div>
                                                            `;
            }).join('')}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
        }

        function closeInsightsModal() {
            const modal = document.getElementById('insightsModal');
            if (modal) {
                modal.style.display = 'none';
            }
            document.body.style.overflow = '';
        }

        // Year Comparison Modal
        function showYearComparisonModal() {
            const modalId = 'yearComparisonModal';
            let modal = document.getElementById(modalId);

            if (!modal) {
                modal = createYearComparisonModal();
                document.body.appendChild(modal);
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            loadYearComparisonData();
        }

        function createYearComparisonModal() {
            const modal = document.createElement('div');
            modal.id = 'yearComparisonModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4';
            modal.style.zIndex = '9999';
            modal.innerHTML = `
                                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-5xl w-full max-h-[95vh] overflow-y-auto">
                                            <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Year-over-Year Comparison</h2>
                                                <button onclick="closeYearComparisonModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <i class="fas fa-times text-xl"></i>
                                                </button>
                                            </div>
                                            <div class="p-6" id="yearComparisonContent">
                                                <div class="text-center py-12">
                                                    <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                                                    <p class="text-gray-500 dark:text-gray-400">Loading comparison data...</p>
                                                </div>
                                            </div>
                                        </div>
                                    `;
            return modal;
        }

        async function loadYearComparisonData() {
            const contentDiv = document.getElementById('yearComparisonContent');

            try {
                const response = await fetch('/reports/year-comparison', {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || 'Failed to load data');
                }

                const data = result.data;
                contentDiv.innerHTML = renderYearComparisonContent(data);

                // Render chart after content is loaded
                setTimeout(() => renderYearComparisonChart(data), 100);
            } catch (error) {
                console.error('Error loading year comparison:', error);
                contentDiv.innerHTML = `
                                            <div class="text-center py-12">
                                                <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                                                <p class="text-gray-500 dark:text-gray-400">Failed to load comparison data. Please try again.</p>
                                            </div>
                                        `;
            }
        }

        function renderYearComparisonContent(data) {
            const { annual_summary, current_year, last_year } = data;

            return `
                                        <div class="space-y-6">
                                            <!-- Annual Summary Cards -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">${current_year}</h3>
                                                    <div class="space-y-3">
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600 dark:text-gray-400">Income</span>
                                                            <span class="font-semibold text-green-600">₹${formatNumber(annual_summary.current.income)}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600 dark:text-gray-400">Expenses</span>
                                                            <span class="font-semibold text-red-600">₹${formatNumber(annual_summary.current.expenses)}</span>
                                                        </div>
                                                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                                            <span class="font-medium text-gray-900 dark:text-white">Savings</span>
                                                            <span class="font-bold ${annual_summary.current.savings >= 0 ? 'text-green-600' : 'text-red-600'}">
                                                                ₹${formatNumber(annual_summary.current.savings)}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-900/40 dark:to-slate-900/40 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">${last_year}</h3>
                                                    <div class="space-y-3">
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600 dark:text-gray-400">Income</span>
                                                            <span class="font-semibold text-green-600">₹${formatNumber(annual_summary.last.income)}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600 dark:text-gray-400">Expenses</span>
                                                            <span class="font-semibold text-red-600">₹${formatNumber(annual_summary.last.expenses)}</span>
                                                        </div>
                                                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                                            <span class="font-medium text-gray-900 dark:text-white">Savings</span>
                                                            <span class="font-bold ${annual_summary.last.savings >= 0 ? 'text-green-600' : 'text-red-600'}">
                                                                ₹${formatNumber(annual_summary.last.savings)}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Change Indicators -->
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 text-center">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Income Change</p>
                                                    <p class="text-2xl font-bold ${annual_summary.change.income >= 0 ? 'text-green-600' : 'text-red-600'}">
                                                        ${annual_summary.change.income >= 0 ? '+' : ''}${annual_summary.change.income}%
                                                    </p>
                                                </div>
                                                <div class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 text-center">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Expenses Change</p>
                                                    <p class="text-2xl font-bold ${annual_summary.change.expenses <= 0 ? 'text-green-600' : 'text-red-600'}">
                                                        ${annual_summary.change.expenses >= 0 ? '+' : ''}${annual_summary.change.expenses}%
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Monthly Chart -->
                                            <div class="bg-white dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Comparison</h4>
                                                <div style="height: 300px; position: relative;">
                                                    <canvas id="yearComparisonChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    `;
        }

        let yearComparisonChartInstance = null;

        function renderYearComparisonChart(data) {
            const ctx = document.getElementById('yearComparisonChart');
            if (!ctx) return;

            if (yearComparisonChartInstance) {
                yearComparisonChartInstance.destroy();
                yearComparisonChartInstance = null;
            }

            const months = data.monthly_comparison.map(m => m.month);
            const currentIncome = data.monthly_comparison.map(m => m.current_income);
            const currentExpenses = data.monthly_comparison.map(m => m.current_expenses);
            const lastIncome = data.monthly_comparison.map(m => m.last_income);
            const lastExpenses = data.monthly_comparison.map(m => m.last_expenses);

            yearComparisonChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: `${data.current_year} Income`,
                            data: currentIncome,
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1
                        },
                        {
                            label: `${data.current_year} Expenses`,
                            data: currentExpenses,
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1
                        },
                        {
                            label: `${data.last_year} Income`,
                            data: lastIncome,
                            backgroundColor: 'rgba(34, 197, 94, 0.3)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1,
                            borderDash: [5, 5]
                        },
                        {
                            label: `${data.last_year} Expenses`,
                            data: lastExpenses,
                            backgroundColor: 'rgba(239, 68, 68, 0.3)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 500
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        function closeYearComparisonModal() {
            const modal = document.getElementById('yearComparisonModal');
            if (modal) {
                modal.style.display = 'none';
            }
            document.body.style.overflow = '';

            if (yearComparisonChartInstance) {
                yearComparisonChartInstance.destroy();
                yearComparisonChartInstance = null;
            }
        }

        function closeReportModal() {
            const modal = document.getElementById('reportModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Restore body scroll
            document.body.style.overflow = '';

            // Destroy all charts stored in reportCharts object
            if (typeof reportCharts !== 'undefined' && reportCharts) {
                Object.values(reportCharts).forEach(chart => {
                    if (chart) chart.destroy();
                });
                reportCharts = {};
            }
        }

        function generateReport() {
            // Initialize dates
            const endDate = new Date();
            const startDate = new Date();
            startDate.setMonth(startDate.getMonth() - 1); // Default to last month

            document.getElementById('consolidatedStartDate').value = startDate.toISOString().split('T')[0];
            document.getElementById('consolidatedEndDate').value = endDate.toISOString().split('T')[0];

            // Show modal
            const modal = document.getElementById('generateReportModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeGenerateModal() {
            const modal = document.getElementById('generateReportModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        async function exportConsolidatedReport(format) {
            const startDate = document.getElementById('consolidatedStartDate').value;
            const endDate = document.getElementById('consolidatedEndDate').value;

            if (!startDate || !endDate) {
                showNotification('Please select date range', 'error');
                return;
            }

            try {
                showNotification(`Generating consolidated ${format.toUpperCase()} report...`, 'info');

                // For PDF, open in new window to show preview
                if (format === 'pdf') {
                    const params = new URLSearchParams({
                        format: format,
                        start_date: startDate,
                        end_date: endDate
                    });

                    window.open(`/reports/consolidated-export?${params.toString()}`, '_blank');

                    setTimeout(() => {
                        closeGenerateModal();
                        showNotification('PDF report opened in new tab!', 'success');
                    }, 500);
                    return;
                }

                // For Excel/CSV, use form submission with _blank
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/reports/consolidated-export';
                form.target = '_blank';

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Add parameters
                const formatInput = document.createElement('input');
                formatInput.type = 'hidden';
                formatInput.name = 'format';
                formatInput.value = format;
                form.appendChild(formatInput);

                const startInput = document.createElement('input');
                startInput.type = 'hidden';
                startInput.name = 'start_date';
                startInput.value = startDate;
                form.appendChild(startInput);

                const endInput = document.createElement('input');
                endInput.type = 'hidden';
                endInput.name = 'end_date';
                endInput.value = endDate;
                form.appendChild(endInput);

                // Submit form
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);

                // Close modal and show success
                setTimeout(() => {
                    closeGenerateModal();
                    showNotification(`Consolidated ${format.toUpperCase()} report is being generated!`, 'success');
                }, 500);

            } catch (error) {
                console.error('Export error:', error);
                showNotification('Failed to generate report. Please try again.', 'error');
            }
        }

        async function exportData(format) {
            try {
                // Show loading notification
                showNotification(`Preparing ${format.toUpperCase()} export...`, 'info');

                // For PDF, open in new window to show preview
                if (format === 'pdf') {
                    const params = new URLSearchParams({
                        format: format
                    });

                    window.open(`/reports/export?${params.toString()}`, '_blank');

                    setTimeout(() => {
                        showNotification('PDF report opened in new tab!', 'success');
                    }, 500);
                    return;
                }

                // For Excel/CSV, use form submission
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/reports/export';
                form.target = '_blank';

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Add format parameter
                const formatInput = document.createElement('input');
                formatInput.type = 'hidden';
                formatInput.name = 'format';
                formatInput.value = format;
                form.appendChild(formatInput);

                // Append to body, submit, and remove
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);

                // Show success notification after a short delay
                setTimeout(() => {
                    showNotification(`${format.toUpperCase()} file download started!`, 'success');
                }, 500);

            } catch (error) {
                console.error('Export error:', error);
                showNotification('Failed to export data. Please try again.', 'error');
            }
        }

        function formatNumber(num) {
            return parseFloat(num).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white transform transition-all duration-300 ${type === 'success' ? 'bg-green-600' :
                type === 'error' ? 'bg-red-600' :
                    'bg-blue-600'
                }`;
            notification.innerHTML = `
                                                                <div class="flex items-center gap-3">
                                                                    <i class="fas ${type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-circle' :
                        'fa-info-circle'
                } text-xl"></i>
                                                                    <span class="font-medium">${message}</span>
                                                                </div>
                                                                `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Make functions globally accessible for onclick handlers
        window.reportData = reportData;
        window.showReportModal = showReportModal;
        window.closeReportModal = closeReportModal;
        window.generateReport = generateReport;
        window.closeGenerateModal = closeGenerateModal;
        window.exportConsolidatedReport = exportConsolidatedReport;
        window.exportData = exportData;
        window.loadQuickStats = loadQuickStats;
        window.formatNumber = formatNumber;
        window.showNotification = showNotification;
        window.closeInsightsModal = closeInsightsModal;
        window.closeYearComparisonModal = closeYearComparisonModal;

        // Load Financial Health Score
        async function loadFinancialHealth() {
            try {
                const response = await fetch('/reports/financial-health', {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message);
                }

                const data = result.data;

                // Update health score display
                document.getElementById('healthScoreValue').textContent = data.health_score;
                document.getElementById('healthScoreLabel').textContent = data.score_label;
                document.getElementById('healthSavingsRate').textContent = data.savings_rate + '%';
                document.getElementById('healthBudgetAdherence').textContent = data.budget_adherence + '%';
                document.getElementById('healthExpenseTrend').textContent = data.expense_trend;
                document.getElementById('healthConsistency').textContent = data.consistency_score + '%';

                // Animate the circular progress
                const circle = document.getElementById('healthScoreCircle');
                const circumference = 2 * Math.PI * 45; // 45 is the radius
                const offset = circumference - (data.health_score / 100) * circumference;
                circle.style.strokeDashoffset = offset;

                // Update circle color based on score
                if (data.health_score >= 70) {
                    circle.style.stroke = '#22c55e'; // green
                } else if (data.health_score >= 50) {
                    circle.style.stroke = '#eab308'; // yellow
                } else {
                    circle.style.stroke = '#ef4444'; // red
                }

            } catch (error) {
                console.error('Error loading financial health:', error);
                document.getElementById('healthScoreValue').textContent = '--';
                document.getElementById('healthScoreLabel').textContent = 'Unable to calculate';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Reports page loaded - initializing...');
            loadQuickStats();
            loadFinancialHealth();

            // Reload stats when user changes currency
            window.addEventListener('storage', function (e) {
                if (e.key === 'currency_updated') {
                    loadQuickStats();
                    loadFinancialHealth();
                }
            });

            // Also listen for custom currency change events
            document.addEventListener('currencyChanged', function () {
                loadQuickStats();
                loadFinancialHealth();
            });
        });

        // Close modal on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeReportModal();
            }
        });
    </script>
@endsection