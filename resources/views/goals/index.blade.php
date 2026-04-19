@extends('layouts.app')

@section('title', 'Goals')
@section('breadcrumb', 'Goals')

@section('content')
    <style>
        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .animate-shimmer {
            animation: shimmer 2s infinite;
        }
    </style>

    <!-- Colorful Glassmorphism Page Background - Teal/Cyan Theme -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-teal-100 via-cyan-50 to-sky-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-teal-300/40 to-cyan-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-teal-600/10 dark:to-cyan-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-sky-300/40 to-blue-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-sky-600/10 dark:to-blue-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-cyan-300/30 to-teal-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-cyan-600/10 dark:to-teal-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-emerald-300/30 to-teal-400/30 rounded-full blur-3xl dark:from-emerald-600/10 dark:to-teal-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-cyan-300/30 to-sky-400/30 rounded-full blur-3xl dark:from-cyan-600/10 dark:to-sky-700/10">
        </div>
    </div>

    <div class="animate-fade-in relative" x-data="{ showAddModal: false }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Financial Goals</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Set and track your savings goals</p>
            </div>
            <button @click="showAddModal = true"
                class="mt-4 sm:mt-0 inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>
                Create Goal
            </button>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
            <!-- Total Goals -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 gradient-primary rounded-lg flex items-center justify-center">
                        <i class="fas fa-bullseye text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Total Goals</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="totalGoals">0</p>
            </div>

            <!-- Target Amount -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-rupee-sign text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Target Amount</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="targetAmount"><span class="font-normal"
                        data-currency-symbol>{{ $currencySymbol }}</span>0.00</p>
            </div>

            <!-- Saved Amount -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-piggy-bank text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Saved</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="savedAmount"><span class="font-normal"
                        data-currency-symbol>{{ $currencySymbol }}</span>0.00</p>
            </div>

            <!-- Active Goals -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">In Progress</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="activeGoals">0</p>
            </div>
        </div>

        <!-- Goals List -->
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 card-shadow">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Goals</h2>
            </div>

            <!-- Goals Container -->
            <div id="goalsContainer" class="p-6">
                <!-- Loading State -->
                <div id="goalsLoading" class="text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Loading goals...</p>
                </div>

                <!-- Empty State -->
                <div id="goalsEmpty" class="hidden text-center py-12">
                    <div
                        class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bullseye text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No goals yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Create your first financial goal to start saving</p>
                    <button @click="showAddModal = true"
                        class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Goal
                    </button>
                </div>

                <!-- Goals Grid -->
                <div id="goalsList" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Add Goal Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.self="showAddModal = false"
            x-data="{ 
                                                goalType: '',
                                                customGoalType: '',
                                                goalName: '',
                                                targetAmount: '',
                                                currentAmount: '0',
                                                deadline: '',
                                                description: '',
                                                selectedIcon: '🎯',
                                                selectedColor: '#3B82F6',
                                                priority: 'medium',
                                                icons: ['🎯', '🏠', '🚗', '✈️', '🎓', '💰', '🏖️', '💍', '🏥', '📱', '🎮', '🎸', '💎', '🎨', '🏋️', '📚', '🌟', '🎁', '💼', '🏦', '👶', '💒', '🎄', '🎂'],
                                                colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#06B6D4', '#84CC16'],
                                                goalTypes: [
                                                    { value: 'emergency', label: 'Emergency Fund', icon: 'fa-shield-alt', color: 'from-red-500 to-rose-600', bgColor: 'bg-red-100 dark:bg-red-900/30' },
                                                    { value: 'vacation', label: 'Vacation', icon: 'fa-plane', color: 'from-blue-500 to-cyan-600', bgColor: 'bg-blue-100 dark:bg-blue-900/30' },
                                                    { value: 'home', label: 'Home', icon: 'fa-home', color: 'from-green-500 to-emerald-600', bgColor: 'bg-green-100 dark:bg-green-900/30' },
                                                    { value: 'car', label: 'Car', icon: 'fa-car', color: 'from-purple-500 to-violet-600', bgColor: 'bg-purple-100 dark:bg-purple-900/30' },
                                                    { value: 'education', label: 'Education', icon: 'fa-graduation-cap', color: 'from-yellow-500 to-amber-600', bgColor: 'bg-yellow-100 dark:bg-yellow-900/30' },
                                                    { value: 'retirement', label: 'Retirement', icon: 'fa-umbrella-beach', color: 'from-teal-500 to-cyan-600', bgColor: 'bg-teal-100 dark:bg-teal-900/30' },
                                                    { value: 'wedding', label: 'Wedding', icon: 'fa-heart', color: 'from-pink-500 to-rose-600', bgColor: 'bg-pink-100 dark:bg-pink-900/30' },
                                                    { value: 'custom', label: 'Custom', icon: 'fa-plus-circle', color: 'from-gray-500 to-slate-600', bgColor: 'bg-gray-100 dark:bg-gray-700' }
                                                ],
                                                getSelectedGoalType() {
                                                    return this.goalTypes.find(g => g.value === this.goalType) || null;
                                                }
                                            }">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" @click.stop>

                <!-- Modal Header with Gradient -->
                <div class="relative bg-gradient-to-r from-primary-600 via-purple-600 to-pink-600 px-6 py-5">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i class="fas fa-bullseye text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Create Financial Goal</h2>
                                <p class="text-white/80 text-sm">Set a new savings target to achieve your dreams</p>
                            </div>
                        </div>
                        <button @click="showAddModal = false"
                            class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body with Scroll -->
                <div class="p-6 lg:p-8 overflow-y-auto max-h-[calc(90vh-180px)]">
                    <form id="createGoalForm" class="space-y-8">
                        @csrf

                        <!-- Goal Type Selection -->
                        <div>
                            <label
                                class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                <span
                                    class="w-6 h-6 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-xs font-bold text-primary-600 dark:text-primary-400">1</span>
                                Choose Goal Type <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <template x-for="type in goalTypes" :key="type.value">
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" x-model="goalType" :value="type.value" class="sr-only peer"
                                            required>
                                        <div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl hover:border-primary-400 peer-checked:border-primary-500 peer-checked:ring-2 peer-checked:ring-primary-500/20 transition-all duration-200 group-hover:shadow-md"
                                            :class="goalType === type.value ? type.bgColor : ''">
                                            <div class="text-center">
                                                <div class="w-12 h-12 mx-auto rounded-xl flex items-center justify-center mb-2 transition-transform group-hover:scale-110"
                                                    :class="'bg-gradient-to-br ' + type.color">
                                                    <i class="fas text-white text-lg" :class="type.icon"></i>
                                                </div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white"
                                                    x-text="type.label"></div>
                                            </div>
                                        </div>
                                        <div x-show="goalType === type.value"
                                            class="absolute -top-1 -right-1 w-5 h-5 bg-primary-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Custom Goal Type Input (shown when custom is selected) -->
                        <div x-show="goalType === 'custom'" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Custom Goal Type <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="customGoalType"
                                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                placeholder="e.g., Wedding, Business, Gadget">
                        </div>

                        <!-- Goal Name & Icon Section -->
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                            <label
                                class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                <span
                                    class="w-6 h-6 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-xs font-bold text-primary-600 dark:text-primary-400">2</span>
                                Goal Details
                            </label>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-tag text-primary-500 mr-2"></i>Goal Name <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <input type="text" x-model="goalName" required
                                        class="w-full px-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                        placeholder="e.g., Dream Vacation to Europe">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-smile text-primary-500 mr-2"></i>Choose Icon
                                    </label>
                                    <div
                                        class="flex gap-2 flex-wrap p-3 bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-600 max-h-32 overflow-y-auto">
                                        <template x-for="icon in icons" :key="icon">
                                            <button type="button" @click="selectedIcon = icon"
                                                :class="selectedIcon === icon ? 'ring-2 ring-primary-500 bg-primary-100 dark:bg-primary-900/30 scale-110' : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                                class="w-10 h-10 rounded-lg flex items-center justify-center text-xl transition-all duration-200">
                                                <span x-text="icon"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Fields -->
                        <div>
                            <label
                                class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                <span
                                    class="w-6 h-6 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-xs font-bold text-primary-600 dark:text-primary-400">3</span>
                                Set Your Target
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-bullseye text-green-500 mr-2"></i>Target Amount <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold"
                                            data-currency-symbol>{{ $currencySymbol }}</span>
                                        <input type="number" x-model="targetAmount" step="0.01" min="1" required
                                            class="w-full pl-10 pr-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-lg font-semibold"
                                            placeholder="10000.00">
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-wallet text-blue-500 mr-2"></i>Current Savings
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold"
                                            data-currency-symbol>{{ $currencySymbol }}</span>
                                        <input type="number" x-model="currentAmount" step="0.01" min="0"
                                            class="w-full pl-10 pr-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-lg font-semibold"
                                            placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400 mr-2 py-1">Quick set:</span>
                                <button type="button" @click="targetAmount = '10000'"
                                    class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900/30 text-gray-700 dark:text-gray-300 rounded-full transition-colors">
                                    {{ $currencySymbol }}10K
                                </button>
                                <button type="button" @click="targetAmount = '50000'"
                                    class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900/30 text-gray-700 dark:text-gray-300 rounded-full transition-colors">
                                    {{ $currencySymbol }}50K
                                </button>
                                <button type="button" @click="targetAmount = '100000'"
                                    class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900/30 text-gray-700 dark:text-gray-300 rounded-full transition-colors">
                                    {{ $currencySymbol }}100K
                                </button>
                                <button type="button" @click="targetAmount = '500000'"
                                    class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900/30 text-gray-700 dark:text-gray-300 rounded-full transition-colors">
                                    {{ $currencySymbol }}500K
                                </button>
                                <button type="button" @click="targetAmount = '1000000'"
                                    class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900/30 text-gray-700 dark:text-gray-300 rounded-full transition-colors">
                                    {{ $currencySymbol }}1M
                                </button>
                            </div>
                        </div>

                        <!-- Deadline, Priority & Color -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-calendar-alt text-orange-500 mr-2"></i>Target Date
                                </label>
                                <input type="date" x-model="deadline"
                                    class="w-full px-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-flag text-purple-500 mr-2"></i>Priority
                                </label>
                                <select x-model="priority"
                                    class="w-full px-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                                    <option value="low">🟢 Low Priority</option>
                                    <option value="medium" selected>🟡 Medium Priority</option>
                                    <option value="high">🔴 High Priority</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-palette text-pink-500 mr-2"></i>Color Theme
                                </label>
                                <div
                                    class="flex gap-2 flex-wrap p-2 bg-white dark:bg-gray-700 rounded-xl border-2 border-gray-200 dark:border-gray-600">
                                    <template x-for="color in colors" :key="color">
                                        <button type="button" @click="selectedColor = color"
                                            :style="`background-color: ${color}`"
                                            :class="selectedColor === color ? 'ring-2 ring-offset-2 ring-gray-400 dark:ring-offset-gray-700 scale-110' : 'hover:scale-110'"
                                            class="w-8 h-8 rounded-lg transition-all duration-200 shadow-sm">
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-align-left text-gray-500 mr-2"></i>Description (Optional)
                            </label>
                            <textarea x-model="description" rows="3"
                                class="w-full px-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all resize-none"
                                placeholder="Add notes about this goal... What's your motivation?"></textarea>
                        </div>

                        <!-- Live Preview Card -->
                        <div class="relative overflow-hidden rounded-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br opacity-10"
                                :style="`background: linear-gradient(135deg, ${selectedColor}, ${selectedColor}88)`"></div>
                            <div
                                class="relative bg-gradient-to-br from-gray-50/80 to-white/80 dark:from-gray-900/80 dark:to-gray-800/80 backdrop-blur-sm p-6 border-2 border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-4">
                                    <i class="fas fa-eye text-gray-400"></i>
                                    <p
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Live Preview</p>
                                </div>
                                <div class="flex items-start gap-5">
                                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-4xl shadow-lg transform hover:scale-105 transition-transform"
                                        :style="`background: linear-gradient(135deg, ${selectedColor}, ${selectedColor}dd)`">
                                        <span x-text="selectedIcon" class="drop-shadow-md"></span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-white text-xl"
                                                    x-text="goalName || 'Your Goal Name'"></h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"
                                                    x-text="goalType ? goalTypes.find(g => g.value === goalType)?.label || 'Custom Goal' : 'Select a goal type'">
                                                </p>
                                            </div>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="{
                                                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': priority === 'low',
                                                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': priority === 'medium',
                                                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': priority === 'high'
                                                                }"
                                                x-text="priority.charAt(0).toUpperCase() + priority.slice(1) + ' Priority'">
                                            </span>
                                        </div>

                                        <div class="mt-4 grid grid-cols-2 gap-4">
                                            <div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">Target</span>
                                                <p class="font-bold text-lg text-gray-900 dark:text-white"
                                                    x-text="targetAmount ? window.AppCurrency.symbol + parseFloat(targetAmount).toLocaleString(window.AppCurrency.locale) : window.AppCurrency.symbol + '0'">
                                                </p>
                                            </div>
                                            <div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">Current</span>
                                                <p class="font-bold text-lg" :style="`color: ${selectedColor}`"
                                                    x-text="currentAmount ? window.AppCurrency.symbol + parseFloat(currentAmount).toLocaleString(window.AppCurrency.locale) : window.AppCurrency.symbol + '0'">
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <div class="flex justify-between items-center mb-1.5">
                                                <span
                                                    class="text-sm font-medium text-gray-600 dark:text-gray-400">Progress</span>
                                                <span class="text-sm font-bold" :style="`color: ${selectedColor}`"
                                                    x-text="`${targetAmount > 0 ? ((currentAmount / targetAmount * 100).toFixed(1)) : 0}%`"></span>
                                            </div>
                                            <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                                <div class="h-3 rounded-full transition-all duration-500 relative"
                                                    :style="`width: ${targetAmount > 0 ? Math.min((currentAmount / targetAmount * 100), 100).toFixed(0) : 0}%; background: linear-gradient(90deg, ${selectedColor}, ${selectedColor}dd)`">
                                                    <div
                                                        class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/30 to-white/0 animate-shimmer">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
                            <i class="fas fa-info-circle mr-1"></i>
                            All fields marked with <span class="text-red-500">*</span> are required
                        </p>
                        <div class="flex items-center gap-3 ml-auto">
                            <button type="button" @click="showAddModal = false"
                                class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold transition-all">
                                Cancel
                            </button>
                            <button type="submit" form="createGoalForm"
                                class="px-8 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105 flex items-center gap-2">
                                <i class="fas fa-rocket"></i>
                                Create Goal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Money Modal -->
        <div id="addMoneyModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full" onclick="event.stopPropagation()">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Add Money</h2>
                    <button onclick="closeAddMoneyModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="addMoneyForm" class="p-6 space-y-6" onsubmit="handleAddMoney(event)">
                    <input type="hidden" id="goalId" name="goal_id">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2" id="goalNameDisplay"></h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Current Amount:</span>
                                <span class="font-semibold text-gray-900 dark:text-white" id="currentAmountDisplay"
                                    data-currency-symbol>{{ $currencySymbol }}0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Target Amount:</span>
                                <span class="font-semibold text-gray-900 dark:text-white" id="targetAmountDisplay"
                                    data-currency-symbol>{{ $currencySymbol }}0</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Amount to Add <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 text-lg"
                                data-currency-symbol>{{ $currencySymbol }}</span>
                            <input type="number" id="addAmount" name="amount" step="0.01" min="0.01" required
                                class="w-full pl-10 pr-4 py-3 text-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="0.00" oninput="updateNewProgress()">
                        </div>
                    </div>

                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border-2 border-blue-200 dark:border-blue-800">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New Amount:</span>
                            <span class="text-xl font-bold text-blue-600 dark:text-blue-400" id="newAmountDisplay"
                                data-currency-symbol>{{ $currencySymbol }}0</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 mb-2">
                            <div id="newProgressBar" class="h-3 rounded-full transition-all bg-blue-600" style="width: 0%">
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                            <span id="newPercentageDisplay">0% Complete</span>
                            <span id="remainingDisplay" data-currency-symbol>{{ $currencySymbol }}0 remaining</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeAddMoneyModal()"
                            class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl">
                            <i class="fas fa-check mr-2"></i>
                            Add Money
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Goal Modal -->
        <div id="deleteGoalModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full" onclick="event.stopPropagation()">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Delete Goal</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">This action cannot be undone</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <input type="hidden" id="deleteGoalId">

                    <div
                        class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border-2 border-red-200 dark:border-red-800 mb-4">
                        <p class="text-gray-700 dark:text-gray-300 mb-2">
                            Are you sure you want to delete this goal?
                        </p>
                        <p class="font-semibold text-gray-900 dark:text-white text-lg" id="deleteGoalName"></p>
                    </div>

                    <div
                        class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                        <div class="flex gap-2">
                            <i class="fas fa-info-circle text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                <p class="font-medium mb-1">This will permanently delete:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>All progress data</li>
                                    <li>Contribution history</li>
                                    <li>Goal details and settings</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 rounded-b-xl flex items-center justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="confirmDeleteGoal()"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl flex items-center gap-2">
                        <i class="fas fa-trash-alt"></i>
                        Delete Goal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let goals = [];

        document.addEventListener('DOMContentLoaded', function () {
            fetchGoals();

            // Refresh when user returns to the tab (instead of constant polling)
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    fetchGoals();
                }
            });
        });

        async function fetchGoals() {
            const loading = document.getElementById('goalsLoading');
            const empty = document.getElementById('goalsEmpty');
            const list = document.getElementById('goalsList');

            try {
                const response = await fetch('/api/v1/goals', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    goals = data.data || [];
                    displayGoals(goals);
                } else {
                    // API not available yet, show empty state
                    loading.classList.add('hidden');
                    empty.classList.remove('hidden');
                    updateStats(0, 0, 0, 0);
                }
            } catch (error) {
                console.log('Goals API not available yet, showing empty state');
                loading.classList.add('hidden');
                empty.classList.remove('hidden');
                updateStats(0, 0, 0, 0);
            }
        }

        function displayGoals(goals) {
            const loading = document.getElementById('goalsLoading');
            const empty = document.getElementById('goalsEmpty');
            const list = document.getElementById('goalsList');

            loading.classList.add('hidden');

            if (goals.length === 0) {
                empty.classList.remove('hidden');
                list.classList.add('hidden');
                updateStats(0, 0, 0, 0);
                return;
            }

            empty.classList.add('hidden');
            list.classList.remove('hidden');

            const totalTarget = goals.reduce((sum, g) => sum + parseFloat(g.target_amount || 0), 0);
            const totalSaved = goals.reduce((sum, g) => sum + parseFloat(g.current_amount || 0), 0);
            const activeCount = goals.filter(g => g.status === 'in_progress').length;

            updateStats(goals.length, totalTarget, totalSaved, activeCount);

            const symbol = window.AppCurrency.symbol;

            list.innerHTML = goals.map(goal => {
                const current = parseFloat(goal.current_amount || 0);
                const target = parseFloat(goal.target_amount || 1);
                const percentage = Math.min((current / target * 100), 100).toFixed(1);
                const remaining = target - current;
                const isCompleted = current >= target;

                return `
                                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow">
                                                                <div class="flex items-start justify-between mb-4">
                                                                    <div class="flex-1">
                                                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">${goal.name}</h3>
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400">${goal.description || 'No description'}</p>
                                                                    </div>
                                                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded ${isCompleted
                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                        : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                    }">
                                                                        ${isCompleted ? 'Completed' : 'In Progress'}
                                                                    </span>
                                                                </div>

                                                                <div class="mb-4">
                                                                    <div class="flex items-center justify-between text-sm mb-2">
                                                                        <span class="text-gray-600 dark:text-gray-400">${symbol}${formatNumber(current)} of ${symbol}${formatNumber(target)}</span>
                                                                        <span class="font-semibold text-gray-900 dark:text-white">${percentage}%</span>
                                                                    </div>
                                                                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                                                                        <div class="h-3 rounded-full transition-all ${isCompleted ? 'bg-green-600' : 'bg-blue-600'
                    }" style="width: ${percentage}%"></div>
                                                                    </div>
                                                                </div>

                                                                <div class="flex items-center justify-between text-sm pt-4 border-t border-gray-200 dark:border-gray-600">
                                                                    <span class="text-gray-600 dark:text-gray-400">Remaining</span>
                                                                    <span class="font-semibold ${isCompleted
                        ? 'text-green-600 dark:text-green-400'
                        : 'text-blue-600 dark:text-blue-400'
                    }">
                                                                        ${symbol}${formatNumber(Math.max(0, remaining))} / ${symbol}${formatNumber(target)}
                                                                    </span>
                                                                </div>

                                                                <div class="mt-3 flex items-center justify-between text-xs border-t border-gray-200 dark:border-gray-600 pt-3">
                                                                    <span class="text-gray-500 dark:text-gray-400">
                                                                        <i class="far fa-calendar mr-1"></i>
                                                                        Target Date
                                                                    </span>
                                                                    <span class="font-medium text-gray-700 dark:text-gray-300">
                                                                        ${goal.target_date ? formatDate(goal.target_date) : 'Not set'}
                                                                    </span>
                                                                </div>

                                                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                                                    <div class="grid grid-cols-2 gap-2">
                                                                        <button onclick="openAddMoneyModal(${goal.id}, '${goal.name}', ${current}, ${target})"
                                                                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                                                            <i class="fas fa-plus-circle"></i>
                                                                            Add Money
                                                                        </button>
                                                                        <button onclick="openDeleteModal(${goal.id}, '${goal.name}')"
                                                                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                            Delete
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        `;
            }).join('');
        }

        function updateStats(count, target, saved, active) {
            document.getElementById('totalGoals').textContent = count;
            const symbol = window.AppCurrency.symbol;
            document.getElementById('targetAmount').innerHTML = `<span class="font-normal" data-currency-symbol>${symbol}</span>` + formatNumber(target);
            document.getElementById('savedAmount').innerHTML = `<span class="font-normal" data-currency-symbol>${symbol}</span>` + formatNumber(saved);
            document.getElementById('activeGoals').textContent = active;
        }

        function formatNumber(num) {
            return parseFloat(num).toLocaleString(window.AppCurrency.locale, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // formatDate function is now global from app.blade.php

        // Handle goal creation form submission
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('createGoalForm');

            if (form) {
                form.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    // Find submit button by form attribute since it's outside the form
                    const submitBtn = document.querySelector('button[type="submit"][form="createGoalForm"]');
                    if (!submitBtn) {
                        console.error('Submit button not found');
                        return;
                    }
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';

                    // Get Alpine.js data
                    const alpineData = Alpine.$data(form.closest('[x-data]'));

                    // Determine the final goal type
                    const finalGoalType = alpineData.goalType === 'custom' && alpineData.customGoalType
                        ? alpineData.customGoalType
                        : alpineData.goalType;

                    const formData = {
                        name: alpineData.goalName,
                        type: finalGoalType,
                        target_amount: parseFloat(alpineData.targetAmount),
                        current_amount: parseFloat(alpineData.currentAmount) || 0,
                        target_date: alpineData.deadline || null,
                        description: alpineData.description || null,
                        icon: alpineData.selectedIcon,
                        color: alpineData.selectedColor,
                        status: 'in_progress'
                    };

                    try {
                        const response = await fetch('/api/v1/goals', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(formData)
                        });

                        if (response.ok) {
                            const result = await response.json();

                            // Show success message
                            showNotification('Goal created successfully!', 'success');

                            // Close modal
                            alpineData.showAddModal = false;

                            // Reset form
                            form.reset();
                            alpineData.goalType = '';
                            alpineData.customGoalType = '';
                            alpineData.goalName = '';
                            alpineData.targetAmount = '';
                            alpineData.currentAmount = '0';
                            alpineData.deadline = '';
                            alpineData.description = '';
                            alpineData.selectedIcon = '🎯';
                            alpineData.selectedColor = '#3B82F6';

                            // Reload goals
                            fetchGoals();
                            // Notify notifications system about the change
                            window.dispatchEvent(new CustomEvent('trackflow:data-changed'));
                        } else {
                            const error = await response.json();
                            showNotification(error.message || 'Failed to create goal', 'error');
                        }
                    } catch (error) {
                        console.error('Error creating goal:', error);
                        showNotification('An error occurred. Please try again.', 'error');
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                });
            }
        });

        // Notification helper
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

        // Add Money Modal Functions
        let currentGoalData = {
            id: null,
            name: '',
            current: 0,
            target: 0
        };

        function openAddMoneyModal(goalId, goalName, current, target) {
            currentGoalData = { id: goalId, name: goalName, current, target };

            const symbol = window.AppCurrency.symbol;
            document.getElementById('goalId').value = goalId;
            document.getElementById('goalNameDisplay').textContent = goalName;
            document.getElementById('currentAmountDisplay').textContent = symbol + formatNumber(current);
            document.getElementById('targetAmountDisplay').textContent = symbol + formatNumber(target);
            document.getElementById('addAmount').value = '';

            updateNewProgress();

            document.getElementById('addMoneyModal').classList.remove('hidden');
        }

        function closeAddMoneyModal() {
            document.getElementById('addMoneyModal').classList.add('hidden');
            document.getElementById('addMoneyForm').reset();
        }

        function updateNewProgress() {
            const addAmountInput = document.getElementById('addAmount');
            const addAmount = parseFloat(addAmountInput.value) || 0;
            const newCurrent = currentGoalData.current + addAmount;
            const percentage = Math.min((newCurrent / currentGoalData.target * 100), 100);
            const remaining = Math.max(0, currentGoalData.target - newCurrent);

            const symbol = window.AppCurrency.symbol;
            document.getElementById('newAmountDisplay').textContent = symbol + formatNumber(newCurrent);
            document.getElementById('newProgressBar').style.width = percentage.toFixed(1) + '%';
            document.getElementById('newPercentageDisplay').textContent = percentage.toFixed(1) + '% Complete';
            document.getElementById('remainingDisplay').textContent = symbol + formatNumber(remaining) + ' remaining';

            // Change color to green if completed
            if (newCurrent >= currentGoalData.target) {
                document.getElementById('newProgressBar').classList.remove('bg-blue-600');
                document.getElementById('newProgressBar').classList.add('bg-green-600');
                document.getElementById('newAmountDisplay').classList.remove('text-blue-600', 'dark:text-blue-400');
                document.getElementById('newAmountDisplay').classList.add('text-green-600', 'dark:text-green-400');
            } else {
                document.getElementById('newProgressBar').classList.remove('bg-green-600');
                document.getElementById('newProgressBar').classList.add('bg-blue-600');
                document.getElementById('newAmountDisplay').classList.remove('text-green-600', 'dark:text-green-400');
                document.getElementById('newAmountDisplay').classList.add('text-blue-600', 'dark:text-blue-400');
            }
        }

        async function handleAddMoney(event) {
            event.preventDefault();

            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';

            const goalId = document.getElementById('goalId').value;
            const amount = parseFloat(document.getElementById('addAmount').value);

            try {
                const response = await fetch(`/api/v1/goals/${goalId}/contribute`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ amount })
                });

                if (response.ok) {
                    const result = await response.json();

                    // Show success message with celebration if goal is completed
                    const newCurrent = currentGoalData.current + amount;
                    if (newCurrent >= currentGoalData.target) {
                        showNotification('🎉 Congratulations! Goal completed!', 'success');
                    } else {
                        showNotification('Money added successfully!', 'success');
                    }

                    // Close modal
                    closeAddMoneyModal();

                    // Reload goals to show updated data
                    fetchGoals();
                    // Notify notifications system about the change
                    window.dispatchEvent(new CustomEvent('trackflow:data-changed'));
                } else {
                    const error = await response.json();
                    showNotification(error.message || 'Failed to add money', 'error');
                }
            } catch (error) {
                console.error('Error adding money:', error);
                showNotification('An error occurred. Please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('addMoneyModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        closeAddMoneyModal();
                    }
                });
            }

            const deleteModal = document.getElementById('deleteGoalModal');
            if (deleteModal) {
                deleteModal.addEventListener('click', function (e) {
                    if (e.target === deleteModal) {
                        closeDeleteModal();
                    }
                });
            }
        });

        // Delete Goal Functions
        let deleteGoalData = {
            id: null,
            name: ''
        };

        function openDeleteModal(goalId, goalName) {
            deleteGoalData = { id: goalId, name: goalName };

            document.getElementById('deleteGoalId').value = goalId;
            document.getElementById('deleteGoalName').textContent = goalName;

            document.getElementById('deleteGoalModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteGoalModal').classList.add('hidden');
            deleteGoalData = { id: null, name: '' };
        }

        async function confirmDeleteGoal() {
            const goalId = deleteGoalData.id;
            const deleteBtn = document.querySelector('#deleteGoalModal button[onclick="confirmDeleteGoal()"]');
            const originalBtnText = deleteBtn.innerHTML;

            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';

            try {
                const response = await fetch(`/api/v1/goals/${goalId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    showNotification('Goal deleted successfully!', 'success');
                    closeDeleteModal();
                    fetchGoals();
                    // Notify notifications system about the change
                    window.dispatchEvent(new CustomEvent('trackflow:data-changed'));
                } else {
                    const error = await response.json();
                    showNotification(error.message || 'Failed to delete goal', 'error');
                }
            } catch (error) {
                console.error('Error deleting goal:', error);
                showNotification('An error occurred. Please try again.', 'error');
            } finally {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = originalBtnText;
            }
        }
    </script>
@endsection