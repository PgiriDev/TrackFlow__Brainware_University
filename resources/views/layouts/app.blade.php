<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4F46E5">
    <meta name="description" content="TrackFlow - Advanced Financial Management Platform">
    <link rel="icon" type="image/png" href="/trackflow-main/fav-icon.png">
    <link rel="shortcut icon" type="image/png" href="/trackflow-main/fav-icon.png">
    <link rel="apple-touch-icon" href="/trackflow-main/fav-icon.png">
    @include('partials.pwa-head')

    <!-- Theme initialization script - runs immediately to prevent flash -->
    <script>
        (function () {
            @php
                // Cache user theme in session to avoid repeat DB reads on every request.
                $savedTheme = 'light';
                if (session('user_id')) {
                    $savedTheme = session('user_theme');
                    if ($savedTheme === null) {
                        $userPrefs = DB::table('user_preferences')->where('user_id', session('user_id'))->first();
                        $savedTheme = $userPrefs->theme ?? 'light';
                        session(['user_theme' => $savedTheme]);
                    }
                }
            @endphp

            // Use database theme or fallback to localStorage
            const dbTheme = '{{ $savedTheme }}';
            const theme = dbTheme || localStorage.getItem('theme') || 'light';

            // Sync localStorage with database value
            if (dbTheme) {
                localStorage.setItem('theme', dbTheme);
            }

            // Remove any existing dark class first to start clean
            document.documentElement.classList.remove('dark');

            function applyTheme(themeMode) {
                // Always remove dark class first
                document.documentElement.classList.remove('dark');

                if (themeMode === 'dark') {
                    document.documentElement.classList.add('dark');
                } else if (themeMode === 'light') {
                    // Already removed above, do nothing more
                } else if (themeMode === 'auto') {
                    // Auto mode - check system preference
                    // Note: This detects browser's prefers-color-scheme setting
                    // Users should ensure their browser theme matches system theme
                    try {
                        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                        if (mediaQuery && mediaQuery.matches === true) {
                            document.documentElement.classList.add('dark');
                        }
                    } catch (e) {
                        // Fallback to light mode if matchMedia not supported
                        console.log('matchMedia not supported, defaulting to light mode');
                    }
                }
            }

            applyTheme(theme);

            // Store the apply function globally for later use
            window.applyTheme = applyTheme;
            window.currentTheme = theme;

            // Update dark mode icon
            function updateDarkModeIcon() {
                const icon = document.getElementById('darkModeIcon');
                if (icon) {
                    const isDark = document.documentElement.classList.contains('dark');
                    icon.className = 'fas ' + (isDark ? 'fa-sun' : 'fa-moon');
                }
            }

            // Toggle dark mode function
            window.toggleDarkMode = function () {
                let newTheme;
                const current = localStorage.getItem('theme') || 'light';

                if (current === 'light') {
                    newTheme = 'dark';
                } else if (current === 'dark') {
                    newTheme = 'auto';
                } else {
                    newTheme = 'light';
                }

                localStorage.setItem('theme', newTheme);
                window.currentTheme = newTheme;
                applyTheme(newTheme);
                updateDarkModeIcon();

                // Save to database
                fetch('/settings/theme', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ theme: newTheme })
                }).catch(err => console.error('Failed to save theme:', err));
            };

            // Update icon on page load
            document.addEventListener('DOMContentLoaded', updateDarkModeIcon);

            // Listen for system theme changes when in auto mode
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addEventListener('change', (e) => {
                const currentTheme = localStorage.getItem('theme') || 'light';
                if (currentTheme === 'auto') {
                    if (e.matches) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    updateDarkModeIcon();
                }
            });
        })();
    </script>

    <script>
        // Global helper: autofill delete-account OTP inputs from notification OTP
        window.autoFillDeleteOtp = function autoFillDeleteOtp(otp) {
            try {
                if (!otp || typeof otp !== 'string') otp = String(otp || '');
                // Only autofill when delete OTP section exists and is visible
                const otpSection = document.getElementById('deleteOtpSection');
                if (!otpSection) return; // not on settings page
                if (otpSection.classList.contains('hidden')) return; // section not visible

                const inputs = Array.from(document.querySelectorAll('#deleteOtpSection .otp-input'));
                if (!inputs.length) return;

                // Set each input to corresponding digit
                for (let i = 0; i < inputs.length; i++) {
                    inputs[i].value = otp[i] || '';
                }

                // Update hidden field if present
                const hidden = document.getElementById('deleteOtpHidden');
                if (hidden) hidden.value = otp.slice(0, inputs.length);

                // Focus last filled input
                const last = inputs[Math.min(inputs.length - 1, otp.length - 1)];
                if (last) last.focus();

                // Provide small visual feedback using transient notification if available
                if (typeof window.showTransientNotification === 'function') {
                    window.showTransientNotification('OTP auto-filled from notifications', 'OTP Auto-Fill', 3000);
                }
            } catch (e) {
                console.error('autoFillDeleteOtp error', e);
            }
        }
    </script>

    <title>{{ config('app.name', 'TrackFlow') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1" defer></script>

    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" defer></script>


    <!-- face-api.js (for Face Authentication) -->
    <script src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js" defer></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Custom Styles -->
    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: #1F2937;
        }

        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: #4B5563;
            border-radius: 2px;
        }

        .card-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .card-shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .gradient-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .gradient-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .mobile-menu-enter {
                animation: mobileMenuSlide 0.3s ease-out;
            }

            @keyframes mobileMenuSlide {
                from {
                    transform: translateX(-100%);
                }

                to {
                    transform: translateX(0);
                }
            }
        }

        /* Loading spinner */
        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Safe area for mobile bottom nav */
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

        /* Add bottom padding on mobile for the fixed bottom nav */
        @media (max-width: 1023px) {
            main {
                padding-bottom: 5rem !important;
            }
        }
    </style>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Global Date Format Configuration -->
    <script>
        @php
            $user = DB::table('users')->where('id', session('user_id'))->first();
            $userPrefs = DB::table('user_preferences')->where('user_id', session('user_id'))->first();
            $dateFormat = $userPrefs->date_format ?? 'Y-m-d';
        @endphp

        // Global date format configuration
        window.AppDateFormat = '{{ $dateFormat }}';

        // Global date formatting function
        function formatDate(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;

            const format = window.AppDateFormat || 'Y-m-d';
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();

            switch (format) {
                case 'd/m/Y':
                    return `${day}/${month}/${year}`;
                case 'm/d/Y':
                    return `${month}/${day}/${year}`;
                case 'Y-m-d':
                default:
                    return `${year}-${month}-${day}`;
            }
        }

        // Format date with time
        function formatDateTime(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;

            const formattedDate = formatDate(dateString);
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${formattedDate} ${hours}:${minutes}`;
        }
    </script>

    <!-- Global Currency Configuration -->
    <script>
        @php
            $userCurrency = $user->currency ?? 'INR';
            $currencyConfig = config('currency.currencies');
            $currencyRates = config('currency.rates');
        @endphp

        // Global currency configuration
        window.AppCurrency = {
            current: '{{ $userCurrency }}',
            symbol: '{{ $currencyConfig[$userCurrency]['symbol'] ?? '₹' }}',
            code: '{{ $userCurrency }}',
            locale: '{{ $currencyConfig[$userCurrency]['locale'] ?? 'en-IN' }}',
            rates: @json($currencyRates),
            baseCurrency: 'INR',

            // Format currency with current user's currency
            format(amount, options = {}) {
                const value = parseFloat(amount) || 0;
                return new Intl.NumberFormat(this.locale, {
                    style: 'currency',
                    currency: this.code,
                    minimumFractionDigits: options.decimals ?? 2,
                    maximumFractionDigits: options.decimals ?? 2,
                }).format(value);
            },

            // Convert amount from one currency to another
            // Rates are relative to INR (1 INR = X units of currency)
            // Example: USD = 0.011 means 1 INR = 0.011 USD
            convert(amount, fromCurrency, toCurrency) {
                if (fromCurrency === toCurrency) return parseFloat(amount);

                const rateFrom = this.rates[fromCurrency] || 1;
                const rateTo = this.rates[toCurrency] || 1;

                // Step 1: Convert from source currency to INR
                // If 1 INR = rateFrom units of FROM, then amount units of FROM = amount / rateFrom INR
                const amountInINR = parseFloat(amount) / rateFrom;

                // Step 2: Convert from INR to target currency
                // If 1 INR = rateTo units of TO, then amountInINR INR = amountInINR * rateTo units of TO
                const convertedAmount = amountInINR * rateTo;
                return convertedAmount;
            },

            // Convert from stored currency to user's display currency
            // All amounts stored in INR, display in user's currency
            convertToDisplay(amount, storedCurrency = 'INR') {
                return this.convert(amount, storedCurrency, this.code);
            },

            // Convert from user's input currency to storage currency (INR)
            convertToStorage(amount, inputCurrency = null) {
                const fromCurrency = inputCurrency || this.code;
                return this.convert(amount, fromCurrency, this.baseCurrency);
            },

            // Format and convert in one step
            formatWithConversion(amount, storedCurrency = 'INR') {
                const converted = this.convertToDisplay(amount, storedCurrency);
                return this.format(converted);
            },

            // Get currency symbol
            getSymbol(currencyCode = null) {
                return currencyCode ?
                    (@json($currencyConfig))[currencyCode]?.symbol || '$' :
                    this.symbol;
            }
        };

        // Global formatCurrency function - converts and formats
        function formatCurrency(amount, storedCurrency = 'INR', options = {}) {
            return window.AppCurrency.formatWithConversion(amount, storedCurrency);
        }

        // Update currency symbol placeholders
        function updateCurrencySymbols() {
            const symbol = window.AppCurrency.symbol;
            document.querySelectorAll('[data-currency-symbol]').forEach(el => {
                el.textContent = symbol;
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateCurrencySymbols);
    </script>

    @stack('styles')

    <!-- Dynamic Island Reload Effect Styles -->
    <style>
        /* Dynamic Island Container */
        .dynamic-island {
            position: fixed;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 99999;
            pointer-events: none;
            width: 100%;
            max-width: 100vw;
            display: flex;
            justify-content: center;
            padding: 0 12px;
            box-sizing: border-box;
        }

        .dynamic-island-pill {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #000;
            color: #fff;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            opacity: 0;
            transform: scale(0.8) translateY(-20px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            min-width: 100px;
            max-width: calc(100vw - 24px);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dark .dynamic-island-pill {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05) inset;
        }

        .dynamic-island-pill.active {
            opacity: 1;
            transform: scale(1) translateY(0);
        }

        .dynamic-island-pill.expanded {
            padding: 8px 18px;
            min-width: 140px;
        }

        .dynamic-island-spinner {
            width: 12px;
            height: 12px;
            min-width: 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: di-spin 0.8s linear infinite;
        }

        .dynamic-island-check {
            width: 12px;
            height: 12px;
            min-width: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4ade80;
        }

        .dynamic-island-check svg {
            width: 12px;
            height: 12px;
            animation: di-scale-in 0.3s ease-out;
        }

        #diText {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: calc(100vw - 80px);
        }

        /* Small phones (320px - 374px) */
        @media screen and (min-width: 320px) {
            .dynamic-island {
                top: 6px;
                padding: 0 8px;
            }
            .dynamic-island-pill {
                padding: 5px 12px;
                font-size: 10px;
                border-radius: 18px;
                gap: 5px;
                min-width: 90px;
            }
            .dynamic-island-pill.expanded {
                padding: 6px 14px;
                min-width: 120px;
            }
            .dynamic-island-spinner,
            .dynamic-island-check,
            .dynamic-island-check svg {
                width: 10px;
                height: 10px;
                min-width: 10px;
            }
            #diText {
                max-width: calc(100vw - 70px);
            }
        }

        /* Standard phones (375px - 424px) */
        @media screen and (min-width: 375px) {
            .dynamic-island {
                top: 8px;
                padding: 0 10px;
            }
            .dynamic-island-pill {
                padding: 6px 14px;
                font-size: 11px;
                border-radius: 20px;
                gap: 6px;
                min-width: 100px;
            }
            .dynamic-island-pill.expanded {
                padding: 7px 16px;
                min-width: 130px;
            }
            .dynamic-island-spinner,
            .dynamic-island-check,
            .dynamic-island-check svg {
                width: 12px;
                height: 12px;
                min-width: 12px;
            }
            #diText {
                max-width: calc(100vw - 75px);
            }
        }

        /* Large phones (425px - 639px) */
        @media screen and (min-width: 425px) {
            .dynamic-island {
                top: 10px;
                padding: 0 12px;
            }
            .dynamic-island-pill {
                padding: 7px 16px;
                font-size: 12px;
                border-radius: 22px;
                gap: 7px;
                min-width: 110px;
            }
            .dynamic-island-pill.expanded {
                padding: 8px 18px;
                min-width: 150px;
            }
            .dynamic-island-spinner,
            .dynamic-island-check,
            .dynamic-island-check svg {
                width: 14px;
                height: 14px;
                min-width: 14px;
            }
            #diText {
                max-width: 280px;
            }
        }

        /* Tablets and small laptops (640px - 1023px) */
        @media screen and (min-width: 640px) {
            .dynamic-island {
                top: 12px;
                padding: 0 16px;
            }
            .dynamic-island-pill {
                padding: 8px 20px;
                font-size: 13px;
                border-radius: 26px;
                gap: 8px;
                min-width: 120px;
            }
            .dynamic-island-pill.expanded {
                padding: 10px 24px;
                min-width: 170px;
            }
            .dynamic-island-spinner,
            .dynamic-island-check,
            .dynamic-island-check svg {
                width: 16px;
                height: 16px;
                min-width: 16px;
            }
            #diText {
                max-width: 350px;
            }
        }

        /* Laptops and desktops (1024px+) */
        @media screen and (min-width: 1024px) {
            .dynamic-island {
                top: 14px;
                padding: 0 20px;
            }
            .dynamic-island-pill {
                padding: 8px 22px;
                font-size: 14px;
                border-radius: 28px;
                gap: 10px;
                min-width: 130px;
            }
            .dynamic-island-pill.expanded {
                padding: 10px 26px;
                min-width: 180px;
            }
            .dynamic-island-spinner,
            .dynamic-island-check,
            .dynamic-island-check svg {
                width: 16px;
                height: 16px;
                min-width: 16px;
            }
            #diText {
                max-width: 400px;
            }
        }

        /* Large desktops (1280px+) */
        @media screen and (min-width: 1280px) {
            .dynamic-island {
                top: 16px;
            }
            .dynamic-island-pill {
                padding: 10px 24px;
                font-size: 14px;
                border-radius: 30px;
                min-width: 140px;
            }
            .dynamic-island-pill.expanded {
                padding: 12px 28px;
                min-width: 200px;
            }
            .dynamic-island-spinner,
            .dynamic-island-check,
            .dynamic-island-check svg {
                width: 18px;
                height: 18px;
                min-width: 18px;
            }
            #diText {
                max-width: 500px;
            }
        }

        @keyframes di-spin {
            to { transform: rotate(360deg); }
        }

        @keyframes di-scale-in {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Pull to Refresh Styles */
        .pull-to-refresh {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            z-index: 99998;
            pointer-events: none;
            padding-top: 60px;
        }

        .pull-indicator {
            width: 40px;
            height: 40px;
            background: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transform: translateY(-100px) scale(0.5);
            opacity: 0;
            transition: transform 0.2s ease-out, opacity 0.2s ease-out;
        }

        .dark .pull-indicator {
            background: #1f2937;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .pull-indicator.visible {
            opacity: 1;
        }

        .pull-indicator svg {
            width: 20px;
            height: 20px;
            color: #fff;
            transition: transform 0.2s ease-out;
        }

        .pull-indicator.refreshing svg {
            animation: di-spin 0.8s linear infinite;
        }

        /* Safe area for notched devices (iPhone X+, etc.) */
        @supports (padding-top: env(safe-area-inset-top)) {
            .dynamic-island {
                top: calc(8px + env(safe-area-inset-top));
            }
            @media screen and (min-width: 375px) {
                .dynamic-island {
                    top: calc(8px + env(safe-area-inset-top));
                }
            }
            @media screen and (min-width: 640px) {
                .dynamic-island {
                    top: calc(12px + env(safe-area-inset-top));
                }
            }
            @media screen and (min-width: 1024px) {
                .dynamic-island {
                    top: calc(14px + env(safe-area-inset-top));
                }
            }
            .pull-to-refresh {
                padding-top: calc(60px + env(safe-area-inset-top));
            }
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 antialiased" x-data="{ sidebarOpen: false }" x-init="
    window.addEventListener('resize', () => {
        if (window.innerWidth < 1024) { sidebarOpen = false; }
    });
">

    <!-- Dynamic Island Reload Effect -->
    <div class="dynamic-island" id="dynamicIsland">
        <div class="dynamic-island-pill" id="dynamicIslandPill">
            <div class="dynamic-island-spinner" id="diSpinner"></div>
            <div class="dynamic-island-check" id="diCheck" style="display: none;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span id="diText">Refreshing...</span>
        </div>
    </div>

    <!-- Pull to Refresh Indicator (Mobile) -->
    <div class="pull-to-refresh" id="pullToRefresh">
        <div class="pull-indicator" id="pullIndicator">
            <svg id="pullArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
            <svg id="pullSpinner" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </div>
    </div>

    <!-- Sidebar - Desktop Only -->
    <aside
        class="hidden lg:block fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 sidebar-scrollbar overflow-y-auto">

        <!-- Logo -->
        <div class="flex items-center h-16 px-6 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow Logo" class="w-10 h-10 object-contain">
                <span class="text-xl font-bold text-gray-900 dark:text-white">TrackFlow</span>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="px-4 py-6 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="ml-3">Dashboard</span>
            </a>

            <!-- Transactions -->
            <a href="{{ route('transactions.index') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('transactions.*') ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <i class="fas fa-exchange-alt w-5 text-center"></i>
                <span class="ml-3">Transactions</span>
            </a>

            <!-- Budgets -->
            <a href="{{ route('budgets.index') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('budgets.*') ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <i class="fas fa-wallet w-5 text-center"></i>
                <span class="ml-3">Budgets</span>
            </a>

            <!-- Categories -->
            <a href="{{ route('categories.index') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('categories.*') ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <i class="fas fa-tags w-5 text-center"></i>
                <span class="ml-3">Categories</span>
            </a>

            <!-- Goals -->
            <a href="{{ route('goals.index') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('goals.*') ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <i class="fas fa-bullseye w-5 text-center"></i>
                <span class="ml-3">Goals</span>
            </a>

            <!-- Reports -->
            <a href="{{ route('reports.index') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <i class="fas fa-chart-bar w-5 text-center"></i>
                <span class="ml-3">Reports</span>
            </a>

            <!-- Group Expense -->
            <a href="{{ route('group-expense.index') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('group-expense.*') ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <i class="fas fa-users w-5 text-center"></i>
                <span class="ml-3">Group Expense</span>
            </a>

            <!-- Community -->
            <a href="{{ route('community.index') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('community.*') ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <i class="fas fa-comments w-5 text-center"></i>
                <span class="ml-3">Community</span>
            </a>

            <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                <!-- Settings -->
                <a href="{{ route('settings.index') }}"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span class="ml-3">Settings</span>
                </a>

                <!-- Help -->
                <a href="{{ route('help') }}"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                    <i class="fas fa-question-circle w-5 text-center"></i>
                    <span class="ml-3">Help & Support</span>
                </a>
            </div>
        </nav>

        <!-- User Info at Bottom -->
        <div
            class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="flex items-center">
                @php
                    $profilePicture = DB::table('users')->where('id', session('user_id'))->value('profile_picture');
                @endphp
                @if($profilePicture)
                    <img src="{{ $profilePicture }}" alt="Profile"
                        class="w-10 h-10 rounded-full object-cover border-2 border-purple-500">
                @else
                    <div
                        class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-semibold">
                        {{ substr(session('user_name', 'User'), 0, 1) }}
                    </div>
                @endif
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ session('user_name', 'User') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ session('user_email', '') }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content - Always has margin on desktop (lg:) -->
    <div class="transition-all duration-300 lg:ml-64">
        <!-- Top Navigation -->
        <header
            class="sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between h-16 px-4 lg:px-8">
                <!-- Left: Breadcrumb -->
                <div class="flex items-center space-x-4">
                    <nav class="hidden md:flex items-center space-x-2 text-sm">
                        <a href="{{ route('dashboard') }}"
                            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                            <i class="fas fa-home"></i>
                        </a>
                        <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                        <span class="text-gray-900 dark:text-white font-medium">@yield('breadcrumb', 'Dashboard')</span>
                    </nav>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center space-x-3">
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" id="darkModeToggle"
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas" id="darkModeIcon"></i>
                    </button>

                    <!-- Notifications -->
                    <div x-data="notificationsDropdown()" x-init="init()" class="relative">
                        <button @click="toggleDropdown()"
                            class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-bell"></i>
                            <span x-show="unreadCount > 0" x-text="unreadCount" x-transition
                                class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"
                                style="display: none;"></span>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                            class="fixed sm:absolute right-2 sm:right-0 left-2 sm:left-auto mt-2 sm:w-80 md:w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100">
                            <!-- Header -->
                            <div
                                class="p-3 sm:p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Notifications
                                    <span x-show="unreadCount > 0" x-text="'(' + unreadCount + ')'"
                                        class="text-primary-600"></span>
                                </h3>
                                <div class="flex items-center gap-3 sm:gap-2">
                                    <button @click="markAllAsRead()" x-show="unreadCount > 0"
                                        class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 whitespace-nowrap">
                                        Mark all read
                                    </button>
                                    <button @click="deleteAllNotifications()" x-show="notifications.length > 0"
                                        class="text-xs text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 whitespace-nowrap">
                                        Delete All
                                    </button>
                                    <a href="/notifications"
                                        class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 whitespace-nowrap">
                                        View All
                                    </a>
                                </div>
                            </div>

                            <!-- Notifications List -->
                            <div class="max-h-[60vh] sm:max-h-96 overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="p-6 sm:p-8 text-center">
                                        <i class="fas fa-bell-slash text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No notifications</p>
                                    </div>
                                </template>

                                <template x-if="notifications.length > 0">
                                    <div>
                                        <template x-for="notification in notifications" :key="notification.id">
                                            <div @click="handleNotificationClick(notification)"
                                                :class="{ 'bg-blue-50 dark:bg-blue-900/10': !notification.is_read }"
                                                class="p-3 sm:p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
                                                <div class="flex items-start gap-2 sm:gap-3">
                                                    <div
                                                        :class="'w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-' + notification.color + '-100 dark:bg-' + notification.color + '-900/30'">
                                                        <i
                                                            :class="'fas ' + notification.icon + ' text-sm sm:text-base text-' + notification.color + '-600 dark:text-' + notification.color + '-400'"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white mb-0.5 sm:mb-1 line-clamp-1"
                                                            x-text="notification.title"></p>
                                                        <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2"
                                                            x-text="notification.message"></p>
                                                        <p class="text-[10px] sm:text-xs text-gray-400 dark:text-gray-500 mt-1"
                                                            x-text="formatTime(notification.created_at)"></p>
                                                    </div>
                                                    <button @click.stop="deleteNotification(notification.id)"
                                                        class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 p-1">
                                                        <i class="fas fa-times text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            @php
                                $profilePicture = DB::table('users')->where('id', session('user_id'))->value('profile_picture');
                            @endphp
                            @if($profilePicture)
                                <img src="{{ $profilePicture }}" alt="Profile"
                                    class="w-8 h-8 rounded-full object-cover border-2 border-purple-500">
                            @else
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr(session('user_name', 'User'), 0, 1) }}
                                </div>
                            @endif
                            <i class="fas fa-chevron-down text-xs text-gray-500 dark:text-gray-400"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="p-2">
                                <a href="{{ route('profile') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                    <i class="fas fa-user w-4"></i> Profile
                                </a>
                                <a href="{{ route('settings.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                    <i class="fas fa-cog w-4"></i> Settings
                                </a>
                                <hr class="my-2 border-gray-200 dark:border-gray-700">
                                <button type="button" onclick="confirmLogout()"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                    <i class="fas fa-sign-out-alt w-4"></i> Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 lg:p-8 pb-20 lg:pb-8">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 dark:text-green-400 text-xl mr-3"></i>
                        <span class="text-green-800 dark:text-green-200">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700 dark:text-green-400">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 dark:text-red-400 text-xl mr-3"></i>
                        <span class="text-red-800 dark:text-red-200">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700 dark:text-red-400">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Mobile Bottom Navigation Bar - Only visible on mobile/tablet -->
        <nav id="mobileBottomNav"
            class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg safe-area-bottom">
            <div class="flex items-center justify-around h-16 px-2">
                <!-- Transactions -->
                <a href="{{ route('transactions.index') }}"
                    class="flex flex-col items-center justify-center flex-1 py-2 transition-colors">
                    <i class="fas fa-exchange-alt text-xl mb-1 {{ request()->routeIs('transactions.*') ? 'text-emerald-500' : 'text-gray-400 dark:text-gray-500' }}"></i>
                    <span class="text-[10px] font-medium {{ request()->routeIs('transactions.*') ? 'text-emerald-500' : 'text-gray-500 dark:text-gray-400' }}">Transactions</span>
                </a>

                <!-- Community -->
                <a href="{{ route('community.index') }}"
                    class="flex flex-col items-center justify-center flex-1 py-2 transition-colors">
                    <i class="fas fa-comments text-xl mb-1 {{ request()->routeIs('community.*') ? 'text-purple-500' : 'text-gray-400 dark:text-gray-500' }}"></i>
                    <span class="text-[10px] font-medium {{ request()->routeIs('community.*') ? 'text-purple-500' : 'text-gray-500 dark:text-gray-400' }}">Community</span>
                </a>

                <!-- Dashboard (Center - Highlighted) -->
                <a href="{{ route('dashboard') }}"
                    class="flex flex-col items-center justify-center flex-1 py-2 -mt-4 transition-colors">
                    <div
                        class="w-14 h-14 rounded-full flex items-center justify-center shadow-lg {{ request()->routeIs('dashboard') ? 'bg-primary-600 text-white ring-4 ring-primary-200 dark:ring-primary-900' : 'bg-gradient-to-r from-primary-500 to-indigo-600 text-white' }}">
                        <i class="fas fa-home text-2xl"></i>
                    </div>
                    <span
                        class="text-[10px] font-medium mt-1 {{ request()->routeIs('dashboard') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">Dashboard</span>
                </a>

                <!-- Group Expense -->
                <a href="{{ route('group-expense.index') }}"
                    class="flex flex-col items-center justify-center flex-1 py-2 transition-colors">
                    <i class="fas fa-users text-xl mb-1 {{ request()->routeIs('group-expense.*') ? 'text-blue-500' : 'text-gray-400 dark:text-gray-500' }}"></i>
                    <span class="text-[10px] font-medium {{ request()->routeIs('group-expense.*') ? 'text-blue-500' : 'text-gray-500 dark:text-gray-400' }}">Groups</span>
                </a>

                <!-- More Menu (Hamburger) -->
                <button onclick="toggleMobileMenu()"
                    class="flex flex-col items-center justify-center flex-1 py-2 transition-colors text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                    <i class="fas fa-bars text-xl mb-1"></i>
                    <span class="text-[10px] font-medium">More</span>
                </button>
            </div>
        </nav>

        <!-- Mobile Menu Popup -->
        <div id="mobileMenuPopup" class="lg:hidden fixed inset-0 z-50 hidden">
            <!-- Backdrop -->
            <div onclick="closeMobileMenu()" class="absolute inset-0 bg-black bg-opacity-50 transition-opacity">
            </div>

            <!-- Menu Content -->
            <div id="mobileMenuContent"
                class="absolute bottom-0 left-0 right-0 bg-white dark:bg-gray-800 rounded-t-3xl shadow-2xl transform translate-y-full transition-transform duration-300 ease-out safe-area-bottom">
                <!-- Handle bar -->
                <div class="flex justify-center py-3">
                    <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                </div>

                <!-- Menu Header -->
                <div class="px-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">More Options</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Access additional features</p>
                </div>

                <!-- Menu Items -->
                <div class="p-4 grid grid-cols-3 gap-4 max-h-[60vh] overflow-y-auto">
                    <!-- Budgets -->
                    <a href="{{ route('budgets.index') }}" onclick="closeMobileMenu()"
                        class="flex flex-col items-center p-4 rounded-xl transition-colors {{ request()->routeIs('budgets.*') ? 'bg-primary-50 dark:bg-primary-900/20' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div
                            class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-2">
                            <i class="fas fa-wallet text-xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Budgets</span>
                    </a>

                    <!-- Categories -->
                    <a href="{{ route('categories.index') }}" onclick="closeMobileMenu()"
                        class="flex flex-col items-center p-4 rounded-xl transition-colors {{ request()->routeIs('categories.*') ? 'bg-primary-50 dark:bg-primary-900/20' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div
                            class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-2">
                            <i class="fas fa-tags text-xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Categories</span>
                    </a>

                    <!-- Goals -->
                    <a href="{{ route('goals.index') }}" onclick="closeMobileMenu()"
                        class="flex flex-col items-center p-4 rounded-xl transition-colors {{ request()->routeIs('goals.*') ? 'bg-primary-50 dark:bg-primary-900/20' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div
                            class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-2">
                            <i class="fas fa-bullseye text-xl text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Goals</span>
                    </a>

                    <!-- Reports -->
                    <a href="{{ route('reports.index') }}" onclick="closeMobileMenu()"
                        class="flex flex-col items-center p-4 rounded-xl transition-colors {{ request()->routeIs('reports.*') ? 'bg-primary-50 dark:bg-primary-900/20' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div
                            class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-2">
                            <i class="fas fa-chart-bar text-xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Reports</span>
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('settings.index') }}" onclick="closeMobileMenu()"
                        class="flex flex-col items-center p-4 rounded-xl transition-colors {{ request()->routeIs('settings.*') ? 'bg-primary-50 dark:bg-primary-900/20' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div
                            class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-2">
                            <i class="fas fa-cog text-xl text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Settings</span>
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('profile') }}" onclick="closeMobileMenu()"
                        class="flex flex-col items-center p-4 rounded-xl transition-colors {{ request()->routeIs('profile') ? 'bg-primary-50 dark:bg-primary-900/20' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div
                            class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mb-2">
                            <i class="fas fa-user text-xl text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Profile</span>
                    </a>

                    <!-- Help Center -->
                    <a href="{{ route('help') }}" onclick="closeMobileMenu()"
                        class="flex flex-col items-center p-4 rounded-xl transition-colors">
                        <div
                            class="w-12 h-12 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center mb-2">
                            <i class="fas fa-question-circle text-xl text-teal-600 dark:text-teal-400"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Help</span>
                    </a>

                    <!-- Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="contents">
                        @csrf
                        <button type="submit" onclick="closeMobileMenu()"
                            class="flex flex-col items-center p-4 rounded-xl transition-colors hover:bg-red-50 dark:hover:bg-red-900/20">
                            <div
                                class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-2">
                                <i class="fas fa-sign-out-alt text-xl text-red-600 dark:text-red-400"></i>
                            </div>
                            <span class="text-xs font-medium text-red-600 dark:text-red-400 text-center">Logout</span>
                        </button>
                    </form>
                </div>

                <!-- Close Button -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <button onclick="closeMobileMenu()"
                        class="w-full py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu JavaScript -->
        <script>
            function toggleMobileMenu() {
                const popup = document.getElementById('mobileMenuPopup');
                const content = document.getElementById('mobileMenuContent');

                popup.classList.remove('hidden');
                // Trigger animation after a small delay
                setTimeout(() => {
                    content.classList.remove('translate-y-full');
                    content.classList.add('translate-y-0');
                }, 10);

                // Prevent body scroll
                document.body.style.overflow = 'hidden';
            }

            function closeMobileMenu() {
                const popup = document.getElementById('mobileMenuPopup');
                const content = document.getElementById('mobileMenuContent');

                content.classList.remove('translate-y-0');
                content.classList.add('translate-y-full');

                // Hide popup after animation
                setTimeout(() => {
                    popup.classList.add('hidden');
                }, 300);

                // Restore body scroll
                document.body.style.overflow = '';
            }

            // Close menu on escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeMobileMenu();
                }
            });
        </script>
    </div>

    <!-- Custom Popup/Modal System -->
    <div id="customPopup"
        class="fixed inset-0 bg-black bg-opacity-50 z-[100] hidden flex items-center justify-center p-4 animate-fade-in">
        <div id="customPopupContent"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all animate-scale-in">
            <!-- Popup Header -->
            <div id="customPopupHeader" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div id="customPopupIcon" class="w-10 h-10 rounded-full flex items-center justify-center">
                        <!-- Icon will be inserted here -->
                    </div>
                    <h3 id="customPopupTitle" class="text-xl font-bold text-gray-900 dark:text-white"></h3>
                </div>
            </div>

            <!-- Popup Body -->
            <div id="customPopupBody" class="px-6 py-5">
                <p id="customPopupMessage" class="text-gray-600 dark:text-gray-300"></p>
                <div id="customPopupInput" class="mt-4 hidden">
                    <input type="text" id="customPopupInputField"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="">
                </div>
            </div>

            <!-- Popup Footer -->
            <div id="customPopupFooter"
                class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 rounded-b-xl flex justify-end gap-3">
                <!-- Buttons will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Toast Notification System (Bottom Right) -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-[150] flex flex-col gap-3 pointer-events-none">
        <!-- Toasts will be inserted here -->
    </div>

    <script>
        // Toast Notification System
        const ToastSystem = {
            container: null,
            toasts: [],

            init() {
                this.container = document.getElementById('toastContainer');
            },

            show(options) {
                const {
                    message = '',
                    type = 'info', // info, success, warning, error, otp
                    title = null,
                    duration = 5000, // Auto-hide after 5 seconds
                    icon = null,
                    closable = true
                } = options;

                // Create toast element
                const toast = document.createElement('div');
                const toastId = 'toast-' + Date.now();
                toast.id = toastId;
                toast.className = 'pointer-events-auto transform transition-all duration-300 translate-x-full opacity-0';

                // Type-specific styling
                const styles = {
                    info: { bg: 'bg-blue-600', icon: 'fa-info-circle', iconBg: 'bg-blue-500' },
                    success: { bg: 'bg-green-600', icon: 'fa-check-circle', iconBg: 'bg-green-500' },
                    warning: { bg: 'bg-yellow-500', icon: 'fa-exclamation-triangle', iconBg: 'bg-yellow-400' },
                    error: { bg: 'bg-red-600', icon: 'fa-times-circle', iconBg: 'bg-red-500' },
                    otp: { bg: 'bg-gradient-to-r from-primary-600 to-purple-600', icon: 'fa-envelope', iconBg: 'bg-primary-500' }
                };

                const style = styles[type] || styles.info;

                toast.innerHTML = `
                    <div class="${style.bg} rounded-xl shadow-2xl p-4 min-w-[320px] max-w-[380px] flex items-start gap-3">
                        <div class="${style.iconBg} w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas ${icon || style.icon} text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            ${title ? `<h4 class="text-white font-semibold text-sm">${title}</h4>` : ''}
                            <p class="text-white/90 text-sm ${title ? 'mt-0.5' : ''}">${message}</p>
                        </div>
                        ${closable ? `
                            <button onclick="ToastSystem.close('${toastId}')" class="text-white/70 hover:text-white transition-colors flex-shrink-0">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                    </div>
                `;

                // Add to container
                this.container.appendChild(toast);
                this.toasts.push(toastId);

                // Animate in
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                    toast.classList.add('translate-x-0', 'opacity-100');
                });

                // Auto-hide after duration
                if (duration > 0) {
                    setTimeout(() => {
                        this.close(toastId);
                    }, duration);
                }

                return toastId;
            },

            close(toastId) {
                const toast = document.getElementById(toastId);
                if (toast) {
                    // Animate out
                    toast.classList.remove('translate-x-0', 'opacity-100');
                    toast.classList.add('translate-x-full', 'opacity-0');

                    // Remove after animation
                    setTimeout(() => {
                        toast.remove();
                        this.toasts = this.toasts.filter(id => id !== toastId);
                    }, 300);
                }
            },

            // Shorthand methods
            info(message, title = null, duration = 5000) {
                return this.show({ message, title, type: 'info', duration });
            },

            success(message, title = null, duration = 5000) {
                return this.show({ message, title, type: 'success', duration });
            },

            warning(message, title = null, duration = 5000) {
                return this.show({ message, title, type: 'warning', duration });
            },

            error(message, title = null, duration = 5000) {
                return this.show({ message, title, type: 'error', duration });
            },

            otp(email, duration = 7000) {
                return this.show({
                    message: `OTP has been sent to ${email}. Please check your inbox.`,
                    title: '📧 OTP Sent Successfully!',
                    type: 'otp',
                    duration,
                    icon: 'fa-paper-plane'
                });
            }
        };

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            ToastSystem.init();
        });

        // Global shorthand functions for Toast
        window.showToast = (options) => ToastSystem.show(options);
        window.toastInfo = (message, title, duration) => ToastSystem.info(message, title, duration);
        window.toastSuccess = (message, title, duration) => ToastSystem.success(message, title, duration);
        window.toastWarning = (message, title, duration) => ToastSystem.warning(message, title, duration);
        window.toastError = (message, title, duration) => ToastSystem.error(message, title, duration);
        window.toastOtp = (email, duration) => ToastSystem.otp(email, duration);
    </script>

    <!-- Popup System JavaScript -->
    <script>
        // Custom Popup System
        const CustomPopup = {
            element: null,
            content: null,
            header: null,
            title: null,
            icon: null,
            body: null,
            message: null,
            input: null,
            inputField: null,
            footer: null,
            callback: null,
            inputCallback: null,

            init() {
                this.element = document.getElementById('customPopup');
                this.content = document.getElementById('customPopupContent');
                this.header = document.getElementById('customPopupHeader');
                this.title = document.getElementById('customPopupTitle');
                this.icon = document.getElementById('customPopupIcon');
                this.body = document.getElementById('customPopupBody');
                this.message = document.getElementById('customPopupMessage');
                this.input = document.getElementById('customPopupInput');
                this.inputField = document.getElementById('customPopupInputField');
                this.footer = document.getElementById('customPopupFooter');

                // Close on background click
                this.element.addEventListener('click', (e) => {
                    if (e.target === this.element) {
                        this.close();
                    }
                });

                // ESC key to close
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !this.element.classList.contains('hidden')) {
                        this.close();
                    }
                });
            },

            show(options) {
                const {
                    title = 'Notification',
                    message = '',
                    type = 'info', // info, success, warning, error, confirm, prompt
                    buttons = [],
                    icon = null,
                    input = false,
                    inputPlaceholder = '',
                    inputValue = '',
                    onConfirm = null,
                    onCancel = null,
                    closeOnBackdrop = true
                } = options;

                // Set title
                this.title.textContent = title;

                // Set icon based on type
                const icons = {
                    info: { bg: 'bg-blue-100 dark:bg-blue-900/30', icon: 'fa-info-circle text-blue-600 dark:text-blue-400' },
                    success: { bg: 'bg-green-100 dark:bg-green-900/30', icon: 'fa-check-circle text-green-600 dark:text-green-400' },
                    warning: { bg: 'bg-yellow-100 dark:bg-yellow-900/30', icon: 'fa-exclamation-triangle text-yellow-600 dark:text-yellow-400' },
                    error: { bg: 'bg-red-100 dark:bg-red-900/30', icon: 'fa-times-circle text-red-600 dark:text-red-400' },
                    confirm: { bg: 'bg-orange-100 dark:bg-orange-900/30', icon: 'fa-question-circle text-orange-600 dark:text-orange-400' },
                    prompt: { bg: 'bg-purple-100 dark:bg-purple-900/30', icon: 'fa-edit text-purple-600 dark:text-purple-400' }
                };

                const iconConfig = icons[type] || icons.info;
                this.icon.className = `w-10 h-10 rounded-full flex items-center justify-center ${iconConfig.bg}`;
                this.icon.innerHTML = `<i class="fas ${icon || iconConfig.icon} text-xl"></i>`;

                // Set message
                this.message.innerHTML = message;

                // Handle input
                if (input) {
                    this.input.classList.remove('hidden');
                    this.inputField.value = inputValue;
                    this.inputField.placeholder = inputPlaceholder;
                    this.inputCallback = onConfirm;

                    // Focus input after showing
                    setTimeout(() => this.inputField.focus(), 100);

                    // Enter key to confirm
                    this.inputField.onkeydown = (e) => {
                        if (e.key === 'Enter') {
                            this.handleInputConfirm();
                        }
                    };
                } else {
                    this.input.classList.add('hidden');
                    this.inputField.value = '';
                }

                // Default buttons based on type
                let defaultButtons = [];
                if (type === 'confirm') {
                    defaultButtons = [
                        { text: 'Cancel', class: 'secondary', action: 'cancel' },
                        { text: 'Confirm', class: 'primary', action: 'confirm' }
                    ];
                } else if (type === 'prompt') {
                    defaultButtons = [
                        { text: 'Cancel', class: 'secondary', action: 'cancel' },
                        { text: 'Submit', class: 'primary', action: 'confirm' }
                    ];
                } else {
                    defaultButtons = [
                        { text: 'OK', class: 'primary', action: 'close' }
                    ];
                }

                const finalButtons = buttons.length > 0 ? buttons : defaultButtons;

                // Clear and create buttons
                this.footer.innerHTML = '';
                finalButtons.forEach(btn => {
                    const button = document.createElement('button');
                    button.textContent = btn.text || 'Button';

                    const classes = {
                        primary: 'px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors',
                        secondary: 'px-5 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors',
                        success: 'px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors',
                        danger: 'px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors',
                        warning: 'px-5 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors'
                    };

                    button.className = classes[btn.class || 'secondary'] || classes.secondary;

                    button.onclick = () => {
                        if (btn.action === 'confirm' && input) {
                            this.handleInputConfirm();
                        } else if (btn.action === 'confirm' && onConfirm) {
                            onConfirm();
                            this.close();
                        } else if (btn.action === 'cancel' && onCancel) {
                            onCancel();
                            this.close();
                        } else if (btn.callback && typeof btn.callback === 'function') {
                            btn.callback();
                            if (btn.closeAfter !== false) {
                                this.close();
                            }
                        } else {
                            this.close();
                        }
                    };

                    this.footer.appendChild(button);
                });

                // Store callbacks
                this.callback = { onConfirm, onCancel };

                // Show popup
                this.element.classList.remove('hidden');
                this.content.classList.add('animate-scale-in');
            },

            handleInputConfirm() {
                const value = this.inputField.value.trim();
                if (this.inputCallback) {
                    this.inputCallback(value);
                }
                this.close();
            },

            close() {
                this.element.classList.add('hidden');
                this.content.classList.remove('animate-scale-in');
                this.inputField.value = '';
                this.inputCallback = null;
                this.callback = null;
            },

            // Shorthand methods
            alert(message, title = 'Alert', type = 'info') {
                this.show({ title, message, type, buttons: [{ text: 'OK', class: 'primary', action: 'close' }] });
            },

            confirm(message, title = 'Confirm', onConfirm = null, onCancel = null) {
                this.show({
                    title,
                    message,
                    type: 'confirm',
                    onConfirm,
                    onCancel,
                    buttons: [
                        { text: 'Cancel', class: 'secondary', action: 'cancel' },
                        { text: 'Confirm', class: 'primary', action: 'confirm' }
                    ]
                });
            },

            success(message, title = 'Success') {
                this.show({ title, message, type: 'success' });
            },

            error(message, title = 'Error') {
                this.show({ title, message, type: 'error' });
            },

            warning(message, title = 'Warning') {
                this.show({ title, message, type: 'warning' });
            },

            prompt(message, title = 'Input Required', placeholder = '', defaultValue = '', onSubmit = null) {
                this.show({
                    title,
                    message,
                    type: 'prompt',
                    input: true,
                    inputPlaceholder: placeholder,
                    inputValue: defaultValue,
                    onConfirm: onSubmit
                });
            }
        };

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            CustomPopup.init();
        });

        // Global shorthand functions
        window.showPopup = (options) => CustomPopup.show(options);
        window.popupAlert = (message, title, type) => CustomPopup.alert(message, title, type);
        window.popupConfirm = (message, title, onConfirm, onCancel) => {
            // If callbacks are provided, use the old callback style
            if (typeof onConfirm === 'function' || typeof onCancel === 'function') {
                return CustomPopup.confirm(message, title, onConfirm, onCancel);
            }
            // Otherwise, return a Promise for async/await usage
            return new Promise((resolve) => {
                CustomPopup.confirm(message, title, 
                    () => resolve(true),   // onConfirm
                    () => resolve(false)   // onCancel
                );
            });
        };
        window.popupSuccess = (message, title) => CustomPopup.success(message, title);
        window.popupError = (message, title) => CustomPopup.error(message, title);
        window.popupWarning = (message, title) => CustomPopup.warning(message, title);
        window.popupPrompt = (message, title, placeholder, defaultValue, onSubmit) => CustomPopup.prompt(message, title, placeholder, defaultValue, onSubmit);
    </script>

    <style>
        @keyframes scale-in {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scale-in 0.2s ease-out;
        }

        /* Back to Top Button */
        #backToTop {
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
        }

        #backToTop.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        #backToTop:hover {
            transform: translateY(-5px);
        }
    </style>

    <!-- Back to Top Button - Desktop Only -->
    <button id="backToTop"
        class="hidden lg:flex fixed bottom-6 right-6 z-50 w-12 h-12 bg-primary-600 hover:bg-primary-700 text-white rounded-full shadow-lg hover:shadow-xl items-center justify-center group"
        onclick="scrollToTop()" aria-label="Back to top">
        <i class="fas fa-arrow-up text-lg group-hover:animate-bounce"></i>
    </button>

    <script>
        // Back to Top Button functionality
        const backToTopButton = document.getElementById('backToTop');

        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Notifications Alpine.js Component
        function notificationsDropdown() {
            const instance = {
                open: false,
                loading: false,
                notifications: [],
                unreadCount: 0,

                async init() {
                    await this.fetchNotifications();
                    await this.fetchUnreadCount();
                    window.notificationsDropdownInstance = instance;
                    console.log('Notifications initialized. Unread count:', this.unreadCount);

                    // Listen for data-changed events from any page to refresh notifications instantly
                    window.addEventListener('trackflow:data-changed', () => {
                        this.fetchUnreadCount();
                        this.fetchNotifications();
                    });

                    // Refresh notifications when user returns to the tab
                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) {
                            this.fetchUnreadCount();
                            if (this.open) {
                                this.fetchNotifications();
                            }
                        }
                    });
                },

                toggleDropdown() {
                    this.open = !this.open;
                    if (this.open) {
                        this.fetchNotifications();
                        this.fetchUnreadCount();
                    }
                },

                async fetchNotifications() {
                    try {
                        const response = await fetch('/notifications/list?filter=unread&limit=10');
                        const data = await response.json();
                        if (data.success) {
                            this.notifications = data.notifications || [];
                            // After fetching, check for OTP notifications and attempt autofill
                            try {
                                if (typeof this.checkForOtpNotifications === 'function') {
                                    this.checkForOtpNotifications();
                                }
                            } catch (e) { /* ignore */ }
                        }
                    } catch (error) {
                        console.error('Error fetching notifications:', error);
                    }
                },

                // Check fetched notifications for OTP patterns and autofill delete OTP inputs when visible
                checkForOtpNotifications() {
                    try {
                        if (!Array.isArray(this.notifications) || this.notifications.length === 0) return;
                        // Find first unread notification that contains an 8-digit OTP
                        for (const n of this.notifications) {
                            if (!n || n.is_read) continue;
                            const msg = (n.message || '') + ' ' + (n.title || '');
                            // Look for 8-digit numeric token
                            const match = msg.match(/\b(\d{8})\b/);
                            if (match) {
                                // Avoid duplicate autofill for the same notification
                                if (this._lastAutoFilledNotificationId && this._lastAutoFilledNotificationId === n.id) return;
                                this._lastAutoFilledNotificationId = n.id;
                                // Call global autofill helper if available
                                if (window && typeof window.autoFillDeleteOtp === 'function') {
                                    window.autoFillDeleteOtp(match[1]);
                                }
                                return;
                            }
                        }
                    } catch (e) {
                        console.error('checkForOtpNotifications error', e);
                    }
                },

                async fetchUnreadCount() {
                    try {
                        const response = await fetch('/notifications/unread-count');
                        const data = await response.json();
                        console.log('Unread count response:', data);
                        if (data.success) {
                            this.unreadCount = data.count || data.unread_count || 0;
                            console.log('Updated unread count to:', this.unreadCount);
                        }
                    } catch (error) {
                        console.error('Error fetching unread count:', error);
                    }
                },

                async handleNotificationClick(notification) {
                    // Mark as read
                    if (!notification.is_read) {
                        await this.markAsRead(notification.id);
                    }

                    // Navigate to action URL if exists
                    if (notification.action_url) {
                        window.location.href = notification.action_url;
                    }
                },

                async markAsRead(notificationId) {
                    try {
                        const response = await fetch(`/notifications/${notificationId}/mark-read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Update notification in list
                            const notification = this.notifications.find(n => n.id === notificationId);
                            if (notification) {
                                notification.is_read = true;
                            }
                            // Instantly refresh unread count and notifications
                            await this.fetchUnreadCount();
                            await this.fetchNotifications();
                        }
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                    }
                },

                async markAllAsRead() {
                    try {
                        const response = await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Update all notifications in list
                            this.notifications.forEach(n => n.is_read = true);
                            this.unreadCount = 0;
                            // Instantly refresh
                            await this.fetchUnreadCount();
                            await this.fetchNotifications();
                            // Show success message
                            showToast('All notifications marked as read', 'success');
                        }
                    } catch (error) {
                        console.error('Error marking all as read:', error);
                    }
                },

                async deleteNotification(notificationId) {
                    // Show custom popup confirmation
                    const self = this;
                    if (typeof popupConfirm === 'function') {
                        popupConfirm(
                            'Are you sure you want to delete this notification?',
                            'Delete Notification',
                            async function () {
                                // onConfirm callback
                                try {
                                    const response = await fetch(`/notifications/${notificationId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        }
                                    });
                                    const data = await response.json();
                                    if (data.success) {
                                        // Remove from list
                                        self.notifications = self.notifications.filter(n => n.id !== notificationId);
                                        // Instantly refresh unread count and notifications
                                        await self.fetchUnreadCount();
                                        await self.fetchNotifications();

                                        // Show success message
                                        if (typeof popupSuccess === 'function') {
                                            popupSuccess('Notification deleted successfully', 'Success');
                                        }
                                    }
                                } catch (error) {
                                    console.error('Error deleting notification:', error);
                                    if (typeof popupError === 'function') {
                                        popupError('Failed to delete notification', 'Error');
                                    }
                                }
                            },
                            null // onCancel callback
                        );
                        return;
                    }

                    // Fallback if popupConfirm is not available
                    if (!confirm('Are you sure you want to delete this notification?')) return;

                    try {
                        const response = await fetch(`/notifications/${notificationId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Remove from list
                            this.notifications = this.notifications.filter(n => n.id !== notificationId);
                            // Instantly refresh unread count and notifications
                            await this.fetchUnreadCount();
                            await this.fetchNotifications();
                        }
                    } catch (error) {
                        console.error('Error deleting notification:', error);
                    }
                },

                async deleteAllNotifications() {
                    // Show custom popup confirmation
                    const self = this;
                    if (typeof popupConfirm === 'function') {
                        popupConfirm(
                            'Are you sure you want to delete all notifications? This action cannot be undone.',
                            'Delete All Notifications',
                            async function () {
                                // onConfirm callback
                                try {
                                    const response = await fetch('/notifications/delete-all', {
                                        method: 'DELETE',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        }
                                    });
                                    const data = await response.json();
                                    if (data.success) {
                                        // Clear all notifications
                                        self.notifications = [];
                                        self.unreadCount = 0;
                                        // Instantly refresh
                                        await self.fetchUnreadCount();
                                        await self.fetchNotifications();

                                        // Show success message
                                        if (typeof popupSuccess === 'function') {
                                            popupSuccess('All notifications deleted successfully', 'Success');
                                        }
                                    }
                                } catch (error) {
                                    console.error('Error deleting all notifications:', error);
                                    if (typeof popupError === 'function') {
                                        popupError('Failed to delete notifications', 'Error');
                                    }
                                }
                            },
                            null // onCancel callback
                        );
                        return;
                    }

                    // Fallback if popupConfirm is not available
                    if (!confirm('Are you sure you want to delete all notifications?')) return;

                    try {
                        const response = await fetch('/notifications/delete-all', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Clear all notifications
                            this.notifications = [];
                            this.unreadCount = 0;
                            // Instantly refresh
                            await this.fetchUnreadCount();
                            await this.fetchNotifications();
                        }
                    } catch (error) {
                        console.error('Error deleting all notifications:', error);
                    }
                },

                formatTime(timestamp) {
                    const date = new Date(timestamp);
                    const now = new Date();
                    const diff = Math.floor((now - date) / 1000); // seconds

                    if (diff < 60) return 'Just now';
                    if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
                    if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
                    if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';

                    return date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
                    });
                }
            };
            return instance;
        }

        // Logout Confirmation
        function confirmLogout() {
            const modal = document.createElement('div');
            modal.id = 'logoutModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fade-in';
            modal.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4 animate-scale-in">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 dark:bg-red-900/20 rounded-full">
                            <i class="fas fa-sign-out-alt text-3xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-2">Logout Confirmation</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-center mb-6">
                            Are you sure you want to logout? You will be redirected to the home page.
                        </p>
                        <div class="flex gap-3">
                            <button onclick="closeLogoutModal()" id="logoutCancelBtn"
                                class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold rounded-lg transition-colors">
                                CANCEL
                            </button>
                            <button onclick="performLogout()" id="logoutOkBtn"
                                class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                                <span>OK</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutModal');
            if (modal) {
                modal.classList.add('animate-fade-out');
                setTimeout(() => modal.remove(), 200);
            }
        }

        function performLogout() {
            // Show loading effect on OK button
            const okBtn = document.getElementById('logoutOkBtn');
            const cancelBtn = document.getElementById('logoutCancelBtn');
            if (okBtn) {
                okBtn.disabled = true;
                okBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Logging out...</span>';
            }
            if (cancelBtn) {
                cancelBtn.disabled = true;
                cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("logout") }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';

            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Global Keyboard Shortcuts
        document.addEventListener('keydown', function (e) {
            // Don't trigger shortcuts when typing in input fields
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
                return;
            }

            // Ctrl + Shift + D for Toggle Dark Mode
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                toggleDarkMode();
                return;
            }

            // Ctrl + C for Community (only when not selecting text)
            if (e.ctrlKey && e.key === 'c' && !window.getSelection().toString()) {
                e.preventDefault();
                window.location.href = '{{ route("community.index") }}';
            }
        });
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fade-out {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        @keyframes scale-in {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.2s ease-out;
        }

        .animate-fade-out {
            animation: fade-out 0.2s ease-out;
        }

        .animate-scale-in {
            animation: scale-in 0.3s ease-out;
        }
    </style>

    <script>
        // Comprehensive prevention of back/forward navigation to auth pages when logged in
        (function () {
            // Check if user is logged in
            @if(!session('user_id'))
                // If no session, redirect to landing page
                window.location.href = '{{ route('home') }}';
                return;
            @endif

            // Prevent browser back button from going to login/register pages
            // Push current state to history to override back navigation
            if (window.history && window.history.pushState) {
                // Replace and push state to prevent back navigation
                window.history.replaceState(null, null, window.location.href);
                window.history.pushState(null, null, window.location.href);

                // Listen for popstate (back/forward button)
                window.addEventListener('popstate', function (event) {
                    // Check if still authenticated
                    @if(session('user_id'))
                        // Push state again to prevent navigation
                        window.history.pushState(null, null, window.location.href);
                    @else
                        // If session lost, redirect to landing page
                        window.location.href = '{{ route('home') }}';
                    @endif
                });
            }

            // Prevent page from being cached by browser
            window.onbeforeunload = function () {
                // This ensures page is not cached
            };

            // Force page reload if accessed from cache (back button)
            window.onpageshow = function (event) {
                if (event.persisted) {
                    // Page was loaded from cache, reload it
                    window.location.reload();
                }
            };

            // Additional check on load
            window.onload = function () {
                @if(!session('user_id'))
                    window.location.href = '{{ route('home') }}';
                @endif
            };
        })();
    </script>

    <!-- Dynamic Island & Pull-to-Refresh JavaScript -->
    <script>
        (function() {
            'use strict';

            // Dynamic Island Controller
            const DynamicIsland = {
                pill: document.getElementById('dynamicIslandPill'),
                spinner: document.getElementById('diSpinner'),
                check: document.getElementById('diCheck'),
                text: document.getElementById('diText'),
                isActive: false,
                hideTimeout: null,
                lastClickedHref: null,
                lastClickTime: 0,

                show(message = 'Refreshing...') {
                    // Clear any pending hide timeout
                    if (this.hideTimeout) {
                        clearTimeout(this.hideTimeout);
                        this.hideTimeout = null;
                    }
                    
                    // Always update message immediately, even if already active
                    this.text.textContent = message;
                    this.spinner.style.display = 'block';
                    this.check.style.display = 'none';
                    
                    if (!this.isActive) {
                        this.isActive = true;
                        this.pill.classList.add('active', 'expanded');
                    }
                },

                success(message = 'Done!') {
                    this.text.textContent = message;
                    this.spinner.style.display = 'none';
                    this.check.style.display = 'flex';
                    
                    this.hideTimeout = setTimeout(() => {
                        this.hide();
                    }, 1200);
                },

                hide() {
                    this.pill.classList.remove('active', 'expanded');
                    setTimeout(() => {
                        this.isActive = false;
                        this.spinner.style.display = 'block';
                        this.check.style.display = 'none';
                    }, 400);
                }
            };

            // Make DynamicIsland available globally for immediate access
            window.DynamicIsland = DynamicIsland;

            // Page name mappings based on href patterns
            const pageNameMappings = {
                'dashboard': 'Dashboard',
                'transactions': 'Transactions',
                'budgets': 'Budgets',
                'categories': 'Categories',
                'goals': 'Goals',
                'reports': 'Reports',
                'group-expense': 'Group Expense',
                'community': 'Community',
                'settings': 'Settings',
                'help': 'Help & Support',
                'profile': 'Profile',
                'notifications': 'Notifications',
            };

            // Get page name from href
            function getPageName(href, linkText) {
                for (const [key, name] of Object.entries(pageNameMappings)) {
                    if (href.includes(key)) {
                        return name;
                    }
                }
                // Fallback to link text if no mapping found
                if (linkText && linkText.length > 0 && linkText.length < 30) {
                    return linkText.trim();
                }
                return null;
            }

            // Instant Navigation Click Handler - attaches to document for immediate response
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a[href]');
                if (!link) return;
                
                const href = link.getAttribute('href');
                
                // Skip invalid links
                if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
                
                // Skip logout links
                if (href.includes('logout')) return;
                
                // Skip if same page (hash navigation)
                if (href.startsWith('#')) return;
                
                // Get full URL for comparison
                let fullHref;
                try {
                    const linkUrl = new URL(href, window.location.origin);
                    if (linkUrl.origin !== window.location.origin) return; // Skip external links
                    fullHref = linkUrl.href;
                } catch(e) {
                    return;
                }
                
                // Check if link is within navigation areas
                const isNavLink = link.closest('aside') || 
                                  link.closest('#mobileBottomNav') || 
                                  link.closest('#mobileMenuPopup') ||
                                  link.closest('#mobileMenuContent') ||
                                  link.closest('nav') ||
                                  link.closest('[class*="sidebar"]') ||
                                  link.closest('header nav');
                
                if (isNavLink) {
                    const now = Date.now();
                    const linkText = link.textContent;
                    const pageName = getPageName(href, linkText);
                    
                    // Check if clicking the same link again (within 3 seconds window)
                    const isSameLink = DynamicIsland.lastClickedHref === fullHref;
                    const isRapidClick = (now - DynamicIsland.lastClickTime) < 3000;
                    
                    // Also check if user is already on this page
                    const isCurrentPage = window.location.href === fullHref;
                    
                    if (isCurrentPage || (isSameLink && isRapidClick)) {
                        // Same link clicked again or already on this page - show "Refreshing..."
                        DynamicIsland.show('Refreshing...');
                        sessionStorage.setItem('dynamicIslandNavClick', 'refresh');
                    } else {
                        // First click on this link - show "Page Opening..."
                        if (pageName) {
                            DynamicIsland.show(pageName + ' Opening...');
                            sessionStorage.setItem('dynamicIslandNavPage', pageName);
                            sessionStorage.removeItem('dynamicIslandNavClick');
                        } else {
                            DynamicIsland.show('Opening...');
                            sessionStorage.setItem('dynamicIslandNavClick', 'open');
                        }
                    }
                    
                    // Track this click
                    DynamicIsland.lastClickedHref = fullHref;
                    DynamicIsland.lastClickTime = now;
                }
            }, true); // Capture phase for instant response

            // Detect page reload (F5, Ctrl+R, browser refresh button)
            let isReloading = false;

            // Store reload state before unload
            window.addEventListener('beforeunload', function() {
                if (performance.navigation) {
                    sessionStorage.setItem('isReloading', 'true');
                }
            });

            // Check if page was reloaded or action was completed
            window.addEventListener('DOMContentLoaded', function() {
                const wasReloading = sessionStorage.getItem('isReloading');
                const actionType = sessionStorage.getItem('dynamicIslandAction');
                const navPageName = sessionStorage.getItem('dynamicIslandNavPage');
                const navClick = sessionStorage.getItem('dynamicIslandNavClick');
                const navType = performance.getEntriesByType('navigation')[0]?.type;
                
                // Check if it was a refresh click (same link clicked again)
                if (navClick === 'refresh') {
                    sessionStorage.removeItem('dynamicIslandNavClick');
                    sessionStorage.removeItem('isReloading');
                    sessionStorage.removeItem('dynamicIslandNavPage');
                    
                    DynamicIsland.show('Refreshing...');
                    
                    setTimeout(() => {
                        DynamicIsland.success('Updated!');
                    }, 300);
                }
                // Check if it was an open click without page name
                else if (navClick === 'open') {
                    sessionStorage.removeItem('dynamicIslandNavClick');
                    sessionStorage.removeItem('isReloading');
                    
                    DynamicIsland.show('Opening...');
                    
                    setTimeout(() => {
                        DynamicIsland.success('Ready!');
                    }, 300);
                }
                // Check if there was a navigation from sidebar/menu with page name
                else if (navPageName) {
                    sessionStorage.removeItem('dynamicIslandNavPage');
                    sessionStorage.removeItem('isReloading');
                    
                    DynamicIsland.show(navPageName + ' Opening...');
                    
                    setTimeout(() => {
                        DynamicIsland.success(navPageName + ' Ready!');
                    }, 400);
                }
                // Check if there was a button action that triggered navigation
                else if (actionType) {
                    sessionStorage.removeItem('dynamicIslandAction');
                    sessionStorage.removeItem('isReloading');
                    
                    // Show success message based on action type
                    let successMessage = 'Done!';
                    if (actionType === 'save') successMessage = 'Saved!';
                    else if (actionType === 'create') successMessage = 'Created!';
                    else if (actionType === 'delete') successMessage = 'Deleted!';
                    else if (actionType === 'submit') successMessage = 'Submitted!';
                    else if (actionType === 'publish') successMessage = 'Published!';
                    else if (actionType === 'confirm') successMessage = 'Confirmed!';
                    else if (actionType === 'process') successMessage = 'Completed!';
                    
                    DynamicIsland.show(actionType === 'save' ? 'Saving...' : 
                                       actionType === 'create' ? 'Creating...' :
                                       actionType === 'delete' ? 'Deleting...' :
                                       actionType === 'submit' ? 'Submitting...' :
                                       actionType === 'publish' ? 'Publishing...' :
                                       actionType === 'confirm' ? 'Confirming...' : 'Processing...');
                    
                    setTimeout(() => {
                        DynamicIsland.success(successMessage);
                    }, 500);
                }
                // Check if it was a manual page reload
                else if (wasReloading === 'true' || navType === 'reload') {
                    sessionStorage.removeItem('isReloading');
                    DynamicIsland.show('Refreshing...');
                    
                    setTimeout(() => {
                        DynamicIsland.success('Updated!');
                    }, 800);
                }
            });

            // Keyboard shortcut for manual refresh with dynamic island
            document.addEventListener('keydown', function(e) {
                // F5 or Ctrl+R
                if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                    sessionStorage.setItem('isReloading', 'true');
                }
            });

            // Auto-attach Dynamic Island to final/submit buttons
            function attachDynamicIslandToButtons() {
                // Selectors for final/submit buttons
                const buttonSelectors = [
                    // By text content patterns (handled separately)
                    'button[type="submit"]',
                    'input[type="submit"]',
                    // Common button IDs
                    '[id*="submit"]',
                    '[id*="Submit"]',
                    '[id*="save"]',
                    '[id*="Save"]',
                    '[id*="create"]',
                    '[id*="Create"]',
                    '[id*="add"]',
                    '[id*="Add"]',
                    '[id*="update"]',
                    '[id*="Update"]',
                    '[id*="confirm"]',
                    '[id*="Confirm"]',
                    '[id*="delete"]',
                    '[id*="Delete"]',
                    '[id*="Btn"]:not([id*="Cancel"]):not([id*="cancel"]):not([id*="Close"]):not([id*="close"])',
                    // Common class patterns
                    '.btn-submit',
                    '.btn-save',
                    '.btn-create',
                    '.btn-confirm',
                    '.submit-btn',
                    '.save-btn',
                    // Gradient buttons (typically action buttons)
                    'button.bg-gradient-to-r',
                    'button[class*="from-purple-600"]',
                    'button[class*="from-green-600"]',
                    'button[class*="from-blue-600"]',
                    'button[class*="from-red-600"]',
                    'button[class*="from-primary"]',
                ];

                // Text patterns to match in button content
                const actionTextPatterns = [
                    'save', 'submit', 'create', 'add', 'update', 'confirm', 'publish',
                    'delete', 'remove', 'send', 'apply', 'done', 'ok', 'yes',
                    'transfer', 'deposit', 'withdraw', 'pay', 'post', 'share'
                ];

                // Excluded text patterns (cancel, close buttons)
                const excludeTextPatterns = ['cancel', 'close', 'back', 'no', 'dismiss'];

                // Get all buttons
                const allButtons = document.querySelectorAll('button, input[type="submit"], [role="button"]');
                
                allButtons.forEach(btn => {
                    // Skip if already attached
                    if (btn.dataset.dynamicIslandAttached) return;

                    const btnText = (btn.textContent || btn.value || '').toLowerCase().trim();
                    const btnId = (btn.id || '').toLowerCase();
                    const btnClass = (btn.className || '').toLowerCase();

                    // Check if it's an excluded button
                    const isExcluded = excludeTextPatterns.some(pattern => 
                        btnText.includes(pattern) || btnId.includes(pattern)
                    );
                    if (isExcluded) return;

                    // Check if it matches action patterns
                    let shouldAttach = false;

                    // Check by selector match
                    for (const selector of buttonSelectors) {
                        try {
                            if (btn.matches(selector)) {
                                shouldAttach = true;
                                break;
                            }
                        } catch (e) {}
                    }

                    // Check by text content
                    if (!shouldAttach) {
                        shouldAttach = actionTextPatterns.some(pattern => btnText.includes(pattern));
                    }

                    // Check for gradient backgrounds (action buttons)
                    if (!shouldAttach && btnClass.includes('bg-gradient')) {
                        shouldAttach = true;
                    }

                    if (shouldAttach) {
                        btn.dataset.dynamicIslandAttached = 'true';
                        btn.addEventListener('click', function(e) {
                            // Don't show if button is disabled
                            if (btn.disabled) return;

                            // Determine action type based on button text
                            let actionType = 'process';
                            if (btnText.includes('save') || btnText.includes('update')) {
                                actionType = 'save';
                            } else if (btnText.includes('create') || btnText.includes('add') || btnText.includes('post')) {
                                actionType = 'create';
                            } else if (btnText.includes('delete') || btnText.includes('remove')) {
                                actionType = 'delete';
                            } else if (btnText.includes('send') || btnText.includes('submit')) {
                                actionType = 'submit';
                            } else if (btnText.includes('transfer') || btnText.includes('pay')) {
                                actionType = 'process';
                            } else if (btnText.includes('publish')) {
                                actionType = 'publish';
                            } else if (btnText.includes('confirm') || btnText.includes('ok') || btnText.includes('yes')) {
                                actionType = 'confirm';
                            }

                            // Store action type for page load
                            sessionStorage.setItem('dynamicIslandAction', actionType);
                        });
                    }
                });
            }

            // Initial attachment
            document.addEventListener('DOMContentLoaded', attachDynamicIslandToButtons);

            // Re-attach when DOM changes (for dynamically added buttons)
            const observer = new MutationObserver(function(mutations) {
                let shouldReattach = false;
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        shouldReattach = true;
                    }
                });
                if (shouldReattach) {
                    setTimeout(attachDynamicIslandToButtons, 100);
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            // Expose function to manually attach to new buttons
            window.attachDynamicIslandToButtons = attachDynamicIslandToButtons;

            // Pull-to-Refresh for Mobile/Touch Devices
            const PullToRefresh = {
                indicator: document.getElementById('pullIndicator'),
                arrow: document.getElementById('pullArrow'),
                spinnerIcon: document.getElementById('pullSpinner'),
                startY: 0,
                currentY: 0,
                pulling: false,
                threshold: 80,
                maxPull: 120,
                enabled: true,

                init() {
                    // Only enable on touch devices
                    if (!('ontouchstart' in window)) return;

                    document.addEventListener('touchstart', this.onTouchStart.bind(this), { passive: true });
                    document.addEventListener('touchmove', this.onTouchMove.bind(this), { passive: false });
                    document.addEventListener('touchend', this.onTouchEnd.bind(this), { passive: true });
                },

                onTouchStart(e) {
                    // Only start if at top of page
                    if (window.scrollY > 5) {
                        this.enabled = false;
                        return;
                    }

                    // Don't trigger when any modal/popup is open
                    if (this.isModalOpen()) {
                        this.enabled = false;
                        return;
                    }

                    // Don't trigger on inputs, textareas, or contenteditable
                    const target = e.target;
                    if (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.isContentEditable) {
                        this.enabled = false;
                        return;
                    }

                    this.enabled = true;
                    this.startY = e.touches[0].clientY;
                    this.pulling = false;
                },

                // Check if any modal/popup is currently open
                isModalOpen() {
                    // Check for common modal patterns
                    const modalSelectors = [
                        // Fixed/absolute positioned modals that are visible
                        '.fixed:not(.hidden)[class*="z-50"]',
                        '.fixed:not(.hidden)[class*="z-[50]"]',
                        '.fixed:not(.hidden)[class*="z-[100]"]',
                        // Common modal IDs
                        '#addTransactionModal:not(.hidden)',
                        '#editTransactionModal:not(.hidden)',
                        '#createCategoryModal:not(.hidden)',
                        '#editCategoryModal:not(.hidden)',
                        '#addGoalModal:not(.hidden)',
                        '#editGoalModal:not(.hidden)',
                        '#addBudgetModal:not(.hidden)',
                        '#editBudgetModal:not(.hidden)',
                        '#createPostModal:not(.hidden)',
                        '#reportModal:not(.hidden)',
                        '#shareModal:not(.hidden)',
                        '#replyModal:not(.hidden)',
                        '#customPopup:not(.hidden)',
                        '#mobileMenu.active',
                        '#logoutModal',
                        // Alpine.js modals
                        '[x-show]:not([style*="display: none"])[class*="fixed"]',
                        // Generic modal classes
                        '.modal:not(.hidden)',
                        '.modal.show',
                        '.modal.active',
                        '[role="dialog"]:not(.hidden)',
                        // Backdrop indicators
                        '.bg-black\\/60:not(.hidden)',
                        '.bg-black\\/50:not(.hidden)',
                    ];

                    for (const selector of modalSelectors) {
                        try {
                            const el = document.querySelector(selector);
                            if (el && el.offsetParent !== null) {
                                // Element exists and is visible
                                return true;
                            }
                        } catch (e) {
                            // Invalid selector, skip
                        }
                    }

                    // Also check if body overflow is hidden (common when modals are open)
                    if (document.body.style.overflow === 'hidden') {
                        return true;
                    }

                    return false;
                },

                onTouchMove(e) {
                    if (!this.enabled || window.scrollY > 5) return;

                    this.currentY = e.touches[0].clientY;
                    const pullDistance = this.currentY - this.startY;

                    if (pullDistance > 10) {
                        this.pulling = true;
                        e.preventDefault();

                        // Calculate progress (0 to 1)
                        const progress = Math.min(pullDistance / this.maxPull, 1);
                        const translateY = Math.min(pullDistance * 0.5, this.maxPull * 0.5);

                        // Update indicator position and visibility
                        this.indicator.style.transform = `translateY(${translateY - 100}px) scale(${0.5 + progress * 0.5})`;
                        this.indicator.classList.add('visible');

                        // Rotate arrow based on progress
                        const rotation = progress >= 1 ? 180 : progress * 180;
                        this.arrow.style.transform = `rotate(${rotation}deg)`;
                    }
                },

                onTouchEnd() {
                    if (!this.pulling) return;

                    const pullDistance = this.currentY - this.startY;

                    if (pullDistance >= this.threshold) {
                        // Trigger refresh
                        this.triggerRefresh();
                    } else {
                        // Cancel pull
                        this.reset();
                    }

                    this.pulling = false;
                },

                triggerRefresh() {
                    // Show refreshing state
                    this.indicator.classList.add('refreshing');
                    this.arrow.style.display = 'none';
                    this.spinnerIcon.style.display = 'block';
                    this.indicator.style.transform = 'translateY(0) scale(1)';

                    // Store reload state for dynamic island
                    sessionStorage.setItem('isReloading', 'true');

                    // Reload page after brief delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                },

                reset() {
                    this.indicator.classList.remove('visible', 'refreshing');
                    this.indicator.style.transform = 'translateY(-100px) scale(0.5)';
                    this.arrow.style.transform = 'rotate(0deg)';
                    this.arrow.style.display = 'block';
                    this.spinnerIcon.style.display = 'none';
                }
            };

            // Initialize Pull-to-Refresh
            PullToRefresh.init();

            // Expose to global scope for manual triggering
            window.DynamicIsland = DynamicIsland;
            window.refreshWithIsland = function() {
                sessionStorage.setItem('isReloading', 'true');
                DynamicIsland.show('Refreshing...');
                setTimeout(() => {
                    window.location.reload();
                }, 300);
            };
        })();
    </script>

    @include('partials.pwa-install-prompt')
    {{-- Always include modals section for camera/cropper support --}}
    @yield('modals')
    @stack('scripts')
</body>

</html>