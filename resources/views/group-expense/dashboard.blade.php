@extends('layouts.app')

@section('title', $group->name . ' - Group Dashboard')

@section('content')
    <!-- Colorful Glassmorphism Page Background - Purple/Blue Theme -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-purple-100 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-purple-300/40 to-violet-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-purple-600/10 dark:to-violet-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-blue-300/40 to-indigo-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-blue-600/10 dark:to-indigo-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-indigo-300/30 to-purple-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-indigo-600/10 dark:to-purple-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-violet-300/30 to-purple-400/30 rounded-full blur-3xl dark:from-violet-600/10 dark:to-purple-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-blue-300/30 to-cyan-400/30 rounded-full blur-3xl dark:from-blue-600/10 dark:to-cyan-700/10">
        </div>
    </div>

    <div class="container mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6 lg:py-8 relative">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6 sm:mb-8">
            <div class="flex-1">
                <div class="flex items-center gap-2 sm:gap-3 mb-2">
                    <a href="{{ route('group-expense.index') }}"
                        class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-white transition-colors bg-gray-100 dark:bg-transparent hover:bg-indigo-50 dark:hover:bg-transparent w-8 h-8 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-left text-sm sm:text-base"></i>
                    </a>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white truncate">
                        {{ $group->name }}
                    </h1>
                    <button onclick="shareGroup('{{ $group->group_code }}', '{{ $group->name }}')"
                        class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors w-8 h-8 rounded-lg flex items-center justify-center hover:bg-indigo-50 dark:hover:bg-indigo-900/30"
                        title="Share group">
                        <i class="fas fa-share-alt text-sm sm:text-base"></i>
                    </button>
                    @if($currentMember->isLeader())
                        <span
                            class="bg-gradient-to-r from-amber-500 to-yellow-500 text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap shadow-md">
                            <i class="fas fa-crown mr-1"></i><span class="hidden xs:inline">Leader</span>
                        </span>
                    @endif
                </div>
                @if($group->description)
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 line-clamp-2 mb-2">{{ $group->description }}
                    </p>
                @endif
                <!-- Group Code Display -->
                <div
                    class="bg-gradient-to-r from-purple-500/90 via-indigo-500/90 to-pink-500/90 dark:from-purple-900/50 dark:via-indigo-900/50 dark:to-blue-900/50 backdrop-blur-xl rounded-xl px-4 py-4 border border-purple-400/50 dark:border-purple-500/30 max-w-md shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-key text-white/80 dark:text-purple-400 text-sm"></i>
                            <span class="text-sm font-semibold text-white/90 dark:text-purple-300">Group Code</span>
                        </div>
                        <button onclick="copyGroupCode('{{ $group->group_code }}')"
                            class="text-white/80 dark:text-purple-300 hover:text-white transition-colors hover:scale-110 transform"
                            title="Copy code">
                            <i class="fas fa-copy text-base"></i>
                        </button>
                    </div>
                    <code
                        class="text-2xl sm:text-3xl font-bold text-white tracking-widest block text-center py-2 bg-white/20 dark:bg-gray-900/30 rounded-lg backdrop-blur-sm border border-white/20 dark:border-transparent"
                        style="font-family: 'Arial Rounded MT Bold', 'Arial', sans-serif; letter-spacing: 0.2em; font-weight: 700;">{{ substr($group->group_code, 0, 4) }}-{{ substr($group->group_code, 4, 4) }}</code>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <button onclick="openAddMemberModal()"
                    class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl transition-all text-sm sm:text-base w-full sm:w-auto shadow-md hover:shadow-lg hover:-translate-y-0.5">
                    <i class="fas fa-user-plus mr-2"></i><span>Add Member</span>
                </button>
                <button onclick="openAddTransactionModal()"
                    class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl transition-all text-sm sm:text-base w-full sm:w-auto shadow-md hover:shadow-lg hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i><span>Add Transaction</span>
                </button>
                @if($currentMember->isLeader())
                    <button onclick="openVerificationPanel()"
                        class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl transition-all text-sm sm:text-base w-full sm:w-auto shadow-md hover:shadow-lg hover:-translate-y-0.5">
                        <i class="fas fa-check-double mr-2"></i><span>Verify Payments</span>
                    </button>
                @endif
                @if(!$currentMember->isLeader())
                    <button onclick="confirmLeaveGroup()"
                        class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl transition-all text-sm sm:text-base w-full sm:w-auto shadow-md hover:shadow-lg hover:-translate-y-0.5">
                        <i class="fas fa-sign-out-alt mr-2"></i><span>Leave Group</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
            <!-- Total Members -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 sm:p-6 shadow-lg border border-white/50 dark:border-gray-700/50 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/20">
                        <i class="fas fa-users text-white text-base sm:text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm font-medium">Total Members</h3>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2">
                    {{ $summary['total_members'] }}
                </p>
            </div>

            <!-- Total Balance -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 sm:p-6 shadow-lg border border-white/50 dark:border-gray-700/50 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <i class="fas fa-wallet text-white text-base sm:text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm font-medium">Total Balance</h3>
                <p
                    class="text-xl sm:text-3xl font-bold {{ $summary['total_balance'] >= 0 ? 'text-emerald-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1 sm:mt-2 truncate">
                    {{ $currencySymbol }}{{ number_format($summary['total_balance'], 2) }}
                </p>
            </div>

            <!-- Total Expenses -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 sm:p-6 shadow-lg border border-white/50 dark:border-gray-700/50 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/20">
                        <i class="fas fa-arrow-down text-white text-base sm:text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm font-medium">Total Expenses</h3>
                <p class="text-xl sm:text-3xl font-bold text-red-600 dark:text-red-400 mt-1 sm:mt-2 truncate">
                    {{ $currencySymbol }}{{ number_format($summary['total_expenses'], 2) }}
                </p>
            </div>

            <!-- Total Income -->
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 sm:p-6 shadow-lg border border-white/50 dark:border-gray-700/50 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/20">
                        <i class="fas fa-arrow-up text-white text-base sm:text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm font-medium">Total Income</h3>
                <p class="text-xl sm:text-3xl font-bold text-emerald-600 dark:text-green-400 mt-1 sm:mt-2 truncate">
                    {{ $currencySymbol }}{{ number_format($summary['total_income'], 2) }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Members List -->
            <div class="lg:col-span-2">
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-white/50 dark:border-gray-700/50 p-4 sm:p-6 shadow-lg">
                    <div class="flex justify-between items-center mb-4 sm:mb-6">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Members</h2>
                        <span
                            class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">{{ $group->members->count() }}
                            members</span>
                    </div>

                    <div class="space-y-2 sm:space-y-3">
                        @php
                            $sortedMembers = $group->members->sortBy(function ($member) {
                                return $member->role === 'leader' ? '0' : '1' . strtolower($member->display_name);
                            });
                        @endphp
                        @foreach($sortedMembers as $member)
                            <div class="flex xs:flex-row items-center justify-between gap-3 p-3 sm:p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700 h-[80px] sm:h-[96px] hover:border-indigo-200 dark:hover:border-gray-600 hover:shadow-sm transition-all"
                                onmouseenter="preloadMemberProfile({{ $member->id }})">
                                <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                                    <div class="relative flex-shrink-0">
                                        @if($member->profile_picture)
                                            <button onclick="openMemberProfileModal({{ $member->id }})"
                                                class="focus:outline-none focus:ring-2 focus:ring-purple-500 rounded-full transition-transform hover:scale-105"
                                                title="View {{ $member->display_name }}'s profile">
                                                <img src="{{ $member->profile_picture }}" alt="{{ $member->display_name }}"
                                                    class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-purple-500 transition-colors cursor-pointer shadow-sm">
                                            </button>
                                        @else
                                            <button onclick="openMemberProfileModal({{ $member->id }})"
                                                class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm sm:text-base hover:from-indigo-400 hover:to-purple-500 transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer shadow-md"
                                                title="View {{ $member->display_name }}'s profile">
                                                {{ strtoupper(substr($member->display_name, 0, 1)) }}
                                            </button>
                                        @endif
                                        @if($member->user && $member->user->two_factor_enabled)
                                            <span
                                                class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center border-2 border-gray-900"
                                                title="2FA Verified">
                                                <i class="fas fa-check text-white text-[8px]"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3
                                                class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base truncate">
                                                {{ $member->display_name }}
                                            </h3>
                                            @if($member->user_id)
                                                <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"
                                                    title="TrackFlow Member"></span>
                                            @endif
                                            @if($member->role === 'leader')
                                                <span
                                                    class="bg-gradient-to-r from-amber-500 to-yellow-500 text-white px-2 py-0.5 rounded text-xs whitespace-nowrap shadow-sm">
                                                    <i class="fas fa-crown mr-1"></i><span class="hidden xs:inline">Leader</span>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            @php
                                                $emailToShow = $member->email ?? ($member->user ? $member->user->email : null);
                                                $phoneToShow = $member->phone ?? ($member->user ? $member->user->phone : null);
                                            @endphp
                                            @if($emailToShow)
                                                <div class="truncate">
                                                    <i class="fas fa-envelope mr-1 text-gray-400 dark:text-gray-500"></i>
                                                    <span>{{ $emailToShow }}</span>
                                                </div>
                                            @endif
                                            @if($phoneToShow)
                                                <div class="truncate mt-0.5">
                                                    <i class="fas fa-phone mr-1 text-gray-400 dark:text-gray-500"></i>
                                                    <span>{{ $phoneToShow }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if($currentMember->isLeader() && $member->role !== 'leader')
                                        <button onclick="transferLeadership({{ $member->id }}, '{{ $member->display_name }}')"
                                            class="text-amber-500 hover:text-amber-600 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors p-2 text-sm hover:bg-amber-50 dark:hover:bg-transparent rounded-lg"
                                            title="Transfer Leadership">
                                            <i class="fas fa-crown"></i>
                                        </button>
                                        <button onclick="confirmRemoveMember({{ $member->id }}, '{{ $member->display_name }}')"
                                            class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 transition-colors p-2 text-sm hover:bg-red-50 dark:hover:bg-transparent rounded-lg"
                                            title="Remove Member">
                                            <i class="fas fa-user-times"></i>
                                        </button>
                                    @endif
                                    @if($member->id === $currentMember->id && !$currentMember->isLeader())
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">(You)</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-white/50 dark:border-gray-700/50 p-4 sm:p-6 mt-4 sm:mt-6 shadow-lg">
                    <div class="flex flex-col xs:flex-row justify-between items-start xs:items-center gap-2 mb-4 sm:mb-6">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Recent Transactions</h2>
                        @if($group->transactions->count() > 6)
                            <div class="flex items-center gap-2">
                                <span id="txnPaginationInfo" class="text-xs sm:text-sm text-gray-500 dark:text-gray-400"></span>
                                <div class="flex gap-1">
                                    <button onclick="prevTransactionPage()" id="txnPrevBtn"
                                        class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                        <i class="fas fa-chevron-left text-xs sm:text-sm"></i>
                                    </button>
                                    <button onclick="nextTransactionPage()" id="txnNextBtn"
                                        class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                        <i class="fas fa-chevron-right text-xs sm:text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($group->transactions->count() > 0)
                        <!-- Transaction container - populated by JavaScript -->
                        <div id="transactionsContainer" class="space-y-3 sm:space-y-4"></div>

                        <!-- Store all transactions as JSON for pagination -->
                        @php
                            $transactionsData = $group->transactions->sortByDesc('date')->values()->map(function ($transaction) use ($currencyService, $userCurrency, $currencySymbol) {
                                return [
                                    'id' => $transaction->id,
                                    'description' => $transaction->description,
                                    'type' => $transaction->type,
                                    'total_amount' => $transaction->total_amount,
                                    'converted_amount' => number_format($currencyService->convert((float) $transaction->total_amount, 'INR', $userCurrency), 2),
                                    'paid_by_name' => $transaction->paidBy->display_name,
                                    'paid_by_id' => $transaction->paidBy->id,
                                    'date' => $transaction->date->format('Y-m-d'),
                                    'date_formatted' => $transaction->date->format('M d, Y'),
                                    'category_id' => $transaction->category_id,
                                    'category_name' => $transaction->category ? $transaction->category->name : null,
                                    'status' => $transaction->status,
                                    'note' => $transaction->note,
                                    'members' => $transaction->members->map(function ($m) {
                                        return [
                                            'member_id' => $m->member_id,
                                            'name' => $m->member->display_name,
                                            'contributed_amount' => $m->contributed_amount,
                                            'final_share_amount' => $m->final_share_amount,
                                            'participated' => $m->participated
                                        ];
                                    })
                                ];
                            });
                        @endphp
                        <script>
                            const allTransactions = @json($transactionsData);
                            const txnCurrencySymbol = '{{ $currencySymbol }}';
                        </script>
                    @else
                        <div class="text-center py-6 sm:py-8">
                            <div
                                class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-receipt text-2xl sm:text-3xl text-gray-400 dark:text-gray-600"></i>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">No transactions yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Balance Sheet -->
            <div class="lg:col-span-1">
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-white/50 dark:border-gray-700/50 p-4 sm:p-6 shadow-lg">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">Balance Sheet</h2>

                    <!-- Member Balances -->
                    <div class="space-y-3 mb-6">
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">Member
                            Contributions</h3>
                        @foreach($balanceSheet['balances'] as $balance)
                            <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="flex justify-between items-center mb-2 gap-2">
                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                        <span
                                            class="text-gray-900 dark:text-white font-medium text-sm sm:text-base truncate">{{ $balance['member']->display_name }}</span>
                                        @if($balance['member']->is_settled)
                                            <span
                                                class="bg-gradient-to-r from-emerald-500 to-green-600 text-white px-2 py-0.5 rounded text-xs whitespace-nowrap shadow-sm">
                                                <i class="fas fa-check-circle mr-1"></i>Settled
                                            </span>
                                        @endif
                                    </div>
                                    <span
                                        class="text-xs sm:text-sm font-bold {{ $balance['net_balance'] >= 0 ? 'text-emerald-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} whitespace-nowrap">
                                        {{ $balance['net_balance'] >= 0 ? '+' : '' }}{{ $currencySymbol }}{{ number_format(abs($balance['net_balance']), 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 gap-2">
                                    <span class="truncate">Contributed:
                                        {{ $currencySymbol }}{{ number_format($balance['contributed'], 2) }}</span>
                                    <span class="whitespace-nowrap">Share:
                                        {{ $currencySymbol }}{{ number_format($balance['should_pay'], 2) }}</span>
                                </div>
                                @if($balance['member']->user_id == session('user_id') && !$balance['member']->is_settled)
                                    <button onclick="settleUp({{ $group->id }})"
                                        class="mt-2 w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all shadow-md hover:shadow-lg">
                                        <i class="fas fa-check-double mr-1"></i>Settle Up
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Settlements -->
                    @php
                        $currentMemberSettled = $currentMember->is_settled;
                        $currentMemberSettlements = collect($balanceSheet['settlements'] ?? [])->filter(function ($s) use ($currentMember) {
                            return $s['from'] === $currentMember->display_name || $s['to'] === $currentMember->display_name;
                        });
                    @endphp

                    @if($currentMemberSettled)
                        <div class="pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-700 text-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                                <i class="fas fa-check-circle text-xl text-white"></i>
                            </div>
                            <p class="text-sm font-semibold text-emerald-600 dark:text-green-400 mb-1">All settled up!</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">You have no pending settlements</p>
                        </div>
                    @elseif(count($currentMemberSettlements) > 0)
                        <div class="pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-700">
                            <h3 class="text-xs sm:text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">Your Settlements
                            </h3>
                            <div class="space-y-2">
                                @foreach($currentMemberSettlements as $settlement)
                                    <div
                                        class="p-3 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl border border-amber-200 dark:border-orange-700/30">
                                        <div class="flex flex-col xs:flex-row items-start xs:items-center justify-between gap-2">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs sm:text-sm text-gray-700 dark:text-white">
                                                    <span class="font-semibold truncate">{{ $settlement['from'] }}</span>
                                                    <i
                                                        class="fas fa-arrow-right mx-2 text-amber-500 dark:text-gray-400 text-xs"></i>
                                                    <span class="font-semibold truncate">{{ $settlement['to'] }}</span>
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="text-amber-600 dark:text-orange-400 font-bold text-sm sm:text-base whitespace-nowrap">
                                                    {{ $currencySymbol }}{{ number_format($settlement['amount'], 2) }}
                                                </span>
                                                @if($settlement['from'] === $currentMember->display_name)
                                                    <button
                                                        onclick="paySettlement({{ $settlement['to_member_id'] }}, {{ $settlement['amount'] }})"
                                                        class="w-8 h-8 flex items-center justify-center bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-lg transition-all shadow-md hover:shadow-lg transform hover:scale-105"
                                                        title="Pay {{ $settlement['to'] }}">
                                                        <i class="fas fa-paper-plane text-xs"></i>
                                                    </button>
                                                @elseif($settlement['to'] === $currentMember->display_name)
                                                    <button
                                                        onclick="requestPayment({{ $settlement['from_member_id'] }}, {{ $settlement['amount'] }})"
                                                        class="w-8 h-8 flex items-center justify-center bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-lg transition-all shadow-md hover:shadow-lg transform hover:scale-105"
                                                        title="Request from {{ $settlement['from'] }}">
                                                        <i class="fas fa-hand-holding-usd text-xs"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="pt-4 sm:pt-6 border-t border-gray-100 dark:border-gray-700 text-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                                <i class="fas fa-check-circle text-xl text-white"></i>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">No settlements needed</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Join with Code Modal -->
    <div id="joinGroupModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-2xl max-w-md w-full mx-4 p-6 border border-white/50 dark:border-gray-700/50">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Join Group with Code</h2>
                <button onclick="closeJoinGroupModal()"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form onsubmit="joinGroupWithCode(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enter Group
                            Code</label>
                        <input type="text" id="groupCodeInput"
                            class="w-full px-4 py-4 text-center text-2xl font-bold tracking-widest bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-300/50 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:border-purple-500 focus:ring-2 focus:ring-purple-500 uppercase placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="XXXX-XXXX" maxlength="9" oninput="formatGroupCode(this)">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Enter or paste the group code (e.g., ABCD-1234)
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeJoinGroupModal()"
                        class="flex-1 px-4 py-2 border border-gray-300/50 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-white/50 dark:hover:bg-gray-700 backdrop-blur-sm transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="joinGroupBtn"
                        class="flex-1 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-4 py-2 rounded-lg transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="joinGroupBtnText"><i class="fas fa-sign-in-alt mr-2"></i>Join Group</span>
                        <span id="joinGroupBtnLoading" class="hidden"><i
                                class="fas fa-spinner fa-spin mr-2"></i>Joining...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div id="addMemberModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-2xl max-w-md w-full p-4 sm:p-6 border border-white/50 dark:border-gray-700/50">
            <div class="flex justify-between items-center mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">Add Member</h2>
                <button onclick="closeAddMemberModal()"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>

            <form onsubmit="addMember(event)">
                <div class="space-y-3 sm:space-y-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="memberName" required
                            class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300/50 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="Member name">
                    </div>

                    <div>
                        <label
                            class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <input type="email" id="memberEmail"
                            class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300/50 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="member@example.com">
                    </div>

                    <div>
                        <label
                            class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                        <input type="tel" id="memberPhone"
                            class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300/50 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="+91 1234567890">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-6">
                    <button type="button" onclick="closeAddMemberModal()"
                        class="flex-1 px-4 py-2 text-sm sm:text-base border border-gray-300/50 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-white/50 dark:hover:bg-gray-700 backdrop-blur-sm transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 py-2 text-sm sm:text-base rounded-lg transition-all shadow-md">
                        Add Member
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Transaction Modal -->
    <div id="addTransactionModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 overflow-y-auto p-4">
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-2xl max-w-2xl w-full my-4 sm:my-8 p-4 sm:p-6 border border-white/50 dark:border-gray-700/50 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">Add Transaction</h2>
                <button onclick="closeAddTransactionModal()"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>

            <form onsubmit="addTransaction(event)">
                <div class="space-y-3 sm:space-y-4 max-h-[60vh] sm:max-h-96 overflow-y-auto pr-2">
                    <!-- Type Selection -->
                    <div>
                        <label
                            class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                        <div class="grid grid-cols-2 gap-2 sm:gap-3">
                            <label
                                class="flex items-center p-2 sm:p-3 border-2 border-gray-300/50 dark:border-gray-600 rounded-lg cursor-pointer hover:border-red-400 dark:hover:border-red-500 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/50 dark:has-[:checked]:bg-red-900/20 backdrop-blur-sm transition-all">
                                <input type="radio" name="type" value="expense" class="mr-2" checked required>
                                <i class="fas fa-arrow-up text-red-500 mr-2"></i>
                                <span class="text-gray-800 dark:text-white text-sm sm:text-base">Expense</span>
                            </label>
                            <label
                                class="flex items-center p-2 sm:p-3 border-2 border-gray-300/50 dark:border-gray-600 rounded-lg cursor-pointer hover:border-green-400 dark:hover:border-green-500 has-[:checked]:border-green-500 has-[:checked]:bg-green-50/50 dark:has-[:checked]:bg-green-900/20 backdrop-blur-sm transition-all">
                                <input type="radio" name="type" value="income" class="mr-2">
                                <i class="fas fa-arrow-down text-green-500 mr-2"></i>
                                <span class="text-gray-800 dark:text-white text-sm sm:text-base">Income</span>
                            </label>
                        </div>
                    </div>

                    <!-- Paid By -->
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Paid
                            By</label>
                        <select id="paidBy" required
                            class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300/50 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-white">
                            @foreach($group->members as $member)
                                <option value="{{ $member->id }}">{{ $member->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Amount</label>
                        <input type="number" id="totalAmount" step="0.01" required
                            class="w-full px-4 py-2 border border-gray-300/50 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="0.00">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <input type="text" id="description" required
                            class="w-full px-4 py-2 border border-gray-300/50 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="What was this for?">
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                        <input type="date" id="transactionDate" required value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300/50 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-white">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category
                            (Optional)</label>
                        <select id="category"
                            class="w-full px-4 py-2 border border-gray-300/50 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-white">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label
                                class="flex items-center p-3 border-2 border-gray-300/50 dark:border-gray-600 rounded-lg cursor-pointer hover:border-green-400 dark:hover:border-green-500 has-[:checked]:border-green-500 has-[:checked]:bg-green-50/50 dark:has-[:checked]:bg-green-900/20 backdrop-blur-sm transition-all">
                                <input type="radio" name="status" value="paid" class="mr-2" checked>
                                <span class="text-gray-800 dark:text-white">Paid</span>
                            </label>
                            <label
                                class="flex items-center p-3 border-2 border-gray-300/50 dark:border-gray-600 rounded-lg cursor-pointer hover:border-yellow-400 dark:hover:border-yellow-500 has-[:checked]:border-yellow-500 has-[:checked]:bg-yellow-50/50 dark:has-[:checked]:bg-yellow-900/20 backdrop-blur-sm transition-all">
                                <input type="radio" name="status" value="unpaid" class="mr-2">
                                <span class="text-gray-800 dark:text-white">Unpaid</span>
                            </label>
                        </div>
                    </div>

                    <!-- Split Among Members -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Split Among (Select participating members)
                        </label>
                        <div
                            class="bg-gray-100/50 dark:bg-gray-900/50 backdrop-blur-sm rounded-lg p-3 border border-gray-200/50 dark:border-gray-700 space-y-2 max-h-48 overflow-y-auto">
                            @foreach($group->members as $member)
                                <label
                                    class="flex items-center p-2 hover:bg-white/50 dark:hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="split_members" value="{{ $member->id }}"
                                        class="mr-3 w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500" checked>
                                    <div class="flex items-center gap-3 flex-1">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                            {{ strtoupper(substr($member->display_name, 0, 1)) }}
                                        </div>
                                        <span class="text-gray-800 dark:text-white">{{ $member->display_name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Amount will be split equally among selected members
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeAddTransactionModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-300/50 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-100/50 dark:hover:bg-gray-700 backdrop-blur-sm transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit" id="addTransactionBtn"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 py-2.5 rounded-xl transition-all shadow-md hover:shadow-lg font-medium flex items-center justify-center gap-2">
                        <i class="fas fa-plus" id="addTransactionIcon"></i>
                        <span id="addTransactionText">Add Transaction</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div id="transactionDetailsModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto border border-white/50 dark:border-gray-700/50 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Transaction Details</h2>
                <button onclick="closeTransactionDetailsModal()"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="transactionDetailsContent" class="space-y-4">
                <!-- Details will be populated by JavaScript -->
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeTransactionDetailsModal()"
                    class="flex-1 px-4 py-2 bg-gray-200/80 dark:bg-gray-700 hover:bg-gray-300/80 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-lg transition-colors border border-gray-300/50 dark:border-transparent">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Transaction Modal -->
    <div id="editTransactionModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto border border-white/50 dark:border-gray-700/50 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Transaction</h2>
                <button onclick="closeEditTransactionModal()"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form onsubmit="updateTransaction(event)" class="space-y-4">
                <input type="hidden" id="editTransactionId">

                <!-- Transaction Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex items-center p-3 border-2 border-gray-300/50 dark:border-gray-600 rounded-lg cursor-pointer hover:border-red-400 dark:hover:border-red-500 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/50 dark:has-[:checked]:bg-red-900/20 backdrop-blur-sm transition-all">
                            <input type="radio" name="editType" value="expense" class="mr-2" checked>
                            <i class="fas fa-arrow-down text-red-500 mr-2"></i>
                            <span class="text-gray-800 dark:text-white">Expense</span>
                        </label>
                        <label
                            class="flex items-center p-3 border-2 border-gray-300/50 dark:border-gray-600 rounded-lg cursor-pointer hover:border-green-400 dark:hover:border-green-500 has-[:checked]:border-green-500 has-[:checked]:bg-green-50/50 dark:has-[:checked]:bg-green-900/20 backdrop-blur-sm transition-all">
                            <input type="radio" name="editType" value="income" class="mr-2">
                            <i class="fas fa-arrow-up text-green-500 mr-2"></i>
                            <span class="text-gray-800 dark:text-white">Income</span>
                        </label>
                    </div>
                </div>

                <!-- Paid By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Paid By</label>
                    <select id="editPaidBy" required
                        class="w-full px-4 py-2 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-300/50 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                        @foreach($group->members as $member)
                            <option value="{{ $member->id }}">{{ $member->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount
                        ({{ $currencySymbol }})</label>
                    <input type="number" id="editTotalAmount" step="0.01" min="0" required
                        class="w-full px-4 py-2 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-300/50 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500 placeholder-gray-500 dark:placeholder-gray-400"
                        placeholder="0.00">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <input type="text" id="editDescription" required
                        class="w-full px-4 py-2 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-300/50 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500 placeholder-gray-500 dark:placeholder-gray-400"
                        placeholder="e.g., Groceries, Rent">
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                    <input type="date" id="editTransactionDate" required
                        class="w-full px-4 py-2 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-300/50 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category
                        (Optional)</label>
                    <select id="editCategory"
                        class="w-full px-4 py-2 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-300/50 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex items-center p-3 border-2 border-gray-300/50 dark:border-gray-600 rounded-lg cursor-pointer hover:border-green-400 dark:hover:border-green-500 has-[:checked]:border-green-500 has-[:checked]:bg-green-50/50 dark:has-[:checked]:bg-green-900/20 backdrop-blur-sm transition-all">
                            <input type="radio" name="editStatus" value="paid" class="mr-2" checked>
                            <span class="text-gray-800 dark:text-white">Paid</span>
                        </label>
                        <label
                            class="flex items-center p-3 border-2 border-gray-300/50 dark:border-gray-600 rounded-lg cursor-pointer hover:border-yellow-400 dark:hover:border-yellow-500 has-[:checked]:border-yellow-500 has-[:checked]:bg-yellow-50/50 dark:has-[:checked]:bg-yellow-900/20 backdrop-blur-sm transition-all">
                            <input type="radio" name="editStatus" value="unpaid" class="mr-2">
                            <span class="text-gray-800 dark:text-white">Unpaid</span>
                        </label>
                    </div>
                </div>

                <!-- Split Among Members -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Split Among (Select participating members)
                    </label>
                    <div
                        class="bg-gray-100/50 dark:bg-gray-900/50 backdrop-blur-sm rounded-lg p-3 border border-gray-200/50 dark:border-gray-700 space-y-2 max-h-48 overflow-y-auto">
                        @foreach($group->members as $member)
                            <label
                                class="flex items-center p-2 hover:bg-white/50 dark:hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
                                <input type="checkbox" name="edit_split_members" value="{{ $member->id }}"
                                    class="mr-3 w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500" checked>
                                <div class="flex items-center gap-3 flex-1">
                                    <div
                                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                        {{ strtoupper(substr($member->display_name, 0, 1)) }}
                                    </div>
                                    <span class="text-gray-800 dark:text-white">{{ $member->display_name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Amount will be split equally among selected members
                    </p>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeEditTransactionModal()"
                        class="flex-1 px-4 py-2 border border-gray-300/50 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-white/50 dark:hover:bg-gray-700 backdrop-blur-sm transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 py-2 rounded-lg transition-all shadow-md">
                        Update Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Confirm Modal -->
    <div id="customConfirmModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl p-6 max-w-md w-full mx-4 border border-white/50 dark:border-gray-700/50 shadow-2xl">
            <div class="flex items-start gap-4 mb-6">
                <div id="confirmIcon" class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 id="confirmTitle" class="text-xl font-bold text-gray-900 dark:text-white mb-2"></h3>
                    <p id="confirmMessage" class="text-gray-600 dark:text-gray-300 text-sm"></p>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeCustomConfirm(false)"
                    class="flex-1 px-4 py-2.5 border border-gray-300/50 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-white/50 dark:hover:bg-gray-700 backdrop-blur-sm transition-colors font-medium">
                    Cancel
                </button>
                <button type="button" onclick="closeCustomConfirm(true)" id="confirmButton"
                    class="flex-1 px-4 py-2.5 rounded-lg text-white transition-colors font-medium">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div id="customAlertModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl p-6 max-w-md w-full mx-4 border border-white/50 dark:border-gray-700/50 shadow-2xl">
            <div class="flex items-start gap-4 mb-6">
                <div id="alertIcon" class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 id="alertTitle" class="text-xl font-bold text-gray-900 dark:text-white mb-2"></h3>
                    <p id="alertMessage" class="text-gray-600 dark:text-gray-300 text-sm"></p>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeCustomAlert()"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 rounded-lg text-white transition-all shadow-md font-medium">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Member Profile Modal -->
    <div id="memberProfileModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-2xl w-full max-w-md mx-auto border border-white/50 dark:border-gray-700/50 shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            <!-- Loading State -->
            <div id="memberProfileLoading" class="p-8 text-center">
                <div
                    class="animate-spin w-12 h-12 border-4 border-purple-500 border-t-transparent rounded-full mx-auto mb-4">
                </div>
                <p class="text-gray-600 dark:text-gray-400">Loading profile...</p>
            </div>

            <!-- Profile Content -->
            <div id="memberProfileContent" class="hidden">
                <!-- Header with Profile Picture -->
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 pt-6 pb-16 relative">
                    <button onclick="closeMemberProfileModal()"
                        class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Profile Picture (overlapping) -->
                <div class="flex justify-center -mt-12 relative z-10">
                    <button id="memberProfilePicture" onclick="openMemberPictureZoom()"
                        class="w-24 h-24 rounded-full border-4 border-white/50 dark:border-gray-800 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg overflow-hidden cursor-pointer hover:border-purple-400 dark:hover:border-purple-500 transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500"
                        title="Click to enlarge">
                        <!-- Will be populated by JS -->
                    </button>
                </div>

                <!-- Name and Badges -->
                <div class="text-center px-6 pt-4">
                    <div class="flex items-center justify-center gap-2">
                        <h3 id="memberProfileName" class="text-xl font-bold text-gray-900 dark:text-white"></h3>
                        <span id="memberProfileTrackflow" class="hidden w-2.5 h-2.5 bg-blue-500 rounded-full"
                            title="TrackFlow Member"></span>
                        <span id="memberProfileVerified" class="hidden text-green-400" title="2FA Verified Account">
                            <i class="fas fa-check-circle"></i>
                        </span>
                    </div>
                    <div id="memberProfileRole" class="mt-2 hidden">
                        <span
                            class="bg-gradient-to-r from-amber-500 to-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1 shadow-md">
                            <i class="fas fa-crown"></i> Leader
                        </span>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="px-6 py-4 space-y-3">
                    <!-- Email -->
                    <div id="memberProfileEmailRow"
                        class="hidden flex items-center gap-3 p-3 bg-blue-50/50 dark:bg-gray-900/50 backdrop-blur-sm rounded-lg border border-blue-100/50 dark:border-transparent">
                        <div
                            class="w-10 h-10 bg-blue-500/20 dark:bg-blue-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-envelope text-blue-500 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-500">Email</p>
                            <p id="memberProfileEmail" class="text-sm text-gray-900 dark:text-white truncate"></p>
                        </div>
                        <button onclick="copyMemberInfo('email')"
                            class="text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors p-2"
                            title="Copy Email">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>

                    <!-- Phone -->
                    <div id="memberProfilePhoneRow"
                        class="hidden flex items-center gap-3 p-3 bg-emerald-50/50 dark:bg-gray-900/50 backdrop-blur-sm rounded-lg border border-emerald-100/50 dark:border-transparent">
                        <div
                            class="w-10 h-10 bg-emerald-500/20 dark:bg-green-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-phone text-emerald-500 dark:text-green-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-500">Phone</p>
                            <p id="memberProfilePhone" class="text-sm text-gray-900 dark:text-white"></p>
                        </div>
                        <button onclick="copyMemberInfo('phone')"
                            class="text-gray-400 hover:text-emerald-500 dark:hover:text-green-400 transition-colors p-2"
                            title="Copy Phone">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>

                    <!-- Bio -->
                    <div id="memberProfileBioRow"
                        class="hidden p-3 bg-purple-50/50 dark:bg-gray-900/50 backdrop-blur-sm rounded-lg border border-purple-100/50 dark:border-transparent">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-user-edit text-purple-500 dark:text-purple-400"></i>
                            <p class="text-xs text-gray-500 dark:text-gray-500">Bio</p>
                        </div>
                        <p id="memberProfileBio" class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"></p>
                    </div>

                    <!-- Joined Date -->
                    <div
                        class="flex items-center gap-3 p-3 bg-gray-100/50 dark:bg-gray-900/50 backdrop-blur-sm rounded-lg border border-gray-200/50 dark:border-transparent">
                        <div
                            class="w-10 h-10 bg-indigo-500/20 dark:bg-gray-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-indigo-500 dark:text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-500">Joined Group</p>
                            <p id="memberProfileJoined" class="text-sm text-gray-900 dark:text-white"></p>
                        </div>
                    </div>
                </div>

                <!-- UPI Section -->
                <div id="memberProfileUpiSection" class="hidden px-6 pb-6">
                    <div class="border-t border-gray-200/50 dark:border-gray-700 pt-4">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3 flex items-center gap-2">
                            <i class="fas fa-wallet text-purple-500 dark:text-purple-400"></i> Payment UPI
                        </h4>

                        <div
                            class="bg-gradient-to-br from-purple-100/80 via-indigo-100/80 to-blue-100/80 dark:from-purple-900/30 dark:via-indigo-900/30 dark:to-blue-900/30 backdrop-blur-sm rounded-xl p-4 border border-purple-200/50 dark:border-purple-500/30">
                            <!-- QR Code -->
                            <div id="memberProfileQrCode" class="hidden text-center mb-4">
                                <div class="inline-block p-2 bg-white rounded-lg">
                                    <img id="memberProfileQrImage" src="" alt="UPI QR Code"
                                        class="w-32 h-32 object-contain">
                                </div>
                            </div>

                            <!-- UPI Details -->
                            <div class="text-center">
                                <p id="memberProfileUpiName" class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                                </p>
                                <button id="memberProfilePayNowBtn" onclick="handlePayNow()"
                                    class="inline-flex items-center justify-center gap-2 w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-xl transition-all shadow-md hover:shadow-lg transform hover:scale-[1.02]">
                                    <i class="fas fa-paper-plane"></i>
                                    Pay Now
                                </button>
                                <input type="hidden" id="memberProfileUpiId" value="">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Close Button -->
                <div class="px-6 pb-6">
                    <button onclick="closeMemberProfileModal()"
                        class="w-full py-3 bg-gray-200/80 dark:bg-gray-700 hover:bg-gray-300/80 dark:hover:bg-gray-600 text-gray-700 dark:text-white font-medium rounded-xl transition-colors border border-gray-300/50 dark:border-transparent">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Profile Picture Zoom Modal -->
    <div id="memberPictureZoomModal"
        class="hidden fixed inset-0 bg-black bg-opacity-90 items-center justify-center z-[60] p-4"
        onclick="closeMemberPictureZoom(event)">
        <div class="relative max-w-lg w-full">
            <button onclick="closeMemberPictureZoom(event, true)"
                class="absolute -top-12 right-0 text-white/80 hover:text-white transition-colors p-2" title="Close">
                <i class="fas fa-times text-2xl"></i>
            </button>
            <div id="memberPictureZoomContent" class="bg-gray-800 rounded-2xl p-2 shadow-2xl">
                <!-- Will be populated by JS -->
            </div>
        </div>
    </div>

    <!-- Payment Proof Modal -->
    <div id="paymentProofModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div
            class="bg-white/90 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-2xl max-w-md w-full mx-4 border border-white/50 dark:border-gray-700/50 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Pay Settlement
                    </h2>
                    <button onclick="closePaymentProofModal()" class="text-white/80 hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Payment Info -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Pay to</span>
                        <span id="paymentReceiverName" class="font-semibold text-gray-900 dark:text-white"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Amount</span>
                        <span id="paymentAmount" class="text-2xl font-bold text-green-600 dark:text-green-400"></span>
                    </div>
                </div>

                <!-- Step 1: Pay via UPI -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span
                            class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        Pay via UPI
                    </h3>
                    <a id="upiPayLink" href="#" onclick="handleUpiPayClick(event)"
                        class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-external-link-alt"></i>
                        Open UPI App
                    </a>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                        UPI ID: <span id="receiverUpiId" class="font-mono"></span>
                        <button onclick="copyReceiverUpi()" class="ml-2 text-indigo-500 hover:text-indigo-600">
                            <i class="fas fa-copy"></i>
                        </button>
                    </p>
                </div>

                <!-- Step 2: Submit Proof -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span
                            class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        Submit Payment Proof
                    </h3>
                    <form id="paymentProofForm" onsubmit="submitPaymentProof(event)">
                        <input type="hidden" id="settlementMemberId" value="">
                        <input type="hidden" id="settlementAmount" value="">
                        <input type="hidden" id="settlementUpiId" value="">

                        <!-- Transaction ID -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Transaction ID / UTR Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="transactionId" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                                placeholder="e.g., 425678901234">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Find this in your UPI app's transaction details
                            </p>
                        </div>

                        <!-- Screenshot Upload -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Payment Screenshot <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" id="proofScreenshot" accept="image/*" class="hidden"
                                    onchange="handleScreenshotSelect(this)">
                                <div id="screenshotPreview" class="hidden mb-2 relative">
                                    <img id="screenshotImage"
                                        class="w-full h-40 object-contain rounded-lg border border-gray-200 dark:border-gray-600">
                                    <button type="button" onclick="removeScreenshot()"
                                        class="absolute top-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center hover:bg-red-600">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                                <button type="button" onclick="document.getElementById('proofScreenshot').click()"
                                    id="uploadBtn"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-gray-600 dark:text-gray-400 hover:border-green-500 hover:text-green-500 transition-all">
                                    <i class="fas fa-camera"></i>
                                    Upload Screenshot
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submitProofBtn"
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Confirm Payment
                        </button>
                    </form>
                </div>

                <!-- Info Note -->
                <div
                    class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-700/30">
                    <p class="text-xs text-green-700 dark:text-green-300 flex items-start gap-2">
                        <i class="fas fa-check-circle mt-0.5"></i>
                        <span>Payment will be verified instantly once you submit the transaction ID and screenshot.</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Verification Modal -->
    @if($currentMember->isLeader())
        <div id="verificationPanelModal"
            class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
            <div
                class="bg-white/90 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-2xl max-w-2xl w-full mx-4 border border-white/50 dark:border-gray-700/50 overflow-hidden max-h-[90vh] flex flex-col">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4 flex-shrink-0">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-check-double"></i>
                            Payment Verification
                        </h2>
                        <button onclick="closeVerificationPanel()" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 overflow-y-auto flex-1">
                    <div id="pendingPaymentsContainer">
                        <!-- Will be populated by JS -->
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Loading pending payments...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        const groupId = {{ $group->id }};
        const members = @json($group->members);
        const currencySymbol = '{{ $currencySymbol }}';
        const userCurrency = '{{ $userCurrency }}';

        // Store current member profile data for copy functions
        let currentMemberProfile = null;

        // Transaction Pagination Variables
        const TXN_PER_PAGE = 6;
        let currentTxnPage = 1;

        // Initialize transaction pagination on page load
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof allTransactions !== 'undefined' && allTransactions.length > 0) {
                renderTransactions();
                updatePaginationControls();
            }
        });

        function getTotalTxnPages() {
            if (typeof allTransactions === 'undefined') return 0;
            return Math.ceil(allTransactions.length / TXN_PER_PAGE);
        }

        function renderTransactions() {
            const container = document.getElementById('transactionsContainer');
            if (!container || typeof allTransactions === 'undefined') return;

            const startIdx = (currentTxnPage - 1) * TXN_PER_PAGE;
            const endIdx = startIdx + TXN_PER_PAGE;
            const pageTransactions = allTransactions.slice(startIdx, endIdx);

            container.innerHTML = pageTransactions.map(txn => {
                const isIncome = txn.type === 'income';
                const iconBgClass = isIncome
                    ? 'bg-gradient-to-br from-emerald-500 to-green-600'
                    : 'bg-gradient-to-br from-red-500 to-rose-600';
                const iconName = isIncome ? 'arrow-up' : 'arrow-down';
                const amountColorClass = isIncome
                    ? 'text-emerald-600 dark:text-green-400'
                    : 'text-red-600 dark:text-red-400';
                const amountPrefix = isIncome ? '+' : '-';
                const statusClass = txn.status === 'paid'
                    ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                    : 'bg-amber-100 text-amber-700 dark:bg-yellow-900 dark:text-yellow-300';

                // Prepare transaction data for onclick
                const txnData = JSON.stringify(txn).replace(/'/g, "\\'").replace(/"/g, '&quot;');
                const editData = JSON.stringify({
                    id: txn.id,
                    description: txn.description,
                    type: txn.type,
                    total_amount: txn.total_amount,
                    paid_by_id: txn.paid_by_id,
                    date: txn.date,
                    category_id: txn.category_id,
                    status: txn.status,
                    members: txn.members.map(m => ({ member_id: m.member_id, participated: m.participated }))
                }).replace(/'/g, "\\'").replace(/"/g, '&quot;');

                return `
                                                                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-3 sm:p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-indigo-200 dark:hover:bg-gray-850 hover:shadow-sm transition-all cursor-pointer group"
                                                                                        onclick="showTransactionDetails(${txnData})">
                                                                                        <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                                                                                            <div class="w-8 h-8 sm:w-10 sm:h-10 ${iconBgClass} rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                                                                                <i class="fas fa-${iconName} text-white text-sm sm:text-base"></i>
                                                                                            </div>
                                                                                            <div class="min-w-0 flex-1">
                                                                                                <h3 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base truncate">
                                                                                                    ${txn.description}
                                                                                                </h3>
                                                                                                <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">
                                                                                                    <span class="truncate">${txn.paid_by_name}</span>
                                                                                                    <span class="mx-1 sm:mx-2 text-gray-300 dark:text-gray-600">•</span>
                                                                                                    <span>${txn.date_formatted}</span>
                                                                                                    ${txn.category_name ? `<span class="mx-1 sm:mx-2 text-gray-300 dark:text-gray-600 hidden sm:inline">•</span><span class="hidden sm:inline">${txn.category_name}</span>` : ''}
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
                                                                                            <div class="text-right">
                                                                                                <p class="font-bold ${amountColorClass} text-sm sm:text-base">
                                                                                                    ${amountPrefix}${txnCurrencySymbol}${txn.converted_amount}
                                                                                                </p>
                                                                                                <span class="text-xs px-2 py-1 rounded-full ${statusClass} whitespace-nowrap font-medium">
                                                                                                    ${txn.status.charAt(0).toUpperCase() + txn.status.slice(1)}
                                                                                                </span>
                                                                                            </div>
                                                                                            <div class="hidden sm:flex opacity-0 group-hover:opacity-100 transition-opacity gap-2">
                                                                                                <button onclick="event.stopPropagation(); editTransaction(${editData})"
                                                                                                    class="p-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-white transition-colors shadow-sm">
                                                                                                    <i class="fas fa-edit"></i>
                                                                                                </button>
                                                                                                <button onclick="event.stopPropagation(); deleteTransaction(${txn.id})"
                                                                                                    class="p-2 bg-red-500 hover:bg-red-600 rounded-lg text-white transition-colors shadow-sm">
                                                                                                    <i class="fas fa-trash"></i>
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                `;
            }).join('');
        }

        function updatePaginationControls() {
            const totalPages = getTotalTxnPages();
            const prevBtn = document.getElementById('txnPrevBtn');
            const nextBtn = document.getElementById('txnNextBtn');
            const infoEl = document.getElementById('txnPaginationInfo');

            if (prevBtn) prevBtn.disabled = currentTxnPage === 1;
            if (nextBtn) nextBtn.disabled = currentTxnPage >= totalPages;

            if (infoEl && typeof allTransactions !== 'undefined') {
                infoEl.textContent = `${currentTxnPage}/${totalPages}`;
            }
        }

        function prevTransactionPage() {
            if (currentTxnPage > 1) {
                currentTxnPage--;
                renderTransactions();
                updatePaginationControls();
            }
        }

        function nextTransactionPage() {
            if (currentTxnPage < getTotalTxnPages()) {
                currentTxnPage++;
                renderTransactions();
                updatePaginationControls();
            }
        }

        // Currency conversion function for group expenses (stored in INR)
        function convertAmount(amount) {
            if (window.AppCurrency && userCurrency !== 'INR') {
                return window.AppCurrency.convert(parseFloat(amount), 'INR', userCurrency);
            }
            return parseFloat(amount);
        }

        function formatGroupAmount(amount) {
            const converted = convertAmount(amount);
            return currencySymbol + converted.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // QR Code cache for instant loading
        const qrCodeCache = new Map();

        // Member profile cache for instant loading
        const memberProfileCache = new Map();

        // Preload QR code image into cache
        function preloadQrCode(url) {
            if (!url || qrCodeCache.has(url)) return;
            const img = new Image();
            img.onload = function () {
                qrCodeCache.set(url, true);
            };
            img.src = url;
        }

        // Preload member profile on hover (called before click)
        async function preloadMemberProfile(memberId) {
            if (memberProfileCache.has(memberId)) return;

            try {
                const response = await fetch(`/group-expense/${groupId}/members/${memberId}/profile`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    memberProfileCache.set(memberId, data.profile);
                    // Also preload QR code if available
                    if (data.profile.upi && data.profile.upi.qr_code_url) {
                        preloadQrCode(data.profile.upi.qr_code_url);
                    }
                }
            } catch (error) {
                // Silently fail - will load normally on click
            }
        }

        // Member Profile Modal Functions
        async function openMemberProfileModal(memberId) {
            const modal = document.getElementById('memberProfileModal');
            const loadingEl = document.getElementById('memberProfileLoading');
            const contentEl = document.getElementById('memberProfileContent');

            // Reset QR code image immediately to prevent showing old cached image
            document.getElementById('memberProfileQrImage').src = '';
            document.getElementById('memberProfileQrCode').classList.add('hidden');
            document.getElementById('memberProfileUpiSection').classList.add('hidden');

            // Check if profile is already cached (from hover preload)
            if (memberProfileCache.has(memberId)) {
                const cachedProfile = memberProfileCache.get(memberId);
                currentMemberProfile = cachedProfile;

                // Show modal instantly without loading state
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                loadingEl.classList.add('hidden');
                contentEl.classList.remove('hidden');

                populateMemberProfile(cachedProfile);
                return;
            }

            // Show modal with loading state
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            loadingEl.classList.remove('hidden');
            contentEl.classList.add('hidden');

            try {
                const response = await fetch(`/group-expense/${groupId}/members/${memberId}/profile`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    currentMemberProfile = data.profile;
                    memberProfileCache.set(memberId, data.profile); // Cache for future use
                    populateMemberProfile(data.profile);
                    loadingEl.classList.add('hidden');
                    contentEl.classList.remove('hidden');
                } else {
                    closeMemberProfileModal();
                    showCustomAlert('Error', data.message || 'Failed to load profile', 'error');
                }
            } catch (error) {
                console.error('Error loading profile:', error);
                closeMemberProfileModal();
                showCustomAlert('Error', 'Failed to load profile. Please try again.', 'error');
            }
        }

        function populateMemberProfile(profile) {
            // Profile Picture
            const pictureEl = document.getElementById('memberProfilePicture');
            if (profile.profile_picture) {
                pictureEl.innerHTML = `<img src="${profile.profile_picture}" alt="${profile.display_name}" class="w-full h-full object-cover">`;
            } else {
                pictureEl.innerHTML = profile.display_name.charAt(0).toUpperCase();
            }

            // Name
            document.getElementById('memberProfileName').textContent = profile.display_name;

            // TrackFlow member badge (blue dot)
            const trackflowEl = document.getElementById('memberProfileTrackflow');
            if (profile.is_trackflow_member) {
                trackflowEl.classList.remove('hidden');
            } else {
                trackflowEl.classList.add('hidden');
            }

            // Verified badge (2FA)
            const verifiedEl = document.getElementById('memberProfileVerified');
            if (profile.is_verified) {
                verifiedEl.classList.remove('hidden');
            } else {
                verifiedEl.classList.add('hidden');
            }

            // Role badge (Leader)
            const roleEl = document.getElementById('memberProfileRole');
            if (profile.role === 'leader') {
                roleEl.classList.remove('hidden');
            } else {
                roleEl.classList.add('hidden');
            }

            // Email
            const emailRow = document.getElementById('memberProfileEmailRow');
            if (profile.email) {
                document.getElementById('memberProfileEmail').textContent = profile.email;
                emailRow.classList.remove('hidden');
                emailRow.classList.add('flex');
            } else {
                emailRow.classList.add('hidden');
                emailRow.classList.remove('flex');
            }

            // Phone
            const phoneRow = document.getElementById('memberProfilePhoneRow');
            if (profile.phone) {
                document.getElementById('memberProfilePhone').textContent = profile.phone;
                phoneRow.classList.remove('hidden');
                phoneRow.classList.add('flex');
            } else {
                phoneRow.classList.add('hidden');
                phoneRow.classList.remove('flex');
            }

            // Bio
            const bioRow = document.getElementById('memberProfileBioRow');
            if (profile.bio) {
                document.getElementById('memberProfileBio').textContent = profile.bio;
                bioRow.classList.remove('hidden');
            } else {
                bioRow.classList.add('hidden');
            }

            // Joined date
            document.getElementById('memberProfileJoined').textContent = profile.joined_at;

            // UPI Section
            const upiSection = document.getElementById('memberProfileUpiSection');
            if (profile.upi) {
                document.getElementById('memberProfileUpiName').textContent = profile.upi.name;
                document.getElementById('memberProfileUpiId').value = profile.upi.upi_id;
                // QR Code - Use cache for instant display
                const qrCodeEl = document.getElementById('memberProfileQrCode');
                const qrImageEl = document.getElementById('memberProfileQrImage');

                if (profile.upi.qr_code_url) {
                    const qrUrl = profile.upi.qr_code_url;

                    // If already cached, display instantly
                    if (qrCodeCache.has(qrUrl)) {
                        qrImageEl.src = qrUrl;
                        qrCodeEl.classList.remove('hidden');
                    } else {
                        // Preload image in memory before displaying
                        const preloadImg = new Image();
                        preloadImg.onload = function () {
                            qrCodeCache.set(qrUrl, true);
                            qrImageEl.src = qrUrl;
                            qrCodeEl.classList.remove('hidden');
                        };
                        preloadImg.onerror = function () {
                            qrCodeEl.classList.add('hidden');
                        };
                        preloadImg.src = qrUrl;
                    }
                } else {
                    qrCodeEl.classList.add('hidden');
                }

                upiSection.classList.remove('hidden');
            } else {
                upiSection.classList.add('hidden');
            }
        }

        function closeMemberProfileModal() {
            const modal = document.getElementById('memberProfileModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentMemberProfile = null;

            // Reset QR code image to prevent showing old image on next open
            document.getElementById('memberProfileQrImage').src = '';
            document.getElementById('memberProfileQrCode').classList.add('hidden');

            // Reset UPI section
            document.getElementById('memberProfileUpiSection').classList.add('hidden');
        }

        function handlePayNow() {
            if (!currentMemberProfile || !currentMemberProfile.upi) return;

            const upiId = currentMemberProfile.upi.upi_id;
            const upiName = currentMemberProfile.upi.name || '';

            // Check if mobile device
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            if (isMobile) {
                // Open UPI app on mobile
                const upiLink = `upi://pay?pa=${encodeURIComponent(upiId)}&pn=${encodeURIComponent(upiName)}`;
                window.location.href = upiLink;
            } else {
                // On desktop, copy UPI ID and show toast
                navigator.clipboard.writeText(upiId).then(() => {
                    ToastSystem.success('UPI ID copied to clipboard!');
                }).catch(() => {
                    ToastSystem.info('Please scan QR code to pay');
                });
            }
        }

        // Pay settlement with pre-filled amount - Opens Payment Proof Modal
        async function paySettlement(memberId, amount) {
            try {
                // Fetch member's UPI info
                const response = await fetch(`/group-expense/${groupId}/members/${memberId}/profile`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (!data.success || !data.profile.upi) {
                    ToastSystem.warning('This member has not set up UPI payment');
                    return;
                }

                const upiId = data.profile.upi.upi_id;
                const upiName = data.profile.upi.name || data.profile.name || '';
                const receiverName = data.profile.display_name || upiName;

                // Open payment proof modal
                openPaymentProofModal(memberId, amount, upiId, upiName, receiverName);
            } catch (error) {
                console.error('Error fetching UPI info:', error);
                ToastSystem.error('Failed to fetch payment details');
            }
        }

        // Open Payment Proof Modal
        function openPaymentProofModal(memberId, amount, upiId, upiName, receiverName) {
            // Set form values
            document.getElementById('settlementMemberId').value = memberId;
            document.getElementById('settlementAmount').value = amount;
            document.getElementById('settlementUpiId').value = upiId;

            // Display info
            document.getElementById('paymentReceiverName').textContent = receiverName;
            document.getElementById('paymentAmount').textContent = '₹' + parseFloat(amount).toFixed(2);
            document.getElementById('receiverUpiId').textContent = upiId;

            // Generate UPI link and store it as data attribute (not as href directly)
            const upiLink = `upi://pay?pa=${encodeURIComponent(upiId)}&pn=${encodeURIComponent(upiName)}&am=${amount}&cu=INR&tn=Settlement_${groupId}`;
            document.getElementById('upiPayLink').setAttribute('data-upi-link', upiLink);

            // Reset form
            document.getElementById('transactionId').value = '';
            document.getElementById('proofScreenshot').value = '';
            removeScreenshot();

            // Show modal
            const modal = document.getElementById('paymentProofModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePaymentProofModal() {
            const modal = document.getElementById('paymentProofModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function copyReceiverUpi() {
            const upiId = document.getElementById('settlementUpiId').value;
            navigator.clipboard.writeText(upiId).then(() => {
                ToastSystem.success('UPI ID copied!');
            });
        }

        // Handle UPI Pay button click - works on both mobile and desktop
        function handleUpiPayClick(event) {
            event.preventDefault();
            const link = document.getElementById('upiPayLink');
            const upiLink = link.getAttribute('data-upi-link');
            if (!upiLink) return;

            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            if (isMobile) {
                // On mobile, try to open the UPI app
                window.location.href = upiLink;
            } else {
                // On desktop, copy UPI ID to clipboard and show helpful message
                const upiId = document.getElementById('settlementUpiId').value;
                navigator.clipboard.writeText(upiId).then(() => {
                    ToastSystem.success('UPI ID copied to clipboard! Open your UPI app on your phone to pay.');
                }).catch(() => {
                    // Fallback: select the UPI ID text for manual copy
                    ToastSystem.info('UPI link cannot open on desktop. Please copy the UPI ID and pay from your phone.');
                });
            }
        }

        function handleScreenshotSelect(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('screenshotImage').src = e.target.result;
                    document.getElementById('screenshotPreview').classList.remove('hidden');
                    document.getElementById('uploadBtn').classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeScreenshot() {
            document.getElementById('proofScreenshot').value = '';
            document.getElementById('screenshotImage').src = '';
            document.getElementById('screenshotPreview').classList.add('hidden');
            document.getElementById('uploadBtn').classList.remove('hidden');
        }

        async function submitPaymentProof(event) {
            event.preventDefault();

            const memberId = document.getElementById('settlementMemberId').value;
            const amount = document.getElementById('settlementAmount').value;
            const transactionId = document.getElementById('transactionId').value.trim();
            const upiId = document.getElementById('settlementUpiId').value;
            const screenshot = document.getElementById('proofScreenshot').files[0];

            if (!transactionId || transactionId.length < 6) {
                ToastSystem.warning('Please enter a valid Transaction ID (at least 6 characters)');
                return;
            }

            if (!screenshot) {
                ToastSystem.warning('Please upload a payment screenshot for verification');
                return;
            }

            const submitBtn = document.getElementById('submitProofBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';

            try {
                // First, create a settlement record
                const createResponse = await fetch(`/group-expense/${groupId}/settlements`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        receiver_member_id: memberId,
                        amount: amount
                    })
                });

                const createData = await createResponse.json();

                if (!createData.success) {
                    throw new Error(createData.message || 'Failed to create settlement');
                }

                const settlementId = createData.settlement.id;

                // Now submit the payment proof
                const formData = new FormData();
                formData.append('transaction_id', transactionId);
                formData.append('upi_id_used', upiId);
                if (screenshot) {
                    formData.append('proof_screenshot', screenshot);
                }

                const proofResponse = await fetch(`/group-expense/${groupId}/settlements/${settlementId}/submit-proof`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const proofData = await proofResponse.json();

                if (proofData.success) {
                    closePaymentProofModal();
                    ToastSystem.success('Payment verified successfully!');
                    // Reload page to reflect changes
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(proofData.message || 'Failed to submit proof');
                }
            } catch (error) {
                console.error('Error submitting payment:', error);
                ToastSystem.error(error.message || 'Failed to submit payment');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Confirm Payment';
            }
        }

        // Request payment - share your UPI details with the payer
        async function requestPayment(fromMemberId, amount) {
            try {
                // Fetch current user's UPI info
                const response = await fetch(`/group-expense/${groupId}/members/{{ $currentMember->id }}/profile`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (!data.success || !data.profile.upi) {
                    ToastSystem.warning('Please set up your UPI payment in Settings first');
                    return;
                }

                const upiId = data.profile.upi.upi_id;
                const upiName = data.profile.upi.name || data.profile.name || '';

                // Generate payment token (base64 encoded: upiId|amount|name)
                const paymentToken = btoa(`${upiId}|${parseFloat(amount).toFixed(2)}|${upiName}`);
                const paymentPageUrl = `{{ url('/pay') }}/${paymentToken}`;

                const shareText = `💰 *Payment Request*

                            Hey! Please pay ₹${parseFloat(amount).toFixed(2)} to *${upiName}*

                            ━━━━━━━━━━━━━━━━━━
                            👇 *TAP TO PAY* 👇
                            ${paymentPageUrl}
                            ━━━━━━━━━━━━━━━━━━

                            📱 *Payment Details:*
                            • Amount: ₹${parseFloat(amount).toFixed(2)}
                            • Name: ${upiName}

                            _Sent via TrackFlow_`;

                if (navigator.share) {
                    navigator.share({
                        title: 'Payment Request - ₹' + parseFloat(amount).toFixed(2),
                        text: shareText,
                        url: paymentPageUrl
                    }).catch(() => {
                        // Fallback to clipboard
                        navigator.clipboard.writeText(shareText).then(() => {
                            ToastSystem.success('Payment request copied to clipboard!');
                        });
                    });
                } else {
                    navigator.clipboard.writeText(shareText).then(() => {
                        ToastSystem.success('Payment request copied to clipboard!');
                    }).catch(() => {
                        ToastSystem.info('Unable to copy. Payment link: ' + paymentPageUrl);
                    });
                }
            } catch (error) {
                console.error('Error creating payment request:', error);
                ToastSystem.error('Failed to create payment request');
            }
        }

        function copyMemberInfo(type) {
            if (!currentMemberProfile) return;

            let textToCopy = '';
            let label = '';

            switch (type) {
                case 'email':
                    textToCopy = currentMemberProfile.email;
                    label = 'Email';
                    break;
                case 'phone':
                    textToCopy = currentMemberProfile.phone;
                    label = 'Phone';
                    break;
                case 'upi':
                    textToCopy = currentMemberProfile.upi?.upi_id;
                    label = 'UPI ID';
                    break;
            }

            if (textToCopy) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    showCustomAlert('Copied!', `${label} copied to clipboard`, 'success');
                }).catch(err => {
                    console.error('Failed to copy:', err);
                });
            }
        }

        // Member Profile Picture Zoom Functions
        function openMemberPictureZoom() {
            if (!currentMemberProfile) return;

            const modal = document.getElementById('memberPictureZoomModal');
            const content = document.getElementById('memberPictureZoomContent');

            if (currentMemberProfile.profile_picture) {
                content.innerHTML = `<img src="${currentMemberProfile.profile_picture}" alt="${currentMemberProfile.display_name}" class="w-full h-auto rounded-xl max-h-[70vh] object-contain">`;
            } else {
                content.innerHTML = `<div class="w-64 h-64 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-8xl font-bold">${currentMemberProfile.display_name.charAt(0).toUpperCase()}</div>`;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeMemberPictureZoom(event, forceClose = false) {
            if (forceClose || event.target.id === 'memberPictureZoomModal') {
                const modal = document.getElementById('memberPictureZoomModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Copy group code to clipboard
        function copyGroupCode(code) {
            const formattedCode = code.substring(0, 4) + '-' + code.substring(4, 8);
            navigator.clipboard.writeText(formattedCode).then(() => {
                showCustomAlert('Copied!', 'Group code copied to clipboard: ' + formattedCode, 'success');
            }).catch(err => {
                console.error('Failed to copy:', err);
                showCustomAlert('Error', 'Failed to copy code. Please copy manually.', 'error');
            });
        }

        // Share group code
        function shareGroup(code, groupName) {
            const formattedCode = code.substring(0, 4) + '-' + code.substring(4, 8);
            const groupUrl = '{{ route("group-expense.index") }}';
            const shareText = `Join my group "${groupName}" on TrackFlow!\nGroup Code: ${formattedCode}\nJoin here: ${groupUrl}`;
            if (navigator.share) {
                navigator.share({
                    title: 'Join Group - ' + groupName,
                    text: shareText
                }).catch(err => {
                    if (err.name !== 'AbortError') {
                        navigator.clipboard.writeText(shareText).then(() => {
                            showCustomAlert('Copied!', 'Share text copied to clipboard', 'success');
                        });
                    }
                });
            } else {
                navigator.clipboard.writeText(shareText).then(() => {
                    showCustomAlert('Copied!', 'Share text copied to clipboard', 'success');
                }).catch(err => {
                    showCustomAlert('Error', 'Failed to copy share text', 'error');
                });
            }
        }

        // Custom Modal Functions
        let confirmCallback = null;

        function showCustomConfirm(title, message, type = 'warning') {
            return new Promise((resolve) => {
                const modal = document.getElementById('customConfirmModal');
                const icon = document.getElementById('confirmIcon');
                const iconElement = icon.querySelector('i');
                const button = document.getElementById('confirmButton');

                document.getElementById('confirmTitle').textContent = title;
                document.getElementById('confirmMessage').textContent = message;

                // Set icon and colors based on type
                if (type === 'danger') {
                    icon.className = 'w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center flex-shrink-0';
                    iconElement.className = 'fas fa-exclamation-triangle text-2xl text-white';
                    button.className = 'flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg text-white transition-colors font-medium';
                } else if (type === 'warning') {
                    icon.className = 'w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center flex-shrink-0';
                    iconElement.className = 'fas fa-exclamation-circle text-2xl text-white';
                    button.className = 'flex-1 px-4 py-2.5 bg-yellow-600 hover:bg-yellow-700 rounded-lg text-white transition-colors font-medium';
                } else {
                    icon.className = 'w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0';
                    iconElement.className = 'fas fa-question-circle text-2xl text-white';
                    button.className = 'flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-white transition-colors font-medium';
                }

                confirmCallback = resolve;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        }

        function closeCustomConfirm(result) {
            const modal = document.getElementById('customConfirmModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            if (confirmCallback) {
                confirmCallback(result);
                confirmCallback = null;
            }
        }

        function showCustomAlert(title, message, type = 'info') {
            const modal = document.getElementById('customAlertModal');
            const icon = document.getElementById('alertIcon');
            const iconElement = icon.querySelector('i');

            document.getElementById('alertTitle').textContent = title;
            document.getElementById('alertMessage').textContent = message;

            // Set icon and colors based on type
            if (type === 'success') {
                icon.className = 'w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0';
                iconElement.className = 'fas fa-check-circle text-2xl text-white';
            } else if (type === 'error') {
                icon.className = 'w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center flex-shrink-0';
                iconElement.className = 'fas fa-times-circle text-2xl text-white';
            } else if (type === 'warning') {
                icon.className = 'w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center flex-shrink-0';
                iconElement.className = 'fas fa-exclamation-triangle text-2xl text-white';
            } else {
                icon.className = 'w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0';
                iconElement.className = 'fas fa-info-circle text-2xl text-white';
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCustomAlert() {
            const modal = document.getElementById('customAlertModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Join Group Modal
        function openJoinGroupModal() {
            document.getElementById('joinGroupModal').classList.remove('hidden');
            document.getElementById('joinGroupModal').classList.add('flex');
            document.getElementById('groupCodeInput').focus();
        }

        function closeJoinGroupModal() {
            document.getElementById('joinGroupModal').classList.add('hidden');
            document.getElementById('joinGroupModal').classList.remove('flex');
            // Clear the input
            document.getElementById('groupCodeInput').value = '';
            // Reset button state
            const btn = document.getElementById('joinGroupBtn');
            const btnText = document.getElementById('joinGroupBtnText');
            const btnLoading = document.getElementById('joinGroupBtnLoading');
            btn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
        }

        function moveToNext(current, nextFieldId) {
            if (current.value.length === current.maxLength && nextFieldId) {
                document.getElementById(nextFieldId).focus();
            }
        }

        function handleBackspace(event, current, prevFieldId) {
            if (event.key === 'Backspace' && current.value.length === 0 && prevFieldId) {
                event.preventDefault();
                document.getElementById(prevFieldId).focus();
            }
        }

        function formatGroupCode(input) {
            // Remove any non-alphanumeric characters except hyphen
            let value = input.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();

            // Add hyphen after 4th character if length > 4
            if (value.length > 4) {
                value = value.substring(0, 4) + '-' + value.substring(4, 8);
            }

            input.value = value;
        }

        function joinGroupWithCode(event) {
            event.preventDefault();

            // Get code from single input and normalize it
            let code = document.getElementById('groupCodeInput').value.toUpperCase().replace(/[^a-zA-Z0-9]/g, '');

            if (code.length !== 8) {
                showCustomAlert('Validation Error', 'Please enter a valid 8-character group code', 'warning');
                return;
            }

            // Show loading state
            const btn = document.getElementById('joinGroupBtn');
            const btnText = document.getElementById('joinGroupBtnText');
            const btnLoading = document.getElementById('joinGroupBtnLoading');
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');

            // Format with hyphen
            code = code.substring(0, 4) + '-' + code.substring(4, 8);

            // For now, just show the code (will be implemented later)
            showCustomAlert('Code Entered', `Group Code: ${code}`, 'info');
            closeJoinGroupModal();

            // Reset button state after closing
            setTimeout(() => {
                btn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }, 500);
        }

        // Add Member Modal
        function openAddMemberModal() {
            document.getElementById('addMemberModal').classList.remove('hidden');
            document.getElementById('addMemberModal').classList.add('flex');
        }

        function closeAddMemberModal() {
            document.getElementById('addMemberModal').classList.add('hidden');
            document.getElementById('addMemberModal').classList.remove('flex');
            document.getElementById('memberName').value = '';
            document.getElementById('memberEmail').value = '';
            document.getElementById('memberPhone').value = '';
        }

        async function addMember(event) {
            event.preventDefault();

            const formData = {
                name: document.getElementById('memberName').value,
                email: document.getElementById('memberEmail').value,
                phone: document.getElementById('memberPhone').value
            };

            try {
                const response = await fetch(`/group-expense/${groupId}/members`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    closeAddMemberModal();
                    window.location.reload();
                } else {
                    showCustomAlert('Error', data.message || 'Failed to add member', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showCustomAlert('Error', 'Failed to add member. Please try again.', 'error');
            }
        }

        // Confirm Remove Member (for leaders)
        async function confirmRemoveMember(memberId, memberName) {
            const confirmed = await showCustomConfirm(
                'Remove Member',
                `Are you sure you want to remove ${memberName} from the group? This action cannot be undone.`,
                'warning'
            );
            if (!confirmed) {
                return;
            }

            try {
                const response = await fetch(`/group-expense/${groupId}/members/${memberId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showCustomAlert('Success', data.message || 'Member removed successfully', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showCustomAlert('Error', data.message || 'Failed to remove member', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showCustomAlert('Error', 'Failed to remove member. Please try again.', 'error');
            }
        }

        // Leave Group (for members)
        async function confirmLeaveGroup() {
            const confirmed = await showCustomConfirm(
                'Leave Group',
                'Are you sure you want to leave this group? Make sure all your debts are settled before leaving.',
                'warning'
            );
            if (!confirmed) {
                return;
            }

            try {
                const response = await fetch(`/group-expense/${groupId}/leave`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showCustomAlert('Success', data.message || 'You have successfully left the group', 'success');
                    setTimeout(() => window.location.href = '{{ route('group-expense.index') }}', 1500);
                } else {
                    showCustomAlert('Error', data.message || 'Failed to leave group', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showCustomAlert('Error', 'Failed to leave group. Please try again.', 'error');
            }
        }

        // Transfer Leadership
        async function transferLeadership(memberId, memberName) {
            const confirmed = await showCustomConfirm(
                'Transfer Leadership',
                `Transfer leadership to ${memberName}? You will become a regular member.`,
                'warning'
            );
            if (!confirmed) {
                return;
            }

            try {
                const response = await fetch(`/group-expense/${groupId}/members/${memberId}/role`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ role: 'leader' })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    showCustomAlert('Error', data.message || 'Failed to transfer leadership', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showCustomAlert('Error', 'Failed to transfer leadership. Please try again.', 'error');
            }
        }

        // Add Transaction Modal
        function openAddTransactionModal() {
            document.getElementById('addTransactionModal').classList.remove('hidden');
            document.getElementById('addTransactionModal').classList.add('flex');
        }

        function closeAddTransactionModal() {
            document.getElementById('addTransactionModal').classList.add('hidden');
            document.getElementById('addTransactionModal').classList.remove('flex');
        }

        async function addTransaction(event) {
            event.preventDefault();

            const totalAmount = parseFloat(document.getElementById('totalAmount').value);

            // Get selected members for split
            const selectedCheckboxes = document.querySelectorAll('input[name="split_members"]:checked');
            const selectedMemberIds = Array.from(selectedCheckboxes).map(cb => parseInt(cb.value));

            if (selectedMemberIds.length === 0) {
                showCustomAlert('Validation Error', 'Please select at least one member to split with', 'warning');
                return;
            }

            const participantCount = selectedMemberIds.length;
            const equalShare = totalAmount / participantCount;

            // Build members array - only selected members participate
            const membersData = members.map(member => {
                const participated = selectedMemberIds.includes(member.id);
                return {
                    member_id: member.id,
                    contributed_amount: 0, // Default to 0, will be set for paid_by member
                    participated: participated
                };
            });

            // Set contributed amount for the paying member
            const paidById = parseInt(document.getElementById('paidBy').value);
            const payingMember = membersData.find(m => m.member_id === paidById);
            if (payingMember) {
                payingMember.contributed_amount = totalAmount;
            }

            const formData = {
                type: document.querySelector('input[name="type"]:checked').value,
                paid_by_member_id: paidById,
                category_id: document.getElementById('category').value || null,
                total_amount: totalAmount,
                description: document.getElementById('description').value,
                date: document.getElementById('transactionDate').value,
                note: '',
                status: document.querySelector('input[name="status"]:checked').value,
                members: membersData
            };

            // Show loading state
            const submitBtn = document.getElementById('addTransactionBtn');
            const btnIcon = document.getElementById('addTransactionIcon');
            const btnText = document.getElementById('addTransactionText');

            submitBtn.disabled = true;
            btnIcon.className = 'fas fa-spinner fa-spin';
            btnText.textContent = 'Adding...';

            try {
                const response = await fetch(`/group-expense/${groupId}/transactions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    closeAddTransactionModal();
                    window.location.reload();
                } else {
                    showCustomAlert('Error', data.message || 'Failed to add transaction', 'error');
                    // Reset button state
                    submitBtn.disabled = false;
                    btnIcon.className = 'fas fa-plus';
                    btnText.textContent = 'Add Transaction';
                }
            } catch (error) {
                console.error('Error:', error);
                showCustomAlert('Error', 'Failed to add transaction. Please try again.', 'error');
                // Reset button state
                submitBtn.disabled = false;
                btnIcon.className = 'fas fa-plus';
                btnText.textContent = 'Add Transaction';
            }
        }

        // Transaction Details Modal
        function showTransactionDetails(transaction) {
            const modal = document.getElementById('transactionDetailsModal');
            const content = document.getElementById('transactionDetailsContent');

            const participants = transaction.members.filter(m => m.participated);
            const participantCount = participants.length;
            const shareAmount = participantCount > 0 ? transaction.total_amount / participantCount : 0;

            content.innerHTML = `
                                                                                                                                                                                                <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                                                                                                                                                                                                    <div class="flex items-center justify-between mb-4">
                                                                                                                                                                                                        <div class="flex items-center gap-3">
                                                                                                                                                                                                            <div class="w-12 h-12 ${transaction.type === 'income' ? 'bg-green-600' : 'bg-red-600'} rounded-lg flex items-center justify-center">
                                                                                                                                                                                                                <i class="fas fa-${transaction.type === 'income' ? 'arrow-up' : 'arrow-down'} text-white text-xl"></i>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                            <div>
                                                                                                                                                                                                                <h3 class="text-xl font-bold text-white">${transaction.description}</h3>
                                                                                                                                                                                                                <p class="text-sm text-gray-400">${transaction.date_formatted}</p>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <div class="text-right">
                                                                                                                                                                                                            <p class="text-2xl font-bold ${transaction.type === 'income' ? 'text-green-400' : 'text-red-400'}">
                                                                                                                                                                                                                ${transaction.type === 'income' ? '+' : '-'}${formatGroupAmount(transaction.total_amount)}
                                                                                                                                                                                                            </p>
                                                                                                                                                                                                            <span class="text-xs px-2 py-1 rounded ${transaction.status === 'paid' ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300'}">
                                                                                                                                                                                                                ${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}
                                                                                                                                                                                                            </span>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>

                                                                                                                                                                                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                                                                                                                                                                                        <div>
                                                                                                                                                                                                            <p class="text-sm text-gray-400">Paid By</p>
                                                                                                                                                                                                            <p class="text-white font-semibold">${transaction.paid_by_name}</p>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        ${transaction.category_name ? `
                                                                                                                                                                                                        <div>
                                                                                                                                                                                                            <p class="text-sm text-gray-400">Category</p>
                                                                                                                                                                                                            <p class="text-white font-semibold">${transaction.category_name}</p>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        ` : ''}
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>

                                                                                                                                                                                                <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                                                                                                                                                                                                    <h4 class="text-lg font-semibold text-white mb-3">Split Details</h4>
                                                                                                                                                                                                    <div class="space-y-2">
                                                                                                                                                                                                        <div class="flex justify-between text-sm">
                                                                                                                                                                                                            <span class="text-gray-400">Total Amount:</span>
                                                                                                                                                                                                            <span class="text-white font-semibold">${formatGroupAmount(transaction.total_amount)}</span>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <div class="flex justify-between text-sm">
                                                                                                                                                                                                            <span class="text-gray-400">Participants:</span>
                                                                                                                                                                                                            <span class="text-white font-semibold">${participantCount} member${participantCount !== 1 ? 's' : ''}</span>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <div class="flex justify-between text-sm">
                                                                                                                                                                                                            <span class="text-gray-400">Share per person:</span>
                                                                                                                                                                                                            <span class="text-white font-semibold">${formatGroupAmount(shareAmount)}</span>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>

                                                                                                                                                                                                <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                                                                                                                                                                                                    <h4 class="text-lg font-semibold text-white mb-3">Participating Members</h4>
                                                                                                                                                                                                    <div class="space-y-2">
                                                                                                                                                                                                        ${participants.map(member => `
                                                                                                                                                                                                            <div class="flex items-center justify-between p-2 bg-gray-800 rounded">
                                                                                                                                                                                                                <div class="flex items-center gap-3">
                                                                                                                                                                                                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                                                                                                                                                                                        ${member.name.charAt(0).toUpperCase()}
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                    <span class="text-white">${member.name}</span>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div class="text-right">
                                                                                                                                                                                                                    ${member.contributed_amount > 0 ?
                    `<span class="text-green-400 text-sm">Paid ${formatGroupAmount(member.contributed_amount)}</span>` :
                    `<span class="text-red-400 text-sm">Owes ${formatGroupAmount(member.final_share_amount)}</span>`
                }
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        `).join('')}
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            `;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeTransactionDetailsModal() {
            document.getElementById('transactionDetailsModal').classList.add('hidden');
            document.getElementById('transactionDetailsModal').classList.remove('flex');
        }

        // Edit Transaction Modal
        function editTransaction(transaction) {
            document.getElementById('editTransactionId').value = transaction.id;
            document.getElementById('editDescription').value = transaction.description;
            document.getElementById('editTotalAmount').value = transaction.total_amount;
            document.getElementById('editPaidBy').value = transaction.paid_by_id;
            document.getElementById('editTransactionDate').value = transaction.date;

            if (transaction.category_id) {
                document.getElementById('editCategory').value = transaction.category_id;
            } else {
                document.getElementById('editCategory').value = '';
            }

            // Set type radio
            document.querySelector(`input[name="editType"][value="${transaction.type}"]`).checked = true;

            // Set status radio
            document.querySelector(`input[name="editStatus"][value="${transaction.status}"]`).checked = true;

            // Set split members checkboxes
            const checkboxes = document.querySelectorAll('input[name="edit_split_members"]');
            checkboxes.forEach(checkbox => {
                const memberId = parseInt(checkbox.value);
                const memberData = transaction.members.find(m => m.member_id === memberId);
                checkbox.checked = memberData ? memberData.participated : false;
            });

            document.getElementById('editTransactionModal').classList.remove('hidden');
            document.getElementById('editTransactionModal').classList.add('flex');
        }

        function closeEditTransactionModal() {
            document.getElementById('editTransactionModal').classList.add('hidden');
            document.getElementById('editTransactionModal').classList.remove('flex');
        }

        async function updateTransaction(event) {
            event.preventDefault();

            const transactionId = document.getElementById('editTransactionId').value;
            const totalAmount = parseFloat(document.getElementById('editTotalAmount').value);

            // Get selected members for split
            const selectedCheckboxes = document.querySelectorAll('input[name="edit_split_members"]:checked');
            const selectedMemberIds = Array.from(selectedCheckboxes).map(cb => parseInt(cb.value));

            if (selectedMemberIds.length === 0) {
                showCustomAlert('Validation Error', 'Please select at least one member to split with', 'warning');
                return;
            }

            const participantCount = selectedMemberIds.length;

            // Build members array - only selected members participate
            const membersData = members.map(member => {
                const participated = selectedMemberIds.includes(member.id);
                return {
                    member_id: member.id,
                    contributed_amount: 0,
                    participated: participated
                };
            });

            // Set contributed amount for the paying member
            const paidById = parseInt(document.getElementById('editPaidBy').value);
            const payingMember = membersData.find(m => m.member_id === paidById);
            if (payingMember) {
                payingMember.contributed_amount = totalAmount;
            }

            const formData = {
                type: document.querySelector('input[name="editType"]:checked').value,
                paid_by_member_id: paidById,
                category_id: document.getElementById('editCategory').value || null,
                total_amount: totalAmount,
                description: document.getElementById('editDescription').value,
                date: document.getElementById('editTransactionDate').value,
                note: '',
                status: document.querySelector('input[name="editStatus"]:checked').value,
                members: membersData
            };

            try {
                const response = await fetch(`/group-expense/${groupId}/transactions/${transactionId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    closeEditTransactionModal();
                    window.location.reload();
                } else {
                    showCustomAlert('Error', data.message || 'Failed to update transaction', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showCustomAlert('Error', 'Failed to update transaction. Please try again.', 'error');
            }
        }

        // Delete Transaction
        async function deleteTransaction(transactionId) {
            const confirmed = await showCustomConfirm(
                'Delete Transaction',
                'Are you sure you want to delete this transaction? This action cannot be undone.',
                'danger'
            );
            if (!confirmed) {
                return;
            }

            try {
                const response = await fetch(`/group-expense/${groupId}/transactions/${transactionId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    showCustomAlert('Error', data.message || 'Failed to delete transaction', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showCustomAlert('Error', 'Failed to delete transaction. Please try again.', 'error');
            }
        }

        // ========== VERIFICATION PANEL FUNCTIONS ==========
        @if($currentMember->isLeader())
            function openVerificationPanel() {
                const modal = document.getElementById('verificationPanelModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                loadPendingPayments();
            }

            function closeVerificationPanel() {
                const modal = document.getElementById('verificationPanelModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            async function loadPendingPayments() {
                const container = document.getElementById('pendingPaymentsContainer');
                container.innerHTML = `
                                                                                                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                                                                                                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                                                                                                        <p>Loading pending payments...</p>
                                                                                                                    </div>
                                                                                                                `;

                try {
                    const response = await fetch(`/group-expense/${groupId}/settlements/pending`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        renderPendingPayments(data.grouped);
                    } else {
                        container.innerHTML = `
                                                                                                                            <div class="text-center py-8 text-red-500">
                                                                                                                                <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                                                                                                                                <p>${data.message || 'Failed to load payments'}</p>
                                                                                                                            </div>
                                                                                                                        `;
                    }
                } catch (error) {
                    console.error('Error loading payments:', error);
                    container.innerHTML = `
                                                                                                                        <div class="text-center py-8 text-red-500">
                                                                                                                            <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                                                                                                                            <p>Failed to load payments</p>
                                                                                                                        </div>
                                                                                                                    `;
                }
            }

            function renderPendingPayments(grouped) {
                const container = document.getElementById('pendingPaymentsContainer');
                const awaitingVerification = grouped.awaiting_verification || [];
                const paid = grouped.paid || [];

                if (awaitingVerification.length === 0 && paid.length === 0) {
                    container.innerHTML = `
                                                                                                                        <div class="text-center py-12">
                                                                                                                            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                                                                                                                <i class="fas fa-check-circle text-3xl text-green-500"></i>
                                                                                                                            </div>
                                                                                                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">All caught up!</h3>
                                                                                                                            <p class="text-gray-500 dark:text-gray-400">No pending payment verifications</p>
                                                                                                                        </div>
                                                                                                                    `;
                    return;
                }

                let html = '';

                // Awaiting Verification Section
                if (awaitingVerification.length > 0) {
                    html += `
                                                                                                                        <div class="mb-6">
                                                                                                                            <h3 class="text-sm font-semibold text-amber-600 dark:text-amber-400 mb-3 flex items-center gap-2">
                                                                                                                                <i class="fas fa-clock"></i>
                                                                                                                                Awaiting Verification (${awaitingVerification.length})
                                                                                                                            </h3>
                                                                                                                            <div class="space-y-3">
                                                                                                                    `;

                    awaitingVerification.forEach(settlement => {
                        const isAutoVerified = settlement.status === 'auto_verified';
                        html += `
                                                                                                                            <div class="bg-gradient-to-r ${isAutoVerified ? 'from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 border-indigo-200 dark:border-indigo-700/30' : 'from-amber-50 to-orange-50 dark:from-orange-900/20 dark:to-red-900/20 border-amber-200 dark:border-orange-700/30'} rounded-xl border p-4">
                                                                                                                                <div class="flex items-start justify-between gap-4 mb-3">
                                                                                                                                    <div>
                                                                                                                                        <p class="font-semibold text-gray-900 dark:text-white">
                                                                                                                                            ${settlement.payer?.display_name || 'Unknown'} 
                                                                                                                                            <i class="fas fa-arrow-right mx-2 text-gray-400 text-xs"></i>
                                                                                                                                            ${settlement.receiver?.display_name || 'Unknown'}
                                                                                                                                        </p>
                                                                                                                                        <p class="text-2xl font-bold ${isAutoVerified ? 'text-indigo-600 dark:text-indigo-400' : 'text-amber-600 dark:text-amber-400'}">
                                                                                                                                            ₹${parseFloat(settlement.amount).toFixed(2)}
                                                                                                                                        </p>
                                                                                                                                    </div>
                                                                                                                                    ${isAutoVerified ? '<span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-semibold"><i class="fas fa-robot mr-1"></i>Auto-Verified</span>' : ''}
                                                                                                                                </div>

                                                                                                                                <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                                                                                                                                    <div>
                                                                                                                                        <span class="text-gray-500 dark:text-gray-400 text-xs">Transaction ID</span>
                                                                                                                                        <p class="font-mono text-gray-900 dark:text-white">${settlement.transaction_id || 'N/A'}</p>
                                                                                                                                    </div>
                                                                                                                                    <div>
                                                                                                                                        <span class="text-gray-500 dark:text-gray-400 text-xs">Paid At</span>
                                                                                                                                        <p class="text-gray-900 dark:text-white">${settlement.paid_at ? new Date(settlement.paid_at).toLocaleString() : 'N/A'}</p>
                                                                                                                                    </div>
                                                                                                                                </div>

                                                                                                                                ${settlement.proof_screenshot ? `
                                                                                                                                    <div class="mb-4">
                                                                                                                                        <span class="text-gray-500 dark:text-gray-400 text-xs block mb-2">Screenshot</span>
                                                                                                                                        <a href="/storage/${settlement.proof_screenshot}" target="_blank" class="block">
                                                                                                                                            <img src="/storage/${settlement.proof_screenshot}" class="w-full h-32 object-contain rounded-lg border border-gray-200 dark:border-gray-600 hover:opacity-80 transition-opacity">
                                                                                                                                        </a>
                                                                                                                                    </div>
                                                                                                                                ` : ''}

                                                                                                                                <div class="flex gap-2">
                                                                                                                                    <button onclick="approvePayment(${settlement.id})" 
                                                                                                                                        class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2">
                                                                                                                                        <i class="fas fa-check"></i> Approve
                                                                                                                                    </button>
                                                                                                                                    <button onclick="showRejectModal(${settlement.id})" 
                                                                                                                                        class="flex-1 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2">
                                                                                                                                        <i class="fas fa-times"></i> Reject
                                                                                                                                    </button>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        `;
                    });

                    html += '</div></div>';
                }

                // Recently Paid Section
                if (paid.length > 0) {
                    html += `
                                                                                                                        <div>
                                                                                                                            <h3 class="text-sm font-semibold text-green-600 dark:text-green-400 mb-3 flex items-center gap-2">
                                                                                                                                <i class="fas fa-check-circle"></i>
                                                                                                                                Recently Verified (${paid.length})
                                                                                                                            </h3>
                                                                                                                            <div class="space-y-2">
                                                                                                                    `;

                    paid.slice(0, 5).forEach(settlement => {
                        html += `
                                                                                                                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700/30 p-3 flex items-center justify-between">
                                                                                                                                <div>
                                                                                                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                                                                                                        ${settlement.payer?.display_name || 'Unknown'} → ${settlement.receiver?.display_name || 'Unknown'}
                                                                                                                                    </p>
                                                                                                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                                                                                        Verified ${settlement.verified_at ? new Date(settlement.verified_at).toLocaleString() : 'N/A'}
                                                                                                                                    </p>
                                                                                                                                </div>
                                                                                                                                <span class="text-green-600 dark:text-green-400 font-bold">₹${parseFloat(settlement.amount).toFixed(2)}</span>
                                                                                                                            </div>
                                                                                                                        `;
                    });

                    html += '</div></div>';
                }

                container.innerHTML = html;
            }

            async function approvePayment(settlementId) {
                const confirmed = await showCustomConfirm(
                    'Approve Payment',
                    'Are you sure you want to approve this payment? This will mark the settlement as paid.',
                    'success'
                );
                if (!confirmed) return;

                try {
                    const response = await fetch(`/group-expense/${groupId}/settlements/${settlementId}/verify`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        ToastSystem.success('Payment approved successfully!');
                        loadPendingPayments();
                    } else {
                        ToastSystem.error(data.message || 'Failed to approve payment');
                    }
                } catch (error) {
                    console.error('Error approving payment:', error);
                    ToastSystem.error('Failed to approve payment');
                }
            }

            async function showRejectModal(settlementId) {
                const reason = await showCustomPrompt(
                    'Reject Payment',
                    'Please provide a reason for rejection:',
                    'Enter reason...'
                );

                if (reason === null) return;
                if (!reason.trim()) {
                    ToastSystem.warning('Please provide a rejection reason');
                    return;
                }

                try {
                    const response = await fetch(`/group-expense/${groupId}/settlements/${settlementId}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ reason: reason.trim() })
                    });

                    const data = await response.json();

                    if (data.success) {
                        ToastSystem.success('Payment rejected');
                        loadPendingPayments();
                    } else {
                        ToastSystem.error(data.message || 'Failed to reject payment');
                    }
                } catch (error) {
                    console.error('Error rejecting payment:', error);
                    ToastSystem.error('Failed to reject payment');
                }
            }

            // Custom prompt dialog
            function showCustomPrompt(title, message, placeholder = '') {
                return new Promise((resolve) => {
                    const modal = document.createElement('div');
                    modal.className = 'fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[60] p-4';
                    modal.innerHTML = `
                                                                                                                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6">
                                                                                                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">${title}</h3>
                                                                                                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">${message}</p>
                                                                                                                            <textarea id="promptInput" rows="3" placeholder="${placeholder}"
                                                                                                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 mb-4"></textarea>
                                                                                                                            <div class="flex gap-3">
                                                                                                                                <button id="promptCancel" class="flex-1 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                                                                                                                                <button id="promptConfirm" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-colors">Submit</button>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    `;
                    document.body.appendChild(modal);

                    const input = modal.querySelector('#promptInput');
                    input.focus();

                    modal.querySelector('#promptCancel').onclick = () => {
                        modal.remove();
                        resolve(null);
                    };

                    modal.querySelector('#promptConfirm').onclick = () => {
                        const value = input.value;
                        modal.remove();
                        resolve(value);
                    };

                    modal.onclick = (e) => {
                        if (e.target === modal) {
                            modal.remove();
                            resolve(null);
                        }
                    };
                });
            }
        @endif
    </script>
@endsection