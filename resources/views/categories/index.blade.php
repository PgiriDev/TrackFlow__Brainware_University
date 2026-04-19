@extends('layouts.app')

@section('title', 'Categories')
@section('breadcrumb', 'Categories')

@section('content')
    <!-- Colorful Glassmorphism Page Background - Rose/Pink Theme -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-rose-100 via-pink-50 to-fuchsia-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-rose-300/40 to-pink-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-rose-600/10 dark:to-pink-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-fuchsia-300/40 to-purple-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-fuchsia-600/10 dark:to-purple-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-pink-300/30 to-rose-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-pink-600/10 dark:to-rose-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-red-300/30 to-rose-400/30 rounded-full blur-3xl dark:from-red-600/10 dark:to-rose-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-pink-300/30 to-fuchsia-400/30 rounded-full blur-3xl dark:from-pink-600/10 dark:to-fuchsia-700/10">
        </div>
    </div>

    <div class="animate-fade-in relative" x-data="{ showAddModal: false, editingCategory: null }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Categories</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Organize your transactions with custom categories</p>
            </div>
            <button @click="showAddModal = true"
                class="mt-4 sm:mt-0 inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>
                Add Category
            </button>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
            <!-- Total Categories -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 gradient-primary rounded-lg flex items-center justify-center">
                        <i class="fas fa-tags text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Total Categories</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="totalCategories">0</p>
            </div>

            <!-- Expense Categories -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-up text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Expense Categories</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="expenseCategories">0</p>
            </div>

            <!-- Income Categories -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-down text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Income Categories</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="incomeCategories">0</p>
            </div>

            <!-- Most Used -->
            <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Most Used</h3>
                <p class="text-lg font-bold text-gray-900 dark:text-white mt-2" id="mostUsed">-</p>
            </div>
        </div>

        <!-- Categories List -->
        <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 card-shadow">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">All Categories</h2>
                <div class="flex items-center gap-2">
                    <select id="filterType"
                        class="text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                        <option value="">All Types</option>
                        <option value="expense">Expenses</option>
                        <option value="income">Income</option>
                    </select>
                </div>
            </div>

            <!-- Categories Grid -->
            <div class="p-6">
                <div id="categoriesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Add/Edit Category Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="showAddModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                @click.stop>
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Category Management</h2>
                    <button @click="showAddModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <!-- Create Category Form -->
                    <form @submit.prevent="saveCategory" id="categoryForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Category Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Category Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="categoryName" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500"
                                    placeholder="e.g., Semester Fees">
                            </div>

                            <!-- Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Type <span class="text-red-500">*</span>
                                </label>
                                <select id="categoryType" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
                                    <option value="expense">Expense</option>
                                    <option value="income">Income</option>
                                </select>
                            </div>

                            <!-- Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Color <span class="text-red-500">*</span>
                                </label>
                                <select id="categoryColor" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
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
                            </div>

                            <!-- Icon -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Icon <span class="text-red-500">*</span>
                                </label>
                                <select id="categoryIcon" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500">
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
                            </div>

                            <!-- Description (Optional) -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Description (Optional)
                                </label>
                                <textarea id="categoryDescription" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500"
                                    placeholder="Brief description of the category"></textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" @click="showAddModal = false"
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit" id="saveCategoryBtn"
                                class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">
                                <i class="fas fa-save mr-2"></i>
                                Create Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let categories = [];

        document.addEventListener('DOMContentLoaded', function () {
            loadCategories();

            // Filter functionality
            document.getElementById('filterType').addEventListener('change', function () {
                const filterValue = this.value;
                if (filterValue === '') {
                    displayCategories(categories);
                } else {
                    const filtered = categories.filter(cat => cat.type === filterValue);
                    displayCategories(filtered);
                }
            });
        });

        function loadCategories() {
            fetch('/categories/list', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Categories API response:', data);
                    if (data.success && data.data) {
                        categories = data.data;
                        displayCategories(categories);
                        updateStats();
                    } else {
                        console.error('Invalid response format:', data);
                        showNotification('Failed to load categories', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                    showNotification('Failed to load categories: ' + error.message, 'error');
                });
        }

        function displayCategories(cats) {
            const grid = document.getElementById('categoriesGrid');

            if (cats.length === 0) {
                grid.innerHTML = `
                                    <div class="col-span-full text-center py-12">
                                        <i class="fas fa-tags text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400">No categories found</p>
                                    </div>
                                `;
                return;
            }

            grid.innerHTML = cats.map(category => {
                const colorClass = getColorClass(category.color);
                return `
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow border-l-4" style="border-left-color: ${category.color};">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background-color: ${category.color}20;">
                                                    <i class="fas ${category.icon || 'fa-tag'} text-xl" style="color: ${category.color};"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-semibold text-gray-900 dark:text-white">${category.name}</h3>
                                                    <span class="text-xs px-2 py-0.5 rounded ${category.type === 'income'
                        ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
                        : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
                    }">
                                                        ${category.type.charAt(0).toUpperCase() + category.type.slice(1)}
                                                    </span>
                                                </div>
                                            </div>
                                            ${!category.is_system ? `
                                                <button onclick="deleteCategory(${category.id})" 
                                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            ` : ''}
                                        </div>
                                    </div>
                                `;
            }).join('');
        }

        function getColorClass(color) {
            const colorMap = {
                '#3B82F6': 'bg-blue-500',
                '#10B981': 'bg-green-500',
                '#EF4444': 'bg-red-500',
                '#F59E0B': 'bg-orange-500',
                '#8B5CF6': 'bg-purple-500',
                '#EC4899': 'bg-pink-500',
                '#14B8A6': 'bg-teal-500',
                '#F97316': 'bg-orange-600',
                '#6366F1': 'bg-indigo-500',
                '#6B7280': 'bg-gray-500'
            };
            return colorMap[color] || 'bg-blue-500';
        }

        function updateStats() {
            const expenseCount = categories.filter(c => c.type === 'expense').length;
            const incomeCount = categories.filter(c => c.type === 'income').length;

            document.getElementById('totalCategories').textContent = categories.length;
            document.getElementById('expenseCategories').textContent = expenseCount;
            document.getElementById('incomeCategories').textContent = incomeCount;

            // Find most used category (for now just show first one)
            if (categories.length > 0) {
                document.getElementById('mostUsed').textContent = categories[0].name;
            }
        }

        function saveCategory(event) {
            event.preventDefault();

            const name = document.getElementById('categoryName').value;
            const type = document.getElementById('categoryType').value;
            const color = document.getElementById('categoryColor').value;
            const icon = document.getElementById('categoryIcon').value;
            const description = document.getElementById('categoryDescription').value;

            const saveBtn = document.getElementById('saveCategoryBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';

            // Map expense/income to debit/credit for API
            const typeMapping = {
                'expense': 'debit',
                'income': 'credit'
            };

            fetch('/categories/create-ajax', {
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
                    description: description
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Category created successfully!', 'success');
                        document.getElementById('categoryForm').reset();
                        Alpine.store('showAddModal', false);
                        loadCategories(); // Reload categories
                    } else {
                        showNotification(data.message || 'Failed to create category', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while creating category', 'error');
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Create Category';
                });
        }

        function deleteCategory(id) {
            if (!confirm('Are you sure you want to delete this category?')) {
                return;
            }

            fetch(`/categories/${id}/delete-ajax`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Category deleted successfully!', 'success');
                        loadCategories(); // Reload categories
                    } else {
                        showNotification(data.message || 'Failed to delete category', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while deleting category', 'error');
                });
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white transform transition-all duration-300 ${type === 'success' ? 'bg-green-600' :
                type === 'error' ? 'bg-red-600' : 'bg-blue-600'
                }`;
            notification.innerHTML = `
                                <div class="flex items-center gap-3">
                                    <i class="fas ${type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'
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

        // Make saveCategory available to Alpine.js
        window.saveCategory = saveCategory;
    </script>
@endsection
