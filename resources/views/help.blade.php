@extends('layouts.app')

@section('title', 'Help & Support')
@section('breadcrumb', 'Help & Support')

@section('content')
    <!-- Colorful Glassmorphism Page Background - Orange/Red Theme -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-orange-100 via-red-50 to-rose-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-orange-300/40 to-amber-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-orange-600/10 dark:to-amber-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-red-300/40 to-rose-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-red-600/10 dark:to-rose-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-amber-300/30 to-orange-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-amber-600/10 dark:to-orange-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-yellow-300/30 to-orange-400/30 rounded-full blur-3xl dark:from-yellow-600/10 dark:to-orange-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-red-300/30 to-pink-400/30 rounded-full blur-3xl dark:from-red-600/10 dark:to-pink-700/10">
        </div>
    </div>

    <div class="animate-fade-in relative">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-2">Help & Support</h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Get help with TrackFlow and manage your finances better</p>
        </div>

        <!-- Search Bar -->
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 mb-8">
            <div class="relative">
                <input type="text" id="searchHelp" placeholder="Search for help articles, guides, or FAQs..."
                    class="w-full pl-12 pr-4 py-4 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    oninput="searchHelpArticles(this.value)">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <a href="#getting-started"
                class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 hover:shadow-lg transition-all transform hover:-translate-y-1">
                <i class="fas fa-rocket text-3xl mb-3"></i>
                <h3 class="font-semibold text-lg mb-1">Getting Started</h3>
                <p class="text-sm text-blue-100">New to TrackFlow?</p>
            </a>
            <a href="#video-tutorials"
                class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 hover:shadow-lg transition-all transform hover:-translate-y-1">
                <i class="fas fa-play-circle text-3xl mb-3"></i>
                <h3 class="font-semibold text-lg mb-1">Video Tutorials</h3>
                <p class="text-sm text-purple-100">Watch and learn</p>
            </a>
            <a href="#faq"
                class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 hover:shadow-lg transition-all transform hover:-translate-y-1">
                <i class="fas fa-question-circle text-3xl mb-3"></i>
                <h3 class="font-semibold text-lg mb-1">FAQ</h3>
                <p class="text-sm text-green-100">Common questions</p>
            </a>
            <a href="#contact"
                class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl p-6 hover:shadow-lg transition-all transform hover:-translate-y-1">
                <i class="fas fa-headset text-3xl mb-3"></i>
                <h3 class="font-semibold text-lg mb-1">Contact Support</h3>
                <p class="text-sm text-orange-100">We're here to help</p>
            </a>
        </div>

        <!-- Getting Started Section -->
        <div id="getting-started"
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-rocket text-blue-500 mr-3"></i>
                Getting Started with TrackFlow
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2">1. Set Up Your Profile</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">Complete your profile information, upload a profile
                        picture, and customize your preferences.</p>
                    <a href="{{ route('settings.index') }}"
                        class="text-blue-600 dark:text-blue-400 text-sm font-medium hover:underline">
                        Go to Settings →
                    </a>
                </div>
                <div class="border-l-4 border-green-500 pl-4 py-2">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2">2. Add Your First Transaction</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">Start tracking your expenses and income by recording
                        your first transaction.</p>
                    <a href="{{ route('transactions.create') }}"
                        class="text-green-600 dark:text-green-400 text-sm font-medium hover:underline">
                        Add Transaction →
                    </a>
                </div>
                <div class="border-l-4 border-purple-500 pl-4 py-2">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2">3. Create Categories</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">Organize your spending by creating custom categories
                        that fit your lifestyle.</p>
                    <a href="{{ route('categories.index') }}"
                        class="text-purple-600 dark:text-purple-400 text-sm font-medium hover:underline">
                        Manage Categories →
                    </a>
                </div>
                <div class="border-l-4 border-orange-500 pl-4 py-2">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2">4. Set Financial Goals</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">Define your financial goals and track your progress
                        towards achieving them.</p>
                    <a href="{{ route('goals.index') }}"
                        class="text-orange-600 dark:text-orange-400 text-sm font-medium hover:underline">
                        Set Goals →
                    </a>
                </div>
            </div>
        </div>

        <!-- Feature Guides -->
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-book-open text-purple-500 mr-3"></i>
                Feature Guides
            </h2>
            <div class="space-y-4">
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                    onclick="toggleGuide('transactions')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-exchange-alt text-2xl text-blue-500"></i>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">Managing Transactions</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Learn how to add, edit, and categorize
                                    your transactions</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                    <div id="guide-transactions" class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Adding transactions:</strong> Click "Add Transaction" button, fill in amount,
                                    description, category, and date</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Transaction types:</strong> Choose between Income (credit) and Expense
                                    (debit)</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Bulk operations:</strong> Select multiple transactions to delete or categorize
                                    at once</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Filtering:</strong> Use filters to view transactions by date range, category,
                                    or type</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                    onclick="toggleGuide('budgets')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-wallet text-2xl text-green-500"></i>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">Setting Up Budgets</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Create and manage your monthly spending
                                    budgets</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                    <div id="guide-budgets" class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Creating budgets:</strong> Navigate to Budgets, click "Create Budget", and set
                                    your monthly limits</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Category budgets:</strong> Set individual budgets for different spending
                                    categories</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Alerts:</strong> Receive notifications when you're close to exceeding your
                                    budget</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Progress tracking:</strong> View real-time progress bars showing budget
                                    utilization</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                    onclick="toggleGuide('goals')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-bullseye text-2xl text-orange-500"></i>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">Financial Goals</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Track your savings goals and milestones
                                </p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                    <div id="guide-goals" class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Creating goals:</strong> Set target amounts, deadlines, and track your
                                    progress</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Adding contributions:</strong> Click "Add Money" to record savings towards
                                    your goal</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Goal types:</strong> Choose from Emergency Fund, Vacation, Home, Car,
                                    Education, or Custom</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Visual tracking:</strong> See progress bars and percentage completion in
                                    real-time</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                    onclick="toggleGuide('reports')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-chart-bar text-2xl text-purple-500"></i>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">Reports & Analytics</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Generate detailed financial reports and
                                    insights</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                    <div id="guide-reports" class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Viewing reports:</strong> Click any report card to view detailed analysis with
                                    charts</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Date filters:</strong> Use custom date ranges or quick filters (This Month,
                                    Last 30 Days, etc.)</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Exporting:</strong> Download reports as CSV, Excel, or PDF for
                                    record-keeping</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span><strong>Charts:</strong> Visual representations include line charts, pie charts, and
                                    trend analysis</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Tutorials Section -->
        <div id="video-tutorials"
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-play-circle text-purple-500 mr-3"></i>
                Video Tutorials
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Watch step-by-step video guides to master TrackFlow's features
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Video 1: Getting Started -->
                <div class="group cursor-pointer" onclick="openVideoModal('getting-started-video')">
                    <div class="relative rounded-lg overflow-hidden mb-3 bg-gradient-to-br from-blue-500 to-purple-600">
                        <div class="aspect-video flex items-center justify-center">
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all"></div>
                            <i
                                class="fas fa-play-circle text-6xl text-white relative z-10 group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="absolute top-3 right-3 bg-black/70 text-white text-xs px-2 py-1 rounded">5:30</div>
                    </div>
                    <h3
                        class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-primary-600 transition-colors">
                        Getting Started with TrackFlow</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Learn the basics and set up your account</p>
                </div>

                <!-- Video 2: Adding Transactions -->
                <div class="group cursor-pointer" onclick="openVideoModal('transactions-video')">
                    <div class="relative rounded-lg overflow-hidden mb-3 bg-gradient-to-br from-green-500 to-teal-600">
                        <div class="aspect-video flex items-center justify-center">
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all"></div>
                            <i
                                class="fas fa-play-circle text-6xl text-white relative z-10 group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="absolute top-3 right-3 bg-black/70 text-white text-xs px-2 py-1 rounded">3:45</div>
                    </div>
                    <h3
                        class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-primary-600 transition-colors">
                        How to Add & Manage Transactions</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Record your income and expenses efficiently</p>
                </div>

                <!-- Video 3: Creating Budgets -->
                <div class="group cursor-pointer" onclick="openVideoModal('budgets-video')">
                    <div class="relative rounded-lg overflow-hidden mb-3 bg-gradient-to-br from-orange-500 to-red-600">
                        <div class="aspect-video flex items-center justify-center">
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all"></div>
                            <i
                                class="fas fa-play-circle text-6xl text-white relative z-10 group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="absolute top-3 right-3 bg-black/70 text-white text-xs px-2 py-1 rounded">4:20</div>
                    </div>
                    <h3
                        class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-primary-600 transition-colors">
                        Setting Up Your Budget</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Create and track your monthly budgets</p>
                </div>

                <!-- Video 4: Financial Goals -->
                <div class="group cursor-pointer" onclick="openVideoModal('goals-video')">
                    <div class="relative rounded-lg overflow-hidden mb-3 bg-gradient-to-br from-pink-500 to-rose-600">
                        <div class="aspect-video flex items-center justify-center">
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all"></div>
                            <i
                                class="fas fa-play-circle text-6xl text-white relative z-10 group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="absolute top-3 right-3 bg-black/70 text-white text-xs px-2 py-1 rounded">6:15</div>
                    </div>
                    <h3
                        class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-primary-600 transition-colors">
                        Achieving Financial Goals</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Set and track your savings goals</p>
                </div>

                <!-- Video 5: Reports & Analytics -->
                <div class="group cursor-pointer" onclick="openVideoModal('reports-video')">
                    <div class="relative rounded-lg overflow-hidden mb-3 bg-gradient-to-br from-indigo-500 to-blue-600">
                        <div class="aspect-video flex items-center justify-center">
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all"></div>
                            <i
                                class="fas fa-play-circle text-6xl text-white relative z-10 group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="absolute top-3 right-3 bg-black/70 text-white text-xs px-2 py-1 rounded">5:50</div>
                    </div>
                    <h3
                        class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-primary-600 transition-colors">
                        Understanding Reports</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Generate and analyze financial reports</p>
                </div>

                <!-- Video 6: Advanced Features -->
                <div class="group cursor-pointer" onclick="openVideoModal('advanced-video')">
                    <div class="relative rounded-lg overflow-hidden mb-3 bg-gradient-to-br from-cyan-500 to-teal-600">
                        <div class="aspect-video flex items-center justify-center">
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all"></div>
                            <i
                                class="fas fa-play-circle text-6xl text-white relative z-10 group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="absolute top-3 right-3 bg-black/70 text-white text-xs px-2 py-1 rounded">7:30</div>
                    </div>
                    <h3
                        class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-primary-600 transition-colors">
                        Advanced Features & Tips</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Master advanced features and pro tips</p>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div id="faq"
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-question-circle text-green-500 mr-3"></i>
                Frequently Asked Questions
            </h2>
            <div class="space-y-3">
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                    <button onclick="toggleFaq('faq1')"
                        class="w-full px-5 py-4 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <span class="font-semibold text-gray-900 dark:text-white">How do I reset my password?</span>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </button>
                    <div id="faq1" class="hidden px-5 pb-4 text-gray-600 dark:text-gray-400">
                        Go to Settings → Profile, click on "Change Password", enter your current password and new password,
                        then save changes.
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                    <button onclick="toggleFaq('faq2')"
                        class="w-full px-5 py-4 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <span class="font-semibold text-gray-900 dark:text-white">Can I import transactions from my
                            bank?</span>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </button>
                    <div id="faq2" class="hidden px-5 pb-4 text-gray-600 dark:text-gray-400">
                        You can manually add transactions in the Transactions section. TrackFlow helps you
                        organize and categorize them automatically.
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                    <button onclick="toggleFaq('faq3')"
                        class="w-full px-5 py-4 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <span class="font-semibold text-gray-900 dark:text-white">How do I export my data?</span>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </button>
                    <div id="faq3" class="hidden px-5 pb-4 text-gray-600 dark:text-gray-400">
                        Navigate to Reports, open any report, and click the export button at the top. You can download your
                        data in CSV, Excel, or PDF format.
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                    <button onclick="toggleFaq('faq4')"
                        class="w-full px-5 py-4 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <span class="font-semibold text-gray-900 dark:text-white">Is my financial data secure?</span>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </button>
                    <div id="faq4" class="hidden px-5 pb-4 text-gray-600 dark:text-gray-400">
                        Absolutely! We use industry-standard encryption (AES-256) to protect your data. All connections are
                        secured with SSL/TLS, and we never share your personal information.
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                    <button onclick="toggleFaq('faq5')"
                        class="w-full px-5 py-4 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <span class="font-semibold text-gray-900 dark:text-white">Can I use TrackFlow on mobile?</span>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </button>
                    <div id="faq5" class="hidden px-5 pb-4 text-gray-600 dark:text-gray-400">
                        Yes! TrackFlow is fully responsive and works great on mobile browsers. Native mobile apps for iOS
                        and Android are coming soon.
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                    <button onclick="toggleFaq('faq6')"
                        class="w-full px-5 py-4 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <span class="font-semibold text-gray-900 dark:text-white">How do I delete my account?</span>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </button>
                    <div id="faq6" class="hidden px-5 pb-4 text-gray-600 dark:text-gray-400">
                        Go to Settings → Profile → scroll to "Danger Zone" section. Click "Delete Account" and confirm your
                        decision. Note: This action cannot be undone.
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div id="contact" class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-md p-8 text-white mb-6">
            <div class="text-center max-w-2xl mx-auto">
                <i class="fas fa-headset text-5xl mb-4"></i>
                <h2 class="text-3xl font-bold mb-3">Still Need Help?</h2>
                <p class="text-blue-100 text-lg mb-6">Our support team is here to assist you with any questions or issues
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                        <i class="fas fa-envelope text-2xl mb-2"></i>
                        <h3 class="font-semibold mb-1">Email Support</h3>
                        <a href="mailto:flowlabs.info@gmail.com"
                            class="text-sm text-blue-100 hover:text-white">flowlabs.info@gmail.com</a>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                        <i class="fas fa-phone text-2xl mb-2"></i>
                        <h3 class="font-semibold mb-1">Phone Support</h3>
                        <a href="tel:+918653021830" class="text-sm text-blue-100 hover:text-white">+91 8653021830</a>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                        <i class="fas fa-comments text-2xl mb-2"></i>
                        <h3 class="font-semibold mb-1">Live Chat</h3>
                        <button onclick="openLiveChat()" class="text-sm text-blue-100 hover:text-white">Start Chat</button>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center gap-3">
                    <button
                        onclick="document.getElementById('contactFormModal').classList.remove('hidden'); document.body.style.overflow = 'hidden';"
                        class="px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Message
                    </button>
                    <a href="#"
                        class="px-6 py-3 bg-white/10 backdrop-blur border-2 border-white/30 text-white font-semibold rounded-lg hover:bg-white/20 transition-colors">
                        <i class="fab fa-discord mr-2"></i>
                        Join Community
                    </a>
                </div>
            </div>
        </div>

        <!-- Resources -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6">
                <i class="fas fa-book text-3xl text-blue-500 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Documentation</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Complete guides and API references</p>
                <button onclick="openDocumentationModal()"
                    class="text-blue-600 dark:text-blue-400 text-sm font-medium hover:underline">
                    Read Docs →
                </button>
            </div>
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6">
                <i class="fas fa-users text-3xl text-green-500 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Community Forum</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Connect with other TrackFlow users</p>
                <a href="{{ route('community.index') }}"
                    onclick="if(window.DynamicIsland){DynamicIsland.show('Community Opening...');sessionStorage.setItem('dynamicIslandNavPage','Community');}"
                    class="text-green-600 dark:text-green-400 text-sm font-medium hover:underline">
                    Join Forum →
                </a>
            </div>
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6">
                <i class="fas fa-graduation-cap text-3xl text-purple-500 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Learning Center</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Video tutorials and courses</p>
                <a href="#video-tutorials" class="text-purple-600 dark:text-purple-400 text-sm font-medium hover:underline">
                    Start Learning →
                </a>
            </div>
        </div>
    </div>

    <!-- Live Chat Modal -->
    <div id="liveChatModal"
        class="hidden fixed bottom-6 right-6 z-50 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                    <i class="fas fa-headset text-white"></i>
                </div>
                <div>
                    <h3 class="text-white font-semibold">Live Support</h3>
                    <div class="flex items-center gap-1 text-xs text-white/80">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        <span>Online</span>
                    </div>
                </div>
            </div>
            <button onclick="closeLiveChat()" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Chat Messages -->
        <div id="chatMessages" class="h-96 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900 space-y-3">
            <!-- Welcome Message -->
            <div class="flex items-start gap-2">
                <div class="w-8 h-8 rounded-full flex-shrink-0 overflow-hidden">
                    <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                        <p class="text-sm text-gray-800 dark:text-gray-200">Hello! 👋 Welcome to TrackFlow Support. How can
                            I help you today?</p>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Just now</span>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <form onsubmit="sendChatMessage(event)" class="flex gap-2">
                <input type="text" id="chatInput" placeholder="Type your message..."
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                    required>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                Avg. response time: &lt; 2 minutes
            </p>
        </div>
    </div>

    <!-- Documentation Modal -->
    <div id="documentationModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div
            class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-2xl rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden border border-white/30 dark:border-gray-700/50">
            <!-- Modal Header -->
            <div
                class="bg-gradient-to-r from-blue-600/90 to-indigo-600/90 backdrop-blur px-6 py-5 flex items-center justify-between border-b border-white/20">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white/20 backdrop-blur-xl rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-book-open text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">TrackFlow Documentation</h2>
                        <p class="text-sm text-white/80">Complete guide to managing your finances</p>
                    </div>
                </div>
                <button onclick="closeDocumentationModal()"
                    class="w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur rounded-lg flex items-center justify-center text-white transition-all">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(90vh-100px)] p-6">
                <!-- Table of Contents -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-slate-500 to-slate-600 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Table of Contents</h3>
                    </div>
                    <div
                        class="bg-gradient-to-r from-slate-50 to-gray-50 dark:from-slate-900/20 dark:to-gray-900/20 rounded-xl p-5 border border-slate-200/50 dark:border-slate-700/30">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                            <a href="#doc-intro"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 1. Introduction</a>
                            <a href="#doc-dashboard"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 2. Dashboard Overview</a>
                            <a href="#doc-transactions"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 3. Transactions</a>
                            <a href="#doc-scheduled"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 4. Scheduled Transactions</a>
                            <a href="#doc-budgets"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 5. Budgets</a>
                            <a href="#doc-categories"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 6. Categories</a>
                            <a href="#doc-goals"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 7. Savings Goals</a>
                            <a href="#doc-reports"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 8. Reports & Analytics</a>
                            <a href="#doc-groups"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 9. Group Expenses</a>
                            <a href="#doc-community"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 10. Community</a>
                            <a href="#doc-notifications"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 11. Notifications</a>
                            <a href="#doc-settings"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 12. Settings & Profile</a>
                            <a href="#doc-security"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 13. Security & Privacy</a>
                            <a href="#doc-shortcuts"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 15. Keyboard Shortcuts</a>
                            <a href="#doc-troubleshoot"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2"><i
                                    class="fas fa-chevron-right text-xs"></i> 16. Troubleshooting</a>
                        </div>
                    </div>
                </div>

                <!-- 1. Introduction -->
                <div class="mb-8" id="doc-intro">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-rocket text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">1. Introduction to TrackFlow</h3>
                    </div>
                    <div
                        class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-5 border border-blue-200/50 dark:border-blue-700/30 space-y-4">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                            <strong>TrackFlow</strong> is a comprehensive personal finance management application built with
                            Laravel 12 and modern web technologies. It's designed to help individuals and families take
                            complete control of their financial lives through intuitive expense tracking, smart budgeting,
                            and insightful analytics.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 dark:text-white mb-2"><i
                                        class="fas fa-check-circle text-green-500 mr-2"></i>What You Can Do</h5>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• Track all income and expenses in real-time</li>
                                    <li>• Create and manage monthly budgets</li>
                                    <li>• Set and achieve savings goals</li>
                                    <li>• Split expenses with friends and family</li>
                                    <li>• Generate detailed financial reports</li>
                                    <li>• Schedule recurring transactions</li>
                                    <li>• Receive smart spending alerts</li>
                                </ul>
                            </div>
                            <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 dark:text-white mb-2"><i
                                        class="fas fa-info-circle text-blue-500 mr-2"></i>System Requirements</h5>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• Modern web browser (Chrome, Firefox, Safari, Edge)</li>
                                    <li>• JavaScript enabled</li>
                                    <li>• Stable internet connection</li>
                                    <li>• Mobile responsive - works on all devices</li>
                                    <li>• Supports dark mode</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Dashboard Overview -->
                <div class="mb-8" id="doc-dashboard">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-home text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">2. Dashboard Overview</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                The Dashboard is your financial command center, providing a quick overview of your entire
                                financial situation at a glance.
                            </p>
                            <div class="space-y-4">
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white">Balance Overview Cards</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Displays your total balance across
                                        all accounts, total income for the current month, total expenses, and net savings.
                                        Each card shows percentage change from the previous period.</p>
                                </div>
                                <div class="border-l-4 border-green-500 pl-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white">Recent Transactions</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Shows your latest 5-10 transactions
                                        with quick actions to view details or add new ones. Color-coded by income (green)
                                        and expense (red).</p>
                                </div>
                                <div class="border-l-4 border-purple-500 pl-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white">Budget Progress</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Visual progress bars showing how
                                        much of each budget category you've used. Orange when approaching limit (80%), red
                                        when exceeded.</p>
                                </div>
                                <div class="border-l-4 border-amber-500 pl-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white">Goals Progress</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Track progress towards your savings
                                        goals with visual indicators showing current amount, target amount, and projected
                                        completion date.</p>
                                </div>
                                <div class="border-l-4 border-cyan-500 pl-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white">Spending Charts</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Interactive pie charts and line
                                        graphs showing spending by category and trends over time. Hover for detailed
                                        breakdowns.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Transactions -->
                <div class="mb-8" id="doc-transactions">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-exchange-alt text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">3. Transactions</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-plus-circle text-green-500 mr-2"></i>Adding a New Transaction</h4>
                            <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-decimal list-inside">
                                <li>Click the <strong>"+ Add Transaction"</strong> button on the transactions page</li>
                                <li>Select transaction type: <strong>Income</strong> or <strong>Expense</strong></li>
                                <li>Enter the <strong>amount</strong> (supports decimals up to 2 places)</li>
                                <li>Choose a <strong>category</strong> from the dropdown (filtered by type)</li>
                                <li>Select the <strong>date</strong> of the transaction</li>
                                <li>Add a <strong>description</strong> for reference (optional but recommended)</li>
                                <li>Click <strong>"Save Transaction"</strong> to record it</li>
                            </ol>
                        </div>

                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-filter text-blue-500 mr-2"></i>Filtering & Searching</h4>
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                <p><strong>Search Bar:</strong> Type to search by description, amount, or category name</p>
                                <p><strong>Type Filter:</strong> Filter by All, Income only, or Expense only</p>
                                <p><strong>Category Filter:</strong> Select specific category to view related transactions
                                </p>
                                <p><strong>Date Range:</strong> Filter by Today, This Week, This Month, or Custom Range</p>
                                <p><strong>Sorting:</strong> Sort by date (newest/oldest), amount (high/low), or category
                                </p>
                            </div>
                        </div>

                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-edit text-amber-500 mr-2"></i>Editing & Deleting</h4>
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                <p><strong>Edit:</strong> Click the edit icon (pencil) on any transaction row to modify
                                    details</p>
                                <p><strong>Delete:</strong> Click the delete icon (trash) and confirm to remove the
                                    transaction</p>
                                <p><strong>Bulk Actions:</strong> Select multiple transactions using checkboxes for bulk
                                    delete</p>
                                <p class="text-amber-600 dark:text-amber-400"><i
                                        class="fas fa-exclamation-triangle mr-1"></i> Note: Deleting transactions will
                                    affect your budget calculations and reports</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Scheduled Transactions -->
                <div class="mb-8" id="doc-scheduled">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">4. Scheduled Transactions</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-gradient-to-r from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-xl p-5 border border-violet-200/50 dark:border-violet-700/30">
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                Schedule future transactions for bills, subscriptions, salary, or any recurring payments.
                                You'll receive email reminders before the due date.
                            </p>
                            <div class="space-y-4">
                                <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white mb-2"><i
                                            class="fas fa-calendar-plus text-violet-500 mr-2"></i>Creating a Scheduled
                                        Transaction</h5>
                                    <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-decimal list-inside">
                                        <li>Go to Transactions page and click <strong>"Schedule"</strong> button</li>
                                        <li>Select transaction type (Income/Expense)</li>
                                        <li>Enter the amount and select category</li>
                                        <li>Set the <strong>scheduled date</strong> (must be future date)</li>
                                        <li>Add a description (e.g., "Monthly Rent", "Netflix Subscription")</li>
                                        <li>Click <strong>"Schedule Transaction"</strong></li>
                                    </ol>
                                </div>
                                <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white mb-2"><i
                                            class="fas fa-envelope text-blue-500 mr-2"></i>Email Notifications</h5>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• <strong>Confirmation Email:</strong> Sent immediately when you schedule a
                                            transaction</li>
                                        <li>• <strong>Reminder Email:</strong> Sent at 12:00 AM on the scheduled date</li>
                                        <li>• <strong>Cancellation Email:</strong> Sent when you cancel a scheduled
                                            transaction</li>
                                    </ul>
                                </div>
                                <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white mb-2"><i
                                            class="fas fa-tasks text-green-500 mr-2"></i>Managing Scheduled Transactions
                                    </h5>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• <strong>Execute Now:</strong> Convert scheduled transaction to actual
                                            transaction before due date</li>
                                        <li>• <strong>Cancel:</strong> Remove the scheduled transaction (you'll receive
                                            confirmation email)</li>
                                        <li>• <strong>Status:</strong> Pending (waiting), Completed (executed), Cancelled
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Budgets -->
                <div class="mb-8" id="doc-budgets">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-wallet text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">5. Budgets</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-plus text-green-500 mr-2"></i>Creating a Budget</h4>
                            <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-decimal list-inside">
                                <li>Navigate to <strong>Budgets</strong> from the sidebar</li>
                                <li>Click <strong>"Create Budget"</strong></li>
                                <li>Enter budget name (e.g., "January 2026 Budget")</li>
                                <li>Set the budget period (Monthly, Weekly, Custom)</li>
                                <li>Add budget items for each expense category:
                                    <ul class="list-disc list-inside ml-4 mt-1">
                                        <li>Select category (Food, Transport, Entertainment, etc.)</li>
                                        <li>Set spending limit for that category</li>
                                        <li>Repeat for all categories you want to track</li>
                                    </ul>
                                </li>
                                <li>Click <strong>"Save Budget"</strong></li>
                            </ol>
                        </div>
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-chart-bar text-blue-500 mr-2"></i>Budget Tracking</h4>
                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                <li>• <strong>Progress Bars:</strong> Visual indication of spending vs. budget limit</li>
                                <li>• <strong>Color Codes:</strong>
                                    <ul class="list-disc list-inside ml-4">
                                        <li><span class="text-green-500">Green (0-60%)</span> - On track</li>
                                        <li><span class="text-yellow-500">Yellow (60-80%)</span> - Approaching limit</li>
                                        <li><span class="text-orange-500">Orange (80-100%)</span> - Near limit</li>
                                        <li><span class="text-red-500">Red (>100%)</span> - Over budget</li>
                                    </ul>
                                </li>
                                <li>• <strong>Alerts:</strong> Get notified when approaching or exceeding budget limits</li>
                                <li>• <strong>Rollover:</strong> Option to roll unused budget to next period</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 6. Categories -->
                <div class="mb-8" id="doc-categories">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-tags text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">6. Categories</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-list-ul text-amber-500 mr-2"></i>Default Categories</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <div><i class="fas fa-utensils text-orange-500 mr-2"></i>Food & Dining</div>
                                <div><i class="fas fa-car text-blue-500 mr-2"></i>Transportation</div>
                                <div><i class="fas fa-shopping-bag text-pink-500 mr-2"></i>Shopping</div>
                                <div><i class="fas fa-home text-green-500 mr-2"></i>Housing</div>
                                <div><i class="fas fa-bolt text-yellow-500 mr-2"></i>Utilities</div>
                                <div><i class="fas fa-heartbeat text-red-500 mr-2"></i>Healthcare</div>
                                <div><i class="fas fa-film text-purple-500 mr-2"></i>Entertainment</div>
                                <div><i class="fas fa-graduation-cap text-cyan-500 mr-2"></i>Education</div>
                                <div><i class="fas fa-briefcase text-gray-500 mr-2"></i>Salary (Income)</div>
                            </div>
                        </div>
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-plus-circle text-green-500 mr-2"></i>Custom Categories</h4>
                            <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-decimal list-inside">
                                <li>Go to <strong>Categories</strong> page</li>
                                <li>Click <strong>"Add Category"</strong></li>
                                <li>Enter category name</li>
                                <li>Select type: <strong>Income</strong> or <strong>Expense</strong></li>
                                <li>Choose an icon from 500+ available icons</li>
                                <li>Select a color for visual identification</li>
                                <li>Click <strong>"Save"</strong></li>
                            </ol>
                        </div>
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-magic text-violet-500 mr-2"></i>Auto-Categorization Rules</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Set up rules to automatically
                                categorize transactions based on keywords:</p>
                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                <li>• "Netflix", "Spotify" → Entertainment</li>
                                <li>• "Uber", "Lyft", "Gas Station" → Transportation</li>
                                <li>• "Restaurant", "Cafe", "Pizza" → Food & Dining</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 7. Savings Goals -->
                <div class="mb-8" id="doc-goals">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-bullseye text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">7. Savings Goals</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-5 border border-purple-200/50 dark:border-purple-700/30">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-flag text-purple-500 mr-2"></i>Creating a Goal</h4>
                            <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-decimal list-inside">
                                <li>Navigate to <strong>Goals</strong> page</li>
                                <li>Click <strong>"Create Goal"</strong></li>
                                <li>Enter goal name (e.g., "Vacation Fund", "Emergency Fund", "New Car")</li>
                                <li>Set <strong>target amount</strong> (the total you want to save)</li>
                                <li>Enter <strong>current amount</strong> (if you've already saved some)</li>
                                <li>Set <strong>target date</strong> (deadline to achieve the goal)</li>
                                <li>Choose a <strong>color</strong> and <strong>icon</strong></li>
                                <li>Click <strong>"Save Goal"</strong></li>
                            </ol>
                        </div>
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-coins text-yellow-500 mr-2"></i>Contributing to Goals</h4>
                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                <li>• <strong>Add Money:</strong> Click "Add Money" on any goal card to contribute</li>
                                <li>• <strong>Withdraw:</strong> Click "Withdraw" if you need to use some funds</li>
                                <li>• <strong>Auto-Save:</strong> Set up automatic weekly/monthly contributions</li>
                                <li>• <strong>Progress:</strong> View percentage complete and days remaining</li>
                                <li>• <strong>Milestones:</strong> Celebrate when you hit 25%, 50%, 75%, 100%</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 8. Reports & Analytics -->
                <div class="mb-8" id="doc-reports">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-chart-pie text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">8. Reports & Analytics</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-file-alt text-pink-500 mr-2"></i>Available Reports</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="bg-white/50 dark:bg-gray-700/50 rounded-lg p-3">
                                    <strong class="text-gray-900 dark:text-white">Income vs Expense Report</strong>
                                    <p>Compare your earnings against spending over any time period</p>
                                </div>
                                <div class="bg-white/50 dark:bg-gray-700/50 rounded-lg p-3">
                                    <strong class="text-gray-900 dark:text-white">Spending by Category</strong>
                                    <p>Pie chart breakdown of where your money goes</p>
                                </div>
                                <div class="bg-white/50 dark:bg-gray-700/50 rounded-lg p-3">
                                    <strong class="text-gray-900 dark:text-white">Monthly Trends</strong>
                                    <p>Line graph showing income/expense trends over months</p>
                                </div>
                                <div class="bg-white/50 dark:bg-gray-700/50 rounded-lg p-3">
                                    <strong class="text-gray-900 dark:text-white">Budget Performance</strong>
                                    <p>How well you're sticking to your budgets</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-download text-green-500 mr-2"></i>Export Options</h4>
                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                <li>• <strong>PDF:</strong> Print-ready reports with charts and tables</li>
                                <li>• <strong>CSV:</strong> Raw data for spreadsheet analysis</li>
                                <li>• <strong>Excel:</strong> Formatted spreadsheets with formulas</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 9. Group Expenses -->
                <div class="mb-8" id="doc-groups">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">9. Group Expenses</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-5 border border-orange-200/50 dark:border-orange-700/30">
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                Split bills with friends, family, or roommates. Perfect for shared households, trips, or
                                group events.
                            </p>
                            <div class="space-y-4">
                                <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white mb-2"><i
                                            class="fas fa-user-plus text-orange-500 mr-2"></i>Creating a Group</h5>
                                    <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-decimal list-inside">
                                        <li>Go to <strong>Group Expense</strong> page</li>
                                        <li>Click <strong>"Create Group"</strong></li>
                                        <li>Enter group name (e.g., "Apartment 4B", "Trip to Goa")</li>
                                        <li>Add members by email or username</li>
                                        <li>Set default split method (Equal, Percentage, Custom)</li>
                                        <li>Click <strong>"Create"</strong></li>
                                    </ol>
                                </div>
                                <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white mb-2"><i
                                            class="fas fa-receipt text-green-500 mr-2"></i>Adding Group Expenses</h5>
                                    <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-decimal list-inside">
                                        <li>Open your group</li>
                                        <li>Click <strong>"Add Expense"</strong></li>
                                        <li>Enter description and total amount</li>
                                        <li>Select who paid</li>
                                        <li>Choose how to split (equal, by percentage, or exact amounts)</li>
                                        <li>Select which members are involved</li>
                                        <li>Add receipt photo (optional)</li>
                                        <li>Click <strong>"Save"</strong></li>
                                    </ol>
                                </div>
                                <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <h5 class="font-semibold text-gray-900 dark:text-white mb-2"><i
                                            class="fas fa-balance-scale text-blue-500 mr-2"></i>Settling Up</h5>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• View who owes whom in the group summary</li>
                                        <li>• Click <strong>"Settle Up"</strong> to record a payment</li>
                                        <li>• Supports partial settlements</li>
                                        <li>• Payment history is tracked for transparency</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 10. Community -->
                <div class="mb-8" id="doc-community">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-comments text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">10. Community</h3>
                    </div>
                    <div
                        class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Connect with other TrackFlow users to share tips, ask questions, and learn from each other's
                            financial journeys.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="text-gray-600 dark:text-gray-400">
                                <h5 class="font-semibold text-gray-900 dark:text-white mb-2">Features:</h5>
                                <ul class="space-y-1">
                                    <li>• Create and share posts</li>
                                    <li>• Like and comment on posts</li>
                                    <li>• Share financial tips and tricks</li>
                                    <li>• Ask the community for advice</li>
                                    <li>• Follow other users</li>
                                </ul>
                            </div>
                            <div class="text-gray-600 dark:text-gray-400">
                                <h5 class="font-semibold text-gray-900 dark:text-white mb-2">Guidelines:</h5>
                                <ul class="space-y-1">
                                    <li>• Be respectful and supportive</li>
                                    <li>• No spam or self-promotion</li>
                                    <li>• Don't share sensitive financial details</li>
                                    <li>• Report inappropriate content</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 11. Notifications -->
                <div class="mb-8" id="doc-notifications">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-amber-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-bell text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">11. Notifications</h3>
                    </div>
                    <div
                        class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Notification Types</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-exclamation-circle text-red-500 mt-1"></i>
                                <div>
                                    <strong>Budget Alerts</strong>
                                    <p>When you're approaching or exceeding budget limits</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-clock text-blue-500 mt-1"></i>
                                <div>
                                    <strong>Scheduled Reminders</strong>
                                    <p>Upcoming scheduled transactions due today</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-trophy text-yellow-500 mt-1"></i>
                                <div>
                                    <strong>Goal Milestones</strong>
                                    <p>When you reach goal milestones (25%, 50%, 75%, 100%)</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-users text-green-500 mt-1"></i>
                                <div>
                                    <strong>Group Updates</strong>
                                    <p>New expenses added, settlements, member changes</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-shield-alt text-purple-500 mt-1"></i>
                                <div>
                                    <strong>Security</strong>
                                    <p>Login from new device, password changes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 12. Settings & Profile -->
                <div class="mb-8" id="doc-settings">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-gray-500 to-slate-600 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-cog text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">12. Settings & Profile</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-user-circle text-blue-500 mr-2"></i>Profile Settings</h4>
                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                <li>• <strong>Profile Picture:</strong> Upload or change your avatar</li>
                                <li>• <strong>Display Name:</strong> How others see you in groups/community</li>
                                <li>• <strong>Email:</strong> Update your email address (requires verification)</li>
                                <li>• <strong>Password:</strong> Change your password anytime</li>
                                <li>• <strong>Two-Factor Auth:</strong> Enable/disable 2FA</li>
                            </ul>
                        </div>
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-sliders-h text-green-500 mr-2"></i>Preferences</h4>
                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                <li>• <strong>Currency:</strong> Set your default currency (₹, $, €, £, etc.)</li>
                                <li>• <strong>Date Format:</strong> DD/MM/YYYY or MM/DD/YYYY</li>
                                <li>• <strong>Theme:</strong> Light mode, Dark mode, or System default</li>
                                <li>• <strong>Language:</strong> Interface language preference</li>
                                <li>• <strong>Notifications:</strong> Email and push notification preferences</li>
                            </ul>
                        </div>
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-database text-amber-500 mr-2"></i>Data Management</h4>
                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                <li>• <strong>Export Data:</strong> Download all your data as CSV/JSON</li>
                                <li>• <strong>Import Data:</strong> Import transactions from CSV files</li>
                                <li>• <strong>Delete Account:</strong> Permanently delete your account and all data</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 13. Security & Privacy -->
                <div class="mb-8" id="doc-security">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-red-500 to-rose-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-shield-alt text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">13. Security & Privacy</h3>
                    </div>
                    <div
                        class="bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 rounded-xl p-5 border border-red-200/50 dark:border-red-700/30">
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300"><strong>Two-Factor Authentication
                                        (2FA):</strong> Add an extra layer of security to your account using authenticator
                                    apps (Google Authenticator, Authy) or email verification codes.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300"><strong>AES-256 Encryption:</strong> All your
                                    financial data is encrypted using industry-standard AES-256 encryption both at rest
                                    (stored data) and in transit (data being sent).</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300"><strong>Login Alerts:</strong> Receive
                                    instant email notifications when your account is accessed from a new device, browser, or
                                    location.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300"><strong>Session Management:</strong> View all
                                    active sessions and remotely log out from any device.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300"><strong>Bank-Level Security:</strong> Bank
                                    connections are handled through secure, encrypted channels with read-only access.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300"><strong>Privacy First:</strong> We never sell
                                    your data. Your financial information is yours and yours alone. We don't share with
                                    third parties.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 15. Keyboard Shortcuts -->
                <div class="mb-8" id="doc-shortcuts">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-gray-600 to-gray-700 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-keyboard text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">15. Keyboard Shortcuts</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div
                            class="flex items-center justify-between bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-lg p-3 border border-gray-200/50 dark:border-gray-700/50">
                            <span class="text-gray-700 dark:text-gray-300">New Transaction</span>
                            <kbd
                                class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm font-mono text-gray-800 dark:text-gray-200">Ctrl
                                + Z</kbd>
                        </div>
                        <div
                            class="flex items-center justify-between bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-lg p-3 border border-gray-200/50 dark:border-gray-700/50">
                            <span class="text-gray-700 dark:text-gray-300">Quick Search</span>
                            <kbd
                                class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm font-mono text-gray-800 dark:text-gray-200">Ctrl
                                + K</kbd>
                        </div>
                        <div
                            class="flex items-center justify-between bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-lg p-3 border border-gray-200/50 dark:border-gray-700/50">
                            <span class="text-gray-700 dark:text-gray-300">Dashboard</span>
                            <kbd
                                class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm font-mono text-gray-800 dark:text-gray-200">Ctrl
                                + D</kbd>
                        </div>
                        <div
                            class="flex items-center justify-between bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-lg p-3 border border-gray-200/50 dark:border-gray-700/50">
                            <span class="text-gray-700 dark:text-gray-300">Settings</span>
                            <kbd
                                class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm font-mono text-gray-800 dark:text-gray-200">Ctrl
                                + ,</kbd>
                        </div>
                        <div
                            class="flex items-center justify-between bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-lg p-3 border border-gray-200/50 dark:border-gray-700/50">
                            <span class="text-gray-700 dark:text-gray-300">Toggle Dark Mode</span>
                            <kbd
                                class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm font-mono text-gray-800 dark:text-gray-200">Ctrl
                                + Shift + D</kbd>
                        </div>
                        <div
                            class="flex items-center justify-between bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-lg p-3 border border-gray-200/50 dark:border-gray-700/50">
                            <span class="text-gray-700 dark:text-gray-300">Close Modal</span>
                            <kbd
                                class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm font-mono text-gray-800 dark:text-gray-200">Escape</kbd>
                        </div>
                    </div>
                </div>

                <!-- 16. Troubleshooting -->
                <div class="mb-8" id="doc-troubleshoot">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-red-500 to-orange-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-wrench text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">16. Troubleshooting</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3"><i
                                    class="fas fa-question-circle text-blue-500 mr-2"></i>Common Issues</h4>
                            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <strong class="text-gray-900 dark:text-white">Q: Transactions are not categorizing
                                        automatically</strong>
                                    <p>A: Check your category rules in Settings > Categories > Auto-Categorization. Make
                                        sure keywords match the transaction descriptions from your bank.</p>
                                </div>
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <strong class="text-gray-900 dark:text-white">Q: I'm not receiving email
                                        notifications</strong>
                                    <p>A: Check your spam/junk folder. Add noreply@trackflow.com to your contacts. Verify
                                        your email address in Settings > Profile.</p>
                                </div>
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <strong class="text-gray-900 dark:text-white">Q: Page is loading slowly</strong>
                                    <p>A: Try clearing your browser cache. If you have many transactions, use date filters
                                        to load smaller data sets.</p>
                                </div>
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <strong class="text-gray-900 dark:text-white">Q: I forgot my password</strong>
                                    <p>A: Click "Forgot Password" on the login page. You'll receive an email with a reset
                                        link valid for 1 hour.</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-5 border border-blue-200/50 dark:border-blue-700/30">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2"><i
                                    class="fas fa-life-ring text-blue-500 mr-2"></i>Still Need Help?</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">If you can't find a solution to your
                                problem, our support team is here to help.</p>
                            <button onclick="openContactSupportFromDocs(this)"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                                <i class="fas fa-envelope"></i>
                                Contact Support
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Version Info -->
                <div
                    class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800/60 dark:to-gray-800/40 rounded-xl p-5 border border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow" class="w-10 h-10 rounded-lg">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">TrackFlow</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Version 2.0.0 • Built with Laravel 12 •
                                    Last Updated: January 2026</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="openContactSupportFromDocs(this)" id="docContactSupportBtn"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2 min-w-[160px] justify-center">
                                <i class="fas fa-envelope"></i>
                                <span>Contact Support</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Form Modal -->
    <div id="contactFormModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Contact Support</h2>
                        <p class="text-sm text-white/80">We'll get back to you within 24 hours</p>
                    </div>
                </div>
                <button onclick="closeContactForm()" class="text-white hover:text-gray-200 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form onsubmit="submitContactForm(event)" class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Your Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="contactName" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="contactEmail" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="john@example.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select id="contactSubject" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select a topic</option>
                        <option value="technical">Technical Support</option>
                        <option value="billing">Billing Question</option>
                        <option value="feature">Feature Request</option>
                        <option value="bug">Bug Report</option>
                        <option value="account">Account Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea id="contactMessage" rows="6" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="Please describe your issue or question in detail..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Attachments (Optional)
                    </label>
                    <div id="dropZone"
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center transition-all cursor-pointer hover:border-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/10">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3" id="dropZoneIcon"></i>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2" id="dropZoneText">Drop files here or click
                            to upload</p>
                        <input type="file" id="contactAttachment" multiple accept="image/*,.pdf,.doc,.docx" class="hidden">
                        <button type="button" onclick="document.getElementById('contactAttachment').click()"
                            class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                            Browse Files
                        </button>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Max file size: 10MB | Formats: Images, PDF,
                            DOC</p>
                    </div>

                    <!-- Upload Progress -->
                    <div id="uploadProgress" class="hidden mt-3">
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Uploading...</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400" id="uploadPercentage">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div id="progressBar" class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                                    style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Uploaded Files List -->
                    <div id="uploadedFilesList" class="mt-3 space-y-2"></div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeContactForm()"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="contactSubmitBtn"
                        class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl disabled:opacity-70 disabled:cursor-not-allowed">
                        <i class="fas fa-paper-plane mr-2" id="submitBtnIcon"></i>
                        <span id="submitBtnText">Send Message</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Video Modal -->
    <div id="videoModal" class="hidden fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4">
        <div class="relative w-full max-w-5xl">
            <!-- Close Button -->
            <button onclick="closeVideoModal()"
                class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-3xl"></i>
            </button>

            <!-- Video Container -->
            <div class="bg-black rounded-lg overflow-hidden shadow-2xl">
                <div class="aspect-video bg-gray-900 flex items-center justify-center" id="videoContainer">
                    <!-- Video will be loaded here -->
                </div>
                <div class="bg-gray-800 p-4">
                    <h3 class="text-white font-semibold text-lg mb-1" id="videoTitle">Video Title</h3>
                    <p class="text-gray-400 text-sm" id="videoDescription">Video description</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Contact Form Functions - Defined at top level for global access
        function openContactForm() {
            document.getElementById('contactFormModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeContactForm() {
            document.getElementById('contactFormModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        const videos = {
            'getting-started-video': {
                title: 'Getting Started with TrackFlow',
                description: 'Learn the basics and set up your account in just a few minutes',
                embedUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ'
            },
            'transactions-video': {
                title: 'How to Add & Manage Transactions',
                description: 'Record your income and expenses efficiently with our intuitive interface',
                embedUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ'
            },
            'budgets-video': {
                title: 'Setting Up Your Budget',
                description: 'Create and track your monthly budgets to stay on top of your finances',
                embedUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ'
            },
            'goals-video': {
                title: 'Achieving Financial Goals',
                description: 'Set and track your savings goals with visual progress tracking',
                embedUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ'
            },
            'reports-video': {
                title: 'Understanding Reports & Analytics',
                description: 'Generate and analyze detailed financial reports with charts and graphs',
                embedUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ'
            },
            'advanced-video': {
                title: 'Advanced Features & Pro Tips',
                description: 'Master advanced features and discover productivity tips',
                embedUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ'
            }
        };

        function openVideoModal(videoId) {
            const video = videos[videoId];
            if (!video) return;

            document.getElementById('videoTitle').textContent = video.title;
            document.getElementById('videoDescription').textContent = video.description;

            const videoContainer = document.getElementById('videoContainer');
            videoContainer.innerHTML = `
                                                            <iframe 
                                                                width="100%" 
                                                                height="100%" 
                                                                src="${video.embedUrl}?autoplay=1" 
                                                                frameborder="0" 
                                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                                allowfullscreen
                                                                class="w-full h-full"
                                                            ></iframe>
                                                        `;

            document.getElementById('videoModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeVideoModal() {
            document.getElementById('videoModal').classList.add('hidden');
            document.getElementById('videoContainer').innerHTML = '';
            document.body.style.overflow = 'auto';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeVideoModal();
            }
        });

        function toggleGuide(id) {
            const element = document.getElementById('guide-' + id);
            const icon = event.currentTarget.querySelector('.fa-chevron-down');

            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                element.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        function toggleFaq(id) {
            const element = document.getElementById(id);
            const icon = event.currentTarget.querySelector('.fa-chevron-down');

            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                element.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        function searchHelpArticles(query) {
            const searchTerm = query.toLowerCase();
            // In a real implementation, this would search through help articles
            console.log('Searching for:', searchTerm);
        }

        // Live Chat Functions - User and Logo URLs
        const trackFlowLogoUrl = "{{ asset('trackflow-main/logo.png') }}";
        @php
            $chatUserProfilePic = DB::table('users')->where('id', session('user_id'))->value('profile_picture');
            $chatUserName = session('user_name', 'User');
            $chatUserInitial = substr($chatUserName, 0, 1);
        @endphp
        const userProfilePicUrl = @json($chatUserProfilePic);
        const userInitial = @json($chatUserInitial);

        // AI Knowledge Base (Enhanced)
        const knowledgeBase = [
            // ==================== GREETINGS & BASICS ====================
            {
                keywords: ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening', 'hii', 'hiii', 'howdy', 'greetings', 'sup', 'yo', 'namaste', 'hola'],
                response: "Hello! 👋 Welcome to TrackFlow Support! I'm your AI assistant, here 24/7 to help you. You can ask me about:<br><br>• 💰 Transactions & Categories<br>• 📊 Budgets & Reports<br>• 🎯 Goals & Savings<br>• 👥 Group Expenses<br>• ⚙️ Settings & Security<br><br>What would you like to know?"
            },
            {
                keywords: ['who are you', 'what are you', 'your name', 'are you bot', 'are you human', 'are you real', 'ai assistant', 'chatbot'],
                response: "🤖 I'm TrackFlow AI Assistant! I'm an intelligent chatbot designed to help you navigate and use TrackFlow effectively. I can answer questions about features, guide you through processes, and provide instant support 24/7. While I'm not human, I'm here to make your experience seamless!"
            },
            {
                keywords: ['what is trackflow', 'about trackflow', 'tell me about', 'what does trackflow do', 'explain trackflow', 'trackflow features', 'why trackflow', 'trackflow app'],
                response: "🚀 <b>TrackFlow - Your Complete Finance Manager</b><br><br>TrackFlow is a powerful personal finance platform offering:<br><br>✅ <b>Transaction Tracking</b> - Log income & expenses<br>✅ <b>Smart Budgets</b> - Set & monitor spending limits<br>✅ <b>Financial Goals</b> - Save for what matters<br>✅ <b>Detailed Reports</b> - Visual analytics & insights<br>✅ <b>Group Expenses</b> - Split bills with friends<br>✅ <b>Multi-Currency</b> - 100+ currencies supported<br>✅ <b>Community</b> - Connect with other users<br>✅ <b>Secure</b> - 2FA & encryption<br><br>Take control of your finances today! 💰"
            },
            {
                keywords: ['how does it work', 'how to use', 'getting started', 'new user', 'beginner', 'start using', 'first time', 'tutorial', 'guide'],
                response: "🎓 <b>Getting Started with TrackFlow:</b><br><br><b>Step 1:</b> Create your account & verify email<br><b>Step 2:</b> Set up your profile & preferences<br><b>Step 3:</b> Add your first transaction (income/expense)<br><b>Step 4:</b> Create categories for organization<br><b>Step 5:</b> Set up a budget for spending control<br><b>Step 6:</b> Create savings goals<br><b>Step 7:</b> Explore reports & analytics<br><br>💡 <b>Pro Tip:</b> Use categories to keep your transactions well-organized!"
            },

            // ==================== TRANSACTIONS ====================
            {
                keywords: ['transaction', 'add transaction', 'create transaction', 'new transaction', 'log transaction', 'record transaction', 'enter transaction'],
                response: "📝 <b>Adding Transactions:</b><br><br>1. Go to <b>Transactions</b> from sidebar<br>2. Click <b>+ Add Transaction</b><br>3. Choose: <span class='text-green-600'>Income</span> or <span class='text-red-600'>Expense</span><br>4. Enter amount, category, date<br>5. Add description (optional)<br>6. Click <b>Save</b><br><br>💡 <b>Tips:</b><br>• Use recurring for regular transactions<br>• Add notes for future reference<br>• Attach receipts if needed"
            },
            {
                keywords: ['income', 'add income', 'salary', 'earning', 'money received', 'payment received'],
                response: "💵 <b>Recording Income:</b><br><br>1. Go to <b>Transactions</b><br>2. Click <b>+ Add Transaction</b><br>3. Select <b>Income</b> type<br>4. Choose category (Salary, Freelance, Investment, Gift, etc.)<br>5. Enter amount and date<br>6. Save<br><br>📊 Your income will reflect in dashboard totals and reports!"
            },
            {
                keywords: ['expense', 'add expense', 'spending', 'money spent', 'purchase', 'bought'],
                response: "💸 <b>Recording Expenses:</b><br><br>1. Go to <b>Transactions</b><br>2. Click <b>+ Add Transaction</b><br>3. Select <b>Expense</b> type<br>4. Choose category (Food, Transport, Shopping, Bills, etc.)<br>5. Enter amount and date<br>6. Save<br><br>📊 Expenses are tracked against your budgets automatically!"
            },
            {
                keywords: ['edit transaction', 'modify transaction', 'change transaction', 'update transaction', 'correct transaction', 'fix transaction'],
                response: "✏️ <b>Editing Transactions:</b><br><br>1. Go to <b>Transactions</b> page<br>2. Find the transaction you want to edit<br>3. Click the <b>Edit</b> icon (pencil)<br>4. Modify the details<br>5. Click <b>Save Changes</b><br><br>You can edit amount, category, date, description, and more!"
            },
            {
                keywords: ['delete transaction', 'remove transaction', 'cancel transaction', 'undo transaction'],
                response: "🗑️ <b>Deleting Transactions:</b><br><br>1. Go to <b>Transactions</b> page<br>2. Find the transaction to delete<br>3. Click the <b>Delete</b> icon (trash)<br>4. Confirm deletion<br><br>⚠️ <b>Note:</b> Deleted transactions cannot be recovered. Make sure before deleting!"
            },
            {
                keywords: ['recurring', 'recurring transaction', 'repeat transaction', 'automatic transaction', 'scheduled', 'monthly transaction'],
                response: "🔄 <b>Recurring Transactions:</b><br><br>Set up automatic entries for regular income/expenses:<br><br>1. Add a new transaction<br>2. Enable <b>Recurring</b> option<br>3. Set frequency: Daily, Weekly, Monthly, Yearly<br>4. Set start date and end date (optional)<br>5. Save<br><br>Perfect for: Salary, rent, subscriptions, EMIs, utility bills!"
            },
            {
                keywords: ['search transaction', 'find transaction', 'filter transaction', 'transaction history', 'past transaction', 'old transaction'],
                response: "🔍 <b>Finding Transactions:</b><br><br>Use powerful filters on the Transactions page:<br><br>• <b>Date Range:</b> Today, This Week, This Month, Custom<br>• <b>Type:</b> Income or Expense<br>• <b>Category:</b> Filter by specific category<br>• <b>Amount:</b> Min/Max range<br>• <b>Search:</b> By description or notes<br><br>Export filtered results to CSV/Excel!"
            },
            {
                keywords: ['bulk', 'multiple transactions', 'import transactions', 'upload transactions', 'batch'],
                response: "📤 <b>Bulk Transaction Import:</b><br><br>Import multiple transactions at once:<br><br>1. Go to <b>Transactions → Import</b><br>2. Download our CSV template<br>3. Fill in your transactions<br>4. Upload the file<br>5. Review and confirm<br><br>💡 Use the CSV template to import transactions quickly!"
            },

            // ==================== CATEGORIES ====================
            {
                keywords: ['category', 'categories', 'add category', 'create category', 'custom category', 'organize', 'categorize'],
                response: "🏷️ <b>Managing Categories:</b><br><br>1. Go to <b>Settings → Categories</b><br>2. Click <b>+ Add Category</b><br>3. Enter category name<br>4. Choose an icon & color<br>5. Select type: Income or Expense<br>6. Save<br><br><b>Default Categories:</b><br>• 🍔 Food & Dining<br>• 🚗 Transportation<br>• 🏠 Housing<br>• 🛒 Shopping<br>• 💡 Utilities<br>• 🎬 Entertainment<br>• 💊 Healthcare"
            },
            {
                keywords: ['category rule', 'auto category', 'automatic categorization', 'smart category', 'categorize automatically'],
                response: "🤖 <b>Category Rules (Auto-Categorization):</b><br><br>Let TrackFlow categorize transactions automatically!<br><br>1. Go to <b>Settings → Category Rules</b><br>2. Click <b>+ Add Rule</b><br>3. Enter keyword (e.g., 'Netflix')<br>4. Select target category (e.g., 'Entertainment')<br>5. Save<br><br>Now any transaction with 'Netflix' will auto-categorize!"
            },
            {
                keywords: ['subcategory', 'sub category', 'nested category', 'child category'],
                response: "📂 <b>Subcategories:</b><br><br>Organize with parent-child categories:<br><br>Example structure:<br>• 🍔 <b>Food</b><br>&nbsp;&nbsp;├─ Groceries<br>&nbsp;&nbsp;├─ Restaurants<br>&nbsp;&nbsp;└─ Coffee & Snacks<br><br>Create by selecting a parent when adding new category. Great for detailed tracking!"
            },

            // ==================== BUDGETS ====================
            {
                keywords: ['budget', 'budgets', 'create budget', 'set budget', 'budget limit', 'spending limit', 'monthly budget', 'budget planning'],
                response: "💵 <b>Creating Budgets:</b><br><br>1. Go to <b>Budgets</b> from sidebar<br>2. Click <b>+ Create Budget</b><br>3. Name your budget<br>4. Set period: Weekly, Monthly, Yearly<br>5. Add categories with limits<br>6. Save<br><br><b>Budget Features:</b><br>• 📊 Visual progress bars<br>• 🔔 Alerts at 80% & 100%<br>• 📈 Spending trends<br>• 🎯 Rollover unused amounts"
            },
            {
                keywords: ['budget alert', 'over budget', 'budget warning', 'budget notification', 'exceeded budget', 'budget limit reached'],
                response: "🚨 <b>Budget Alerts:</b><br><br>TrackFlow notifies you when:<br><br>• ⚠️ <b>80% used:</b> Warning alert<br>• 🔴 <b>100% reached:</b> Limit exceeded<br>• 📊 <b>Weekly summary:</b> Progress update<br><br>Configure alerts in <b>Settings → Notifications</b><br><br>Stay on track and avoid overspending!"
            },
            {
                keywords: ['budget tip', 'budgeting advice', 'save money tip', 'spending tip', 'financial tip'],
                response: "💡 <b>Smart Budgeting Tips:</b><br><br>1. <b>50/30/20 Rule:</b> 50% needs, 30% wants, 20% savings<br>2. <b>Track Everything:</b> Even small expenses add up<br>3. <b>Review Weekly:</b> Adjust before month ends<br>4. <b>Emergency Fund:</b> Budget for unexpected expenses<br>5. <b>Automate Savings:</b> Pay yourself first<br>6. <b>Use Categories:</b> Know where money goes<br><br>📊 Check Reports for spending insights!"
            },

            // ==================== GOALS ====================
            {
                keywords: ['goal', 'goals', 'savings goal', 'financial goal', 'save money', 'target', 'saving', 'save for'],
                response: "🎯 <b>Setting Financial Goals:</b><br><br>1. Go to <b>Goals</b> from sidebar<br>2. Click <b>+ Create Goal</b><br>3. Enter goal name<br>4. Set target amount<br>5. Set deadline date<br>6. Add initial contribution<br>7. Save<br><br><b>Goal Ideas:</b><br>• 🚗 New Car<br>• ✈️ Vacation Trip<br>• 🏠 Home Down Payment<br>• 💼 Emergency Fund<br>• 🎓 Education<br>• 💍 Wedding"
            },
            {
                keywords: ['goal progress', 'goal tracking', 'add to goal', 'contribute goal', 'goal contribution'],
                response: "📈 <b>Tracking Goal Progress:</b><br><br>1. Go to <b>Goals</b><br>2. Select your goal<br>3. Click <b>+ Add Contribution</b><br>4. Enter amount saved<br>5. Add note (optional)<br><br><b>Features:</b><br>• Visual progress bar<br>• Percentage completed<br>• Days remaining<br>• Required monthly savings<br>• Contribution history"
            },
            {
                keywords: ['goal complete', 'reached goal', 'goal achieved', 'withdraw goal'],
                response: "🎉 <b>Completing Goals:</b><br><br>When you reach your target:<br><br>1. You'll receive a celebration notification<br>2. Goal moves to 'Completed' section<br>3. You can:<br>&nbsp;&nbsp;• Keep it for records<br>&nbsp;&nbsp;• Archive it<br>&nbsp;&nbsp;• Delete it<br><br>Start a new goal and keep the momentum going! 💪"
            },

            // ==================== REPORTS & ANALYTICS ====================
            {
                keywords: ['report', 'reports', 'analytics', 'statistics', 'analysis', 'chart', 'graph', 'insights', 'spending pattern'],
                response: "📊 <b>Reports & Analytics:</b><br><br>Go to <b>Reports</b> for insights:<br><br><b>Available Reports:</b><br>• 📈 Income vs Expense Trends<br>• 🥧 Category Breakdown (Pie Chart)<br>• 📅 Daily/Weekly/Monthly Summary<br>• 🔄 Cash Flow Analysis<br>• 📉 Spending Patterns<br>• 🎯 Budget Performance<br>• 🏆 Goal Progress<br><br><b>Export as:</b> PDF, Excel, CSV"
            },
            {
                keywords: ['spending analysis', 'where money goes', 'spending breakdown', 'expense analysis', 'spending habits'],
                response: "🔍 <b>Spending Analysis:</b><br><br>Understand your spending with:<br><br>• <b>Category Pie Chart:</b> See % per category<br>• <b>Top Spending Categories:</b> Where most money goes<br>• <b>Daily Average:</b> Spending per day<br>• <b>Comparison:</b> This month vs last month<br>• <b>Trends:</b> Increasing/decreasing patterns<br><br>Go to <b>Reports → Spending Analysis</b> for details!"
            },
            {
                keywords: ['compare', 'comparison', 'month to month', 'year to year', 'trend', 'vs last month'],
                response: "📈 <b>Comparison Reports:</b><br><br>Compare your finances over time:<br><br>• <b>Month vs Month:</b> This vs last month<br>• <b>Year over Year:</b> Annual comparison<br>• <b>Category Trends:</b> How spending changed<br>• <b>Income Growth:</b> Track earnings over time<br><br>Go to <b>Reports → Trends</b> for visual comparisons!"
            },

            // ==================== GROUP EXPENSES ====================
            {
                keywords: ['group', 'group expense', 'split', 'share expense', 'friends', 'roommate', 'split bill', 'settle', 'shared expense'],
                response: "👥 <b>Group Expenses:</b><br><br>Split bills easily with friends:<br><br>1. Go to <b>Group Expense</b><br>2. Click <b>+ Create Group</b><br>3. Add members (email or phone)<br>4. Add shared expenses<br>5. Select who paid & who's involved<br>6. TrackFlow calculates balances<br>7. Settle up when ready<br><br><b>Perfect for:</b> Trips, roommates, dinners, events!"
            },
            {
                keywords: ['add member', 'invite member', 'group member', 'add friend', 'add people'],
                response: "➕ <b>Adding Group Members:</b><br><br>1. Open your group<br>2. Click <b>Members</b> tab<br>3. Click <b>+ Add Member</b><br>4. Enter email or phone<br>5. They'll receive an invite<br><br><b>Member Types:</b><br>• TrackFlow users - Full access<br>• Non-users - Can view via link<br><br>Members can view profile & UPI for easy payments!"
            },
            {
                keywords: ['settle up', 'settle', 'pay back', 'clear dues', 'group payment', 'who owes'],
                response: "💳 <b>Settling Up:</b><br><br>1. Go to your group<br>2. View <b>Balances</b> - See who owes whom<br>3. Click <b>Settle Up</b><br>4. Select the payment<br>5. Mark as settled<br><br><b>Payment Options:</b><br>• Use member's UPI (copy from profile)<br>• Cash payment<br>• Bank transfer<br><br>All settlements are recorded in history!"
            },
            {
                keywords: ['equal split', 'unequal split', 'split by percent', 'custom split', 'split options'],
                response: "➗ <b>Split Options:</b><br><br>When adding group expense:<br><br>• <b>Equal Split:</b> Divide evenly among all<br>• <b>Unequal Split:</b> Custom amounts per person<br>• <b>Percentage Split:</b> By % contribution<br>• <b>Shares:</b> 1x, 2x multipliers<br>• <b>Exclude:</b> Leave out specific members<br><br>Flexible splitting for any situation!"
            },

            // ==================== SETTINGS & PREFERENCES ====================
            {
                keywords: ['bank', 'bank account', 'link bank', 'connect bank', 'sync', 'automatic', 'import', 'bank sync'],
                response: "🏦 <b>Bank Account Integration:</b><br><br>Bank account linking feature has been removed from TrackFlow.<br><br>You can still manually add all your transactions in the Transactions section. TrackFlow will help you categorize and track them!"
            },
            {
                keywords: ['sync problem', 'not syncing', 'sync error', 'bank not working', 'transaction not showing'],
                response: "🔧 <b>Transaction Issues:</b><br><br>If your transactions are not showing:<br><br>1. <b>Check filters:</b> Make sure no filters are hiding transactions<br>2. <b>Add manually:</b> Use the + button to add transactions<br>3. <b>Refresh page:</b> Try refreshing your browser<br><br>Still having issues? Contact support!"
            },

            // ==================== SETTINGS & PREFERENCES ====================
            {
                keywords: ['setting', 'settings', 'preference', 'configure', 'customize', 'options'],
                response: "⚙️ <b>Settings Overview:</b><br><br>Customize TrackFlow in Settings:<br><br>• 👤 <b>Profile:</b> Name, email, photo<br>• 🔐 <b>Security:</b> Password, 2FA<br>• 🔔 <b>Notifications:</b> Alerts & reminders<br>• 💱 <b>Currency:</b> Default currency<br>• 🏷️ <b>Categories:</b> Manage categories<br>• 📱 <b>UPI:</b> Payment methods<br>• 🔒 <b>Privacy:</b> Data & security<br>• 🎨 <b>Appearance:</b> Dark/Light mode"
            },
            {
                keywords: ['profile', 'update profile', 'change name', 'change email', 'profile picture', 'photo', 'avatar'],
                response: "👤 <b>Updating Profile:</b><br><br>1. Go to <b>Settings → Profile</b><br>2. Update your information:<br>&nbsp;&nbsp;• Name<br>&nbsp;&nbsp;• Email<br>&nbsp;&nbsp;• Phone<br>&nbsp;&nbsp;• Profile Picture<br>&nbsp;&nbsp;• Bio<br>3. Click <b>Save Changes</b><br><br>Your profile is visible to group members!"
            },
            {
                keywords: ['dark mode', 'light mode', 'theme', 'appearance', 'color scheme', 'night mode'],
                response: "🎨 <b>Theme Settings:</b><br><br>Switch between themes:<br><br>1. Click your profile icon (top right)<br>2. Toggle <b>Dark Mode</b> switch<br><br>Or go to <b>Settings → Appearance</b><br><br>Options:<br>• ☀️ Light Mode<br>• 🌙 Dark Mode<br>• 🔄 System (follows device setting)"
            },
            {
                keywords: ['language', 'change language', 'english', 'hindi', 'translate'],
                response: "🌐 <b>Language Settings:</b><br><br>Currently TrackFlow is available in English.<br><br>More languages coming soon:<br>• हिंदी (Hindi)<br>• Español (Spanish)<br>• Français (French)<br>• Deutsch (German)<br><br>Want your language added? Let us know through the Contact Form!"
            },

            // ==================== SECURITY ====================
            {
                keywords: ['2fa', 'two factor', 'authentication', 'security', 'secure', 'otp', 'authenticator', 'verification'],
                response: "🔐 <b>Two-Factor Authentication:</b><br><br>Add extra security to your account:<br><br>1. Go to <b>Settings → Security</b><br>2. Click <b>Enable 2FA</b><br>3. Download an authenticator app:<br>&nbsp;&nbsp;• Google Authenticator<br>&nbsp;&nbsp;• Microsoft Authenticator<br>&nbsp;&nbsp;• Authy<br>4. Scan the QR code<br>5. Enter 6-digit code<br>6. Save backup codes safely!<br><br>✅ 2FA users get a verified badge!"
            },
            {
                keywords: ['password', 'forgot password', 'reset password', 'change password', 'login problem', 'cant login', 'locked out'],
                response: "🔑 <b>Password Help:</b><br><br><b>Change Password:</b><br>1. Settings → Security<br>2. Enter current password<br>3. Enter new password (min 8 chars)<br>4. Confirm & save<br><br><b>Forgot Password:</b><br>1. Go to login page<br>2. Click 'Forgot Password'<br>3. Enter your email<br>4. Check inbox for reset link<br>5. Create new password<br><br>⚠️ Still locked out? Contact support!"
            },
            {
                keywords: ['privacy', 'data privacy', 'my data', 'personal data', 'data protection', 'gdpr'],
                response: "🔒 <b>Privacy & Data Protection:</b><br><br>Your data is safe with TrackFlow:<br><br>• 🔐 End-to-end encryption<br>• 🚫 We never sell your data<br>• 📋 GDPR compliant<br>• 🗑️ Right to delete anytime<br>• 📥 Export your data anytime<br>• 🏦 Bank-level security<br><br>Read our full Privacy Policy in the footer."
            },
            {
                keywords: ['hacked', 'suspicious', 'unauthorized', 'someone accessed', 'security breach', 'compromised'],
                response: "🚨 <b>Account Security Alert:</b><br><br>If you suspect unauthorized access:<br><br>1. <b>Change password immediately</b><br>2. <b>Enable 2FA</b> if not already<br>3. <b>Check recent activity</b> in Settings<br>4. <b>Log out all devices:</b> Settings → Security → Log Out Everywhere<br>5. <b>Contact support</b> immediately<br><br>We take security seriously and will help investigate!"
            },

            // ==================== UPI & PAYMENTS ====================
            {
                keywords: ['upi', 'upi id', 'payment', 'pay', 'qr code', 'receive payment', 'gpay', 'phonepe', 'paytm'],
                response: "📱 <b>UPI Management:</b><br><br>1. Go to <b>Settings → UPI</b><br>2. Click <b>+ Add UPI ID</b><br>3. Enter UPI ID (e.g., name@okaxis)<br>4. Optionally upload QR code<br>5. Set one as <b>Primary</b><br><br><b>Uses:</b><br>• Displayed on your profile<br>• Group members can pay you directly<br>• Easy copy-paste functionality"
            },
            {
                keywords: ['primary upi', 'default upi', 'main upi', 'change primary'],
                response: "⭐ <b>Setting Primary UPI:</b><br><br>1. Go to <b>Settings → UPI</b><br>2. Find the UPI ID to make primary<br>3. Click <b>Set as Primary</b><br><br>Your primary UPI:<br>• Shows on your profile page<br>• Displayed to group members<br>• First option for payments"
            },

            // ==================== NOTIFICATIONS ====================
            {
                keywords: ['notification', 'notifications', 'alert', 'alerts', 'remind', 'reminder', 'email notification'],
                response: "🔔 <b>Notification Settings:</b><br><br>Customize your alerts:<br><br>1. Go to <b>Settings → Notifications</b><br>2. Toggle preferences:<br><br>• 💰 Transaction confirmations<br>• 📊 Budget alerts (80%, 100%)<br>• 🎯 Goal milestones<br>• 👥 Group activity<br>• 📅 Bill reminders<br>• 📧 Weekly summaries<br>• 🔐 Security alerts<br><br>Choose: Push, Email, or Both"
            },
            {
                keywords: ['too many notifications', 'stop notifications', 'disable notifications', 'unsubscribe'],
                response: "🔕 <b>Reducing Notifications:</b><br><br>1. Go to <b>Settings → Notifications</b><br>2. Disable unwanted alerts<br>3. Keep only essential ones<br><br><b>Recommended to keep ON:</b><br>• Security alerts<br>• Budget warnings<br>• Bill reminders<br><br>You can always turn them back on!"
            },

            // ==================== ACCOUNT MANAGEMENT ====================
            {
                keywords: ['delete account', 'remove account', 'close account', 'deactivate', 'delete my account'],
                response: "⚠️ <b>Deleting Your Account:</b><br><br>1. Go to <b>Settings → Privacy</b><br>2. Scroll to <b>Danger Zone</b><br>3. Click <b>Delete Account</b><br>4. Enter your password<br>5. Receive OTP on email<br>6. Confirm deletion<br><br>❗ <b>Warning:</b><br>• All data permanently deleted<br>• Cannot be recovered<br>• Active subscriptions cancelled<br><br>Consider exporting data first!"
            },
            {
                keywords: ['export', 'download', 'backup', 'data export', 'csv', 'excel', 'download data'],
                response: "📥 <b>Exporting Your Data:</b><br><br>1. Go to <b>Settings → Privacy</b><br>2. Click <b>Export Data</b><br>3. Select what to export:<br>&nbsp;&nbsp;• Transactions<br>&nbsp;&nbsp;• Categories<br>&nbsp;&nbsp;• Budgets<br>&nbsp;&nbsp;• Goals<br>4. Choose format: CSV or Excel<br>5. Select date range<br>6. Download<br><br>Keep backups of your financial data!"
            },

            // ==================== CURRENCY ====================
            {
                keywords: ['currency', 'currencies', 'dollar', 'rupee', 'euro', 'exchange rate', 'convert', 'inr', 'usd'],
                response: "💱 <b>Currency Settings:</b><br><br>1. Go to <b>Settings → Preferences</b><br>2. Select default currency<br><br><b>Supported:</b> 100+ currencies<br>• 🇮🇳 INR - Indian Rupee<br>• 🇺🇸 USD - US Dollar<br>• 🇪🇺 EUR - Euro<br>• 🇬🇧 GBP - British Pound<br>• And many more!<br><br>Exchange rates update automatically daily!"
            },

            // ==================== DASHBOARD ====================
            {
                keywords: ['dashboard', 'home', 'overview', 'summary', 'main page', 'home page'],
                response: "🏠 <b>Dashboard Overview:</b><br><br>Your financial command center shows:<br><br>• 💰 <b>Balance:</b> Total money available<br>• 📈 <b>Income:</b> This month's earnings<br>• 📉 <b>Expenses:</b> This month's spending<br>• 🕐 <b>Recent Transactions:</b> Latest activity<br>• 📊 <b>Budget Progress:</b> Visual bars<br>• 🎯 <b>Goal Progress:</b> Savings status<br>• 🥧 <b>Spending Chart:</b> Category breakdown<br>• ⚡ <b>Quick Actions:</b> Add transaction, etc."
            },

            // ==================== MOBILE & ACCESS ====================
            {
                keywords: ['mobile', 'app', 'android', 'ios', 'phone', 'download app', 'mobile app', 'smartphone'],
                response: "📱 <b>Mobile Access:</b><br><br><b>Currently:</b><br>TrackFlow works perfectly in mobile browsers! Just visit our website on your phone for full functionality.<br><br><b>Coming Soon:</b><br>• 📲 Android App (Play Store)<br>• 🍎 iOS App (App Store)<br>• ⌚ Apple Watch support<br><br>Sign up for notifications to know when apps launch!"
            },
            {
                keywords: ['offline', 'no internet', 'works offline', 'without internet'],
                response: "📶 <b>Offline Access:</b><br><br>TrackFlow currently requires internet for full functionality.<br><br><b>Coming Soon:</b><br>• Offline mode for viewing data<br>• Queue transactions when offline<br>• Auto-sync when back online<br><br>For now, you can export data as CSV for offline reference!"
            },

            // ==================== HELP & SUPPORT ====================
            {
                keywords: ['help', 'support', 'contact', 'problem', 'issue', 'stuck', 'how to', 'assistance', 'customer service'],
                response: "🆘 <b>Getting Help:</b><br><br>Multiple support options:<br><br>• 🤖 <b>AI Chat:</b> You're using it now! Ask anything<br>• ❓ <b>FAQ:</b> Common questions answered<br>• 📧 <b>Email:</b> support@trackflow.com<br>• 📝 <b>Contact Form:</b> Detailed inquiries<br>• 📚 <b>Help Center:</b> Guides & tutorials<br><br>Response time: Within 24 hours for emails!"
            },
            {
                keywords: ['bug', 'error', 'not working', 'broken', 'glitch', 'crash', 'problem'],
                response: "🐛 <b>Reporting Issues:</b><br><br>Found a bug? Help us fix it!<br><br>1. Click <b>Contact Form</b> below<br>2. Select subject: 'Bug Report'<br>3. Describe the issue:<br>&nbsp;&nbsp;• What happened?<br>&nbsp;&nbsp;• What were you trying to do?<br>&nbsp;&nbsp;• Error message (if any)<br>4. Attach screenshot if possible<br>5. Submit<br><br>We investigate all reports promptly!"
            },
            {
                keywords: ['feedback', 'suggestion', 'feature request', 'idea', 'improve', 'recommend'],
                response: "💡 <b>Share Your Ideas:</b><br><br>We love hearing from you!<br><br>1. Use the <b>Contact Form</b><br>2. Select 'Feature Request'<br>3. Describe your idea<br>4. Submit<br><br>Many features came from user suggestions:<br>• Group expenses<br>• QR code upload<br>• Dark mode<br>• And more!<br><br>Your feedback shapes TrackFlow! 🚀"
            },

            // ==================== PRICING & PLANS ====================
            {
                keywords: ['price', 'pricing', 'cost', 'free', 'premium', 'subscription', 'plan', 'upgrade', 'paid'],
                response: "💎 <b>TrackFlow Plans:</b><br><br><b>🆓 Free Plan:</b><br>• Unlimited transactions<br>• Unlimited budgets & goals<br>• Basic reports<br>• Group expenses<br><br><b>⭐ Premium Plan:</b><br>• Everything in Free<br>• Advanced analytics<br>• Priority support<br>• Data export<br>• Custom categories<br><br>Start free, upgrade when ready!"
            },
            {
                keywords: ['cancel subscription', 'cancel premium', 'stop subscription', 'unsubscribe premium'],
                response: "🔄 <b>Cancelling Premium:</b><br><br>1. Go to <b>Settings → Billing</b><br>2. Click <b>Manage Subscription</b><br>3. Select <b>Cancel Plan</b><br>4. Confirm<br><br>• Access continues until period ends<br>• Downgrade to Free plan<br>• No data is deleted<br>• Can resubscribe anytime"
            },

            // ==================== SOCIAL & LOGIN ====================
            {
                keywords: ['google login', 'social login', 'sign in google', 'facebook login', 'login with google'],
                response: "🔗 <b>Social Login:</b><br><br>Sign in quickly with:<br><br>• 🔴 <b>Google:</b> One-click sign in<br>• 🔵 <b>Facebook:</b> Connect account<br><br>To link social accounts:<br>1. Go to <b>Settings → Connected Accounts</b><br>2. Click <b>Connect</b> next to provider<br>3. Authorize access<br><br>You can use multiple login methods!"
            },
            {
                keywords: ['logout', 'log out', 'sign out', 'exit'],
                response: "🚪 <b>Logging Out:</b><br><br>1. Click your profile icon (top right)<br>2. Click <b>Log Out</b><br><br><b>Log out everywhere:</b><br>1. Settings → Security<br>2. Click 'Log Out All Devices'<br>3. All sessions terminated<br><br>Use this if you suspect unauthorized access!"
            },

            // ==================== CONVERSATIONAL ====================
            {
                keywords: ['thank', 'thanks', 'thank you', 'thx', 'ty', 'appreciate', 'helpful'],
                response: "You're welcome! 😊 I'm glad I could help! Is there anything else you'd like to know about TrackFlow? I'm here to assist with any questions!"
            },
            {
                keywords: ['bye', 'goodbye', 'see you', 'later', 'exit chat', 'close chat'],
                response: "Goodbye! 👋 Thanks for chatting with TrackFlow Support! Remember, I'm available 24/7 if you need help. Have a great day and happy tracking! 💰"
            },
            {
                keywords: ['great', 'awesome', 'amazing', 'excellent', 'wonderful', 'perfect', 'love it'],
                response: "Thank you so much! 🌟 We're thrilled you're enjoying TrackFlow! Your positive feedback motivates us to keep improving. If you have any suggestions or questions, feel free to ask anytime!"
            },
            {
                keywords: ['ok', 'okay', 'got it', 'understood', 'i see', 'makes sense', 'alright'],
                response: "Great! 👍 Let me know if you have any other questions. I'm here to help you make the most of TrackFlow!"
            },
            {
                keywords: ['who made', 'who created', 'developer', 'built by', 'made by', 'creator'],
                response: "🛠️ <b>About TrackFlow:</b><br><br>TrackFlow was created with ❤️ by a passionate team dedicated to making personal finance management simple and accessible for everyone.<br><br>Our mission: Help you achieve financial freedom through smart tracking and insights!"
            },
            {
                keywords: ['are you free', 'is it free', 'cost anything', 'charge', 'fees'],
                response: "💰 <b>Yes, TrackFlow is FREE!</b><br><br>Our free plan includes:<br>• Unlimited transactions<br>• Budgets & goals<br>• Basic reports<br>• Group expenses<br>• Community features<br><br>Premium features available for power users, but most people never need to pay! 🎉"
            },
            {
                keywords: ['slow', 'loading', 'taking time', 'performance', 'speed'],
                response: "⚡ <b>Performance Tips:</b><br><br>If TrackFlow is slow:<br><br>1. <b>Clear browser cache</b><br>2. <b>Check internet connection</b><br>3. <b>Try different browser</b><br>4. <b>Disable browser extensions</b><br>5. <b>Refresh the page</b><br><br>Still slow? Let us know your browser & device through the Contact Form!"
            },
            {
                keywords: ['safe', 'is it safe', 'trust', 'reliable', 'can i trust'],
                response: "🔒 <b>TrackFlow Security:</b><br><br>Absolutely safe! Here's why:<br><br>✅ Bank-level 256-bit encryption<br>✅ Two-factor authentication<br>✅ Regular security audits<br>✅ GDPR compliant<br>✅ We never sell your data<br>✅ Read-only bank access<br>✅ SOC 2 certified infrastructure<br><br>Your financial data is in safe hands! 🛡️"
            },
            {
                keywords: ['where are you from', 'company location', 'based in', 'headquarters'],
                response: "🌍 <b>About Us:</b><br><br>TrackFlow is a global platform accessible worldwide! We serve users across:<br>• 🇮🇳 India<br>• 🇺🇸 United States<br>• 🇬🇧 United Kingdom<br>• 🇪🇺 Europe<br>• And 50+ countries!<br><br>Our servers are distributed globally for fast, reliable access."
            },
            {
                keywords: ['competitor', 'vs', 'better than', 'compare', 'alternative', 'mint', 'ynab'],
                response: "🏆 <b>Why Choose TrackFlow:</b><br><br>• <b>Free forever</b> core features<br>• <b>Simple & intuitive</b> interface<br>• <b>Group expenses</b> built-in<br>• <b>UPI support</b> for Indian users<br>• <b>Multi-currency</b> support<br>• <b>Privacy focused</b> - your data stays yours<br>• <b>Regular updates</b> based on feedback<br><br>We focus on what matters most! 💪"
            }
        ];

        // AI Response Generator (Enhanced with Smart Matching)
        function getAIResponse(userMessage) {
            const message = userMessage.toLowerCase().trim();

            // Remove common filler words for better matching
            const fillerWords = ['please', 'can', 'you', 'i', 'want', 'to', 'know', 'about', 'the', 'a', 'an', 'is', 'are', 'do', 'does', 'how', 'what', 'where', 'when', 'why', 'would', 'could', 'should', 'tell', 'me', 'my', 'need', 'help', 'with'];
            const words = message.split(/\s+/);
            const meaningfulWords = words.filter(w => !fillerWords.includes(w) && w.length > 1);
            const cleanMessage = meaningfulWords.join(' ');

            // Check for exact phrase matches first (highest priority)
            let bestMatch = null;
            let highestScore = 0;

            for (const entry of knowledgeBase) {
                let score = 0;
                let exactMatchBonus = 0;

                for (const keyword of entry.keywords) {
                    const keywordLower = keyword.toLowerCase();

                    // Exact phrase match (highest priority)
                    if (message === keywordLower || message.includes(keywordLower)) {
                        score += keyword.length * 2;
                        if (message === keywordLower) {
                            exactMatchBonus = 50; // Exact match bonus
                        }
                    }

                    // Word-by-word matching for multi-word keywords
                    const keywordWords = keywordLower.split(' ');
                    if (keywordWords.length > 1) {
                        let wordMatches = 0;
                        for (const kw of keywordWords) {
                            if (message.includes(kw)) wordMatches++;
                        }
                        if (wordMatches === keywordWords.length) {
                            score += keyword.length * 1.5;
                        } else if (wordMatches > 0) {
                            score += wordMatches * 2;
                        }
                    }

                    // Check against cleaned message too
                    if (cleanMessage.includes(keywordLower)) {
                        score += keyword.length;
                    }
                }

                const totalScore = score + exactMatchBonus;
                if (totalScore > highestScore) {
                    highestScore = totalScore;
                    bestMatch = entry;
                }
            }

            // If we found a match with reasonable confidence
            if (bestMatch && highestScore >= 3) {
                return bestMatch.response;
            }

            // Intent-based fallbacks
            if (message.match(/how (do|can|to|should)/i)) {
                return "🤔 Great question! Could you be more specific about what you'd like to do? For example:<br><br>• 'How to add a transaction'<br>• 'How to create a budget'<br>• 'How to create a savings goal'<br>• 'How to set up 2FA'<br><br>I'll provide detailed step-by-step instructions!";
            }

            if (message.match(/what is|what are|what does/i)) {
                return "📚 I'd be happy to explain! Could you specify what you'd like to know about?<br><br>• 'What is TrackFlow'<br>• 'What are budgets'<br>• 'What is 2FA'<br>• 'What are group expenses'<br><br>I'll give you a detailed explanation!";
            }

            if (message.match(/why|problem|issue|error|not working|doesn't work|can't/i)) {
                return "🔧 I understand you're having an issue. To help you better:<br><br>1. <b>Describe the problem</b> in detail<br>2. <b>What were you trying to do?</b><br>3. <b>Any error message?</b><br><br>Or you can:<br>• Check our <a href='#faq' class='text-primary-600 hover:underline'>FAQ section</a><br>• Submit a <button onclick='closeLiveChat(); openContactForm();' class='text-primary-600 hover:underline'>bug report</button>";
            }

            if (message.match(/\?$/)) {
                return "🤔 That's a good question! I'm not sure I understood correctly. Could you rephrase or ask about a specific TrackFlow feature?<br><br><b>Popular topics:</b><br>• Transactions & Categories<br>• Budgets & Goals<br>• Reports & Analytics<br>• Group Expenses<br>• Settings & Security";
            }

            // Default intelligent fallback
            return "👋 I'm here to help with TrackFlow! Here's what I can assist with:<br><br>💰 <b>Transactions</b> - Add, edit, categorize<br>📊 <b>Budgets</b> - Create spending limits<br>🎯 <b>Goals</b> - Set savings targets<br>📈 <b>Reports</b> - View analytics<br>👥 <b>Groups</b> - Split expenses<br>⚙️ <b>Settings</b> - Customize your experience<br>🔐 <b>Security</b> - 2FA, passwords<br><br>What would you like to know more about? Just type your question!";
        }

        // Documentation Modal Functions
        function openDocumentationModal() {
            document.getElementById('documentationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDocumentationModal() {
            document.getElementById('documentationModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Contact Support from Documentation with loading effect
        function openContactSupportFromDocs(btn) {
            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Opening...</span>`;

            setTimeout(() => {
                closeDocumentationModal();
                document.getElementById('contactFormModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }, 800);
        }

        // Global Keyboard Shortcuts
        document.addEventListener('keydown', function (e) {
            // Don't trigger shortcuts when typing in input fields
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
                return;
            }

            // Ctrl + Z - New Transaction
            if (e.ctrlKey && e.key === 'z') {
                e.preventDefault();
                window.location.href = '/transactions?action=add';
            }

            // Ctrl + K - Quick Search (focus search if exists)
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('input[type="search"], input[name="search"], #searchInput, .search-input');
                if (searchInput) {
                    searchInput.focus();
                } else {
                    // If no search input, redirect to transactions with search focus
                    window.location.href = '/transactions?focus=search';
                }
            }

            // Ctrl + D - Dashboard
            if (e.ctrlKey && e.key === 'd') {
                e.preventDefault();
                window.location.href = '/dashboard';
            }

            // Ctrl + , - Settings
            if (e.ctrlKey && e.key === ',') {
                e.preventDefault();
                window.location.href = '/settings';
            }

            // Escape - Close documentation modal
            if (e.key === 'Escape') {
                const docModal = document.getElementById('documentationModal');
                if (docModal && !docModal.classList.contains('hidden')) {
                    closeDocumentationModal();
                }
            }
        });

        // Close documentation modal when clicking outside
        document.getElementById('documentationModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeDocumentationModal();
            }
        });

        function openLiveChat() {
            document.getElementById('liveChatModal').classList.remove('hidden');
            document.getElementById('chatInput').focus();
        }

        function closeLiveChat() {
            document.getElementById('liveChatModal').classList.add('hidden');
        }

        // Typing indicator
        function showTypingIndicator() {
            const chatMessages = document.getElementById('chatMessages');
            const typingDiv = document.createElement('div');
            typingDiv.id = 'typingIndicator';
            typingDiv.className = 'flex items-start gap-2';
            typingDiv.innerHTML = `
                                                                                                        <div class="w-8 h-8 rounded-full flex-shrink-0 overflow-hidden">
                                                                                                            <img src="${trackFlowLogoUrl}" alt="TrackFlow" class="w-full h-full object-cover">
                                                                                                        </div>
                                                                                                        <div class="flex-1">
                                                                                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm inline-block">
                                                                                                                <div class="flex gap-1">
                                                                                                                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></span>
                                                                                                                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></span>
                                                                                                                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></span>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    `;
            chatMessages.appendChild(typingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function hideTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        function sendChatMessage(event) {
            event.preventDefault();

            const input = document.getElementById('chatInput');
            const message = input.value.trim();

            if (!message) return;

            // Add user message
            const chatMessages = document.getElementById('chatMessages');
            const userMessageDiv = document.createElement('div');
            userMessageDiv.className = 'flex items-start gap-2 justify-end';
            userMessageDiv.innerHTML = `
                                                                                                        <div class="flex-1 max-w-[80%]">
                                                                                                            <div class="bg-primary-600 text-white rounded-lg p-3 shadow-sm">
                                                                                                                <p class="text-sm">${escapeHtml(message)}</p>
                                                                                                            </div>
                                                                                                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block text-right">Just now</span>
                                                                                                        </div>
                                                                                                        ${userProfilePicUrl
                    ? `<div class="w-8 h-8 rounded-full flex-shrink-0 overflow-hidden"><img src="${userProfilePicUrl}" alt="You" class="w-full h-full object-cover"></div>`
                    : `<div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-semibold">${userInitial}</div>`
                }
                                                                                                    `;

            chatMessages.appendChild(userMessageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            input.value = '';
            input.focus();

            // Show typing indicator
            setTimeout(() => {
                showTypingIndicator();
            }, 300);

            // Get AI response with realistic delay
            const responseDelay = Math.random() * 1000 + 800; // 800-1800ms for realistic feel

            setTimeout(() => {
                hideTypingIndicator();

                const aiResponse = getAIResponse(message);

                const botMessageDiv = document.createElement('div');
                botMessageDiv.className = 'flex items-start gap-2';
                botMessageDiv.innerHTML = `
                                                                                                            <div class="w-8 h-8 rounded-full flex-shrink-0 overflow-hidden">
                                                                                                                <img src="${trackFlowLogoUrl}" alt="TrackFlow" class="w-full h-full object-cover">
                                                                                                            </div>
                                                                                                            <div class="flex-1">
                                                                                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                                                                                                    <p class="text-sm text-gray-800 dark:text-gray-200">${aiResponse}</p>
                                                                                                                </div>
                                                                                                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Just now</span>
                                                                                                            </div>
                                                                                                        `;
                chatMessages.appendChild(botMessageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }, responseDelay);
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Contact Form Functions - Make globally accessible
        window.openContactForm = function () {
            document.getElementById('contactFormModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        window.closeContactForm = function () {
            document.getElementById('contactFormModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            // Reset form state
            resetContactForm();
        }

        // Store uploaded files
        let uploadedFiles = [];

        window.submitContactForm = function (event) {
            event.preventDefault();

            const name = document.getElementById('contactName').value;
            const email = document.getElementById('contactEmail').value;
            const subject = document.getElementById('contactSubject').value;
            const message = document.getElementById('contactMessage').value;

            // Validate form fields
            if (!name || !email || !subject || !message) {
                showNotification('Please fill in all required fields.', 'error');
                return;
            }

            // Create FormData object
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('subject', subject);
            formData.append('message', message);

            // Add uploaded files to FormData (from both file input and drag-drop)
            uploadedFiles.forEach((file, index) => {
                formData.append('attachments[]', file);
            });

            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Show loading state with animation
            const submitButton = document.getElementById('contactSubmitBtn');
            const submitBtnIcon = document.getElementById('submitBtnIcon');
            const submitBtnText = document.getElementById('submitBtnText');

            submitButton.disabled = true;
            submitBtnIcon.className = 'fas fa-spinner fa-spin mr-2';
            submitBtnText.textContent = 'Sending...';
            submitButton.classList.add('animate-pulse');

            // Send data to backend
            fetch('/contact/submit', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success animation
                        submitBtnIcon.className = 'fas fa-check mr-2';
                        submitBtnText.textContent = 'Sent!';
                        submitButton.classList.remove('animate-pulse');
                        submitButton.classList.add('bg-green-600');

                        showNotification(data.message, 'success');

                        setTimeout(() => {
                            closeContactForm();
                            // Reset form
                            event.target.reset();
                            resetContactForm();
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Failed to send message. Please try again.', 'error');
                        restoreSubmitButton();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again later.', 'error');
                    restoreSubmitButton();
                });
        }

        function restoreSubmitButton() {
            const submitButton = document.getElementById('contactSubmitBtn');
            const submitBtnIcon = document.getElementById('submitBtnIcon');
            const submitBtnText = document.getElementById('submitBtnText');

            submitButton.disabled = false;
            submitButton.classList.remove('animate-pulse', 'bg-green-600');
            submitBtnIcon.className = 'fas fa-paper-plane mr-2';
            submitBtnText.textContent = 'Send Message';
        }

        function resetContactForm() {
            uploadedFiles = [];
            document.getElementById('uploadedFilesList').innerHTML = '';
            document.getElementById('contactAttachment').value = '';
            restoreSubmitButton();
        }

        // Drag and Drop functionality
        function initDragAndDrop() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('contactAttachment');
            const dropZoneIcon = document.getElementById('dropZoneIcon');
            const dropZoneText = document.getElementById('dropZoneText');

            if (!dropZone) return;

            // Click to upload
            dropZone.addEventListener('click', (e) => {
                if (e.target.tagName !== 'BUTTON') {
                    fileInput.click();
                }
            });

            // Drag enter
            dropZone.addEventListener('dragenter', (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                dropZone.classList.remove('border-gray-300', 'dark:border-gray-600');
                dropZoneIcon.classList.add('text-primary-500', 'scale-110');
                dropZoneIcon.classList.remove('text-gray-400');
                dropZoneText.textContent = 'Release to upload files';
                dropZoneText.classList.add('text-primary-600');
            });

            // Drag over
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
            });

            // Drag leave
            dropZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                e.stopPropagation();
                resetDropZoneStyle();
            });

            // Drop
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                resetDropZoneStyle();

                const files = e.dataTransfer.files;
                handleFiles(files);
            });

            // File input change
            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
            });
        }

        function resetDropZoneStyle() {
            const dropZone = document.getElementById('dropZone');
            const dropZoneIcon = document.getElementById('dropZoneIcon');
            const dropZoneText = document.getElementById('dropZoneText');

            dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            dropZone.classList.add('border-gray-300', 'dark:border-gray-600');
            dropZoneIcon.classList.remove('text-primary-500', 'scale-110');
            dropZoneIcon.classList.add('text-gray-400');
            dropZoneText.textContent = 'Drop files here or click to upload';
            dropZoneText.classList.remove('text-primary-600');
        }

        function handleFiles(files) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

            Array.from(files).forEach(file => {
                // Check file size
                if (file.size > maxSize) {
                    showNotification(`File "${file.name}" exceeds 10MB limit.`, 'error');
                    return;
                }

                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    showNotification(`File "${file.name}" has an unsupported format.`, 'error');
                    return;
                }

                // Check if already added
                if (uploadedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    showNotification(`File "${file.name}" is already added.`, 'error');
                    return;
                }

                // Add to array
                uploadedFiles.push(file);
                displayUploadedFile(file);
            });
        }

        function displayUploadedFile(file) {
            const filesList = document.getElementById('uploadedFilesList');
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2';
            fileItem.dataset.fileName = file.name;
            fileItem.dataset.fileSize = file.size;

            const fileIcon = getFileIcon(file.type);
            const fileSize = formatFileSize(file.size);

            fileItem.innerHTML = `
                                                <div class="flex items-center gap-3">
                                                    <i class="${fileIcon} text-lg text-primary-600"></i>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px]">${file.name}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">${fileSize}</p>
                                                    </div>
                                                </div>
                                                <button type="button" onclick="removeUploadedFile('${file.name}', ${file.size})" class="text-red-500 hover:text-red-700 p-1">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            `;

            filesList.appendChild(fileItem);
        }

        window.removeUploadedFile = function (fileName, fileSize) {
            // Remove from array
            uploadedFiles = uploadedFiles.filter(f => !(f.name === fileName && f.size === fileSize));

            // Remove from display
            const fileItem = document.querySelector(`[data-file-name="${fileName}"][data-file-size="${fileSize}"]`);
            if (fileItem) {
                fileItem.remove();
            }
        }

        function getFileIcon(mimeType) {
            if (mimeType.startsWith('image/')) return 'fas fa-file-image';
            if (mimeType === 'application/pdf') return 'fas fa-file-pdf';
            if (mimeType.includes('word')) return 'fas fa-file-word';
            return 'fas fa-file';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Initialize drag and drop when DOM is ready
        document.addEventListener('DOMContentLoaded', initDragAndDrop);

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-[9999] px-6 py-4 rounded-lg shadow-lg text-white transform transition-all duration-300 ${type === 'success' ? 'bg-green-600' :
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
    </script>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection