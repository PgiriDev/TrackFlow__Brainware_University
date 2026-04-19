@extends('layouts.app')

@section('title', 'Budgets')
@section('breadcrumb', 'Budgets')

@section('content')
    @php
        $user = DB::table('users')->where('id', session('user_id'))->first();
        // Prefer per-user display currency from user_settings (set by currency selector)
        $userSetting = DB::table('user_settings')->where('user_id', session('user_id'))->first();
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? 'INR';
        $currencyConfig = config('currency.currencies');
        $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? ($userCurrency === 'INR' ? '₹' : '$');
    @endphp

    <!-- Colorful Glassmorphism Page Background - Amber/Orange Theme -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-amber-100 via-orange-50 to-yellow-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-amber-300/40 to-orange-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-amber-600/10 dark:to-orange-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-yellow-300/40 to-amber-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-yellow-600/10 dark:to-amber-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-orange-300/30 to-amber-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-orange-600/10 dark:to-amber-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-yellow-300/30 to-orange-400/30 rounded-full blur-3xl dark:from-yellow-600/10 dark:to-orange-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-amber-300/30 to-red-400/30 rounded-full blur-3xl dark:from-amber-600/10 dark:to-red-700/10">
        </div>
    </div>

    <div class="animate-fade-in relative" x-data="{ showAddModal: false }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Budgets</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Set and track your spending limits</p>
            </div>
            <button @click="showAddModal = true"
                class="mt-4 sm:mt-0 inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>
                Create Budget
            </button>
        </div>

        <!-- Overview Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
            <!-- Total Budgets -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 gradient-primary rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Total Budgets</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="totalBudgets">0</p>
            </div>

            <!-- Total Allocated -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-rupee-sign text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Total Allocated</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="totalAllocated"><span
                        class="font-normal">{{ $currencySymbol }}</span>0.00</p>
            </div>

            <!-- Total Spent -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Total Spent</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="totalSpent"><span
                        class="font-normal">{{ $currencySymbol }}</span>0.00</p>
            </div>

            <!-- Remaining -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-piggy-bank text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Remaining</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="totalRemaining"><span
                        class="font-normal">{{ $currencySymbol }}</span>0.00</p>
            </div>
        </div>

        <!-- Budgets List -->
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 card-shadow">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Budgets</h2>
            </div>

            <!-- Budgets Container -->
            <div id="budgetsContainer" class="p-6">
                <!-- Loading State -->
                <div id="budgetsLoading" class="text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Loading budgets...</p>
                </div>

                <!-- Empty State -->
                <div id="budgetsEmpty" class="hidden text-center py-12">
                    <div
                        class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-wallet text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No budgets yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Create your first budget to start tracking your
                        spending</p>
                    <button @click="showAddModal = true"
                        class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Budget
                    </button>
                </div>

                <!-- Budgets Grid -->
                <div id="budgetsList" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Add Budget Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.self="showAddModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" @click.stop>

                <!-- Modal Header with Gradient -->
                <div class="relative bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 px-6 py-5">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i class="fas fa-wallet text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Create Budget</h2>
                                <p class="text-white/80 text-sm">Plan your monthly spending limits</p>
                            </div>
                        </div>
                        <button @click="showAddModal = false"
                            class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form id="createBudgetForm" class="overflow-y-auto max-h-[calc(90vh-180px)]">
                    @csrf
                    <div class="p-6 lg:p-8 space-y-6">

                        <!-- Budget Period Section -->
                        <div
                            class="bg-gradient-to-br from-gray-50 to-gray-100/50 dark:from-gray-900/50 dark:to-gray-800/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 mb-5">
                                <span
                                    class="w-7 h-7 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center text-xs font-bold text-emerald-600 dark:text-emerald-400">1</span>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Budget Period & Amount
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-calendar-alt text-emerald-500 mr-2"></i>Month
                                    </label>
                                    <div class="relative">
                                        <select id="budgetMonth" required
                                            class="w-full px-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none cursor-pointer transition-all">
                                            <option value="1">📅 January</option>
                                            <option value="2">📅 February</option>
                                            <option value="3">📅 March</option>
                                            <option value="4">📅 April</option>
                                            <option value="5">📅 May</option>
                                            <option value="6">📅 June</option>
                                            <option value="7">📅 July</option>
                                            <option value="8">📅 August</option>
                                            <option value="9">📅 September</option>
                                            <option value="10">📅 October</option>
                                            <option value="11">📅 November</option>
                                            <option value="12">📅 December</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-calendar text-blue-500 mr-2"></i>Year
                                    </label>
                                    <div class="relative">
                                        <select id="budgetYear" required
                                            class="w-full px-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none cursor-pointer transition-all">
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-coins text-amber-500 mr-2"></i>Total Budget <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">{{ $currencySymbol }}</span>
                                        <input type="number" id="totalBudget" step="0.01" min="0" required
                                            class="w-full pl-10 pr-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-lg font-semibold transition-all"
                                            placeholder="10000.00">
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400 mr-2 py-1.5">Quick set:</span>
                                <button type="button" onclick="document.getElementById('totalBudget').value = '5000'"
                                    class="px-3 py-1.5 text-xs font-medium bg-white dark:bg-gray-700 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 text-gray-700 dark:text-gray-300 rounded-full border border-gray-200 dark:border-gray-600 transition-colors">
                                    {{ $currencySymbol }}5K
                                </button>
                                <button type="button" onclick="document.getElementById('totalBudget').value = '10000'"
                                    class="px-3 py-1.5 text-xs font-medium bg-white dark:bg-gray-700 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 text-gray-700 dark:text-gray-300 rounded-full border border-gray-200 dark:border-gray-600 transition-colors">
                                    {{ $currencySymbol }}10K
                                </button>
                                <button type="button" onclick="document.getElementById('totalBudget').value = '25000'"
                                    class="px-3 py-1.5 text-xs font-medium bg-white dark:bg-gray-700 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 text-gray-700 dark:text-gray-300 rounded-full border border-gray-200 dark:border-gray-600 transition-colors">
                                    {{ $currencySymbol }}25K
                                </button>
                                <button type="button" onclick="document.getElementById('totalBudget').value = '50000'"
                                    class="px-3 py-1.5 text-xs font-medium bg-white dark:bg-gray-700 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 text-gray-700 dark:text-gray-300 rounded-full border border-gray-200 dark:border-gray-600 transition-colors">
                                    {{ $currencySymbol }}50K
                                </button>
                                <button type="button" onclick="document.getElementById('totalBudget').value = '100000'"
                                    class="px-3 py-1.5 text-xs font-medium bg-white dark:bg-gray-700 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 text-gray-700 dark:text-gray-300 rounded-full border border-gray-200 dark:border-gray-600 transition-colors">
                                    {{ $currencySymbol }}100K
                                </button>
                            </div>
                        </div>

                        <!-- Budget Name Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-tag text-purple-500 mr-2"></i>Budget Name <span
                                    class="text-gray-400 text-xs font-normal">(Optional)</span>
                            </label>
                            <input type="text" id="budgetName"
                                class="w-full px-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all"
                                placeholder="e.g., January Monthly Budget, Holiday Season Budget">
                        </div>

                        <!-- Category Budgets Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="w-7 h-7 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center text-xs font-bold text-emerald-600 dark:text-emerald-400">2</span>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Category Budgets</h3>
                                </div>
                                <button type="button" onclick="addCategoryBudget()"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg">
                                    <i class="fas fa-plus mr-2"></i> Add Category
                                </button>
                            </div>

                            <div id="categoryBudgetsContainer" class="space-y-3">
                                <!-- Category budget items will be added here -->
                            </div>

                            <div id="categoryBudgetsEmpty"
                                class="text-center py-10 bg-gradient-to-br from-gray-50 to-gray-100/50 dark:from-gray-900/50 dark:to-gray-800/50 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                <div
                                    class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-layer-group text-2xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1 font-medium">No categories added yet</p>
                                <p class="text-gray-500 dark:text-gray-500 text-sm mb-5">Add categories to allocate your
                                    budget</p>
                                <button type="button" onclick="addCategoryBudget()"
                                    class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg">
                                    <i class="fas fa-plus mr-2"></i> Add First Category
                                </button>
                            </div>
                        </div>

                        <!-- Budget Summary Card -->
                        <div class="relative overflow-hidden rounded-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-teal-500/10"></div>
                            <div
                                class="relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm p-6 border-2 border-emerald-200 dark:border-emerald-800/50 rounded-2xl">
                                <div class="flex items-center gap-2 mb-4">
                                    <i class="fas fa-chart-pie text-emerald-500"></i>
                                    <h4
                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                        Budget Summary</h4>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4">
                                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium mb-1">Total
                                            Allocated</p>
                                        <p id="totalAllocatedDisplay"
                                            class="text-xl font-bold text-emerald-700 dark:text-emerald-300">
                                            {{ $currencySymbol }}0.00
                                        </p>
                                    </div>
                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4">
                                        <p class="text-xs text-blue-600 dark:text-blue-400 font-medium mb-1">Unallocated</p>
                                        <p id="unallocatedDisplay"
                                            class="text-xl font-bold text-blue-700 dark:text-blue-300">
                                            {{ $currencySymbol }}0.00
                                        </p>
                                    </div>
                                </div>

                                <!-- Allocation Progress Bar -->
                                <div class="mt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs text-gray-600 dark:text-gray-400">Allocation Progress</span>
                                        <span id="allocationPercentDisplay"
                                            class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">0%</span>
                                    </div>
                                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                        <div id="allocationProgressBar"
                                            class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2.5 rounded-full transition-all duration-500"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500 dark:text-gray-400 hidden sm:flex items-center gap-2">
                                <i class="fas fa-lightbulb text-amber-500"></i>
                                Tip: Allocate 100% of your budget across categories
                            </p>
                            <div class="flex items-center gap-3 ml-auto">
                                <button type="button" @click="showAddModal = false"
                                    class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                                    Cancel
                                </button>
                                <button type="submit" id="createBudgetBtn"
                                    class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                                    <i class="fas fa-save" id="createBudgetIcon"></i>
                                    <span id="createBudgetText">Create Budget</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- No Categories Modal -->
    <div id="noCategoriesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full" onclick="event.stopPropagation()">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-2xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">No Categories</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    No categories available. You need to create at least one category before you can set budget limits.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="closeNoCategoriesModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button onclick="openQuickCreateCategory()"
                        class="flex-1 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors font-medium">
                        <i class="fas fa-plus mr-2"></i>Create Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Create Category Modal -->
    <div id="quickCreateCategoryModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Create Category</h2>
                <button onclick="closeQuickCreateCategory()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="quickCreateCategoryForm" class="p-6" onsubmit="handleQuickCreateCategory(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="quickCategoryName" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="e.g., Groceries, Transport">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select id="quickCategoryType" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="expense" selected>Expense</option>
                        <option value="income">Income</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Icon (Optional)
                    </label>
                    <input type="text" id="quickCategoryIcon"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="e.g., shopping-cart">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeQuickCreateCategory()"
                        class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="quickCategorySubmitBtn"
                        class="flex-1 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors font-medium">
                        <i class="fas fa-check mr-2"></i>Create
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Spent Modal -->
    <div id="addSpentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Add Spent</h2>
                <button onclick="closeAddSpentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="addSpentForm" class="p-6" onsubmit="handleAddSpent(event)">
                <input type="hidden" id="spentBudgetId">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Budget <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="spentBudgetName" readonly
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="spentCategoryId" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Select Category</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Amount ({{ $currencySymbol }}) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="spentAmount" step="0.01" min="0.01" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="0.00">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="spentDescription" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Optional note about this expense"></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="spentDate" required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeAddSpentModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="addSpentSubmitBtn"
                        class="flex-1 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors font-medium">
                        <i class="fas fa-check mr-2"></i>Add Spent
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Budget Details Modal -->
    <div id="budgetDetailsModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4"
        onclick="closeBudgetDetailsModal()">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden"
            onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="relative bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 px-6 py-5">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-pie text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 id="budgetDetailsTitle" class="text-xl font-bold text-white">Budget Details</h2>
                            <p id="budgetDetailsPeriod" class="text-white/80 text-sm">January 2026</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span id="budgetDetailsStatus"
                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-white border border-white/30">
                            On Track
                        </span>
                        <button onclick="closeBudgetDetailsModal()"
                            class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(90vh-180px)]">
                <!-- Budget Overview -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div
                            class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800">
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium mb-1">Total Budget</p>
                            <p id="budgetDetailsTotalLimit"
                                class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $currencySymbol }}0.00
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-xl p-4 border border-orange-200 dark:border-orange-800">
                            <p class="text-xs text-orange-600 dark:text-orange-400 font-medium mb-1">Spent</p>
                            <p id="budgetDetailsSpent" class="text-2xl font-bold text-orange-700 dark:text-orange-300">
                                {{ $currencySymbol }}0.00
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <p class="text-xs text-blue-600 dark:text-blue-400 font-medium mb-1">Remaining</p>
                            <p id="budgetDetailsRemaining" class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                {{ $currencySymbol }}0.00
                            </p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Budget Usage</span>
                            <span id="budgetDetailsPercentage"
                                class="text-sm font-bold text-emerald-600 dark:text-emerald-400">0%</span>
                        </div>
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div id="budgetDetailsProgressBar"
                                class="bg-gradient-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-500"
                                style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-layer-group text-emerald-500"></i>
                        Category Breakdown
                    </h3>
                    <div id="budgetDetailsCategoryBreakdown" class="space-y-3">
                        <!-- Categories will be populated here -->
                    </div>
                </div>

                <!-- Transactions Section -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-receipt text-purple-500"></i>
                            Transactions
                        </h3>
                        <span id="budgetDetailsTransactionCount" class="text-sm text-gray-500 dark:text-gray-400">0
                            transactions</span>
                    </div>

                    <!-- Transactions Loading -->
                    <div id="budgetTransactionsLoading" class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">Loading transactions...</p>
                    </div>

                    <!-- Transactions Empty State -->
                    <div id="budgetTransactionsEmpty" class="hidden text-center py-8">
                        <div
                            class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-receipt text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">No transactions found for this budget period</p>
                    </div>

                    <!-- Transactions List -->
                    <div id="budgetTransactionsList" class="hidden space-y-2 max-h-80 overflow-y-auto">
                        <!-- Transactions will be populated here -->
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <button id="budgetDetailsAddSpentBtn" onclick="closeBudgetDetailsModal()"
                        class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-xl transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Add Spent
                    </button>
                    <button onclick="closeBudgetDetailsModal()"
                        class="px-6 py-2.5 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let budgets = [];
        const CURRENCY_SYMBOL = '{{ $currencySymbol }}';
        const CURRENCY_LOCALE = '{{ $currencyConfig[$userCurrency]['locale'] ?? 'en-IN' }}';
        let categories = [];
        let categoryBudgetCount = 0;
        let categoriesLoaded = false;
        let currentBudgetItems = [];

        document.addEventListener('DOMContentLoaded', function () {
            initializeYearSelect();
            initializeMonthSelect();
            fetchCategories();
            fetchBudgets();
            setupFormHandlers();

            // Refresh when user returns to the tab (instead of constant polling)
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    fetchBudgets();
                }
            });
        });

        function initializeYearSelect() {
            const yearSelect = document.getElementById('budgetYear');
            const currentYear = new Date().getFullYear();
            for (let year = currentYear - 1; year <= 2042; year++) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                if (year === currentYear) option.selected = true;
                yearSelect.appendChild(option);
            }
        }

        function initializeMonthSelect() {
            const monthSelect = document.getElementById('budgetMonth');
            const currentMonth = new Date().getMonth() + 1;
            monthSelect.value = currentMonth;
        }

        function setupFormHandlers() {
            const form = document.getElementById('createBudgetForm');
            form.addEventListener('submit', handleCreateBudget);

            // Listen to total budget changes
            document.getElementById('totalBudget').addEventListener('input', updateSummary);
        }

        async function fetchCategories() {
            try {
                const response = await fetch('/categories/list', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        categories = data.data || [];
                        categoriesLoaded = true;
                        console.log('Categories loaded:', categories.length);
                    }
                } else {
                    console.error('Failed to fetch categories:', response.status);
                    categoriesLoaded = true;
                }
            } catch (error) {
                console.error('Error fetching categories:', error);
                categoriesLoaded = true;
            }
        }

        async function fetchBudgets() {
            const loading = document.getElementById('budgetsLoading');
            const empty = document.getElementById('budgetsEmpty');
            const list = document.getElementById('budgetsList');

            try {
                const response = await fetch('/budgets/list-ajax', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    budgets = data.data || [];
                    displayBudgets(budgets);
                } else {
                    loading.classList.add('hidden');
                    empty.classList.remove('hidden');
                    updateStats(0, 0, 0, 0);
                }
            } catch (error) {
                console.log('Error fetching budgets:', error);
                loading.classList.add('hidden');
                empty.classList.remove('hidden');
                updateStats(0, 0, 0, 0);
            }
        }

        async function addCategoryBudget() {
            // Ensure categories are loaded
            if (categories.length === 0) {
                await fetchCategories();
            }

            if (categories.length === 0) {
                showNoCategoriesModal();
                return;
            }

            categoryBudgetCount++;
            const container = document.getElementById('categoryBudgetsContainer');
            const emptyState = document.getElementById('categoryBudgetsEmpty');

            emptyState.classList.add('hidden');
            container.classList.remove('hidden');

            const item = document.createElement('div');
            item.className = 'flex items-center gap-4 p-4 bg-white dark:bg-gray-700/50 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-emerald-300 dark:hover:border-emerald-600 transition-all category-budget-item shadow-sm';
            item.dataset.id = categoryBudgetCount;

            item.innerHTML = `
                                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-tag text-emerald-600 dark:text-emerald-400"></i>
                                    </div>
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="relative">
                                            <select class="category-select w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none cursor-pointer transition-all" required>
                                                <option value="">Select Category</option>
                                                ${categories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('')}
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                                <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">${CURRENCY_SYMBOL}</span>
                                            <input type="number" class="category-amount w-full pl-10 pr-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-semibold transition-all" 
                                                placeholder="Amount" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeCategoryBudget(${categoryBudgetCount})" 
                                        class="w-10 h-10 flex items-center justify-center text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all hover:scale-110">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                `;

            container.appendChild(item);

            // Add event listener for amount changes
            item.querySelector('.category-amount').addEventListener('input', updateSummary);
        }

        function removeCategoryBudget(id) {
            const item = document.querySelector(`.category-budget-item[data-id="${id}"]`);
            if (item) {
                item.remove();
                updateSummary();

                const container = document.getElementById('categoryBudgetsContainer');
                const emptyState = document.getElementById('categoryBudgetsEmpty');
                if (container.children.length === 0) {
                    container.classList.add('hidden');
                    emptyState.classList.remove('hidden');
                }
            }
        }

        function updateSummary() {
            const totalBudget = parseFloat(document.getElementById('totalBudget').value) || 0;
            const categoryItems = document.querySelectorAll('.category-amount');
            let totalAllocated = 0;

            categoryItems.forEach(input => {
                totalAllocated += parseFloat(input.value) || 0;
            });

            const unallocated = totalBudget - totalAllocated;
            const allocationPercent = totalBudget > 0 ? Math.min((totalAllocated / totalBudget) * 100, 100) : 0;

            document.getElementById('totalAllocatedDisplay').textContent = CURRENCY_SYMBOL + formatNumber(totalAllocated);
            document.getElementById('unallocatedDisplay').textContent = CURRENCY_SYMBOL + formatNumber(Math.abs(unallocated));

            // Update unallocated display styling
            const unallocatedDisplay = document.getElementById('unallocatedDisplay');
            if (unallocated < 0) {
                unallocatedDisplay.className = 'text-xl font-bold text-red-600 dark:text-red-400';
                unallocatedDisplay.previousElementSibling.textContent = 'Over Budget';
            } else {
                unallocatedDisplay.className = 'text-xl font-bold text-blue-700 dark:text-blue-300';
                unallocatedDisplay.previousElementSibling.textContent = 'Unallocated';
            }

            // Update progress bar
            const progressBar = document.getElementById('allocationProgressBar');
            const percentDisplay = document.getElementById('allocationPercentDisplay');
            if (progressBar && percentDisplay) {
                progressBar.style.width = allocationPercent + '%';
                percentDisplay.textContent = allocationPercent.toFixed(0) + '%';

                // Change progress bar color based on allocation
                if (totalAllocated > totalBudget) {
                    progressBar.className = 'bg-gradient-to-r from-red-500 to-rose-500 h-2.5 rounded-full transition-all duration-500';
                } else if (allocationPercent >= 100) {
                    progressBar.className = 'bg-gradient-to-r from-green-500 to-emerald-500 h-2.5 rounded-full transition-all duration-500';
                } else {
                    progressBar.className = 'bg-gradient-to-r from-emerald-500 to-teal-500 h-2.5 rounded-full transition-all duration-500';
                }
            }
        }

        async function handleCreateBudget(e) {
            e.preventDefault();

            const month = parseInt(document.getElementById('budgetMonth').value);
            const year = parseInt(document.getElementById('budgetYear').value);
            const totalBudget = parseFloat(document.getElementById('totalBudget').value);
            const name = document.getElementById('budgetName').value;

            const categoryItems = document.querySelectorAll('.category-budget-item');
            if (categoryItems.length === 0) {
                popupError('Please add at least one category budget.', 'Validation Error');
                return;
            }

            const items = [];
            let totalAllocated = 0;

            categoryItems.forEach(item => {
                const categoryId = item.querySelector('.category-select').value;
                const amount = parseFloat(item.querySelector('.category-amount').value);

                if (categoryId && amount > 0) {
                    items.push({
                        category_id: categoryId,
                        limit_amount: amount
                    });
                    totalAllocated += amount;
                }
            });

            if (items.length === 0) {
                popupError('Please fill in all category budget fields.', 'Validation Error');
                return;
            }

            if (totalAllocated > totalBudget) {
                popupConfirm(
                    `The total allocated amount (${CURRENCY_SYMBOL}${formatNumber(totalAllocated)}) exceeds your budget (${CURRENCY_SYMBOL}${formatNumber(totalBudget)}). Do you want to continue?`,
                    'Budget Exceeded',
                    () => submitBudget(month, year, totalBudget, name, items)
                );
                return;
            }

            await submitBudget(month, year, totalBudget, name, items);
        }

        async function submitBudget(month, year, totalBudget, name, items) {
            const submitBtn = document.getElementById('createBudgetBtn');
            const btnIcon = document.getElementById('createBudgetIcon');
            const btnText = document.getElementById('createBudgetText');

            // Show loading state
            submitBtn.disabled = true;
            btnIcon.className = 'fas fa-spinner fa-spin';
            btnText.textContent = 'Creating...';

            try {
                const response = await fetch('/budgets/create-ajax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        month,
                        year,
                        total_limit: totalBudget,
                        name: name || null,
                        items
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    btnIcon.className = 'fas fa-check';
                    btnText.textContent = 'Created!';
                    popupSuccess('Budget created successfully!', 'Success');
                    document.getElementById('createBudgetForm').reset();
                    document.getElementById('categoryBudgetsContainer').innerHTML = '';
                    document.getElementById('categoryBudgetsEmpty').classList.remove('hidden');
                    updateSummary();

                    // Close modal and refresh data instantly
                    setTimeout(() => {
                        const modal = document.getElementById('createBudgetModal');
                        if (modal) modal.classList.add('hidden');
                        fetchBudgets();
                        // Notify notifications system about the change
                        window.dispatchEvent(new CustomEvent('trackflow:data-changed'));
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to create budget');
                }
            } catch (error) {
                popupError('Error creating budget: ' + error.message, 'Error');
                // Reset button state on error
                submitBtn.disabled = false;
                btnIcon.className = 'fas fa-save';
                btnText.textContent = 'Create Budget';
            }
        }

        function displayBudgets(budgets) {
            const loading = document.getElementById('budgetsLoading');
            const empty = document.getElementById('budgetsEmpty');
            const list = document.getElementById('budgetsList');

            loading.classList.add('hidden');

            if (budgets.length === 0) {
                empty.classList.remove('hidden');
                list.classList.add('hidden');
                updateStats(0, 0, 0, 0);
                return;
            }

            empty.classList.add('hidden');
            list.classList.remove('hidden');

            const totalAllocated = budgets.reduce((sum, b) => sum + parseFloat(b.total_limit || 0), 0);
            const totalSpent = budgets.reduce((sum, b) => {
                const spent = b.items?.reduce((s, i) => s + (parseFloat(i.spent_amount) || 0), 0) || 0;
                return sum + spent;
            }, 0);
            const totalRemaining = totalAllocated - totalSpent;

            updateStats(budgets.length, totalAllocated, totalSpent, totalRemaining);

            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            list.innerHTML = budgets.map(budget => {
                const spent = budget.items?.reduce((sum, item) => sum + (parseFloat(item.spent_amount) || 0), 0) || 0;
                const limit = parseFloat(budget.total_limit || 0);
                const percentage = limit > 0 ? (spent / limit * 100).toFixed(1) : 0;
                const remaining = limit - spent;
                const isOverBudget = spent > limit;
                const isWarning = percentage > 80 && !isOverBudget;

                const budgetName = budget.name || `${monthNames[budget.month - 1]} ${budget.year} Budget`;

                return `
                                        <div class="bg-white dark:bg-gray-700/50 rounded-2xl p-6 hover:shadow-lg transition-all border-2 border-gray-100 dark:border-gray-600 hover:border-emerald-200 dark:hover:border-emerald-600 cursor-pointer group" onclick="openBudgetDetailsModal(${budget.id})">
                                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                                                <div class="flex-1" onclick="event.stopPropagation(); openBudgetDetailsModal(${budget.id})">
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">${budgetName}</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        <i class="fas fa-calendar-alt mr-1"></i> ${monthNames[budget.month - 1]} ${budget.year}
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-2 flex-wrap" onclick="event.stopPropagation()">
                                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full ${isOverBudget
                        ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
                        : isWarning
                            ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'
                            : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
                    }">
                                                        ${isOverBudget ? 'Over Budget' : isWarning ? 'Warning' : 'On Track'}
                                                    </span>
                                                    <button onclick="event.stopPropagation(); openAddSpentModal(${budget.id}, '${budgetName.replace(/'/g, "\\'")}')" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-medium rounded-lg transition-all shadow-sm hover:shadow-md hover:scale-105">
                                                        <i class="fas fa-plus mr-1.5"></i>Add Spent
                                                    </button>
                                                    <button onclick="event.stopPropagation(); deleteBudget(${budget.id})" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-all shadow-sm hover:shadow-md hover:scale-105">
                                                        <i class="fas fa-trash mr-1.5"></i>Delete
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <div class="flex items-center justify-between text-sm mb-2">
                                                    <span class="text-gray-600 dark:text-gray-400">${CURRENCY_SYMBOL}${formatNumber(spent)} of ${CURRENCY_SYMBOL}${formatNumber(limit)}</span>
                                                    <span class="font-bold ${isOverBudget ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'
                    }">${percentage}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 overflow-hidden">
                                                    <div class="h-3 rounded-full transition-all ${isOverBudget
                        ? 'bg-gradient-to-r from-red-500 to-rose-500'
                        : isWarning
                            ? 'bg-gradient-to-r from-yellow-500 to-orange-500'
                            : 'bg-gradient-to-r from-emerald-500 to-teal-500'
                    }" style="width: ${Math.min(percentage, 100)}%"></div>
                                                </div>
                                            </div>

                                            ${budget.items && budget.items.length > 0 ? `
                                                <div class="border-t border-gray-200 dark:border-gray-600 pt-4 mt-4">
                                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Category Breakdown</h4>
                                                    <div class="space-y-2">
                                                        ${budget.items.map(item => {
                        const itemSpent = parseFloat(item.spent_amount) || 0;
                        const itemLimit = parseFloat(item.limit_amount) || 0;
                        const itemPercentage = itemLimit > 0 ? (itemSpent / itemLimit * 100).toFixed(0) : 0;
                        return `
                                                            <div class="flex items-center justify-between text-sm">
                                                                <span class="text-gray-600 dark:text-gray-400">${item.category?.name || 'Unknown'}</span>
                                                                <div class="flex items-center gap-2">
                                                                    <span class="text-gray-900 dark:text-white">${CURRENCY_SYMBOL}${formatNumber(itemSpent)} / ${CURRENCY_SYMBOL}${formatNumber(itemLimit)}</span>
                                                                    <span class="text-xs ${itemPercentage > 100 ? 'text-red-600' : 'text-gray-500'}">(${itemPercentage}%)</span>
                                                                </div>
                                                            </div>
                                                        `;
                    }).join('')}
                                                    </div>
                                                </div>
                                            ` : ''}

                                            <div class="flex items-center justify-between text-sm pt-4 border-t border-gray-200 dark:border-gray-600 mt-4">
                                                <span class="text-gray-600 dark:text-gray-400">Remaining</span>
                                                <span class="font-bold text-lg ${remaining < 0
                        ? 'text-red-600 dark:text-red-400'
                        : 'text-green-600 dark:text-green-400'
                    }">
                                                    ${remaining < 0 ? '-' : ''}${CURRENCY_SYMBOL}${formatNumber(Math.abs(remaining))}
                                                </span>
                                            </div>

                                            <!-- Click hint -->
                                            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-center">
                                                <span class="text-xs text-gray-400 dark:text-gray-500 group-hover:text-emerald-500 transition-colors">
                                                    <i class="fas fa-hand-pointer mr-1"></i>Click to view details
                                                </span>
                                            </div>
                                        </div>
                                    `;
            }).join('');
        }

        async function deleteBudget(id) {
            popupConfirm(
                'Are you sure you want to delete this budget? This action cannot be undone.',
                'Delete Budget',
                async () => {
                    try {
                        const response = await fetch(`/budgets/${id}/delete-ajax`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            credentials: 'same-origin'
                        });

                        if (response.ok) {
                            popupSuccess('Budget deleted successfully!', 'Success');
                            fetchBudgets();
                            // Notify notifications system about the change
                            window.dispatchEvent(new CustomEvent('trackflow:data-changed'));
                        } else {
                            throw new Error('Failed to delete budget');
                        }
                    } catch (error) {
                        popupError('Error deleting budget: ' + error.message, 'Error');
                    }
                }
            );
        }

        function updateStats(count, allocated, spent, remaining) {
            document.getElementById('totalBudgets').textContent = count;
            document.getElementById('totalAllocated').innerHTML = '<span class="font-normal" data-currency-symbol>{{ $currencySymbol }}</span>' + formatNumber(allocated);
            document.getElementById('totalSpent').innerHTML = '<span class="font-normal" data-currency-symbol>{{ $currencySymbol }}</span>' + formatNumber(spent);
            document.getElementById('totalRemaining').innerHTML = '<span class="font-normal" data-currency-symbol>{{ $currencySymbol }}</span>' + formatNumber(remaining);
        }

        function formatNumber(num) {
            // Format number without currency conversion (amounts already in user's currency)
            const value = parseFloat(num) || 0;
            return new Intl.NumberFormat(CURRENCY_LOCALE, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(value);
        }

        // No Categories Modal Functions
        function showNoCategoriesModal() {
            const modal = document.getElementById('noCategoriesModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeNoCategoriesModal() {
            const modal = document.getElementById('noCategoriesModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openQuickCreateCategory() {
            closeNoCategoriesModal();
            const modal = document.getElementById('quickCreateCategoryModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('quickCategoryName').focus();
        }

        function closeQuickCreateCategory() {
            const modal = document.getElementById('quickCreateCategoryModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('quickCreateCategoryForm').reset();
        }

        async function handleQuickCreateCategory(event) {
            event.preventDefault();

            const submitBtn = document.getElementById('quickCategorySubmitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';

            try {
                const categoryType = document.getElementById('quickCategoryType').value;

                // Map expense/income to debit/credit for API
                const typeMapping = {
                    'expense': 'debit',
                    'income': 'credit'
                };

                // Generate a random color
                const colors = ['#3B82F6', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981', '#06B6D4', '#6366F1', '#EF4444'];
                const randomColor = colors[Math.floor(Math.random() * colors.length)];

                const formData = {
                    name: document.getElementById('quickCategoryName').value.trim(),
                    type: typeMapping[categoryType] || 'debit',
                    color: randomColor,
                    icon: document.getElementById('quickCategoryIcon').value.trim() || 'fa-tag',
                    description: ''
                };

                const response = await fetch('/categories/create-ajax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('[name="_token"]')?.value || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    popupSuccess('Category created successfully!', 'Success');
                    closeQuickCreateCategory();

                    // Reload categories
                    await fetchCategories();

                    // Now try adding category budget again
                    setTimeout(() => {
                        addCategoryBudget();
                    }, 500);
                } else {
                    throw new Error(data.message || 'Failed to create category');
                }
            } catch (error) {
                console.error('Error creating category:', error);
                popupError(error.message || 'Failed to create category. Please try again.', 'Error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // Add Spent Modal Functions
        function openAddSpentModal(budgetId, budgetName) {
            const budget = budgets.find(b => b.id === budgetId);
            if (!budget) return;

            currentBudgetItems = budget.items || [];

            document.getElementById('spentBudgetId').value = budgetId;
            document.getElementById('spentBudgetName').value = budgetName;

            // Populate categories dropdown with items from this budget
            const categorySelect = document.getElementById('spentCategoryId');
            categorySelect.innerHTML = '<option value="">Select Category</option>';

            currentBudgetItems.forEach(item => {
                const option = document.createElement('option');
                option.value = item.category?.id || 0;
                option.textContent = item.category?.name || 'General Expense';
                option.dataset.itemId = item.id;
                categorySelect.appendChild(option);
            });

            // Set date based on budget month - use today if within budget month, otherwise first day of budget month
            const today = new Date();
            const budgetYear = budget.year;
            const budgetMonth = budget.month;

            let defaultDate;
            if (today.getFullYear() === budgetYear && (today.getMonth() + 1) === budgetMonth) {
                // We're in the budget month, use today
                defaultDate = today.toISOString().split('T')[0];
            } else {
                // Not in budget month, use first day of budget month
                defaultDate = `${budgetYear}-${String(budgetMonth).padStart(2, '0')}-01`;
            }
            document.getElementById('spentDate').value = defaultDate;

            const modal = document.getElementById('addSpentModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('spentAmount').focus();
        }

        function closeAddSpentModal() {
            const modal = document.getElementById('addSpentModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('addSpentForm').reset();
        }

        async function handleAddSpent(event) {
            event.preventDefault();

            const submitBtn = document.getElementById('addSpentSubmitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';

            try {
                const budgetId = document.getElementById('spentBudgetId').value;
                const categoryId = document.getElementById('spentCategoryId').value;
                const amount = parseFloat(document.getElementById('spentAmount').value);
                const description = document.getElementById('spentDescription').value.trim();
                const date = document.getElementById('spentDate').value;

                // Find the budget item ID for this category
                const selectedOption = document.querySelector('#spentCategoryId option:checked');
                const itemId = selectedOption?.dataset?.itemId;

                if (!itemId) {
                    throw new Error('Invalid category selection');
                }

                const response = await fetch(`/budgets/${budgetId}/items/${itemId}/add-spent`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        amount: amount,
                        description: description,
                        date: date
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    popupSuccess('Spent amount added successfully!', 'Success');
                    closeAddSpentModal();
                    fetchBudgets(); // Refresh the budget list
                    // Notify notifications system about the change
                    window.dispatchEvent(new CustomEvent('trackflow:data-changed'));
                } else {
                    throw new Error(data.message || 'Failed to add spent amount');
                }
            } catch (error) {
                console.error('Error adding spent:', error);
                popupError(error.message || 'Failed to add spent amount. Please try again.', 'Error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // Budget Details Modal Functions
        let currentBudgetDetails = null;

        function openBudgetDetailsModal(budgetId) {
            const budget = budgets.find(b => b.id === budgetId);
            if (!budget) return;

            currentBudgetDetails = budget;
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

            const spent = budget.items?.reduce((sum, item) => sum + (parseFloat(item.spent_amount) || 0), 0) || 0;
            const limit = parseFloat(budget.total_limit || 0);
            const percentage = limit > 0 ? (spent / limit * 100) : 0;
            const remaining = limit - spent;
            const isOverBudget = spent > limit;
            const isWarning = percentage > 80 && !isOverBudget;

            const budgetName = budget.name || `${monthNames[budget.month - 1]} ${budget.year} Budget`;

            // Update modal header
            document.getElementById('budgetDetailsTitle').textContent = budgetName;
            document.getElementById('budgetDetailsPeriod').textContent = `${monthNames[budget.month - 1]} ${budget.year}`;

            // Update status badge
            const statusEl = document.getElementById('budgetDetailsStatus');
            if (isOverBudget) {
                statusEl.textContent = 'Over Budget';
                statusEl.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-red-500/30 text-white border border-red-300/50';
            } else if (isWarning) {
                statusEl.textContent = 'Warning';
                statusEl.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/30 text-white border border-yellow-300/50';
            } else {
                statusEl.textContent = 'On Track';
                statusEl.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-green-500/30 text-white border border-green-300/50';
            }

            // Update overview stats
            document.getElementById('budgetDetailsTotalLimit').textContent = CURRENCY_SYMBOL + formatNumber(limit);
            document.getElementById('budgetDetailsSpent').textContent = CURRENCY_SYMBOL + formatNumber(spent);
            document.getElementById('budgetDetailsRemaining').textContent = (remaining < 0 ? '-' : '') + CURRENCY_SYMBOL + formatNumber(Math.abs(remaining));

            // Update remaining color
            const remainingEl = document.getElementById('budgetDetailsRemaining');
            if (remaining < 0) {
                remainingEl.className = 'text-2xl font-bold text-red-600 dark:text-red-400';
            } else {
                remainingEl.className = 'text-2xl font-bold text-blue-700 dark:text-blue-300';
            }

            // Update progress bar
            document.getElementById('budgetDetailsPercentage').textContent = percentage.toFixed(1) + '%';
            const progressBar = document.getElementById('budgetDetailsProgressBar');
            progressBar.style.width = Math.min(percentage, 100) + '%';

            if (isOverBudget) {
                progressBar.className = 'bg-gradient-to-r from-red-500 to-rose-500 h-3 rounded-full transition-all duration-500';
            } else if (isWarning) {
                progressBar.className = 'bg-gradient-to-r from-yellow-500 to-orange-500 h-3 rounded-full transition-all duration-500';
            } else {
                progressBar.className = 'bg-gradient-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-500';
            }

            // Update category breakdown
            const categoryBreakdownEl = document.getElementById('budgetDetailsCategoryBreakdown');
            if (budget.items && budget.items.length > 0) {
                categoryBreakdownEl.innerHTML = budget.items.map(item => {
                    const itemSpent = parseFloat(item.spent_amount) || 0;
                    const itemLimit = parseFloat(item.limit_amount) || 0;
                    const itemPercentage = itemLimit > 0 ? (itemSpent / itemLimit * 100) : 0;
                    const itemOverBudget = itemSpent > itemLimit;
                    const categoryName = item.category?.name || 'Unknown';
                    const categoryIcon = item.category?.icon || 'fa-tag';
                    const categoryColor = item.category?.color || '#6B7280';

                    return `
                                            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: ${categoryColor}20">
                                                    <i class="fas ${categoryIcon}" style="color: ${categoryColor}"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span class="font-medium text-gray-900 dark:text-white truncate">${categoryName}</span>
                                                        <span class="text-sm ${itemOverBudget ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400'}">
                                                            ${CURRENCY_SYMBOL}${formatNumber(itemSpent)} / ${CURRENCY_SYMBOL}${formatNumber(itemLimit)}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex-1 bg-gray-200 dark:bg-gray-600 rounded-full h-2 overflow-hidden">
                                                            <div class="h-2 rounded-full transition-all ${itemOverBudget ? 'bg-red-500' : itemPercentage > 80 ? 'bg-yellow-500' : 'bg-emerald-500'}" 
                                                                style="width: ${Math.min(itemPercentage, 100)}%"></div>
                                                        </div>
                                                        <span class="text-xs font-medium ${itemOverBudget ? 'text-red-600' : 'text-gray-500'} w-12 text-right">${itemPercentage.toFixed(0)}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                }).join('');
            } else {
                categoryBreakdownEl.innerHTML = `
                                        <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-layer-group text-2xl mb-2"></i>
                                            <p>No categories in this budget</p>
                                        </div>
                                    `;
            }

            // Update Add Spent button with budget context
            const addSpentBtn = document.getElementById('budgetDetailsAddSpentBtn');
            addSpentBtn.onclick = function () {
                closeBudgetDetailsModal();
                setTimeout(() => openAddSpentModal(budget.id, budgetName), 200);
            };

            // Show modal
            const modal = document.getElementById('budgetDetailsModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Fetch transactions for this budget
            fetchBudgetTransactions(budget);
        }

        function closeBudgetDetailsModal() {
            const modal = document.getElementById('budgetDetailsModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentBudgetDetails = null;
        }

        async function fetchBudgetTransactions(budget) {
            const loadingEl = document.getElementById('budgetTransactionsLoading');
            const emptyEl = document.getElementById('budgetTransactionsEmpty');
            const listEl = document.getElementById('budgetTransactionsList');
            const countEl = document.getElementById('budgetDetailsTransactionCount');

            // Show loading, hide others
            loadingEl.classList.remove('hidden');
            emptyEl.classList.add('hidden');
            listEl.classList.add('hidden');

            try {
                // Fetch all transactions
                const response = await fetch('/transactions/list', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) throw new Error('Failed to fetch transactions');

                const data = await response.json();
                const allTransactions = data.data || [];

                // Get the budget period (first and last day of the month)
                const year = budget.year;
                const month = budget.month;
                const startDate = new Date(year, month - 1, 1);
                const endDate = new Date(year, month, 0); // Last day of month

                // Filter transactions that belong to THIS budget specifically
                const filteredTransactions = allTransactions.filter(tx => {
                    // Primary filter: transaction must be linked to this budget
                    if (tx.budget_id === budget.id) {
                        return true;
                    }

                    // Secondary filter: match by date range AND category for non-budget transactions
                    const txDate = new Date(tx.date);
                    const inDateRange = txDate >= startDate && txDate <= endDate;
                    const isExpense = tx.type === 'debit' || tx.type === 'expense';

                    if (!inDateRange || !isExpense) return false;

                    // Get category names from budget items
                    const budgetCategories = (budget.items || [])
                        .filter(item => item.category && item.category.name)
                        .map(item => item.category.name.toLowerCase());

                    // Match by category
                    if (tx.category && tx.category.name && budgetCategories.length > 0) {
                        return budgetCategories.includes(tx.category.name.toLowerCase());
                    }

                    return false;
                });

                loadingEl.classList.add('hidden');

                if (filteredTransactions.length === 0) {
                    // Check if budget is for a future month
                    const now = new Date();
                    const isFutureMonth = (year > now.getFullYear()) || (year === now.getFullYear() && month > now.getMonth() + 1);

                    if (isFutureMonth) {
                        emptyEl.innerHTML = `
                                                <div class="flex flex-col items-center justify-center py-8 text-center">
                                                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-4">
                                                        <i class="fas fa-calendar-alt text-2xl text-blue-600 dark:text-blue-400"></i>
                                                    </div>
                                                    <p class="text-gray-500 dark:text-gray-400">This budget is for a future month</p>
                                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Transactions will appear here once the month begins</p>
                                                </div>
                                            `;
                    } else {
                        emptyEl.innerHTML = `
                                                <div class="flex flex-col items-center justify-center py-8 text-center">
                                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                                        <i class="fas fa-receipt text-2xl text-gray-400"></i>
                                                    </div>
                                                    <p class="text-gray-500 dark:text-gray-400">No transactions found for this budget period</p>
                                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Add transactions to track your spending</p>
                                                </div>
                                            `;
                    }
                    emptyEl.classList.remove('hidden');
                    countEl.textContent = '0 transactions';
                    return;
                }

                countEl.textContent = `${filteredTransactions.length} transaction${filteredTransactions.length > 1 ? 's' : ''}`;

                // Sort by date descending
                filteredTransactions.sort((a, b) => new Date(b.date) - new Date(a.date));

                // Render transactions
                listEl.innerHTML = filteredTransactions.map(tx => {
                    const date = new Date(tx.date);
                    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    const categoryColor = tx.category?.color || '#6B7280';
                    const categoryIcon = tx.category?.icon || 'fa-receipt';

                    return `
                                            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: ${categoryColor}20">
                                                    <i class="fas ${categoryIcon}" style="color: ${categoryColor}"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-medium text-gray-900 dark:text-white truncate">${tx.description || tx.merchant || 'Transaction'}</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">${tx.category?.name || 'Uncategorized'} • ${formattedDate}</p>
                                                </div>
                                                <div class="text-right flex-shrink-0">
                                                    <p class="font-semibold text-red-600 dark:text-red-400">-${CURRENCY_SYMBOL}${formatNumber(tx.amount)}</p>
                                                </div>
                                            </div>
                                        `;
                }).join('');

                listEl.classList.remove('hidden');

            } catch (error) {
                console.error('Error fetching budget transactions:', error);
                loadingEl.classList.add('hidden');
                emptyEl.classList.remove('hidden');
                countEl.textContent = '0 transactions';
            }
        }
    </script>
@endsection