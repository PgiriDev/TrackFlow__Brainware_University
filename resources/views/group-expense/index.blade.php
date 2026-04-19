@extends('layouts.app')

@section('title', 'Group Expense Sharing')

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
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 sm:mb-8">
            <div class="flex-1">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-users mr-2 sm:mr-3"></i><span class="inline sm:inline">Group Expense Sharing</span>
                </h1>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mt-1 sm:mt-2">
                    Manage shared expenses with friends, roommates, or family
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <button onclick="openJoinGroupModal()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg shadow-lg transition-all text-sm sm:text-base w-full sm:w-auto">
                    <i class="fas fa-key mr-2"></i><span>Join with Code</span>
                </button>
                <button onclick="openCreateGroupModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg shadow-lg transition-all text-sm sm:text-base w-full sm:w-auto">
                    <i class="fas fa-plus mr-2"></i><span>Create New Group</span>
                </button>
            </div>
        </div>

        <!-- Groups List -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @forelse($groups as $group)
                @php
                    $currentMember = $group->members()->where('user_id', session('user_id'))->first();
                    $isLeader = $currentMember && $currentMember->role === 'leader';
                @endphp
                <div onclick="window.location.href='{{ route('group-expense.show', $group->id) }}'"
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 hover:bg-white/50 dark:hover:bg-gray-800/60 hover:shadow-xl transition-all p-4 sm:p-6 cursor-pointer">
                    <!-- Header with delete button -->
                    <div class="flex flex-col sm:flex-row items-start justify-between gap-3 mb-4">
                        <div class="flex-1 w-full sm:w-auto">
                            <h3 onclick="event.stopPropagation(); window.location.href='{{ route('group-expense.show', $group->id) }}'"
                                class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-2 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer transition-colors">
                                {{ $group->name }}
                            </h3>
                            @if($group->description)
                                <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm mb-3">
                                    {{ Str::limit($group->description, 80) }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-start">
                            <span
                                class="bg-blue-600 text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                                {{ $group->members->count() }} <span class="hidden xs:inline">Members</span><span
                                    class="xs:hidden">M</span>
                            </span>
                            @if($isLeader)
                                <button onclick="event.stopPropagation(); deleteGroup({{ $group->id }}, '{{ $group->name }}')"
                                    class="text-red-400 hover:text-red-300 transition-colors p-1" title="Delete Group">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Income/Expense Cards -->
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4">
                        <div
                            class="bg-emerald-50/80 dark:bg-emerald-900/30 backdrop-blur-sm p-2 sm:p-3 rounded-lg border border-emerald-200/50 dark:border-emerald-700/50">
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 mb-1 font-medium">Income</p>
                            <p class="text-base sm:text-lg font-bold text-emerald-600 dark:text-emerald-400 truncate">
                                {{ $currencySymbol }}{{ number_format($currencyService->convert((float) $group->transactions->where('type', 'income')->sum('total_amount'), 'INR', $userCurrency), 2) }}
                            </p>
                        </div>
                        <div
                            class="bg-rose-50/80 dark:bg-rose-900/30 backdrop-blur-sm p-2 sm:p-3 rounded-lg border border-rose-200/50 dark:border-rose-700/50">
                            <p class="text-xs text-rose-600 dark:text-rose-400 mb-1 font-medium">Expenses</p>
                            <p class="text-base sm:text-lg font-bold text-rose-600 dark:text-rose-400 truncate">
                                {{ $currencySymbol }}{{ number_format($currencyService->convert((float) $group->transactions->where('type', 'expense')->sum('total_amount'), 'INR', $userCurrency), 2) }}
                            </p>
                        </div>
                    </div>

                    <!-- Group Code -->
                    <div
                        class="bg-gradient-to-r from-purple-100/80 via-indigo-100/80 to-blue-100/80 dark:from-purple-900/50 dark:via-indigo-900/50 dark:to-blue-900/50 backdrop-blur-sm rounded-xl px-4 py-4 mb-4 border border-purple-300/50 dark:border-purple-500/30">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-key text-purple-600 dark:text-purple-400 text-sm"></i>
                                <span class="text-sm font-semibold text-purple-700 dark:text-purple-300">Group Code</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    onclick="event.stopPropagation(); shareGroup('{{ $group->group_code }}', '{{ $group->name }}')"
                                    class="text-purple-600 dark:text-purple-300 hover:text-purple-800 dark:hover:text-purple-100 transition-colors"
                                    title="Share group">
                                    <i class="fas fa-share-alt text-base"></i>
                                </button>
                                <button onclick="event.stopPropagation(); copyCode('{{ $group->group_code }}')"
                                    class="text-purple-600 dark:text-purple-300 hover:text-purple-800 dark:hover:text-purple-100 transition-colors"
                                    title="Copy code">
                                    <i class="fas fa-copy text-base"></i>
                                </button>
                            </div>
                        </div>
                        <code
                            class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white tracking-widest block text-center py-2 bg-white/50 dark:bg-gray-900/30 rounded-lg border border-purple-200/50 dark:border-transparent"
                            style="font-family: 'Arial Rounded MT Bold', 'Arial', sans-serif; letter-spacing: 0.2em; font-weight: 700;">{{ substr($group->group_code, 0, 4) }}-{{ substr($group->group_code, 4, 4) }}</code>
                    </div>

                    <!-- Footer -->
                    <div
                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-4 border-t border-gray-200/50 dark:border-gray-700">
                        <div class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-crown text-amber-500 mr-2"></i>
                            <span
                                class="truncate max-w-[150px] sm:max-w-none">{{ $group->leader()->name ?? 'No Leader' }}</span>
                        </div>
                        <a href="{{ route('group-expense.show', $group->id) }}" onclick="event.stopPropagation()"
                            class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 py-2 rounded-lg text-xs sm:text-sm transition-all flex items-center gap-2 w-full sm:w-auto justify-center shadow-md hover:shadow-lg">
                            View Group <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div
                        class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6 sm:p-12 text-center">
                        <i class="fas fa-users text-4xl sm:text-6xl text-gray-400 dark:text-gray-600 mb-3 sm:mb-4"></i>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-2">No Groups Yet</h3>
                        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-4 sm:mb-6">Create your first group to
                            start sharing
                            expenses</p>
                        <button onclick="openCreateGroupModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors text-sm sm:text-base">
                            <i class="fas fa-plus mr-2"></i>Create Your First Group
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Create Group Modal -->
    <div id="createGroupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div
            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-lg shadow-lg border border-white/50 dark:border-gray-700/50-2xl max-w-md w-full p-4 sm:p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">Create New Group</h2>
                <button onclick="closeCreateGroupModal()"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>

            <form onsubmit="createGroup(event)">
                <!-- Group Name -->
                <div class="mb-3 sm:mb-4">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Group Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="groupName" required
                        class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="e.g., Hostel Roommates, Mess Group">
                </div>

                <!-- Description -->
                <div class="mb-3 sm:mb-4">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description (Optional)
                    </label>
                    <textarea id="groupDescription" rows="2"
                        class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Brief description about the group"></textarea>
                </div>

                <!-- Leader Details -->
                <div class="mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-crown text-yellow-500 mr-2"></i>Leader/Captain Details
                    </h3>

                    <div class="space-y-2 sm:space-y-3">
                        <input type="text" id="leaderName" required
                            class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Your Name *">

                        <input type="email" id="leaderEmail"
                            class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Your Email (optional)">

                        <input type="tel" id="leaderPhone"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Your Mobile (optional)">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="button" onclick="closeCreateGroupModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit" id="createGroupBtn"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-check mr-2" id="createGroupIcon"></i><span id="createGroupText">Create Group</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Join with Code Modal -->
    <div id="joinGroupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-4 sm:mb-6 lg:mb-8">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">Join Group with Code</h2>
                <button onclick="closeJoinGroupModal()"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>

            <form onsubmit="joinGroupWithCode(event)">
                <div class="space-y-4 sm:space-y-6 lg:space-y-8">
                    <div>
                        <label
                            class="block text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-4 sm:mb-6 text-center">Enter
                            Group Code</label>
                        <input type="text" id="groupCodeInput"
                            class="w-full px-4 py-4 text-center text-xl sm:text-2xl font-bold tracking-widest bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:border-purple-500 focus:ring-2 sm:focus:ring-4 focus:ring-purple-200 dark:focus:ring-purple-900 transition-all uppercase shadow-sm placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="XXXX-XXXX" maxlength="9" oninput="formatGroupCode(this)">
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-4 sm:mt-6 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Enter or paste the group code (e.g., ABCD-1234)
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-6 sm:mt-8">
                    <button type="button" onclick="closeJoinGroupModal()"
                        class="flex-1 px-4 py-2.5 sm:py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm sm:text-base">
                        Cancel
                    </button>
                    <button type="submit" id="joinGroupBtn"
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 sm:py-2 rounded-lg transition-colors text-sm sm:text-base disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="joinGroupBtnText"><i class="fas fa-sign-in-alt mr-2"></i>Join Group</span>
                        <span id="joinGroupBtnLoading" class="hidden"><i
                                class="fas fa-spinner fa-spin mr-2"></i>Joining...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Confirm Modal -->
    <div id="customConfirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 border border-gray-700 shadow-2xl">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-2xl text-white"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-2">Delete Group</h3>
                    <p id="confirmMessage" class="text-gray-300 text-sm"></p>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeCustomConfirm(false)"
                    class="flex-1 px-4 py-2.5 border-2 border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors font-medium">
                    Cancel
                </button>
                <button type="button" onclick="closeCustomConfirm(true)"
                    class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg text-white transition-colors font-medium">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div id="customAlertModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 border border-gray-700 shadow-2xl">
            <div class="flex items-start gap-4 mb-6">
                <div id="alertIcon" class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 id="alertTitle" class="text-xl font-bold text-white mb-2"></h3>
                    <p id="alertMessage" class="text-gray-300 text-sm"></p>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeCustomAlert()"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-white transition-colors font-medium">
                    OK
                </button>
            </div>
        </div>
    </div>

    <script>
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

        // Copy group code to clipboard
        function copyCode(code) {
            const formattedCode = code.substring(0, 4) + '-' + code.substring(4, 8);
            navigator.clipboard.writeText(formattedCode).then(() => {
                showCustomAlert('Copied!', 'Group code copied: ' + formattedCode, 'success');
            }).catch(err => {
                console.error('Failed to copy:', err);
                showCustomAlert('Error', 'Failed to copy code. Please copy manually.', 'error');
            });
        }

        // Custom Modal Functions
        let confirmCallback = null;

        function showCustomConfirm(message) {
            return new Promise((resolve) => {
                document.getElementById('confirmMessage').textContent = message;
                confirmCallback = resolve;
                const modal = document.getElementById('customConfirmModal');
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
            if (type === 'success') {
                icon.className = 'w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0';
                iconElement.className = 'fas fa-check-circle text-2xl text-white';
            } else {
                icon.className = 'w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center flex-shrink-0';
                iconElement.className = 'fas fa-times-circle text-2xl text-white';
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
            document.getElementById('groupCodeInput').value = '';
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
            let value = input.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
            if (value.length > 4) {
                value = value.substring(0, 4) + '-' + value.substring(4, 8);
            }
            input.value = value;
        }

        function joinGroupWithCode(event) {
            event.preventDefault();
            let code = document.getElementById('groupCodeInput').value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            if (code.length !== 8) {
                showCustomAlert('Validation Error', 'Please enter a valid 8-character group code', 'warning');
                return;
            }
            const btn = document.getElementById('joinGroupBtn');
            const btnText = document.getElementById('joinGroupBtnText');
            const btnLoading = document.getElementById('joinGroupBtnLoading');
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            fetch('{{ route('group-expense.join-by-code') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ group_code: code })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCustomAlert('Success!', data.message, 'success');
                    closeJoinGroupModal();
                    setTimeout(() => {
                        window.location.href = `/group-expense/${data.group_id}`;
                    }, 1500);
                } else {
                    showCustomAlert('Error', data.message, 'error');
                    btn.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomAlert('Error', 'Failed to join group. Please try again.', 'error');
                btn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            });
        }

        function openCreateGroupModal() {
            document.getElementById('createGroupModal').classList.remove('hidden');
            document.getElementById('createGroupModal').classList.add('flex');
        }

        function closeCreateGroupModal() {
            document.getElementById('createGroupModal').classList.add('hidden');
            document.getElementById('createGroupModal').classList.remove('flex');
            document.getElementById('groupName').value = '';
            document.getElementById('groupDescription').value = '';
            document.getElementById('leaderName').value = '';
            document.getElementById('leaderEmail').value = '';
            document.getElementById('leaderPhone').value = '';
            const btn = document.getElementById('createGroupBtn');
            const icon = document.getElementById('createGroupIcon');
            const text = document.getElementById('createGroupText');
            btn.disabled = false;
            icon.className = 'fas fa-check mr-2';
            text.textContent = 'Create Group';
        }

        async function createGroup(event) {
            event.preventDefault();
            const btn = document.getElementById('createGroupBtn');
            const icon = document.getElementById('createGroupIcon');
            const text = document.getElementById('createGroupText');
            btn.disabled = true;
            icon.className = 'fas fa-spinner fa-spin mr-2';
            text.textContent = 'Creating...';
            const formData = {
                name: document.getElementById('groupName').value,
                description: document.getElementById('groupDescription').value,
                leader_name: document.getElementById('leaderName').value,
                leader_email: document.getElementById('leaderEmail').value,
                leader_phone: document.getElementById('leaderPhone').value
            };
            try {
                const response = await fetch('{{ route("group-expense.create") }}', {
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
                    closeCreateGroupModal();
                    showCustomAlert('Success', 'Group created successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = `/group-expense/${data.group_id}`;
                    }, 1000);
                } else {
                    btn.disabled = false;
                    icon.className = 'fas fa-check mr-2';
                    text.textContent = 'Create Group';
                    showCustomAlert('Error', data.message || 'Failed to create group', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                btn.disabled = false;
                icon.className = 'fas fa-check mr-2';
                text.textContent = 'Create Group';
                showCustomAlert('Error', 'Failed to create group. Please try again.', 'error');
            }
        }

        async function deleteGroup(groupId, groupName) {
            const confirmed = await showCustomConfirm(`Are you sure you want to delete "${groupName}"?\n\nThis will permanently delete all members, transactions, and data associated with this group. This action cannot be undone.`);
            if (!confirmed) {
                return;
            }
            try {
                const response = await fetch(`/group-expense/${groupId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showCustomAlert('Success', 'Group deleted successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showCustomAlert('Error', data.message || 'Failed to delete group', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showCustomAlert('Error', 'Failed to delete group. Please try again.', 'error');
            }
        }
    </script>

@endsection