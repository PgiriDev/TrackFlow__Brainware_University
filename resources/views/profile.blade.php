@extends('layouts.app')

@section('content')
    <!-- Colorful Glassmorphism Page Background - Cyan/Teal Theme -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-100 via-teal-50 to-emerald-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900"></div>
        <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-cyan-300/40 to-teal-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-cyan-600/10 dark:to-teal-700/10"></div>
        <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-emerald-300/40 to-green-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-emerald-600/10 dark:to-green-700/10"></div>
        <div class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-teal-300/30 to-cyan-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-teal-600/10 dark:to-cyan-700/10"></div>
        <div class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-sky-300/30 to-cyan-400/30 rounded-full blur-3xl dark:from-sky-600/10 dark:to-cyan-700/10"></div>
        <div class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-teal-300/30 to-emerald-400/30 rounded-full blur-3xl dark:from-teal-600/10 dark:to-emerald-700/10"></div>
    </div>

    <div class="min-h-screen py-8 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Profile</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage your personal information and account
                    settings</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Profile Card -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 overflow-hidden">
                        <!-- Profile Header -->
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-32"></div>

                        <!-- Avatar Section -->
                        <div class="px-6 pb-6">
                            <div class="flex flex-col items-center -mt-16">
                                <div class="relative group">
                                    <!-- Profile Picture Display -->
                                    <div id="profilePictureContainer"
                                        class="w-32 h-32 rounded-full bg-white dark:bg-gray-700 border-4 border-white dark:border-gray-800 shadow-lg flex items-center justify-center overflow-hidden cursor-pointer"
                                        onclick="viewProfilePicture()">
                                        @php
                                            $profilePicture = DB::table('users')->where('id', session('user_id'))->value('profile_picture');
                                        @endphp
                                        @if($profilePicture)
                                            <img id="profilePictureImg" src="{{ $profilePicture }}" alt="Profile"
                                                class="w-full h-full object-cover">
                                        @else
                                            <span id="profileInitial"
                                                class="text-5xl font-bold text-indigo-600 dark:text-indigo-400">
                                                {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Upload/Camera Button -->
                                    <div class="absolute bottom-0 right-0 flex gap-1">
                                        <button onclick="document.getElementById('fileInput').click()"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-2 shadow-lg transition-colors"
                                            title="Upload Image">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button onclick="openCamera()"
                                            class="bg-green-600 hover:bg-green-700 text-white rounded-full p-2 shadow-lg transition-colors"
                                            title="Take Photo">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Hidden File Input -->
                                    <input type="file" id="fileInput" accept="image/*" class="hidden"
                                        onchange="handleFileSelect(event)">
                                </div>

                                <div class="mt-4 flex items-center justify-center gap-2">
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ session('user_name', 'User') }}
                                    </h2>
                                    @if($user->two_factor_enabled)
                                        <span class="text-green-500" title="2FA Verified Account">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ session('user_email',
                                    'user@example.com') }}</p>

                                <!-- Recrop & Remove Buttons (shown when profile picture exists) -->
                                @if($profilePicture)
                                    <div class="mt-4 flex gap-2">
                                        <button onclick="recropImage()"
                                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16V4m0 0L1 7m3-3l3 3m4 12h8m0 0l-3-3m3 3l-3 3"></path>
                                            </svg>
                                            Recrop
                                        </button>
                                        <button onclick="removeProfilePicture()"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                @endif

                                <!-- Quick Stats -->
                                <div class="mt-6 w-full">
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400"
                                                id="transactionCount">0</div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Transactions</div>
                                        </div>
                                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="text-2xl font-bold text-green-600 dark:text-green-400"
                                                id="accountCount">0</div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Accounts</div>
                                        </div>
                                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                                {{ $groupsCount }}
                                            </div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Groups</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Member Since -->
                                <div class="mt-6 w-full pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Member since</span>
                                        <span class="font-medium text-gray-900 dark:text-white"
                                            id="memberSince">Loading...</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm mt-3">
                                        <span class="text-gray-600 dark:text-gray-400">Last login</span>
                                        <span class="font-medium text-gray-900 dark:text-white">Today</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Primary UPI Section -->
                    <div
                        class="mt-6 bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment UPI</h3>
                            <a href="{{ url('/settings') }}#profile-tab"
                                class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                Manage
                            </a>
                        </div>

                        @if($primaryUpi)
                            <div class="text-center">
                                <!-- QR Code -->
                                @if($primaryUpi->qr_code_path)
                                    <div class="inline-block mb-4">
                                        <div class="w-32 h-32 bg-white rounded-lg shadow-sm p-2 mx-auto">
                                            <img src="{{ asset('storage/' . $primaryUpi->qr_code_path) }}" 
                                                 alt="UPI QR Code" 
                                                 class="w-full h-full object-contain rounded">
                                        </div>
                                    </div>
                                @endif

                                <!-- UPI Details -->
                                <div class="flex items-center justify-center gap-1 mb-2">
                                    <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-semibold rounded-full">
                                        Primary
                                    </span>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $primaryUpi->name }}</h4>
                                <div class="mt-2 flex items-center justify-center gap-2">
                                    <span class="font-mono text-xs text-gray-600 dark:text-gray-400">{{ $primaryUpi->upi_id }}</span>
                                    <button onclick="copyToClipboard('{{ $primaryUpi->upi_id }}')" 
                                            class="p-1 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                            title="Copy UPI ID">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">No UPI added yet</p>
                                <a href="{{ url('/settings') }}#profile-tab" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add UPI
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div
                        class="mt-6 bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="{{ url('/settings') }}"
                                class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Settings</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Manage preferences</div>
                                </div>
                            </a>
                            <a href="{{ url('/transactions') }}"
                                class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Transactions</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">View all activity</div>
                                </div>
                            </a>
                            <a href="{{ url('/reports') }}"
                                class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Reports</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Financial insights</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Profile Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <div
                        class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Personal Information</h3>
                            <button onclick="window.location.href='{{ url('/settings') }}'"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Edit Profile
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Full
                                    Name</label>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-gray-900 dark:text-white"
                                        id="profileName">{{ $user->name ?? 'User' }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Email
                                    Address</label>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span class="text-gray-900 dark:text-white" id="profileEmail">{{ $user->email ??
                                        'user@example.com' }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Phone
                                    Number</label>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                    <span class="text-gray-900 dark:text-white"
                                        id="profilePhone">{{ $user->phone ?? 'Not set' }}</span>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Currency</label>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <span class="text-gray-900 dark:text-white" id="profileCurrency">{{ $userCurrency }}
                                        ({{ $currencySymbol }})</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bio Section (Read-only) -->
                    <div
                        class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">About Me</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">A little about this user</p>
                            </div>
                            <a href="{{ url('/settings') }}#bio-section"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span>Edit in Settings</span>
                            </a>
                        </div>

                        <!-- Bio Display (Read-only) -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg min-h-[100px]">
                            @if($user->bio)
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $user->bio }}</p>
                            @else
                                <p class="text-gray-400 dark:text-gray-500 italic">No bio added yet. Go to Settings to add one!</p>
                            @endif
                        </div>
                    </div>

                    <!-- My Groups Section -->
                    <div
                        class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">My Groups</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Groups you are a member of</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-4 py-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-lg font-semibold">
                                    {{ $groupsCount }} {{ $groupsCount === 1 ? 'Group' : 'Groups' }}
                                </span>
                            </div>
                        </div>

                        @if($groupsCount > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($userGroups as $group)
                                    <div class="p-3 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800 hover:shadow-lg transition-shadow">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="text-base font-bold text-gray-900 dark:text-white">{{ $group['name'] }}</h4>
                                                    @if($group['is_leader'])
                                                        <span class="px-2 py-0.5 bg-yellow-400 dark:bg-yellow-600 text-yellow-900 dark:text-yellow-100 text-xs font-bold rounded-full flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                            </svg>
                                                            Leader
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-full">
                                                            Member
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($group['description'])
                                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-1">{{ $group['description'] }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-indigo-200 dark:border-indigo-800/50">
                                            <div class="flex items-center gap-4 text-xs">
                                                <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    <span class="font-medium">{{ $group['members_count'] }}</span>
                                                </div>
                                                <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>{{ $group['joined_at'] }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 font-mono bg-white dark:bg-gray-800 px-2 py-1 rounded">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                                {{ $group['group_code'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Groups Yet</h4>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">You haven't joined any groups yet.</p>
                                <a href="{{ url('/settlement') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create or Join a Group
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Account Activity -->
                    <div
                        class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Account Activity</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Total Income</span>
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div class="text-2xl font-bold text-blue-900 dark:text-blue-100" id="totalIncome">₹0</div>
                                <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">This month</div>
                            </div>

                            <div
                                class="p-4 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl border border-red-200 dark:border-red-800">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-red-700 dark:text-red-300">Total Expenses</span>
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                    </svg>
                                </div>
                                <div class="text-2xl font-bold text-red-900 dark:text-red-100" id="totalExpenses">₹0</div>
                                <div class="text-xs text-red-600 dark:text-red-400 mt-1">This month</div>
                            </div>

                            <div
                                class="p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl border border-green-200 dark:border-green-800">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-green-700 dark:text-green-300">Net Balance</span>
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-2xl font-bold text-green-900 dark:text-green-100" id="netBalance">₹0</div>
                                <div class="text-xs text-green-600 dark:text-green-400 mt-1">This month</div>
                            </div>
                        </div>
                    </div>

                    <!-- Community Activity Section -->
                    <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-comments text-purple-500"></i>
                                    Community Activity
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Your posts, points, and engagement</p>
                            </div>
                            <a href="{{ route('community.index') }}" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-sm font-medium rounded-lg transition-all flex items-center gap-2">
                                <i class="fas fa-arrow-right"></i>
                                Go to Community
                            </a>
                        </div>

                        <!-- Community Stats Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <!-- Reputation Badge -->
                            <div class="col-span-2 md:col-span-1 p-4 bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center">
                                        <i class="fas fa-{{ $communityReputation->level === 'Newbie' ? 'seedling' : ($communityReputation->level === 'Contributor' ? 'star' : ($communityReputation->level === 'Top Voice' ? 'fire' : 'crown')) }} text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-yellow-800 dark:text-yellow-200">{{ $communityStats['points'] }}</div>
                                        <div class="text-xs text-yellow-600 dark:text-yellow-400">{{ $communityReputation->level }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Posts -->
                            <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl border border-purple-200 dark:border-purple-800">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-purple-700 dark:text-purple-300">Posts</span>
                                    <i class="fas fa-pen-to-square text-purple-500"></i>
                                </div>
                                <div class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $communityStats['total_posts'] }}</div>
                            </div>

                            <!-- Total Comments -->
                            <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Comments</span>
                                    <i class="fas fa-comments text-blue-500"></i>
                                </div>
                                <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $communityStats['total_comments'] }}</div>
                            </div>

                            <!-- Total Views -->
                            <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl border border-green-200 dark:border-green-800">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-green-700 dark:text-green-300">Views</span>
                                    <i class="fas fa-eye text-green-500"></i>
                                </div>
                                <div class="text-2xl font-bold text-green-900 dark:text-green-100">{{ number_format($communityStats['total_views']) }}</div>
                            </div>
                        </div>

                        <!-- User's Posts List -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                                <i class="fas fa-list text-gray-500"></i>
                                My Recent Posts
                            </h4>

                            @if($communityPosts->count() > 0)
                                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                                    @foreach($communityPosts as $post)
                                        <a href="{{ route('community.show', $post->id) }}" 
                                           class="block p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1 min-w-0">
                                                    <!-- Post Type & Status Badges -->
                                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                                        @php $typeInfo = $post->type_info; @endphp
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $typeInfo['color'] }}-100 dark:bg-{{ $typeInfo['color'] }}-900/30 text-{{ $typeInfo['color'] }}-700 dark:text-{{ $typeInfo['color'] }}-400">
                                                            <i class="fas {{ $typeInfo['icon'] }} text-[10px]"></i>
                                                            {{ $typeInfo['label'] }}
                                                        </span>
                                                        @php $statusInfo = $post->status_info; @endphp
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $statusInfo['color'] }}-100 dark:bg-{{ $statusInfo['color'] }}-900/30 text-{{ $statusInfo['color'] }}-700 dark:text-{{ $statusInfo['color'] }}-400">
                                                            <i class="fas {{ $statusInfo['icon'] }} text-[10px]"></i>
                                                            {{ $statusInfo['label'] }}
                                                        </span>
                                                        @if($post->is_anonymous)
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                                                <i class="fas fa-user-secret text-[10px]"></i>
                                                                Anonymous
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <!-- Title -->
                                                    <h5 class="font-semibold text-gray-900 dark:text-white text-sm line-clamp-1 mb-1">{{ $post->title }}</h5>
                                                    
                                                    <!-- Date -->
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $post->created_at->format('M d, Y') }}</p>
                                                </div>

                                                <!-- Stats -->
                                                <div class="flex flex-col items-end gap-1 text-xs text-gray-500 dark:text-gray-400 shrink-0">
                                                    <div class="flex items-center gap-1" title="Vote Score">
                                                        <i class="fas fa-arrow-up text-green-500"></i>
                                                        <span class="font-medium {{ $post->vote_score > 0 ? 'text-green-600' : ($post->vote_score < 0 ? 'text-red-600' : '') }}">{{ $post->vote_score }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1" title="Comments">
                                                        <i class="fas fa-comment text-blue-500"></i>
                                                        <span>{{ $post->comments_count }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1" title="Views">
                                                        <i class="fas fa-eye text-gray-500"></i>
                                                        <span>{{ $post->view_count }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1" title="Reactions">
                                                        <i class="fas fa-heart text-pink-500"></i>
                                                        <span>{{ $post->reactions_count }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tags -->
                                            @if($post->tags->count() > 0)
                                                <div class="flex flex-wrap gap-1 mt-2">
                                                    @foreach($post->tags->take(3) as $postTag)
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium"
                                                              style="background-color: {{ $postTag->color }}20; color: {{ $postTag->color }};">
                                                            {{ $postTag->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($post->tags->count() > 3)
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                                            +{{ $post->tags->count() - 3 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>

                                @if($communityStats['total_posts'] > 10)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('community.index', ['my_posts' => 1]) }}" 
                                           class="inline-flex items-center gap-2 text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium">
                                            View all {{ $communityStats['total_posts'] }} posts
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8">
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full mb-4">
                                        <i class="fas fa-pen-to-square text-2xl text-purple-500"></i>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Posts Yet</h4>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">Share your ideas and feedback with the community!</p>
                                    <a href="{{ route('community.index') }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-lg transition-all">
                                        <i class="fas fa-plus-circle"></i>
                                        Create Your First Post
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Crop Modal -->
    <div id="cropModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Crop Profile Picture</h3>
                    <button onclick="closeCropModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <!-- Image Container -->
                <div class="max-h-[50vh] overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-lg">
                    <img id="cropImage" class="max-w-full" style="display:block;">
                </div>

                <!-- Zoom Controls -->
                <div class="mt-4 flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Zoom:</label>
                    <input type="range" id="zoomRange" min="0" max="1" step="0.01" value="0" class="flex-1"
                        oninput="handleZoom(this.value)">
                    <button onclick="cropper.reset()"
                        class="px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-lg transition-colors">Reset</button>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <button onclick="closeCropModal()"
                    class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                    Cancel
                </button>
                <button onclick="saveCroppedImage()"
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                    Done
                </button>
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div id="cameraModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Take Photo</h3>
                    <button onclick="closeCameraModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <video id="cameraVideo" autoplay class="w-full max-h-[50vh] bg-gray-900 rounded-lg"></video>
                <canvas id="cameraCanvas" class="hidden"></canvas>
            </div>

            <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-center gap-3">
                <button onclick="capturePhoto()"
                    class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Capture
                </button>
                <button onclick="closeCameraModal()"
                    class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Image Zoom Modal -->
    <div id="zoomModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4"
        onclick="closeZoomModal()">
        <div class="relative max-w-6xl w-full">
            <button onclick="closeZoomModal()"
                class="absolute top-4 right-4 bg-white/20 hover:bg-white/30 text-white rounded-full p-2 backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img id="zoomImage" class="w-full h-auto rounded-lg shadow-2xl" onclick="event.stopPropagation()">
        </div>
    </div>

    <script>
        let cropper = null;
        let cameraStream = null;
        let isRecropping = false;
        let currentImageSrc = null;

        // Load profile data on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadProfileData();
            loadRecentTransactions();
        });

        async function loadProfileData() {
            try {
                const response = await fetch('/api/user/stats');
                const data = await response.json();

                if (data.success) {
                    document.getElementById('transactionCount').textContent = data.stats.transaction_count || 0;
                    document.getElementById('accountCount').textContent = data.stats.account_count || 0;

                    if (data.stats.member_since) {
                        const date = new Date(data.stats.member_since);
                        const formatted = date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                        document.getElementById('memberSince').textContent = formatted;
                    }

                    document.getElementById('totalIncome').textContent = '₹' + (data.stats.total_income || 0).toLocaleString();
                    document.getElementById('totalExpenses').textContent = '₹' + (data.stats.total_expenses || 0).toLocaleString();

                    const netBalance = (data.stats.total_income || 0) - (data.stats.total_expenses || 0);
                    document.getElementById('netBalance').textContent = '₹' + netBalance.toLocaleString();
                }
            } catch (error) {
                console.error('Error loading profile data:', error);
            }
        }

        async function loadRecentTransactions() {
            try {
                const container = document.getElementById('recentTransactions');
                if (!container) {
                    return; // Element doesn't exist, skip loading
                }

                const response = await fetch('/api/transactions/recent?limit=5');
                const data = await response.json();

                if (data.success && data.transactions && data.transactions.length > 0) {
                    container.innerHTML = data.transactions.map(transaction => {
                        const isIncome = transaction.type === 'income';
                        const icon = isIncome
                            ? '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>'
                            : '<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>';

                        return `
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg ${isIncome ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'} flex items-center justify-center">
                                                    ${icon}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">${transaction.description || 'Transaction'}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">${transaction.category || 'Uncategorized'} • ${transaction.date}</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-semibold ${isIncome ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">
                                                    ${isIncome ? '+' : '-'}₹${Math.abs(transaction.amount).toLocaleString()}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                    }).join('');
                } else {
                    container.innerHTML = `
                                    <div class="flex flex-col items-center justify-center py-12 text-gray-500 dark:text-gray-400">
                                        <svg class="w-16 h-16 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-sm">No transactions yet</p>
                                    </div>
                                `;
                }
            } catch (error) {
                console.error('Error loading transactions:', error);
                const errorContainer = document.getElementById('recentTransactions');
                if (errorContainer) {
                    errorContainer.innerHTML = `
                                <div class="text-center py-12 text-red-500 dark:text-red-400">
                                    <p class="text-sm">Failed to load transactions</p>
                                </div>
                            `;
                }
            }
        }

        // Handle file selection
        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    openCropModal(e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }

        // Open camera
        async function openCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: 1280, height: 720 }
                });
                cameraStream = stream;
                document.getElementById('cameraVideo').srcObject = stream;
                document.getElementById('cameraModal').classList.remove('hidden');
            } catch (error) {
                popupError('Unable to access camera. Please ensure you have granted camera permissions to your browser.\n\nError: ' + error.message, 'Camera Access Denied');
            }
        }

        // Capture photo from camera
        function capturePhoto() {
            const video = document.getElementById('cameraVideo');
            const canvas = document.getElementById('cameraCanvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);
            const imageData = canvas.toDataURL('image/jpeg');
            closeCameraModal();
            openCropModal(imageData);
        }

        // Close camera modal
        function closeCameraModal() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            document.getElementById('cameraModal').classList.add('hidden');
        }

        // Open crop modal
        function openCropModal(imageSrc) {
            currentImageSrc = imageSrc;
            const cropImage = document.getElementById('cropImage');
            cropImage.src = imageSrc;
            document.getElementById('cropModal').classList.remove('hidden');

            // Initialize cropper after image loads
            cropImage.onload = function () {
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    ready: function () {
                        document.getElementById('zoomRange').value = 0;
                    }
                });
            };
        }

        // Close crop modal
        function closeCropModal() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            document.getElementById('cropModal').classList.add('hidden');
            document.getElementById('fileInput').value = '';
            isRecropping = false;
        }

        // Handle zoom
        function handleZoom(value) {
            if (cropper) {
                cropper.zoomTo(parseFloat(value));
            }
        }

        // Save cropped image
        async function saveCroppedImage() {
            if (!cropper) return;

            try {
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                const croppedImage = canvas.toDataURL('image/jpeg', 0.9);

                // Show loading state
                const container = document.getElementById('profilePictureContainer');
                container.innerHTML = '<div class="flex items-center justify-center"><svg class="animate-spin h-12 w-12 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

                // Upload to server
                const url = isRecropping ? '/profile/update-picture' : '/profile/upload-picture';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        profile_picture: croppedImage
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update UI
                    container.innerHTML = `<img src="${data.data.profile_picture}?t=${Date.now()}" alt="Profile" class="w-full h-full object-cover">`;
                    closeCropModal();
                    showToast('Profile picture updated successfully!', 'success');

                    // Reload page to show recrop/remove buttons
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            } catch (error) {
                showToast('Failed to upload image: ' + error.message, 'error');
                // Restore previous image
                const container = document.getElementById('profilePictureContainer');
                const img = document.getElementById('profilePictureImg');
                if (img) {
                    container.innerHTML = `<img src="${img.src}" alt="Profile" class="w-full h-full object-cover">`;
                }
            }
        }

        // Recrop existing image
        function recropImage() {
            const img = document.getElementById('profilePictureImg');
            if (img) {
                isRecropping = true;
                openCropModal(img.src);
            }
        }

        // Remove profile picture
        async function removeProfilePicture() {
            popupConfirm(
                'Are you sure you want to remove your profile picture? This action cannot be undone.',
                'Remove Profile Picture',
                async function () {
                    await performRemoveProfilePicture();
                }
            );
        }

        async function performRemoveProfilePicture() {
            try {
                const response = await fetch('/profile/delete-picture', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Profile picture removed successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Remove failed');
                }
            } catch (error) {
                showToast('Failed to remove image: ' + error.message, 'error');
            }
        }

        // View profile picture (zoom)
        function viewProfilePicture() {
            const img = document.getElementById('profilePictureImg');
            if (img) {
                document.getElementById('zoomImage').src = img.src;
                document.getElementById('zoomModal').classList.remove('hidden');
            }
        }

        // Close zoom modal
        function closeZoomModal() {
            document.getElementById('zoomModal').classList.add('hidden');
        }

        // Toast notification
        function showToast(message, type = 'info') {
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('UPI ID copied to clipboard!', 'success');
            }, function() {
                showToast('Failed to copy to clipboard', 'error');
            });
        }
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
@endsection
