@extends('layouts.app')

@section('title', 'Add Transaction')
@section('breadcrumb', 'Add Transaction')

@section('content')
    <div class="animate-fade-in">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Add New Transaction</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Record a new financial transaction</p>
            </div>
            <a href="{{ route('transactions.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Transactions
            </a>
        </div>

        <!-- Transaction Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 lg:p-8">
            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Transaction Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Transaction Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label
                                class="relative flex items-center justify-center p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary-500 transition-colors">
                                <input type="radio" name="type" value="debit" class="sr-only peer" required>
                                <div class="text-center peer-checked:text-red-600">
                                    <i class="fas fa-arrow-down text-2xl mb-2"></i>
                                    <div class="font-medium">Expense</div>
                                </div>
                                <div
                                    class="absolute inset-0 border-2 border-red-600 rounded-lg opacity-0 peer-checked:opacity-100">
                                </div>
                            </label>
                            <label
                                class="relative flex items-center justify-center p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary-500 transition-colors">
                                <input type="radio" name="type" value="credit" class="sr-only peer" required>
                                <div class="text-center peer-checked:text-green-600">
                                    <i class="fas fa-arrow-up text-2xl mb-2"></i>
                                    <div class="font-medium">Income</div>
                                </div>
                                <div
                                    class="absolute inset-0 border-2 border-green-600 rounded-lg opacity-0 peer-checked:opacity-100">
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span
                                class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                            <input type="number" step="0.01" min="0" name="amount" id="amount" required
                                value="{{ old('amount') }}"
                                class="w-full pl-8 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="0.00">
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="description" id="description" required value="{{ old('description') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="e.g., Grocery shopping, Salary payment">
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Select a category</option>
                            @forelse($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @empty
                                <option value="" disabled>No categories available - Please create categories first</option>
                            @endforelse
                        </select>
                        @if($categories->isEmpty())
                            <p class="mt-1 text-sm text-amber-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                You need to <a href="{{ route('categories.index') }}" class="underline font-medium">create
                                    categories</a> before adding transactions.
                            </p>
                        @endif
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <label for="transaction_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="transaction_date" id="transaction_date" required
                            value="{{ old('transaction_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        @error('transaction_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Merchant (Optional) -->
                    <div>
                        <label for="merchant" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Merchant/Payee (Optional)
                        </label>
                        <input type="text" name="merchant" id="merchant" value="{{ old('merchant') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="e.g., Walmart, John Doe">
                        @error('merchant')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes (Full Width) -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="Add any additional notes about this transaction...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('transactions.index') }}"
                        class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>
                        Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection