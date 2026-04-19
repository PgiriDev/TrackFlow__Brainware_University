@extends('layouts.app')

@section('title', 'Transactions')
@section('breadcrumb', 'Transactions')

@section('content')
    @php
        $user = DB::table('users')->where('id', session('user_id'))->first();
        $userCurrency = $user->currency ?? 'INR';
        $currencyConfig = config('currency.currencies');
        $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? '$';
    @endphp

    <div class="animate-fade-in" x-data="{
                                showFilters: false,
                                selectedTransactions: [],
                                selectAll: false,
                                toggleSelectAll() {
                                    this.selectAll = !this.selectAll;
                                    if (this.selectAll) {
                                        this.selectedTransactions = Array.from(document.querySelectorAll('[data-transaction-id]')).map(el => el.dataset.transactionId);
                                    } else {
                                        this.selectedTransactions = [];
                                    }
                                }
                            }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Transactions</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and track all your financial transactions</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-3">
                <button onclick="openCategoryModal()"
                    class="inline-flex items-center px-5 py-3 border-2 border-primary-600 text-primary-600 dark:text-primary-400 font-medium rounded-lg transition-colors hover:bg-primary-50 dark:hover:bg-primary-900/20">
                    <i class="fas fa-tags mr-2"></i>
                    Manage Categories
                </button>
                <button onclick="openAddTransactionModal()"
                    class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>
                    Add Transaction
                </button>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 lg:p-6 mb-6 card-shadow">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Search -->
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Search transactions, merchants..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>

                <!-- Filter Toggle -->
                <div class="flex items-center gap-2">
                    <button @click="showFilters = !showFilters"
                        class="px-6 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors flex items-center">
                        <i class="fas fa-filter mr-2"></i>
                        <span class="hidden sm:inline">Filters</span>
                    </button>

                    <select id="sortBy"
                        class="px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="date-desc">Newest First</option>
                        <option value="date-asc">Oldest First</option>
                        <option value="amount-desc">Highest Amount</option>
                        <option value="amount-asc">Lowest Amount</option>
                    </select>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div x-show="showFilters" x-cloak x-transition
                class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                    <select id="filterType"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                        <option value="">All Types</option>
                        <option value="credit">Income</option>
                        <option value="debit">Expense</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select id="filterCategory"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                        <option value="">All Categories</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                    <input type="date" id="filterDateFrom"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                    <input type="date" id="filterDateTo"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                </div>

                <div class="sm:col-span-2 lg:col-span-4 flex items-center justify-between pt-4">
                    <button onclick="clearFilters()"
                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-times mr-1"></i> Clear Filters
                    </button>
                    <button onclick="applyFilters()"
                        class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div x-show="selectedTransactions.length > 0" x-cloak
            class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-4 mb-6 flex items-center justify-between">
            <span class="text-sm font-medium text-primary-900 dark:text-primary-300">
                <span x-text="selectedTransactions.length"></span> transaction(s) selected
            </span>
            <div class="flex items-center gap-2">
                <button onclick="bulkCategorize()"
                    class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors text-sm">
                    <i class="fas fa-tags mr-1"></i> Categorize
                </button>
                <button onclick="bulkDelete()"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden card-shadow">
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-4 text-left">
                                <input type="checkbox" @change="toggleSelectAll()" :checked="selectAll"
                                    class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Category</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Amount</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Loading skeleton -->
                        <tr class="animate-pulse">
                            <td class="px-6 py-4">
                                <div class="w-4 h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-20"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-48"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-24"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-32"></div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-20 ml-auto"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-16 mx-auto"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700" id="transactionsMobileList">
                <!-- Loading skeleton -->
                <div class="p-4 animate-pulse">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-lg"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
                        </div>
                        <div class="h-5 bg-gray-300 dark:bg-gray-600 rounded w-20"></div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div
                class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-600">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <span id="paginationStart">1</span> to <span id="paginationEnd">10</span> of <span
                        id="paginationTotal">0</span> transactions
                </div>
                <div class="flex items-center space-x-2" id="paginationButtons">
                    <button
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Transaction Modal -->
    <div id="transactionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4"
        onclick="if(event.target === this) closeTransactionModal()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
            onclick="event.stopPropagation()">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white" id="modalTitle">Add Transaction</h2>
                <button onclick="closeTransactionModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="transactionForm" class="p-6 space-y-6">
                <input type="hidden" name="transaction_id" id="transactionId">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label
                                class="flex items-center justify-center p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary-500 transition-colors has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-900/20">
                                <input type="radio" name="type" value="debit" checked class="hidden">
                                <div class="text-center">
                                    <i class="fas fa-arrow-up text-red-600 text-2xl mb-2"></i>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Expense</div>
                                </div>
                            </label>
                            <label
                                class="flex items-center justify-center p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary-500 transition-colors has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-900/20">
                                <input type="radio" name="type" value="credit" class="hidden">
                                <div class="text-center">
                                    <i class="fas fa-arrow-down text-green-600 text-2xl mb-2"></i>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Income</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount *</label>
                        <div class="relative">
                            <span data-currency-symbol
                                class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">{{ $currencySymbol }}</span>
                            <input type="number" step="0.01" name="amount" required
                                class="w-full pl-8 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description *</label>
                        <input type="text" name="description" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                        <div class="flex gap-2">
                            <select name="category_id" id="transactionCategory" required
                                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                                <option value="">Select category...</option>
                                <!-- Categories will be loaded dynamically -->
                            </select>
                            <button type="button" onclick="openQuickAddCategory()"
                                class="px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap">
                                <i class="fas fa-plus"></i>
                                <span>Add</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date *</label>
                        <input type="date" name="date" id="transactionDate" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Merchant</label>
                        <input type="text" name="merchant"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeTransactionModal()"
                        class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors shadow-lg">
                        <i class="fas fa-save mr-2"></i> Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Category Management Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div
                class="sticky top-0 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between z-10">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Category Management</h2>
                <button onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-6">
                <!-- Add Category Section -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-plus-circle mr-2 text-primary-600"></i>Create New Category
                        </h3>
                    </div>

                    <form id="createCategoryForm" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 sm:p-8 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Category Name *
                                </label>
                                <input type="text" id="categoryName" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500"
                                    placeholder="e.g., Groceries">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Type *
                                </label>
                                <select id="categoryType" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                                    <option value="debit">Expense</option>
                                    <option value="credit">Income</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Icon
                                </label>
                                <div class="flex items-center gap-2">
                                    <select id="categoryIcon"
                                        class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                                        <option value="fa-wallet">💼 Wallet</option>
                                        <option value="fa-utensils">🍽️ Food</option>
                                        <option value="fa-car">🚗 Car</option>
                                        <option value="fa-shopping-bag">🛍️ Shopping</option>
                                        <option value="fa-home">🏠 Home</option>
                                        <option value="fa-heartbeat">❤️ Health</option>
                                        <option value="fa-graduation-cap">🎓 Education</option>
                                        <option value="fa-plane">✈️ Travel</option>
                                        <option value="fa-film">🎬 Entertainment</option>
                                        <option value="fa-laptop-code">💻 Work</option>
                                        <option value="fa-piggy-bank">🐷 Savings</option>
                                        <option value="fa-chart-line">📈 Investment</option>
                                        <option value="fa-gift">🎁 Gifts</option>
                                        <option value="fa-coins">💰 Money</option>
                                        <option value="fa-file-invoice-dollar">🧾 Bills</option>
                                    </select>
                                    <button type="button" id="openIconPickerManage"
                                        class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600"
                                        title="Icon map">
                                        <i class="fas fa-icons"></i>
                                    </button>
                                    <button type="button" id="uploadIconBtnManage"
                                        class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600"
                                        title="Upload icon">
                                        <i class="fas fa-upload"></i>
                                    </button>
                                    <input type="file" id="categoryIconFileManage" accept="image/*" class="hidden">
                                </div>

                                <div id="iconPickerManage"
                                    class="hidden mt-3 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm max-h-56 overflow-auto">
                                    <div class="grid grid-cols-6 sm:grid-cols-8 gap-3" id="iconPickerGridManage"></div>
                                </div>
                                <input type="hidden" id="categoryIconCustomManage" name="category_icon_custom_manage">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Color
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="categoryColor" value="#3b82f6"
                                        class="h-12 w-16 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                                    <input type="text" id="categoryColorHex" value="#3b82f6"
                                        class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500"
                                        placeholder="#3b82f6">
                                    <button type="button" id="openColorMapMain"
                                        class="ml-2 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600"
                                        title="Color map">
                                        <i class="fas fa-palette"></i>
                                    </button>
                                </div>
                                <div id="colorMapMain"
                                    class="hidden mt-3 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm max-h-56 overflow-auto">
                                    <div class="grid grid-cols-6 sm:grid-cols-8 gap-3" id="colorMapGridMain"></div>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description (Optional)
                            </label>
                            <input type="text" id="categoryDescription"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500"
                                placeholder="Brief description of this category">
                        </div>
                </div>

                <button type="submit"
                    class="w-full px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Category
                </button>
                </form>
            </div>

            <!-- Existing Categories List -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-list mr-2 text-primary-600"></i>Your Categories (<span id="categoryCount">0</span>)
                </h3>

                <div id="categoriesLoading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Loading categories...</p>
                </div>

                <div id="categoriesEmpty" class="hidden text-center py-8">
                    <i class="fas fa-tags text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No categories yet. Create your first one above!</p>
                </div>

                <div id="categoriesList" class="hidden grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Categories will be populated here -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <button onclick="window.location.href='/transactions'"
                    class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-list mr-2"></i>Add Transaction
                </button>
                <button onclick="closeCategoryModal()"
                    class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
    </div>

    <!-- Quick Add Category Modal -->
    <div id="quickCategoryModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full" @click.stop>
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Quick Add Category</h2>
                <button onclick="closeQuickCategoryModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form id="quickCategoryForm" onsubmit="saveQuickCategory(event)">
                    <div class="space-y-4">
                        <!-- Category Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Category Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="quickCategoryName" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500"
                                placeholder="e.g., Semester Fees">
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select id="quickCategoryType" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>

                            <div class="flex items-center gap-2 mt-2">
                                <button type="button" id="openIconPickerMain"
                                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600"
                                    title="Icon map">
                                    <i class="fas fa-icons"></i>
                                </button>
                            </div>

                            <div id="iconPickerMain"
                                class="hidden mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm max-h-48 overflow-auto">
                                <div class="grid grid-cols-6 gap-2" id="iconPickerGridMain"></div>
                            </div>
                            <input type="hidden" id="categoryIconCustom" name="category_icon_custom">
                        </div>

                        <!-- Color -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Color <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <select id="quickCategoryColor" required
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                                    <option value="#3B82F6">Blue</option>
                                    <option value="#10B981">Green</option>
                                    <option value="#EF4444">Red</option>
                                    <option value="#F59E0B">Orange</option>
                                    <option value="#8B5CF6">Purple</option>
                                    <option value="#EC4899">Pink</option>
                                    <option value="#14B8A6">Teal</option>
                                    <option value="#F97316">Dark Orange</option>
                                    <option value="#6366F1">Indigo</option>
                                    <option value="#6B7280">Gray</option>
                                </select>
                                <button type="button" id="openQuickColorMap"
                                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg border" title="Color map">
                                    <i class="fas fa-palette"></i>
                                </button>
                            </div>
                            <div id="quickColorMap"
                                class="hidden mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg border shadow-sm max-h-48 overflow-auto">
                                <div class="grid grid-cols-6 sm:grid-cols-8 gap-3" id="quickColorMapGrid"></div>
                            </div>
                        </div>

                        <!-- Icon -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Icon <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <select id="quickCategoryIcon" required
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                                    <option value="fa-tag">Tag (Default)</option>
                                    <option value="fa-shopping-cart">Shopping Cart</option>
                                    <option value="fa-utensils">Food</option>
                                    <option value="fa-car">Car</option>
                                    <option value="fa-home">Home</option>
                                    <option value="fa-plane">Travel</option>
                                    <option value="fa-heart">Health</option>
                                    <option value="fa-graduation-cap">Education</option>
                                    <option value="fa-film">Entertainment</option>
                                    <option value="fa-shopping-bag">Shopping</option>
                                    <option value="fa-dollar-sign">Money</option>
                                    <option value="fa-wallet">Wallet</option>
                                    <option value="fa-credit-card">Credit Card</option>
                                    <option value="fa-briefcase">Business</option>
                                    <option value="fa-gift">Gift</option>
                                </select>
                                <button type="button" id="openQuickIconPicker"
                                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg border">
                                    <i class="fas fa-icons"></i>
                                </button>
                                <button type="button" id="uploadQuickIconBtn"
                                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg border">
                                    <i class="fas fa-upload"></i>
                                </button>
                                <input type="file" id="quickCategoryIconFile" accept="image/*" class="hidden">
                            </div>
                            <div id="quickIconPicker"
                                class="hidden mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg border shadow-sm max-h-48 overflow-auto">
                                <div class="grid grid-cols-6 sm:grid-cols-8 gap-3" id="quickIconPickerGrid"></div>
                            </div>
                            <input type="hidden" id="quickCategoryIconCustom" name="quick_category_icon_custom">
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeQuickCategoryModal()"
                            class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">
                            <i class="fas fa-save mr-2"></i>
                            Create Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        let currentPage = 1;
        let transactionsData = [];

        document.addEventListener('DOMContentLoaded', function () {
            fetchTransactions();
            loadCategories();

            // Refresh when user returns to the tab (instead of constant polling)
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    fetchTransactions();
                }
            });

            // Search with debounce
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => fetchTransactions(), 500);
            });

            // Sort change
            document.getElementById('sortBy').addEventListener('change', () => fetchTransactions());

            // Set today's date as default
            document.querySelector('[name="date"]').value = new Date().toISOString().split('T')[0];
        });

        // Populate color/icon pickers and handle uploads (non-destructive enhancement)
        (function () {
            const colors = [
                '#000000', '#030303', '#050505', '#080808', '#0A0A0A', '#0D0D0D', '#0F0F0F', '#121212', '#141414', '#171717',
                '#191919', '#1C1C1C', '#1E1E1E', '#212121', '#232323', '#262626', '#282828', '#2B2B2B', '#2D2D2D', '#303030',
                '#323232', '#353535', '#373737', '#3A3A3A', '#3C3C3C', '#3F3F3F', '#414141', '#444444', '#464646', '#494949',
                '#4B4B4B', '#4E4E4E', '#505050', '#535353', '#555555', '#585858', '#5A5A5A', '#5D5D5D', '#5F5F5F', '#626262',
                '#646464', '#676767', '#696969', '#6C6C6C', '#6E6E6E', '#717171', '#737373', '#767676', '#787878', '#7B7B7B',

                '#7D0000', '#800000', '#820000', '#850000', '#870000', '#8A0000', '#8C0000', '#8F0000', '#910000', '#940000',
                '#960000', '#990000', '#9B0000', '#9E0000', '#A00000', '#A30000', '#A50000', '#A80000', '#AA0000', '#AD0000',
                '#AF0000', '#B20000', '#B40000', '#B70000', '#B90000', '#BC0000', '#BE0000', '#C10000', '#C30000', '#C60000',

                '#C80000', '#CB0000', '#CD0000', '#D00000', '#D20000', '#D50000', '#D70000', '#DA0000', '#DC0000', '#DF0000',
                '#E10000', '#E40000', '#E60000', '#E90000', '#EB0000', '#EE0000', '#F00000', '#F30000', '#F50000', '#F80000',

                '#FF0000', '#FF0505', '#FF0A0A', '#FF0F0F', '#FF1414', '#FF1919', '#FF1E1E', '#FF2323', '#FF2828', '#FF2D2D',
                '#FF3232', '#FF3737', '#FF3C3C', '#FF4141', '#FF4646', '#FF4B4B', '#FF5050', '#FF5555', '#FF5A5A', '#FF5F5F',
                '#FF6464', '#FF6969', '#FF6E6E', '#FF7373', '#FF7878', '#FF7D7D', '#FF8282', '#FF8787', '#FF8C8C', '#FF9191',

                '#FF9600', '#FF9A00', '#FF9F00', '#FFA300', '#FFA800', '#FFAC00', '#FFB100', '#FFB500', '#FFBA00', '#FFBE00',
                '#FFC300', '#FFC700', '#FFCC00', '#FFD000', '#FFD500', '#FFD900', '#FFDE00', '#FFE200', '#FFE700', '#FFEB00',

                '#FFFF00', '#FAFF00', '#F5FF00', '#F0FF00', '#EBFF00', '#E6FF00', '#E1FF00', '#DCFF00', '#D7FF00', '#D2FF00',
                '#CDFF00', '#C8FF00', '#C3FF00', '#BEFF00', '#B9FF00', '#B4FF00', '#AFFF00', '#AAFF00', '#A5FF00', '#A0FF00',

                '#00FF00', '#00FA00', '#00F500', '#00F000', '#00EB00', '#00E600', '#00E100', '#00DC00', '#00D700', '#00D200',
                '#00CD00', '#00C800', '#00C300', '#00BE00', '#00B900', '#00B400', '#00AF00', '#00AA00', '#00A500', '#00A000',

                '#00FF64', '#00FF69', '#00FF6E', '#00FF73', '#00FF78', '#00FF7D', '#00FF82', '#00FF87', '#00FF8C', '#00FF91',
                '#00FF96', '#00FF9B', '#00FFA0', '#00FFA5', '#00FFAA', '#00FFAF', '#00FFB4', '#00FFB9', '#00FFBE', '#00FFC3',

                '#00FFFF', '#00FAFF', '#00F5FF', '#00F0FF', '#00EBFF', '#00E6FF', '#00E1FF', '#00DCFF', '#00D7FF', '#00D2FF',
                '#00CDFF', '#00C8FF', '#00C3FF', '#00BEFF', '#00B9FF', '#00B4FF', '#00AFFF', '#00AAFF', '#00A5FF', '#00A0FF',

                '#0000FF', '#0505FF', '#0A0AFF', '#0F0FFF', '#1414FF', '#1919FF', '#1E1EFF', '#2323FF', '#2828FF', '#2D2DFF',
                '#3232FF', '#3737FF', '#3C3CFF', '#4141FF', '#4646FF', '#4B4BFF', '#5050FF', '#5555FF', '#5A5AFF', '#5F5FFF',

                '#6400FF', '#6900FF', '#6E00FF', '#7300FF', '#7800FF', '#7D00FF', '#8200FF', '#8700FF', '#8C00FF', '#9100FF',
                '#9600FF', '#9B00FF', '#A000FF', '#A500FF', '#AA00FF', '#AF00FF', '#B400FF', '#B900FF', '#BE00FF', '#C300FF',

                '#FF00FF', '#FF05FF', '#FF0AFF', '#FF0FFF', '#FF14FF', '#FF19FF', '#FF1EFF', '#FF23FF', '#FF28FF', '#FF2DFF',
                '#FF32FF', '#FF37FF', '#FF3CFF', '#FF41FF', '#FF46FF', '#FF4BFF', '#FF50FF', '#FF55FF', '#FF5AFF', '#FF5FFF',

                '#FFFFFF', '#FAFAFA', '#F5F5F5', '#F0F0F0', '#EBEBEB', '#E6E6E6', '#E1E1E1', '#DCDCDC', '#D7D7D7', '#D2D2D2'
            ];

            const icons = [
                // Font Awesome classes (value stored without the prefix used when rendering elsewhere)
                { type: 'fa', value: 'fa-house' }, { type: 'fa', value: 'fa-gauge' }, { type: 'fa', value: 'fa-bars' },
                { type: 'fa', value: 'fa-xmark' }, { type: 'fa', value: 'fa-magnifying-glass' }, { type: 'fa', value: 'fa-gear' },
                { type: 'fa', value: 'fa-user' }, { type: 'fa', value: 'fa-users' }, { type: 'fa', value: 'fa-id-card' },
                { type: 'fa', value: 'fa-right-to-bracket' }, { type: 'fa', value: 'fa-right-from-bracket' }, { type: 'fa', value: 'fa-user-plus' },
                { type: 'fa', value: 'fa-lock' }, { type: 'fa', value: 'fa-lock-open' }, { type: 'fa', value: 'fa-key' },
                { type: 'fa', value: 'fa-shield-halved' }, { type: 'fa', value: 'fa-bell' }, { type: 'fa', value: 'fa-bell-slash' },
                { type: 'fa', value: 'fa-volume-high' }, { type: 'fa', value: 'fa-volume-low' }, { type: 'fa', value: 'fa-microphone' },
                { type: 'fa', value: 'fa-microphone-slash' }, { type: 'fa', value: 'fa-camera' }, { type: 'fa', value: 'fa-image' },
                { type: 'fa', value: 'fa-images' }, { type: 'fa', value: 'fa-upload' }, { type: 'fa', value: 'fa-download' },
                { type: 'fa', value: 'fa-cloud' }, { type: 'fa', value: 'fa-cloud-arrow-up' }, { type: 'fa', value: 'fa-cloud-arrow-down' },
                { type: 'fa', value: 'fa-file' }, { type: 'fa', value: 'fa-folder' }, { type: 'fa', value: 'fa-folder-open' },
                { type: 'fa', value: 'fa-trash' }, { type: 'fa', value: 'fa-pen' }, { type: 'fa', value: 'fa-floppy-disk' },
                { type: 'fa', value: 'fa-copy' }, { type: 'fa', value: 'fa-paste' }, { type: 'fa', value: 'fa-scissors' },
                { type: 'fa', value: 'fa-rotate-left' }, { type: 'fa', value: 'fa-rotate-right' }, { type: 'fa', value: 'fa-play' },
                { type: 'fa', value: 'fa-pause' }, { type: 'fa', value: 'fa-stop' }, { type: 'fa', value: 'fa-forward' },
                { type: 'fa', value: 'fa-backward' }, { type: 'fa', value: 'fa-battery-full' }, { type: 'fa', value: 'fa-battery-half' },
                { type: 'fa', value: 'fa-battery-empty' }, { type: 'fa', value: 'fa-wifi' }, { type: 'fa', value: 'fa-wifi-slash' },
                { type: 'fa', value: 'fa-plane' }, { type: 'fa', value: 'fa-location-dot' }, { type: 'fa', value: 'fa-map' },
                { type: 'fa', value: 'fa-compass' }, { type: 'fa', value: 'fa-bookmark' }, { type: 'fa', value: 'fa-star' },
                { type: 'fa', value: 'fa-heart' }, { type: 'fa', value: 'fa-thumbs-up' }, { type: 'fa', value: 'fa-thumbs-down' },
                { type: 'fa', value: 'fa-share-nodes' }, { type: 'fa', value: 'fa-link' }, { type: 'fa', value: 'fa-paperclip' },
                { type: 'fa', value: 'fa-envelope' }, { type: 'fa', value: 'fa-inbox' }, { type: 'fa', value: 'fa-paper-plane' },
                { type: 'fa', value: 'fa-calendar' }, { type: 'fa', value: 'fa-clock' }, { type: 'fa', value: 'fa-list' },
                { type: 'fa', value: 'fa-table' }, { type: 'fa', value: 'fa-filter' }, { type: 'fa', value: 'fa-chart-column' },
                { type: 'fa', value: 'fa-chart-line' }, { type: 'fa', value: 'fa-chart-pie' }, { type: 'fa', value: 'fa-check' },
                { type: 'fa', value: 'fa-circle-xmark' }, { type: 'fa', value: 'fa-triangle-exclamation' }, { type: 'fa', value: 'fa-circle-info' },
                { type: 'fa', value: 'fa-comments' }, { type: 'fa', value: 'fa-briefcase' }, { type: 'fa', value: 'fa-cart-shopping' },
                { type: 'fa', value: 'fa-credit-card' }, { type: 'fa', value: 'fa-wallet' }, { type: 'fa', value: 'fa-truck' },
                { type: 'fa', value: 'fa-print' }, { type: 'fa', value: 'fa-phone' }, { type: 'fa', value: 'fa-award' },
                { type: 'fa', value: 'fa-trophy' }, { type: 'fa', value: 'fa-bug' }, { type: 'fa', value: 'fa-code' },
                { type: 'fa', value: 'fa-terminal' }, { type: 'fa', value: 'fa-database' }, { type: 'fa', value: 'fa-server' },
                { type: 'fa', value: 'fa-wrench' }, { type: 'fa', value: 'fa-heart-pulse' }, { type: 'fa', value: 'fa-car' },
                { type: 'fa', value: 'fa-bus' }, { type: 'fa', value: 'fa-train' }, { type: 'fa', value: 'fa-ship' },
                { type: 'fa', value: 'fa-globe' }, { type: 'fa', value: 'fa-language' }, { type: 'fa', value: 'fa-moon' },
                { type: 'fa', value: 'fa-sun' }, { type: 'fa', value: 'fa-cloud-rain' }, { type: 'fa', value: 'fa-fire' },
                { type: 'fa', value: 'fa-leaf' }, { type: 'fa', value: 'fa-recycle' }, { type: 'fa', value: 'fa-laptop' },
                { type: 'fa', value: 'fa-desktop' }, { type: 'fa', value: 'fa-mobile-screen' }, { type: 'fa', value: 'fa-headphones' },
                { type: 'fa', value: 'fa-music' }, { type: 'fa', value: 'fa-video' }, { type: 'fa', value: 'fa-film' },
                { type: 'fa', value: 'fa-rss' }, { type: 'fa', value: 'fa-newspaper' }, { type: 'fa', value: 'fa-bold' },
                { type: 'fa', value: 'fa-italic' }, { type: 'fa', value: 'fa-align-left' }, { type: 'fa', value: 'fa-align-center' },
                { type: 'fa', value: 'fa-align-right' }, { type: 'fa', value: 'fa-palette' }, { type: 'fa', value: 'fa-brush' },
                { type: 'fa', value: 'fa-crop' }, { type: 'fa', value: 'fa-eye' }, { type: 'fa', value: 'fa-eye-slash' },
                { type: 'fa', value: 'fa-fingerprint' }, { type: 'fa', value: 'fa-shield-check' }, { type: 'fa', value: 'fa-ban' },
                { type: 'fa', value: 'fa-hospital' }, { type: 'fa', value: 'fa-user-doctor' }, { type: 'fa', value: 'fa-scale-balanced' },
                { type: 'fa', value: 'fa-graduation-cap' },

                // Material icons (rendered using the material-icons font if available)
                { type: 'mi', value: 'home' }, { type: 'mi', value: 'dashboard' }, { type: 'mi', value: 'menu' },
                { type: 'mi', value: 'close' }, { type: 'mi', value: 'search' }, { type: 'mi', value: 'settings' },
                { type: 'mi', value: 'person' }, { type: 'mi', value: 'group' }, { type: 'mi', value: 'badge' },
                { type: 'mi', value: 'login' }, { type: 'mi', value: 'logout' }, { type: 'mi', value: 'person_add' },
                { type: 'mi', value: 'lock' }, { type: 'mi', value: 'lock_open' }, { type: 'mi', value: 'key' },
                { type: 'mi', value: 'security' }, { type: 'mi', value: 'notifications' }, { type: 'mi', value: 'notifications_off' },
                { type: 'mi', value: 'volume_up' }, { type: 'mi', value: 'volume_down' }, { type: 'mi', value: 'mic' },
                { type: 'mi', value: 'mic_off' }, { type: 'mi', value: 'photo_camera' }, { type: 'mi', value: 'image' },
                { type: 'mi', value: 'photo_library' }, { type: 'mi', value: 'upload' }, { type: 'mi', value: 'download' },
                { type: 'mi', value: 'cloud' }, { type: 'mi', value: 'cloud_upload' }, { type: 'mi', value: 'cloud_download' },
                { type: 'mi', value: 'description' }, { type: 'mi', value: 'folder' }, { type: 'mi', value: 'folder_open' },
                { type: 'mi', value: 'delete' }, { type: 'mi', value: 'edit' }, { type: 'mi', value: 'save' },
                { type: 'mi', value: 'content_copy' }, { type: 'mi', value: 'content_paste' }, { type: 'mi', value: 'content_cut' },
                { type: 'mi', value: 'undo' }, { type: 'mi', value: 'redo' }, { type: 'mi', value: 'play_arrow' },
                { type: 'mi', value: 'pause' }, { type: 'mi', value: 'stop' }, { type: 'mi', value: 'fast_forward' },
                { type: 'mi', value: 'fast_rewind' }, { type: 'mi', value: 'battery_full' }, { type: 'mi', value: 'battery_std' },
                { type: 'mi', value: 'battery_alert' }, { type: 'mi', value: 'wifi' }, { type: 'mi', value: 'wifi_off' },
                { type: 'mi', value: 'flight' }, { type: 'mi', value: 'location_on' }, { type: 'mi', value: 'map' },
                { type: 'mi', value: 'explore' }, { type: 'mi', value: 'bookmark' }, { type: 'mi', value: 'star' },
                { type: 'mi', value: 'favorite' }, { type: 'mi', value: 'thumb_up' }, { type: 'mi', value: 'thumb_down' },
                { type: 'mi', value: 'share' }, { type: 'mi', value: 'link' }, { type: 'mi', value: 'attach_file' },
                { type: 'mi', value: 'email' }, { type: 'mi', value: 'inbox' }, { type: 'mi', value: 'send' },
                { type: 'mi', value: 'calendar_today' }, { type: 'mi', value: 'schedule' }, { type: 'mi', value: 'list' },
                { type: 'mi', value: 'table_chart' }, { type: 'mi', value: 'filter_list' }, { type: 'mi', value: 'bar_chart' },
                { type: 'mi', value: 'show_chart' }, { type: 'mi', value: 'pie_chart' }, { type: 'mi', value: 'check_circle' },
                { type: 'mi', value: 'cancel' }, { type: 'mi', value: 'warning' }, { type: 'mi', value: 'info' },
                { type: 'mi', value: 'chat' }, { type: 'mi', value: 'work' }, { type: 'mi', value: 'shopping_cart' },
                { type: 'mi', value: 'credit_card' }, { type: 'mi', value: 'account_balance_wallet' }, { type: 'mi', value: 'local_shipping' },
                { type: 'mi', value: 'print' }, { type: 'mi', value: 'phone' }, { type: 'mi', value: 'emoji_events' },
                { type: 'mi', value: 'military_tech' }, { type: 'mi', value: 'bug_report' }, { type: 'mi', value: 'code' },
                { type: 'mi', value: 'terminal' }, { type: 'mi', value: 'storage' }, { type: 'mi', value: 'dns' },
                { type: 'mi', value: 'build' }, { type: 'mi', value: 'monitor_heart' }, { type: 'mi', value: 'directions_car' },
                { type: 'mi', value: 'directions_bus' }, { type: 'mi', value: 'train' }, { type: 'mi', value: 'directions_boat' },
                { type: 'mi', value: 'public' }, { type: 'mi', value: 'translate' }, { type: 'mi', value: 'dark_mode' },
                { type: 'mi', value: 'light_mode' }, { type: 'mi', value: 'rainy' }, { type: 'mi', value: 'local_fire_department' },
                { type: 'mi', value: 'eco' }, { type: 'mi', value: 'recycling' }, { type: 'mi', value: 'laptop' },
                { type: 'mi', value: 'desktop_windows' }, { type: 'mi', value: 'smartphone' }, { type: 'mi', value: 'headphones' },
                { type: 'mi', value: 'music_note' }, { type: 'mi', value: 'videocam' }, { type: 'mi', value: 'movie' },
                { type: 'mi', value: 'rss_feed' }, { type: 'mi', value: 'article' }, { type: 'mi', value: 'format_bold' },
                { type: 'mi', value: 'format_italic' }, { type: 'mi', value: 'format_align_left' }, { type: 'mi', value: 'format_align_center' },
                { type: 'mi', value: 'format_align_right' }, { type: 'mi', value: 'palette' }, { type: 'mi', value: 'brush' },
                { type: 'mi', value: 'crop' }, { type: 'mi', value: 'visibility' }, { type: 'mi', value: 'visibility_off' },
                { type: 'mi', value: 'fingerprint' }, { type: 'mi', value: 'verified_user' }, { type: 'mi', value: 'block' },
                { type: 'mi', value: 'local_hospital' }, { type: 'mi', value: 'medical_services' }, { type: 'mi', value: 'gavel' },
                { type: 'mi', value: 'school' }
            ];

            function buildGrid(gridEl, items, type, onClick) {
                if (!gridEl) return;
                gridEl.innerHTML = '';
                items.forEach(it => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = type === 'color' ? 'w-8 h-8 rounded-lg border' : 'w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700';

                    if (type === 'color') {
                        btn.style.background = it;
                        btn.setAttribute('data-hex', it);
                        btn.addEventListener('click', () => onClick && onClick(it));
                    } else {
                        // handle object entries like { type: 'fa'|'mi', value: '...' }
                        if (typeof it === 'object' && it.type) {
                            if (it.type === 'fa') {
                                btn.innerHTML = `<i class="fas ${it.value}"></i>`;
                                btn.setAttribute('data-value', it.value);
                                btn.addEventListener('click', () => onClick && onClick(it.value));
                            } else if (it.type === 'mi') {
                                btn.innerHTML = `<span class="material-icons" style="font-size:18px;line-height:1">${it.value}</span>`;
                                btn.setAttribute('data-value', it.value);
                                btn.addEventListener('click', () => onClick && onClick(it.value));
                            } else {
                                btn.textContent = it.value || '';
                                btn.setAttribute('data-value', it.value || '');
                                btn.addEventListener('click', () => onClick && onClick(it.value));
                            }
                        } else {
                            btn.textContent = it;
                            btn.setAttribute('data-value', it);
                            btn.addEventListener('click', () => onClick && onClick(it));
                        }
                    }

                    gridEl.appendChild(btn);
                });
            }

            function readAndSetFile(inputEl, hiddenEl, selectEl) {
                const f = inputEl.files && inputEl.files[0];
                if (!f) return;
                const reader = new FileReader();
                reader.onload = function (e) {
                    hiddenEl.value = e.target.result;
                    if (selectEl) {
                        const opt = Array.from(selectEl.options).find(o => o.value === 'custom');
                        if (opt) selectEl.value = 'custom';
                    }
                };
                reader.readAsDataURL(f);
            }

            document.addEventListener('DOMContentLoaded', () => {
                // Main maps
                buildGrid(document.getElementById('colorMapGridMain'), colors, 'color', (hex) => {
                    const cp = document.getElementById('categoryColor');
                    const ch = document.getElementById('categoryColorHex');
                    if (cp) cp.value = hex;
                    if (ch) ch.value = hex;
                });
                // Main/manage icon grid (for Category Management modal)
                buildGrid(document.getElementById('iconPickerGridManage'), icons, 'icon', (val) => {
                    const sel = document.getElementById('categoryIcon');
                    const hiddenCustom = document.getElementById('categoryIconCustomManage');
                    if (sel) {
                        let opt = Array.from(sel.options).find(o => o.value === val || o.text.includes(val));
                        if (!opt) {
                            opt = document.createElement('option');
                            opt.value = val;
                            opt.text = val;
                            sel.appendChild(opt);
                        }
                        sel.value = opt.value;
                    }
                    if (hiddenCustom) hiddenCustom.value = '';
                });

                // Quick maps
                buildGrid(document.getElementById('quickColorMapGrid'), colors, 'color', (hex) => {
                    const q = document.getElementById('quickCategoryColor');
                    if (q) {
                        const match = Array.from(q.options).find(o => (o.value || '').toLowerCase() === (hex || '').toLowerCase());
                        if (match) {
                            q.value = match.value;
                        } else {
                            const opt = document.createElement('option');
                            opt.value = hex;
                            opt.text = hex;
                            q.appendChild(opt);
                            q.value = hex;
                        }
                    }
                });
                // Populate main icon grid (used by the quick-type section)
                buildGrid(document.getElementById('iconPickerGridMain'), icons, 'icon', (val) => {
                    const sel = document.getElementById('quickCategoryIcon');
                    const hidden = document.getElementById('categoryIconCustom');
                    if (sel) {
                        let opt = Array.from(sel.options).find(o => o.value === val || o.text.includes(val));
                        if (!opt) {
                            opt = document.createElement('option');
                            opt.value = val;
                            opt.text = val;
                            sel.appendChild(opt);
                        }
                        sel.value = opt.value;
                    }
                    if (hidden) hidden.value = '';
                });
                buildGrid(document.getElementById('quickIconPickerGrid'), icons, 'icon', (val) => {
                    const sel = document.getElementById('quickCategoryIcon');
                    const hiddenQuick = document.getElementById('quickCategoryIconCustom');
                    if (sel) {
                        let opt = Array.from(sel.options).find(o => o.value === val || o.text.includes(val));
                        if (!opt) {
                            opt = document.createElement('option');
                            opt.value = val;
                            opt.text = val;
                            sel.appendChild(opt);
                        }
                        sel.value = opt.value;
                    }
                    if (hiddenQuick) hiddenQuick.value = '';
                });

                // Toggle buttons
                const ocMain = document.getElementById('openColorMapMain');
                const cmMain = document.getElementById('colorMapMain');
                if (ocMain && cmMain) ocMain.addEventListener('click', () => cmMain.classList.toggle('hidden'));

                // quick/main icon picker (quick add)
                const oiMain = document.getElementById('openIconPickerMain');
                const ipMain = document.getElementById('iconPickerMain');
                if (oiMain && ipMain) oiMain.addEventListener('click', () => ipMain.classList.toggle('hidden'));

                // manage modal icon picker
                const oiManage = document.getElementById('openIconPickerManage');
                const ipManage = document.getElementById('iconPickerManage');
                if (oiManage && ipManage) oiManage.addEventListener('click', () => ipManage.classList.toggle('hidden'));

                const oq = document.getElementById('openQuickColorMap');
                const qcm = document.getElementById('quickColorMap');
                if (oq && qcm) oq.addEventListener('click', () => qcm.classList.toggle('hidden'));

                const oqi = document.getElementById('openQuickIconPicker');
                const qip = document.getElementById('quickIconPicker');
                if (oqi && qip) oqi.addEventListener('click', () => qip.classList.toggle('hidden'));

                // Upload handlers
                const upMain = document.getElementById('uploadIconBtnMain');
                const fileMain = document.getElementById('categoryIconFileMain');
                const hiddenMain = document.getElementById('categoryIconCustom');
                const selMain = document.getElementById('categoryIcon');
                if (upMain && fileMain) upMain.addEventListener('click', () => fileMain.click());
                if (fileMain && hiddenMain) fileMain.addEventListener('change', () => readAndSetFile(fileMain, hiddenMain, selMain));

                // manage upload handlers
                const upManage = document.getElementById('uploadIconBtnManage');
                const fileManage = document.getElementById('categoryIconFileManage');
                const hiddenManage = document.getElementById('categoryIconCustomManage');
                const selManage = document.getElementById('categoryIcon');
                if (upManage && fileManage) upManage.addEventListener('click', () => fileManage.click());
                if (fileManage && hiddenManage) fileManage.addEventListener('change', () => readAndSetFile(fileManage, hiddenManage, selManage));

                const upQuick = document.getElementById('uploadQuickIconBtn');
                const fileQuick = document.getElementById('quickCategoryIconFile');
                const hiddenQuick = document.getElementById('quickCategoryIconCustom');
                const selQuick = document.getElementById('quickCategoryIcon');
                if (upQuick && fileQuick) upQuick.addEventListener('click', () => fileQuick.click());
                if (fileQuick && hiddenQuick) fileQuick.addEventListener('change', () => readAndSetFile(fileQuick, hiddenQuick, selQuick));
            });
        })();

        async function fetchTransactions(page = 1) {
            try {
                const search = document.getElementById('searchInput').value;
                const sortBy = document.getElementById('sortBy').value;
                const filterType = document.getElementById('filterType').value;
                const filterCategory = document.getElementById('filterCategory').value;
                const filterDateFrom = document.getElementById('filterDateFrom').value;
                const filterDateTo = document.getElementById('filterDateTo').value;

                const response = await fetch('/transactions/list', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    let transactions = data.data;

                    // Apply search filter
                    if (search) {
                        transactions = transactions.filter(tx =>
                            tx.description?.toLowerCase().includes(search.toLowerCase()) ||
                            tx.merchant?.toLowerCase().includes(search.toLowerCase()) ||
                            tx.category?.name?.toLowerCase().includes(search.toLowerCase())
                        );
                    }

                    // Apply type filter
                    if (filterType) {
                        transactions = transactions.filter(tx => tx.type === filterType);
                    }

                    // Apply category filter
                    if (filterCategory) {
                        transactions = transactions.filter(tx => tx.category?.name === filterCategory);
                    }

                    // Apply date range filter
                    if (filterDateFrom) {
                        transactions = transactions.filter(tx => tx.date >= filterDateFrom);
                    }
                    if (filterDateTo) {
                        transactions = transactions.filter(tx => tx.date <= filterDateTo);
                    }

                    // Apply sorting
                    const [field, direction] = sortBy.split('-');
                    transactions.sort((a, b) => {
                        let aVal = field === 'amount' ? parseFloat(a.amount) : a.date;
                        let bVal = field === 'amount' ? parseFloat(b.amount) : b.date;

                        if (direction === 'asc') {
                            return aVal > bVal ? 1 : -1;
                        } else {
                            return aVal < bVal ? 1 : -1;
                        }
                    });

                    transactionsData = transactions;
                    displayTransactions(transactions);
                    updatePagination({
                        total: transactions.length,
                        from: transactions.length > 0 ? 1 : 0,
                        to: transactions.length
                    });
                }
            } catch (error) {
                console.error('Error fetching transactions:', error);
            }
        }

        function displayTransactions(transactions) {
            // Desktop table
            const tbody = document.getElementById('transactionsTableBody');

            if (!transactions || transactions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No transactions found</td></tr>';
                return;
            }

            tbody.innerHTML = transactions.map(tx => `
                                                                                                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" data-transaction-id="${tx.id}">
                                                                                                                                        <td class="px-6 py-4">
                                                                                                                                            <input type="checkbox" value="${tx.id}" 
                                                                                                                                                   class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500"
                                                                                                                                                   @change="toggleTransaction(${tx.id})">
                                                                                                                                        </td>
                                                                                                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                                                                                                            ${formatDate(tx.date)}
                                                                                                                                        </td>
                                                                                                                                        <td class="px-6 py-4">
                                                                                                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">${tx.description || 'N/A'}</div>
                                                                                                                                            ${tx.merchant ? `<div class="text-xs text-gray-500 dark:text-gray-400">${tx.merchant}</div>` : ''}
                                                                                                                                        </td>
                                                                                                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                                                                                                            ${tx.category ? `
                                                                                                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color: ${tx.category.color}20; color: ${tx.category.color}">
                                                                                                                                                    ${tx.category.icon ? `<i class="${tx.category.icon} mr-1"></i>` : ''}
                                                                                                                                                    ${tx.category.name}
                                                                                                                                                </span>
                                                                                                                                            ` : '<span class="text-xs text-gray-500 dark:text-gray-400">Uncategorized</span>'}
                                                                                                                                        </td>
                                                                                                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold ${tx.type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">
                                                                                                                                            ${tx.type === 'credit' ? '+' : '-'}${formatCurrency(tx.amount)}
                                                                                                                                        </td>
                                                                                                                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                                                                                                                            <button onclick="editTransaction('${tx.id}')" class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 mr-3" title="Edit">
                                                                                                                                                <i class="fas fa-edit"></i>
                                                                                                                                            </button>
                                                                                                                                            <button onclick="deleteTransaction('${tx.id}')" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300" title="Delete">
                                                                                                                                                <i class="fas fa-trash"></i>
                                                                                                                                            </button>
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                `).join('');

            // Mobile cards
            const mobileList = document.getElementById('transactionsMobileList');
            mobileList.innerHTML = transactions.map(tx => `
                                                                                                                                    <div class="p-4" data-transaction-id="${tx.id}">
                                                                                                                                        <div class="flex items-center space-x-4">
                                                                                                                                            <div class="w-12 h-12 ${tx.type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'} rounded-lg flex items-center justify-center flex-shrink-0">
                                                                                                                                                <i class="fas ${tx.type === 'credit' ? 'fa-arrow-down text-green-600 dark:text-green-400' : 'fa-arrow-up text-red-600 dark:text-red-400'}"></i>
                                                                                                                                            </div>
                                                                                                                                            <div class="flex-1 min-w-0">
                                                                                                                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${tx.description || 'N/A'}</p>
                                                                                                                                                <p class="text-xs text-gray-500 dark:text-gray-400">${tx.category?.name || 'Uncategorized'} • ${formatDate(tx.date)}</p>
                                                                                                                                            </div>
                                                                                                                                            <div class="text-right">
                                                                                                                                                <p class="text-sm font-semibold ${tx.type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">
                                                                                                                                                    ${tx.type === 'credit' ? '+' : '-'}${formatCurrency(tx.amount)}
                                                                                                                                                </p>
                                                                                                                                                <div class="mt-1 space-x-2">
                                                                                                                                                    <button onclick="editTransaction(${tx.id})" class="text-xs text-primary-600 dark:text-primary-400">
                                                                                                                                                        <i class="fas fa-edit"></i>
                                                                                                                                                    </button>
                                                                                                                                                    <button onclick="editTransaction('${tx.id}')" class="text-xs text-primary-600 dark:text-primary-400 mr-2" title="Edit">
                                                                                                                                                        <i class="fas fa-edit"></i>
                                                                                                                                                    </button>
                                                                                                                                                    <button onclick="deleteTransaction('${tx.id}')" class="text-xs text-red-600 dark:text-red-400" title="Delete">
                                                                                                                                                        <i class="fas fa-trash"></i>
                                                                                                                                                    </button>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                `).join('');
        }

        function updatePagination(meta) {
            document.getElementById('paginationStart').textContent = meta.from || 0;
            document.getElementById('paginationEnd').textContent = meta.to || 0;
            document.getElementById('paginationTotal').textContent = meta.total || 0;
        }

        async function loadCategories() {
            try {
                const response = await fetch('/categories/list', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        window.categoriesData = data.data;
                        populateCategoryDropdowns(data.data);
                    }
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        function populateCategoryDropdowns(categories) {
            // Populate filter dropdown
            const filterSelect = document.getElementById('filterCategory');
            const incomeCategories = categories.filter(c => c.type === 'income');
            const expenseCategories = categories.filter(c => c.type === 'expense');

            let filterOptions = '<option value="">All Categories</option>';
            if (incomeCategories.length > 0) {
                filterOptions += '<optgroup label="Income">';
                incomeCategories.forEach(cat => {
                    filterOptions += `<option value="${cat.id}">${cat.name}</option>`;
                });
                filterOptions += '</optgroup>';
            }
            if (expenseCategories.length > 0) {
                filterOptions += '<optgroup label="Expenses">';
                expenseCategories.forEach(cat => {
                    filterOptions += `<option value="${cat.id}">${cat.name}</option>`;
                });
                filterOptions += '</optgroup>';
            }
            filterSelect.innerHTML = filterOptions;

            // Populate transaction category dropdown
            const categorySelect = document.getElementById('transactionCategory');
            let categoryOptions = '<option value="">Select category...</option>';
            if (incomeCategories.length > 0) {
                categoryOptions += '<optgroup label="Income">';
                incomeCategories.forEach(cat => {
                    categoryOptions += `<option value="${cat.id}"><i class="fas ${cat.icon}"></i> ${cat.name}</option>`;
                });
                categoryOptions += '</optgroup>';
            }
            if (expenseCategories.length > 0) {
                categoryOptions += '<optgroup label="Expenses">';
                expenseCategories.forEach(cat => {
                    categoryOptions += `<option value="${cat.id}"><i class="fas ${cat.icon}"></i> ${cat.name}</option>`;
                });
                categoryOptions += '</optgroup>';
            }
            categorySelect.innerHTML = categoryOptions;
        }

        function openQuickAddCategory() {
            document.getElementById('quickCategoryModal').classList.remove('hidden');
        }

        function closeQuickCategoryModal() {
            document.getElementById('quickCategoryModal').classList.add('hidden');
            document.getElementById('quickCategoryForm').reset();
        }

        async function saveQuickCategory(event) {
            event.preventDefault();

            const name = document.getElementById('quickCategoryName').value;
            const type = document.getElementById('quickCategoryType').value;
            const color = document.getElementById('quickCategoryColor').value;
            const icon = document.getElementById('quickCategoryIcon').value;

            const saveBtn = event.target.querySelector('button[type="submit"]');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';

            // Map expense/income to debit/credit for API
            const typeMapping = {
                'expense': 'debit',
                'income': 'credit'
            };

            try {
                const response = await fetch('/categories/create-ajax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        name: name,
                        type: typeMapping[type],
                        color: color,
                        icon: icon,
                        icon_custom: document.getElementById('quickCategoryIconCustom') ? document.getElementById('quickCategoryIconCustom').value : null
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showNotification('Category created successfully!', 'success');
                    closeQuickCategoryModal();
                    await loadCategories(); // Reload categories

                    // Auto-select the newly created category
                    const categorySelect = document.getElementById('transactionCategory');
                    categorySelect.value = data.data.id;
                } else {
                    showNotification(data.message || 'Failed to create category', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('An error occurred while creating category', 'error');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Create Category';
            }
        }

        function applyFilters() {
            fetchTransactions();
        }

        function clearFilters() {
            document.getElementById('filterType').value = '';
            document.getElementById('filterCategory').value = '';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            document.getElementById('searchInput').value = '';
            fetchTransactions();
        }

        function openAddTransactionModal() {
            document.getElementById('modalTitle').textContent = 'Add Transaction';
            document.getElementById('transactionId').value = '';
            document.getElementById('transactionModal').classList.remove('hidden');
            document.getElementById('transactionForm').reset();
            document.querySelector('[name="date"]').value = new Date().toISOString().split('T')[0];
        }

        function closeTransactionModal() {
            document.getElementById('transactionModal').classList.add('hidden');
        }

        async function editTransaction(id) {
            try {
                const response = await fetch(`/transactions/${id}/show`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    const tx = result.data;

                    document.getElementById('modalTitle').textContent = 'Edit Transaction';
                    document.getElementById('transactionId').value = tx.id;
                    document.querySelector('[name="type"][value="' + tx.type + '"]').checked = true;
                    document.querySelector('[name="amount"]').value = tx.amount;
                    document.querySelector('[name="description"]').value = tx.description;
                    document.querySelector('[name="category_id"]').value = tx.category_id;
                    document.querySelector('[name="date"]').value = tx.date;
                    document.querySelector('[name="merchant"]').value = tx.merchant || '';
                    document.querySelector('[name="notes"]').value = tx.notes || '';

                    document.getElementById('transactionModal').classList.remove('hidden');
                } else {
                    showNotification('Failed to load transaction', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Failed to load transaction', 'error');
            }
        }

        document.getElementById('transactionForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            const transactionId = document.getElementById('transactionId').value;
            const isEdit = transactionId !== '';

            try {
                const url = isEdit ? `/transactions/${transactionId}/update` : '/transactions/store';
                const method = isEdit ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    closeTransactionModal();
                    fetchTransactions();
                    showNotification(isEdit ? 'Transaction updated successfully' : 'Transaction added successfully', 'success');
                } else {
                    showNotification(result.message || 'Failed to save transaction', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Failed to save transaction', 'error');
            }
        });

        async function deleteTransaction(id) {
            popupConfirm(
                'Are you sure you want to delete this transaction? This action cannot be undone.',
                'Delete Transaction',
                async function () {
                    await performDeleteTransaction(id);
                }
            );
        }

        async function performDeleteTransaction(id) {
            try {
                const response = await fetch(`/transactions/${id}/delete-ajax`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    fetchTransactions();
                    showNotification('Transaction deleted successfully', 'success');
                } else {
                    const data = await response.json();
                    showNotification(data.message || 'Failed to delete transaction', 'error');
                }
            } catch (error) {
                console.error('Error deleting transaction:', error);
                showNotification('Failed to delete transaction', 'error');
            }
        }

        function formatCurrency(amount) {
            return window.AppCurrency.format(amount);
        }

        // formatDate function is now global from app.blade.php

        function showNotification(message, type = 'info') {
            // Using custom popup system
            if (type === 'error') {
                popupError(message, 'Error');
            } else if (type === 'success') {
                popupSuccess(message, 'Success');
            } else if (type === 'warning') {
                popupWarning(message, 'Warning');
            } else {
                popupAlert(message, 'Notification', 'info');
            }
        }

        function applyFilters() {
            fetchTransactions();
        }

        function clearFilters() {
            document.getElementById('filterType').value = '';
            document.getElementById('filterCategory').value = '';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            fetchTransactions();
        }

        // Category Management Functions
        let categoriesData = [];

        function openCategoryModal() {
            document.getElementById('categoryModal').classList.remove('hidden');
            fetchCategoriesForModal();
        }

        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.add('hidden');
            document.getElementById('createCategoryForm').reset();
        }

        async function fetchCategoriesForModal() {
            const loading = document.getElementById('categoriesLoading');
            const empty = document.getElementById('categoriesEmpty');
            const list = document.getElementById('categoriesList');

            loading.classList.remove('hidden');
            empty.classList.add('hidden');
            list.classList.add('hidden');

            try {
                console.log('Fetching categories from /categories/list...');
                const response = await fetch('/categories/list', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);

                if (response.ok) {
                    const data = await response.json();
                    console.log('Raw response data:', data);
                    console.log('Categories data:', data.data);
                    console.log('Categories count:', data.data ? data.data.length : 0);

                    categoriesData = data.data || [];
                    displayCategories(categoriesData);
                } else {
                    throw new Error('Failed to fetch categories');
                }
            } catch (error) {
                loading.classList.add('hidden');
                empty.classList.remove('hidden');
                console.error('Error fetching categories:', error);
            }
        }

        function displayCategories(categories) {
            console.log('displayCategories called with:', categories);
            console.log('Categories length:', categories.length);

            const loading = document.getElementById('categoriesLoading');
            const empty = document.getElementById('categoriesEmpty');
            const list = document.getElementById('categoriesList');
            const count = document.getElementById('categoryCount');

            loading.classList.add('hidden');

            if (categories.length === 0) {
                console.log('No categories to display, showing empty state');
                empty.classList.remove('hidden');
                list.classList.add('hidden');
                count.textContent = '0';
                return;
            }

            console.log('Displaying categories, hiding empty state');
            empty.classList.add('hidden');
            list.classList.remove('hidden');
            count.textContent = categories.length;

            list.innerHTML = categories.map(cat => {
                // Handle both 'type' formats
                const typeDisplay = (cat.type === 'income' || cat.display_type === 'credit' || cat.type === 'credit') ? '💰 Income' : '💸 Expense';

                return `
                                                                                                                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow">
                                                                                                                                <div class="flex items-center gap-3 flex-1">
                                                                                                                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: ${cat.color}20;">
                                                                                                                                        <i class="fas ${cat.icon || 'fa-tag'} text-lg" style="color: ${cat.color};"></i>
                                                                                                                                    </div>
                                                                                                                                    <div class="flex-1">
                                                                                                                                        <h4 class="font-semibold text-gray-900 dark:text-white">${cat.name}</h4>
                                                                                                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                                                                                            ${typeDisplay}
                                                                                                                                            ${cat.description ? ' • ' + cat.description : ''}
                                                                                                                                        </p>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                                <button onclick="deleteCategory(${cat.id}, '${cat.name}')" 
                                                                                                                                    class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                                                                                                                    <i class="fas fa-trash text-sm"></i>
                                                                                                                                </button>
                                                                                                                            </div>
                                                                                                                        `;
            }).join('');
        }

        async function deleteCategory(id, name) {
            popupConfirm(
                `Are you sure you want to delete "${name}"? Transactions in this category will become uncategorized.`,
                'Delete Category',
                async () => {
                    try {
                        const response = await fetch(`/categories/${id}/delete-ajax`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            popupSuccess('Category deleted successfully!', 'Success');
                            fetchCategoriesForModal();
                            fetchTransactions(); // Refresh transactions to update categories
                        } else {
                            throw new Error('Failed to delete category');
                        }
                    } catch (error) {
                        popupError('Error deleting category: ' + error.message, 'Error');
                    }
                }
            );
        }

        // Color picker sync
        document.addEventListener('DOMContentLoaded', function () {
            const colorPicker = document.getElementById('categoryColor');
            const colorHex = document.getElementById('categoryColorHex');

            if (colorPicker && colorHex) {
                colorPicker.addEventListener('input', function () {
                    colorHex.value = this.value;
                });

                colorHex.addEventListener('input', function () {
                    if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                        colorPicker.value = this.value;
                    }
                });
            }
        });

        // Create Category Form Handler
        document.getElementById('createCategoryForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const name = document.getElementById('categoryName').value.trim();
            const type = document.getElementById('categoryType').value;
            const icon = document.getElementById('categoryIcon').value;
            const color = document.getElementById('categoryColor').value;
            const description = document.getElementById('categoryDescription').value.trim();

            if (!name) {
                popupError('Please enter a category name.', 'Validation Error');
                return;
            }

            try {
                const response = await fetch('/categories/create-ajax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        name,
                        type,
                        icon,
                        icon_custom: document.getElementById('categoryIconCustomManage') ? document.getElementById('categoryIconCustomManage').value : null,
                        color,
                        description: description || null,
                        is_active: true
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    popupSuccess(`Category "${name}" created successfully!`, 'Success');
                    document.getElementById('createCategoryForm').reset();
                    document.getElementById('categoryColor').value = '#3b82f6';
                    document.getElementById('categoryColorHex').value = '#3b82f6';
                    fetchCategoriesForModal();
                    fetchTransactions(); // Refresh to update category filters
                } else {
                    throw new Error(data.message || 'Failed to create category');
                }
            } catch (error) {
                popupError('Error creating category: ' + error.message, 'Error');
            }
        });
    </script>
@endpush