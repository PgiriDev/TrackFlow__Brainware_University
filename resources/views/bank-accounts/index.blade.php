@extends('layouts.app')

@section('title', 'Bank Accounts')
@section('breadcrumb', 'Bank Accounts')

@section('content')
    <!-- Colorful Glassmorphism Page Background - Indigo/Violet Theme -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-indigo-100 via-violet-50 to-purple-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-indigo-300/40 to-violet-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-indigo-600/10 dark:to-violet-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-purple-300/40 to-fuchsia-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-purple-600/10 dark:to-fuchsia-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-violet-300/30 to-indigo-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-violet-600/10 dark:to-indigo-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-blue-300/30 to-indigo-400/30 rounded-full blur-3xl dark:from-blue-600/10 dark:to-indigo-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-purple-300/30 to-violet-400/30 rounded-full blur-3xl dark:from-purple-600/10 dark:to-violet-700/10">
        </div>
    </div>

    <div class="animate-fade-in relative" x-data="{ showAddModal: false }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Bank Accounts</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your connected bank accounts and balances</p>
            </div>
            <button @click="showAddModal = true"
                class="mt-4 sm:mt-0 inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>
                Link Bank Account
            </button>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
            <!-- Total Accounts -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 gradient-primary rounded-lg flex items-center justify-center">
                        <i class="fas fa-university text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Total Accounts</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="totalAccounts">0</p>
            </div>

            <!-- Total Balance -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-rupee-sign text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Total Balance</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="totalBalance"><span
                        class="font-normal">₹</span>0.00</p>
            </div>

            <!-- Active Accounts -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Active</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="activeAccounts">0</p>
            </div>

            <!-- Last Synced -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 card-shadow">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sync-alt text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mt-4">Last Synced</h3>
                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-2" id="lastSynced">Never</p>
            </div>
        </div>

        <!-- Bank Accounts List -->
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 card-shadow">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Connected Accounts</h2>
                <button onclick="syncAllAccounts()"
                    class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Sync All
                </button>
            </div>

            <!-- Accounts Grid -->
            <div id="accountsContainer" class="p-6">
                <!-- Loading State -->
                <div id="accountsLoading" class="text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Loading bank accounts...</p>
                </div>

                <!-- Empty State -->
                <div id="accountsEmpty" class="hidden text-center py-12">
                    <div
                        class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-university text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No bank accounts linked</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Start by connecting your first bank account</p>
                    <button @click="showAddModal = true"
                        class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Link Bank Account
                    </button>
                </div>

                <!-- Accounts List -->
                <div id="accountsList" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Add Bank Account Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="showAddModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                @click.stop>
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Link Bank Account</h2>
                    <button @click="showAddModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <div id="comingSoonContent">
                        <div class="text-center py-4">
                            <div
                                class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-spinner fa-spin text-3xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">Loading feature information...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let accounts = [];
        let featureData = null;

        document.addEventListener('DOMContentLoaded', function () {
            fetchBankAccounts();
            loadFeatureInfo();

            // Refresh when user returns to the tab (instead of constant polling)
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    fetchBankAccounts();
                }
            });
        });

        async function loadFeatureInfo() {
            try {
                const response = await fetch('/coming-soon/feature/bank_account_integration', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        featureData = data.data;
                        renderComingSoonContent();
                    }
                }
            } catch (error) {
                console.error('Error loading feature info:', error);
                renderFallbackContent();
            }
        }

        function renderComingSoonContent() {
            if (!featureData) {
                renderFallbackContent();
                return;
            }

            const content = document.getElementById('comingSoonContent');
            const providers = featureData.metadata?.providers || [];
            const features = featureData.metadata?.features || [];

            content.innerHTML = `
                                    <div class="text-center">
                                        <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas ${featureData.icon || 'fa-link'} text-3xl text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">${featureData.feature_name}</h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-6">${featureData.description}</p>

                                        ${providers.length > 0 ? `
                                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6">
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supported Providers:</p>
                                                <div class="flex flex-wrap justify-center gap-2">
                                                    ${providers.map(p => `<span class="px-3 py-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-300">${p}</span>`).join('')}
                                                </div>
                                            </div>
                                        ` : ''}

                                        ${features.length > 0 ? `
                                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6 text-left">
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">What's Included:</p>
                                                <ul class="space-y-2">
                                                    ${features.map(f => `
                                                        <li class="flex items-start">
                                                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2 flex-shrink-0"></i>
                                                            <span class="text-sm text-gray-600 dark:text-gray-400">${f}</span>
                                                        </li>
                                                    `).join('')}
                                                </ul>
                                            </div>
                                        ` : ''}

                                        <div class="bg-gradient-to-r from-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-50 to-${featureData.status === 'in_progress' ? 'indigo' : 'orange'}-50 dark:from-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-900/20 dark:to-${featureData.status === 'in_progress' ? 'indigo' : 'orange'}-900/20 border border-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-200 dark:border-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-800 rounded-lg p-4 mb-6">
                                            <div class="flex items-start">
                                                <i class="fas fa-${featureData.status === 'in_progress' ? 'cog fa-spin' : 'clock'} text-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-600 dark:text-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-400 mt-1 mr-3"></i>
                                                <div class="text-left flex-1">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <p class="text-sm font-medium text-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-800 dark:text-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-300">
                                                            ${featureData.status === 'in_progress' ? 'In Development' : 'Coming Soon'}
                                                        </p>
                                                        <span class="text-xs font-semibold text-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-700 dark:text-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-400">${featureData.progress_percentage}% Complete</span>
                                                    </div>
                                                    <div class="w-full bg-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-200 dark:bg-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-800 rounded-full h-2 mb-2">
                                                        <div class="bg-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-600 h-2 rounded-full transition-all" style="width: ${featureData.progress_percentage}%"></div>
                                                    </div>
                                                    <p class="text-xs text-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-700 dark:text-${featureData.status === 'in_progress' ? 'blue' : 'yellow'}-400">
                                                        ${featureData.estimated_release ? `Expected: ${featureData.estimated_release}` : 'Release date TBD'} • ${featureData.interest_count} ${featureData.interest_count === 1 ? 'user' : 'users'} interested
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                            <button onclick="toggleInterest()" id="interestButton" class="px-6 py-3 ${featureData.is_interested ? 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' : 'bg-primary-600 hover:bg-primary-700 text-white'} font-medium rounded-lg transition-colors">
                                                <i class="fas ${featureData.is_interested ? 'fa-check' : 'fa-star'} mr-2"></i>
                                                ${featureData.is_interested ? 'Interested' : 'Notify Me When Available'}
                                            </button>
                                            <a href="/transactions" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors text-center">
                                                <i class="fas fa-plus mr-2"></i>
                                                Add Transaction Manually
                                            </a>
                                            <button onclick="closeModal()" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                Close
                                            </button>
                                        </div>

                                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                            <a href="/coming-soon" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 hover:underline">
                                                <i class="fas fa-rocket mr-1"></i>
                                                View All Upcoming Features
                                            </a>
                                        </div>
                                    </div>
                                `;
        }

        function renderFallbackContent() {
            const content = document.getElementById('comingSoonContent');
            content.innerHTML = `
                                    <div class="text-center py-8">
                                        <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-link text-3xl text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Bank Account Integration</h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                                            This feature requires integration with a banking service provider like Finvu, SaltEdge, or Plaid.
                                        </p>
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                                            <div class="flex items-start">
                                                <i class="fas fa-info-circle text-yellow-600 dark:text-yellow-400 mt-1 mr-3"></i>
                                                <div class="text-left">
                                                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-1">Coming Soon</p>
                                                    <p class="text-xs text-yellow-700 dark:text-yellow-400">
                                                        Bank account integration is currently under development. For now, you can manually add transactions from the Transactions page.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-3 justify-center">
                                            <a href="/transactions" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                                <i class="fas fa-plus mr-2"></i>
                                                Add Transaction Manually
                                            </a>
                                            <button onclick="closeModal()" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                `;
        }

        function closeModal() {
            const modal = document.querySelector('[x-data]');
            if (modal && modal.__x) {
                modal.__x.$data.showAddModal = false;
            }
        }

        async function toggleInterest() {
            if (!featureData) return;

            const button = document.getElementById('interestButton');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

            try {
                const response = await fetch(`/coming-soon/feature/${featureData.id}/interest`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    featureData.is_interested = data.is_interested;
                    featureData.interest_count = data.interest_count;
                    renderComingSoonContent();

                    if (data.is_interested) {
                        popupSuccess('You will be notified when this feature is available!', 'Success');
                    }
                } else {
                    throw new Error(data.message || 'Failed to update interest');
                }
            } catch (error) {
                console.error('Error toggling interest:', error);
                button.disabled = false;
                button.innerHTML = originalText;
                popupError('Failed to update your interest. Please try again.', 'Error');
            }
        }
                            });

        async function fetchBankAccounts() {
            const loading = document.getElementById('accountsLoading');
            const empty = document.getElementById('accountsEmpty');
            const list = document.getElementById('accountsList');

            try {
                // Since we don't have an API yet, we'll check the database
                const response = await fetch('/api/v1/bank-accounts', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    accounts = data.data || [];
                    displayBankAccounts(accounts);
                } else {
                    // No API yet, show empty state
                    loading.classList.add('hidden');
                    empty.classList.remove('hidden');
                    updateStats(0, 0, 0);
                }
            } catch (error) {
                console.log('Bank accounts API not available yet, showing empty state');
                loading.classList.add('hidden');
                empty.classList.remove('hidden');
                updateStats(0, 0, 0);
            }
        }

        function displayBankAccounts(accounts) {
            const loading = document.getElementById('accountsLoading');
            const empty = document.getElementById('accountsEmpty');
            const list = document.getElementById('accountsList');

            loading.classList.add('hidden');

            if (accounts.length === 0) {
                empty.classList.remove('hidden');
                list.classList.add('hidden');
                updateStats(0, 0, 0);
                return;
            }

            empty.classList.add('hidden');
            list.classList.remove('hidden');

            const totalBalance = accounts.reduce((sum, acc) => sum + parseFloat(acc.balance || 0), 0);
            const activeCount = accounts.filter(acc => acc.status === 'active').length;

            updateStats(accounts.length, totalBalance, activeCount);

            list.innerHTML = accounts.map(account => `
                                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow">
                                                    <div class="flex items-start justify-between mb-4">
                                                        <div class="flex-1">
                                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">${account.bank_name}</h3>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">${account.account_number_masked || '****'}</p>
                                                            <span class="inline-block mt-2 px-2 py-1 text-xs font-medium rounded ${account.status === 'active'
                    ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                    : 'bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300'
                }">
                                                                ${account.status.charAt(0).toUpperCase() + account.status.slice(1)}
                                                            </span>
                                                        </div>
                                                        <button onclick="syncAccount(${account.id})" 
                                                            class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                                                            <i class="fas fa-sync-alt"></i>
                                                        </button>
                                                    </div>

                                                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-sm text-gray-600 dark:text-gray-400">Balance</span>
                                                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                                                ₹${formatNumber(account.balance || 0)}
                                                            </span>
                                                        </div>
                                                        ${account.last_synced_at ? `
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                                Last synced: ${formatDate(account.last_synced_at)}
                                                            </p>
                                                        ` : ''}
                                                    </div>
                                                </div>
                                            `).join('');
        }

        function updateStats(total, balance, active) {
            document.getElementById('totalAccounts').textContent = total;
            document.getElementById('totalBalance').innerHTML = '<span class="font-normal">₹</span>' + formatNumber(balance);
            document.getElementById('activeAccounts').textContent = active;

            if (total > 0) {
                const lastSynced = accounts.reduce((latest, acc) => {
                    if (!acc.last_synced_at) return latest;
                    const syncDate = new Date(acc.last_synced_at);
                    return !latest || syncDate > latest ? syncDate : latest;
                }, null);

                document.getElementById('lastSynced').textContent = lastSynced
                    ? formatDate(lastSynced)
                    : 'Never';
            }
        }

        function formatNumber(num) {
            return parseFloat(num).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} min${diffMins > 1 ? 's' : ''} ago`;
            if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
            if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;

            // Use global formatDate for dates older than 7 days
            return window.formatDate ? window.formatDate(dateString) : dateString;
        }

        async function syncAccount(accountId) {
            console.log('Syncing account:', accountId);
            popupAlert('Bank sync functionality will be available once API integration is complete.', 'Coming Soon', 'info');
        }

        async function syncAllAccounts() {
            console.log('Syncing all accounts');
            popupAlert('Bank sync functionality will be available once API integration is complete.', 'Coming Soon', 'info');
        }
    </script>
@endsection