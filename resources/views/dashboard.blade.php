@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    @php
        $user = DB::table('users')->where('id', session('user_id'))->first();
        $userCurrency = $user->currency ?? 'INR';
        $currencyConfig = config('currency.currencies');
    @endphp

    <!-- Colorful Glassmorphism Page Background - Blue/Cyan Theme -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-blue-100 via-cyan-50 to-teal-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-blue-300/40 to-cyan-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-blue-600/10 dark:to-cyan-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-teal-300/40 to-emerald-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-teal-600/10 dark:to-emerald-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-sky-300/30 to-blue-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-sky-600/10 dark:to-blue-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-cyan-300/30 to-teal-400/30 rounded-full blur-3xl dark:from-cyan-600/10 dark:to-teal-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-blue-300/30 to-indigo-400/30 rounded-full blur-3xl dark:from-blue-600/10 dark:to-indigo-700/10">
        </div>
    </div>

    <div class="animate-fade-in relative">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome back, {{ session('user_name', 'User') }}!
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Here's what's happening with your finances today.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
            <!-- Total Balance -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg p-6 border border-white/50 dark:border-gray-700/50 hover:shadow-xl hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 gradient-primary rounded-lg flex items-center justify-center">
                        <i class="fas fa-rupee-sign text-white text-xl"></i>
                    </div>
                    <span id="balanceBadge" class="text-xs font-medium px-2 py-1 rounded hidden">
                    </span>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Balance</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="totalBalance"><span
                        data-currency-symbol>{{ $currencyConfig[$userCurrency]['symbol'] ?? '$' }}</span>0.00</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Across all accounts</p>
            </div>

            <!-- Monthly Income -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg p-6 pl-8 border border-white/50 dark:border-gray-700/50 hover:shadow-xl hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 gradient-success rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-up text-white text-xl"></i>
                    </div>
                    <span id="incomeBadge" class="text-xs font-medium px-2 py-1 rounded hidden">
                    </span>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium">Monthly Income</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="monthlyIncome"><span
                        data-currency-symbol>{{ $currencyConfig[$userCurrency]['symbol'] ?? '$' }}</span>0.00</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">This month</p>
            </div>

            <!-- Monthly Expenses -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg p-6 pr-8 border border-white/50 dark:border-gray-700/50 hover:shadow-xl hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 gradient-danger rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-down text-white text-xl"></i>
                    </div>
                    <span id="expenseBadge" class="text-xs font-medium px-2 py-1 rounded hidden">
                    </span>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium">Monthly Expenses</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="monthlyExpenses"><span
                        data-currency-symbol>{{ $currencyConfig[$userCurrency]['symbol'] ?? '$' }}</span>0.00</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">This month</p>
            </div>

            <!-- Savings Rate -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg p-6 border border-white/50 dark:border-gray-700/50 hover:shadow-xl hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 gradient-info rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-white text-xl"></i>
                    </div>
                    <span id="savingsBadge" class="text-xs font-medium px-2 py-1 rounded hidden">
                    </span>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium">Savings Rate</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="savingsRate">0%</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Of your income</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Income vs Expenses Chart -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg p-6 border border-white/50 dark:border-gray-700/50">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Income vs Expenses</h2>
                    <select
                        class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option>Last 6 Months</option>
                        <option>Last 12 Months</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="h-[300px] relative">
                    <canvas id="incomeExpensesChart" class="hidden"></canvas>
                    <div id="incomeExpensesEmpty" class="flex flex-col items-center justify-center h-full text-center">
                        <i class="fas fa-chart-bar text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No transaction data yet</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Add income and expenses to see the chart
                        </p>
                    </div>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg p-6 border border-white/50 dark:border-gray-700/50">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Spending by Category</h2>
                    <select
                        class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>Last 3 Months</option>
                    </select>
                </div>
                <div class="h-[300px] relative">
                    <canvas id="categoryChart" class="hidden"></canvas>
                    <div id="categoryEmpty" class="flex flex-col items-center justify-center h-full text-center">
                        <i class="fas fa-chart-pie text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No spending data yet</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Add categorized expenses to see breakdown
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Recent Transactions -->
            <div
                class="lg:col-span-2 bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg p-6 border border-white/50 dark:border-gray-700/50">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h2>
                    <a href="{{ route('transactions.index') }}"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="space-y-4" id="recentTransactions">
                    <!-- Loading skeleton -->
                    <div
                        class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg animate-pulse">
                        <div class="flex items-center space-x-4 flex-1">
                            <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-lg"></div>
                            <div class="flex-1">
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/3 mb-2"></div>
                                <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/4"></div>
                            </div>
                        </div>
                        <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-20"></div>
                    </div>
                </div>
            </div>

            <!-- Budget and Goal Overview Column -->
            <div class="flex flex-col gap-6">
                <!-- Budget Overview -->
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg p-5 border border-white/50 dark:border-gray-700/50 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Budget Overview</h2>
                        <a href="{{ route('budgets.index') }}"
                            class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                            <i class="fas fa-cog"></i>
                        </a>
                    </div>

                    <div class="space-y-3" id="budgetOverview">
                        <!-- Loading skeleton -->
                        <div class="animate-pulse">
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2 mb-2"></div>
                            <div class="h-2 bg-gray-300 dark:bg-gray-600 rounded mb-2"></div>
                            <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/3"></div>
                        </div>
                    </div>
                </div>

                <!-- Goal Overview -->
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg p-5 border border-white/50 dark:border-gray-700/50 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Goal Overview</h2>
                        <a href="{{ route('goals.index') }}"
                            class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                            <i class="fas fa-cog"></i>
                        </a>
                    </div>

                    <div class="space-y-3" id="goalOverview">
                        <!-- Loading skeleton -->
                        <div class="animate-pulse">
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2 mb-2"></div>
                            <div class="h-2 bg-gray-300 dark:bg-gray-600 rounded mb-2"></div>
                            <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg p-6 border border-white/50 dark:border-gray-700/50">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Quick Actions</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4">
                <button onclick="openAddTransactionModal()"
                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-lg hover:shadow-lg transition-all duration-300 group">
                    <div
                        class="w-12 h-12 bg-primary-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Add Transaction</span>
                </button>

                <a href="{{ route('group-expense.index') }}"
                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg hover:shadow-lg transition-all duration-300 group">
                    <div
                        class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Group Expense</span>
                </a>

                <a href="{{ route('budgets.create') }}"
                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-lg hover:shadow-lg transition-all duration-300 group">
                    <div
                        class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-wallet text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Create Budget</span>
                </a>

                <a href="{{ route('reports.index') }}"
                    class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg hover:shadow-lg transition-all duration-300 group">
                    <div
                        class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Generate Report</span>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Fetch dashboard data
            fetchDashboardData();

            // Fetch and initialize charts
            fetchChartData();

            // Refresh when user returns to the tab (instead of constant polling)
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    fetchDashboardData();
                    fetchChartData();
                }
            });
        });

        async function fetchDashboardData() {
            try {
                const response = await fetch('/dashboard/stats', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    updateDashboardStats(data.data);
                }
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
            }

            // Fetch recent transactions
            fetchRecentTransactions();
            fetchBudgetOverview();
            fetchGoalOverview();
        }

        function updateDashboardStats(stats) {
            const totalBalanceEl = document.getElementById('totalBalance');
            const monthlyIncomeEl = document.getElementById('monthlyIncome');
            const monthlyExpensesEl = document.getElementById('monthlyExpenses');
            const savingsRateEl = document.getElementById('savingsRate');

            totalBalanceEl.textContent = formatCurrency(stats.total_balance || 0);
            monthlyIncomeEl.textContent = formatCurrency(stats.monthly_income || 0);
            monthlyExpensesEl.textContent = formatCurrency(stats.monthly_expenses || 0);
            savingsRateEl.textContent = (stats.savings_rate || 0) + '%';

            // Update balance change badge
            updateBadge('balanceBadge', stats.balance_change);

            // Update income change badge
            updateBadge('incomeBadge', stats.income_change);

            // Update expense change badge (inverse colors - higher is bad)
            updateBadge('expenseBadge', stats.expense_change, true);

            // Update savings status badge
            const savingsBadge = document.getElementById('savingsBadge');
            if (stats.savings_status) {
                savingsBadge.textContent = stats.savings_status;
                savingsBadge.classList.remove('hidden');
                savingsBadge.className = 'text-xs font-medium px-2 py-1 rounded ';

                if (stats.savings_status === 'Good') {
                    savingsBadge.className += 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20';
                } else if (stats.savings_status === 'Fair') {
                    savingsBadge.className += 'text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20';
                } else {
                    savingsBadge.className += 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20';
                }
            }
        }

        function updateBadge(badgeId, changeValue, inverse = false) {
            const badge = document.getElementById(badgeId);
            if (!badge) return;

            // Always show badge if we have transaction data, default to 0% if no comparison
            if (changeValue === undefined || changeValue === null) {
                changeValue = 0;
            }

            badge.classList.remove('hidden');
            const isPositive = inverse ? changeValue < 0 : changeValue > 0;
            const prefix = changeValue > 0 ? '+' : '';
            badge.textContent = prefix + changeValue.toFixed(1) + '%';

            badge.className = 'text-xs font-medium px-2 py-1 rounded ';
            if (isPositive) {
                badge.className += 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20';
            } else if (changeValue < 0) {
                badge.className += 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20';
            } else {
                badge.className += 'text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20';
            }
        }

        async function fetchChartData() {
            try {
                console.log('Fetching chart data...');

                // Fetch income vs expenses chart data
                const incomeExpenseResponse = await fetch('/dashboard/income-expenses-chart', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                // Fetch category chart data
                const categoryResponse = await fetch('/dashboard/category-chart', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                console.log('Income/Expense Response:', incomeExpenseResponse.status);
                console.log('Category Response:', categoryResponse.status);

                if (incomeExpenseResponse.ok && categoryResponse.ok) {
                    const incomeExpenseData = await incomeExpenseResponse.json();
                    const categoryData = await categoryResponse.json();

                    console.log('Income/Expense Data:', incomeExpenseData);
                    console.log('Category Data:', categoryData);

                    if (incomeExpenseData.success && categoryData.success) {
                        console.log('Initializing charts with data...');
                        initializeCharts(
                            incomeExpenseData.data.labels,
                            incomeExpenseData.data.income,
                            incomeExpenseData.data.expenses,
                            categoryData.data
                        );
                    } else {
                        console.error('Data fetch was not successful');
                        initializeCharts([], [], [], {});
                    }
                } else {
                    console.error('Failed to fetch chart data');
                    initializeCharts([], [], [], {});
                }
            } catch (error) {
                console.error('Error fetching chart data:', error);
                // Initialize with empty data if fetch fails
                initializeCharts([], [], [], {});
            }
        }

        async function fetchRecentTransactions() {
            const container = document.getElementById('recentTransactions');

            try {
                const response = await fetch('/transactions/list', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const recentTransactions = data.data.slice(0, 5);
                    displayTransactions(recentTransactions, container);
                }
            } catch (error) {
                container.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8">No recent transactions</p>';
            }
        }

        function displayTransactions(transactions, container) {
            if (!transactions || transactions.length === 0) {
                container.innerHTML = `
                                                                                                                                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                                                                                                                                    <i class="fas fa-receipt text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                                                                                                                                    <p class="text-gray-500 dark:text-gray-400 font-medium mb-2">No transactions yet</p>
                                                                                                                                                    <p class="text-sm text-gray-400 dark:text-gray-500 mb-4">Start tracking your finances by adding your first transaction</p>
                                                                                                                                                    <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors text-sm font-medium">
                                                                                                                                                        <i class="fas fa-plus mr-2"></i>Add Transaction
                                                                                                                                                    </a>
                                                                                                                                                </div>
                                                                                                                                            `;
                return;
            }

            container.innerHTML = transactions.map(tx => `
                                                                                                                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                                                                                                            <div class="flex items-center space-x-4 flex-1">
                                                                                                                                                <div class="w-10 h-10 ${tx.type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'} rounded-lg flex items-center justify-center">
                                                                                                                                                    <i class="fas ${tx.type === 'credit' ? 'fa-arrow-down text-green-600 dark:text-green-400' : 'fa-arrow-up text-red-600 dark:text-red-400'}"></i>
                                                                                                                                                </div>
                                                                                                                                                <div class="flex-1 min-w-0">
                                                                                                                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${tx.description || tx.merchant || 'Transaction'}</p>
                                                                                                                                                    <p class="text-xs text-gray-500 dark:text-gray-400">${tx.category?.name || 'Uncategorized'} • ${formatDate(tx.date)}</p>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <span class="text-sm font-semibold ${tx.type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'} ml-4">
                                                                                                                                                ${tx.type === 'credit' ? '+' : '-'}${formatCurrency(tx.amount)}
                                                                                                                                            </span>
                                                                                                                                        </div>
                                                                                                                                    `).join('');
        }

        async function fetchBudgetOverview() {
            const container = document.getElementById('budgetOverview');

            try {
                const response = await fetch('/budgets/list-ajax', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        displayBudgets(data.data, container);
                    } else {
                        container.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8">No active budgets</p>';
                    }
                } else {
                    container.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8">Failed to load budgets</p>';
                }
            } catch (error) {
                console.error('Error fetching budgets:', error);
                container.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8">No active budgets</p>';
            }
        }

        function displayBudgets(budgets, container) {
            if (!budgets || budgets.length === 0) {
                container.innerHTML = `
                                                                                                                        <p class="text-center text-gray-500 dark:text-gray-400 py-4 text-sm">No active budgets</p>
                                                                                                                        <a href="/budgets/create" class="block w-full text-center py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">
                                                                                                                            Create Budget
                                                                                                                        </a>
                                                                                                                    `;
                return;
            }

            container.innerHTML = budgets.slice(0, 2).map(budget => {
                const spent = budget.items?.reduce((sum, item) => sum + parseFloat(item.spent_amount || 0), 0) || 0;
                const totalLimit = budget.total_limit || 0;
                const percentage = totalLimit > 0 ? (spent / totalLimit * 100).toFixed(1) : 0;
                const isOverBudget = spent > totalLimit;

                return `
                                                                                                                        <div>
                                                                                                                            <div class="flex items-center justify-between mb-1.5">
                                                                                                                                <span class="text-xs font-medium text-gray-900 dark:text-white truncate">${budget.name || 'Budget ' + budget.month + '/' + budget.year}</span>
                                                                                                                                <span class="text-xs text-gray-500 dark:text-gray-400">${formatCurrency(spent)} / ${formatCurrency(totalLimit)}</span>
                                                                                                                            </div>
                                                                                                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mb-1.5">
                                                                                                                                <div class="h-1.5 rounded-full ${isOverBudget ? 'bg-red-600' : percentage > 80 ? 'bg-yellow-600' : 'bg-green-600'}" style="width: ${Math.min(percentage, 100)}%"></div>
                                                                                                                            </div>
                                                                                                                            <p class="text-xs ${isOverBudget ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'}">
                                                                                                                                ${percentage}% used ${isOverBudget ? '(Over budget!)' : ''}
                                                                                                                            </p>
                                                                                                                        </div>
                                                                                                                    `;
            }).join('');
        }

        async function fetchGoalOverview() {
            const container = document.getElementById('goalOverview');

            try {
                const response = await fetch('/goals/list-ajax', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        displayGoals(data.data, container);
                    } else {
                        container.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8">No active goals</p>';
                    }
                } else {
                    container.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8">Failed to load goals</p>';
                }
            } catch (error) {
                console.error('Error fetching goals:', error);
                container.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8">No active goals</p>';
            }
        }

        function displayGoals(goals, container) {
            if (!goals || goals.length === 0) {
                container.innerHTML = `
                                                                                                                        <p class="text-center text-gray-500 dark:text-gray-400 py-4 text-sm">No active goals</p>
                                                                                                                        <a href="/goals" class="block w-full text-center py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">
                                                                                                                            Create Goal
                                                                                                                        </a>
                                                                                                                    `;
                return;
            }

            container.innerHTML = goals.slice(0, 2).map(goal => {
                const saved = parseFloat(goal.current_amount || 0);
                const targetAmount = parseFloat(goal.target_amount || 0);
                const percentage = targetAmount > 0 ? (saved / targetAmount * 100).toFixed(1) : 0;
                const isComplete = saved >= targetAmount;

                return `
                                                                                                                        <div>
                                                                                                                            <div class="flex items-center justify-between mb-1.5">
                                                                                                                                <span class="text-xs font-medium text-gray-900 dark:text-white truncate">${goal.name || 'Goal'}</span>
                                                                                                                                <span class="text-xs text-gray-500 dark:text-gray-400">${formatCurrency(saved)} / ${formatCurrency(targetAmount)}</span>
                                                                                                                            </div>
                                                                                                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mb-1.5">
                                                                                                                                <div class="h-1.5 rounded-full ${isComplete ? 'bg-green-600' : percentage > 75 ? 'bg-blue-600' : 'bg-primary-600'}" style="width: ${Math.min(percentage, 100)}%"></div>
                                                                                                                            </div>
                                                                                                                            <p class="text-xs ${isComplete ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'}">
                                                                                                                                ${percentage}% achieved ${isComplete ? '(Completed!)' : ''}
                                                                                                                            </p>
                                                                                                                        </div>
                                                                                                                    `;
            }).join('');
        }

        let incomeExpensesChart = null;
        let categoryChart = null;

        function initializeCharts(labels = [], incomeData = [], expenseData = [], categoryData = {}) {
            console.log('initializeCharts called with:', { labels, incomeData, expenseData, categoryData });

            // Destroy existing charts if they exist
            if (incomeExpensesChart) {
                incomeExpensesChart.destroy();
            }
            if (categoryChart) {
                categoryChart.destroy();
            }

            // Show charts only if data exists
            const hasIncomeExpenseData = incomeData.length > 0 || expenseData.length > 0;
            const hasCategoryData = categoryData.labels && categoryData.labels.length > 0;

            console.log('Has income/expense data:', hasIncomeExpenseData);
            console.log('Has category data:', hasCategoryData);

            // Income vs Expenses Chart
            if (hasIncomeExpenseData) {
                console.log('Creating income vs expenses chart...');
                document.getElementById('incomeExpensesChart').classList.remove('hidden');
                document.getElementById('incomeExpensesEmpty').classList.add('hidden');

                const incomeExpensesCtx = document.getElementById('incomeExpensesChart').getContext('2d');

                incomeExpensesChart = new Chart(incomeExpensesCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Income',
                                data: incomeData,
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderRadius: 6
                            },
                            {
                                label: 'Expenses',
                                data: expenseData,
                                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#4B5563',
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
                                    callback: function (value) {
                                        return '₹' + value.toLocaleString('en-IN');
                                    }
                                },
                                grid: {
                                    color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
                                }
                            },
                            x: {
                                ticks: {
                                    color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Category Chart
            if (hasCategoryData) {
                console.log('Creating category chart...');
                document.getElementById('categoryChart').classList.remove('hidden');
                document.getElementById('categoryEmpty').classList.add('hidden');

                const categoryCtx = document.getElementById('categoryChart').getContext('2d');
                categoryChart = new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryData.labels,
                        datasets: [{
                            data: categoryData.values,
                            backgroundColor: [
                                '#FF5722',
                                '#E91E63',
                                '#2196F3',
                                '#607D8B',
                                '#9C27B0',
                                '#BDBDBD',
                                '#4CAF50',
                                '#FF9800',
                                '#00BCD4',
                                '#FFC107'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#4B5563',
                                    usePointStyle: true,
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }
        }

        function formatCurrency(amount) {
            return window.AppCurrency.format(amount);
        }

        // formatDate function is now global from app.blade.php

        function openAddTransactionModal() {
            // This will be implemented with a modal component
            window.location.href = '/transactions/create';
        }
    </script>
@endpush