<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrackFlow - Smart Financial Management</title>
    <link rel="icon" type="image/png" href="{{ asset('trackflow-main/fav-icon.png') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        /* Dark Background with Gradient */
        body {
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Prevent horizontal overflow globally */
        html {
            overflow-x: hidden;
        }

        html,
        body {
            max-width: 100vw;
        }

        /* Glassmorphism Styles */
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .glass-strong {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-pattern {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }

        /* Mobile Menu Animation */
        .mobile-menu {
            transition: all 0.3s ease-in-out;
        }

        /* Floating Animation */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        /* Pulse Animation */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Feature Image Styles */
        .feature-image-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
        }

        /* ============================================
           RESPONSIVE DESIGN - Universal Device Support
           ============================================ */

        /* Extra Small Devices (phones, less than 576px) */
        @media (max-width: 575.98px) {
            .hero-title {
                font-size: 1.875rem !important;
                /* 30px */
                line-height: 1.2;
            }

            .hero-subtitle {
                font-size: 1rem !important;
            }

            .section-title {
                font-size: 1.75rem !important;
                /* 28px */
            }

            .feature-title {
                font-size: 1.5rem !important;
                /* 24px */
            }

            .stats-container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .stats-container>div {
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                padding-bottom: 1rem;
            }

            .stats-container>div:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .hero-buttons a {
                width: 100%;
            }

            .feature-badge {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }

            .footer-grid {
                grid-template-columns: 1fr !important;
            }

            .contact-grid {
                gap: 1.5rem;
            }

            /* Modal responsive */
            .modal-content-custom {
                max-width: 95vw !important;
                margin: 1rem;
            }

            .modal-content-custom h2 {
                font-size: 1.25rem !important;
            }

            .modal-content-custom h3 {
                font-size: 1.125rem !important;
            }

            .modal-content-custom p,
            .modal-content-custom li {
                font-size: 0.875rem;
            }

            /* Navigation */
            .nav-logo-text {
                font-size: 1.25rem !important;
            }

            .nav-logo-img {
                width: 2.5rem;
                height: 2.5rem;
            }

            /* Cards */
            .about-card {
                padding: 1.5rem;
            }

            .about-card h3 {
                font-size: 1.25rem;
            }

            /* Feature images */
            .feature-image-wrapper {
                margin: 0 -1rem;
            }

            .feature-image-wrapper img {
                border-radius: 0.75rem;
            }

            /* Contact form */
            .contact-form input,
            .contact-form textarea {
                padding: 0.75rem 1rem;
            }

            /* Scroll to top button */
            #scrollToTop {
                bottom: 1rem;
                right: 1rem;
                width: 2.5rem;
                height: 2.5rem;
            }
        }

        /* Small Devices (landscape phones, 576px and up) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .hero-title {
                font-size: 2.25rem !important;
            }

            .section-title {
                font-size: 2rem !important;
            }

            .stats-container {
                justify-content: center;
                flex-wrap: wrap;
            }

            .footer-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        /* Medium Devices (tablets, 768px and up) */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .hero-title {
                font-size: 2.75rem !important;
            }

            .footer-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }

            .footer-grid>div:nth-child(3),
            .footer-grid>div:nth-child(4) {
                margin-top: 1.5rem;
            }
        }

        /* Large Devices (desktops, 992px and up) */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .hero-title {
                font-size: 3rem !important;
            }
        }

        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .card-hover:hover {
                transform: none;
            }

            .btn-primary:hover {
                transform: none;
            }


        }

        /* Landscape orientation fixes */
        @media (max-height: 500px) and (orientation: landscape) {
            .hero-pattern {
                padding-top: 5rem;
                padding-bottom: 2rem;
            }

            .float-animation {
                animation: none;
            }
        }

        /* Print styles */
        @media print {

            .glass,
            .glass-strong {
                background: white !important;
                border: 1px solid #ddd !important;
            }

            body {
                background: white !important;
            }

            .text-white,
            .text-gray-200,
            .text-gray-300 {
                color: #333 !important;
            }
        }

        /* Safe area insets for notched devices */
        @supports (padding: max(0px)) {
                body {
                    padding-left: max(0px, env(safe-area-inset-left));
                    padding-right: max(0px, env(safe-area-inset-right));
                }

                nav {
                    padding-top: max(0px, env(safe-area-inset-top));
                }

                footer {
                    padding-bottom: max(1rem, env(safe-area-inset-bottom));
                }
            }

            /* Fix horizontal overflow on all sections */
            section, nav, footer {
                max-width: 100vw;
                overflow-x: hidden;
            }

            /* Prevent AOS animations from causing horizontal overflow */
            [data-aos] {
                overflow-x: hidden;
                overflow-y: visible;
            }

            /* Ensure all containers stay within viewport */
            .max-w-7xl {
                max-width: min(80rem, 100vw);
            }

            /* Allow float animation to move without clipping */
            .float-animation {
                margin-top: 20px;
                margin-bottom: 20px;
            }
    </style>
</head>

<body class="scroll-smooth overflow-x-hidden" x-data="{ mobileMenuOpen: false }">

    <!-- Header/Navigation -->
    <nav class="fixed w-full top-0 z-50 glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow Logo"
                        class="w-10 h-10 sm:w-12 sm:h-12 object-contain nav-logo-img">
                    <span class="text-xl sm:text-2xl font-bold text-white nav-logo-text">TrackFlow</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-gray-200 hover:text-white font-medium transition">About</a>
                    <a href="#features" class="text-gray-200 hover:text-white font-medium transition">Features</a>
                    <a href="#contact" class="text-gray-200 hover:text-white font-medium transition">Contact</a>
                    <a href="{{ route('login') }}"
                        class="text-gray-200 hover:text-white font-medium transition">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary text-white px-6 py-2.5 rounded-lg font-medium">
                        Get Started
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white focus:outline-none">
                    <i class="fas text-2xl" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="md:hidden glass-strong border-t border-white/10">
            <div class="px-4 py-6 space-y-4">
                <a href="#about" @click="mobileMenuOpen = false"
                    class="block text-gray-200 hover:text-white font-medium transition">About</a>
                <a href="#features" @click="mobileMenuOpen = false"
                    class="block text-gray-200 hover:text-white font-medium transition">Features</a>
                <a href="#contact" @click="mobileMenuOpen = false"
                    class="block text-gray-200 hover:text-white font-medium transition">Contact</a>
                <a href="{{ route('login') }}"
                    class="block text-gray-200 hover:text-white font-medium transition">Login</a>
                <a href="{{ route('register') }}"
                    class="block btn-primary text-white px-6 py-2.5 rounded-lg font-medium text-center">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-pattern pt-24 sm:pt-28 md:pt-32 pb-12 sm:pb-16 md:pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
                <!-- Left Content -->
                <div class="text-white" data-aos="fade-right">
                    <h1
                        class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 sm:mb-6 leading-tight hero-title">
                        Smart Financial Management Made <span class="text-yellow-300">Easy</span>
                    </h1>
                    <p class="text-base sm:text-lg md:text-xl mb-6 sm:mb-8 text-gray-100 hero-subtitle">
                        Take control of your finances with TrackFlow. Track expenses, manage budgets, and achieve your
                        financial goals with our powerful yet simple platform.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 hero-buttons">
                        <a href="{{ route('register') }}"
                            class="bg-white text-purple-600 px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold text-base sm:text-lg hover:bg-gray-100 transition transform hover:scale-105 text-center">
                            <i class="fas fa-rocket mr-2"></i> Get Started
                        </a>
                        <a href="#"
                            class="bg-purple-800 bg-opacity-50 backdrop-blur-sm text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold text-base sm:text-lg hover:bg-opacity-70 transition transform hover:scale-105 text-center border-2 border-white border-opacity-20">
                            <i class="fas fa-download mr-2"></i> Download APK
                        </a>
                    </div>
                    <div
                        class="mt-6 sm:mt-8 flex flex-wrap items-center justify-center sm:justify-start gap-6 sm:gap-8 stats-container">
                        <div class="text-center sm:text-left">
                            <p class="text-2xl sm:text-3xl font-bold">10K+</p>
                            <p class="text-gray-200 text-sm sm:text-base">Active Users</p>
                        </div>
                        <div class="text-center sm:text-left">
                            <p class="text-2xl sm:text-3xl font-bold">4.8/5</p>
                            <p class="text-gray-200 text-sm sm:text-base">User Rating</p>
                        </div>
                        <div class="text-center sm:text-left">
                            <p class="text-2xl sm:text-3xl font-bold">50+</p>
                            <p class="text-gray-200 text-sm sm:text-base">Countries</p>
                        </div>
                    </div>
                </div>

                <!-- Right Image -->
                <div class="relative mt-8 md:mt-0 lg:mt-8" data-aos="fade-left">
                    <div class="float-animation relative lg:py-10 lg:px-12">
                        <img src="{{ asset('img/dashboard.png') }}" alt="Dashboard Preview"
                            class="w-full max-w-lg mx-auto md:max-w-none rounded-xl sm:rounded-2xl shadow-2xl border-2 sm:border-4 border-white/20"
                            loading="lazy" decoding="async">
                        <!-- Floating Cards -->
                        <div
                            class="absolute -bottom-4 -left-4 glass-strong p-4 rounded-xl shadow-xl pulse-animation hidden lg:block">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                                    <i class="fas fa-arrow-up text-green-400 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-300">Income</p>
                                    <p class="text-xl font-bold text-white">₹45,230</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-4 -right-4 glass-strong p-4 rounded-xl shadow-xl pulse-animation hidden lg:block"
                            style="animation-delay: 1s;">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                                    <i class="fas fa-arrow-down text-red-400 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-300">Expenses</p>
                                    <p class="text-xl font-bold text-white">₹12,840</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-12 sm:py-16 md:py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10 sm:mb-12 md:mb-16" data-aos="fade-up">
                <h2
                    class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-3 sm:mb-4 section-title">
                    Why Choose <span class="gradient-text">TrackFlow?</span></h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-300 max-w-3xl mx-auto px-2">
                    We provide comprehensive financial management tools designed to help you make better financial
                    decisions.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                <!-- Card 1 -->
                <div class="glass p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-lg card-hover about-card"
                    data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 gradient-bg rounded-lg sm:rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <i class="fas fa-shield-alt text-white text-lg sm:text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2 sm:mb-4">Secure & Private</h3>
                    <p class="text-gray-300 leading-relaxed text-sm sm:text-base">
                        Your financial data is encrypted and secured with bank-level security. We never share your data
                        with third parties.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="glass p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-lg card-hover about-card"
                    data-aos="fade-up" data-aos-delay="200">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 gradient-bg rounded-lg sm:rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <i class="fas fa-mobile-alt text-white text-lg sm:text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2 sm:mb-4">Mobile Friendly</h3>
                    <p class="text-gray-300 leading-relaxed text-sm sm:text-base">
                        Access your finances anytime, anywhere. Our responsive design works seamlessly on all devices.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="glass p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-lg card-hover about-card sm:col-span-2 md:col-span-1"
                    data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 gradient-bg rounded-lg sm:rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <i class="fas fa-chart-bar text-white text-lg sm:text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2 sm:mb-4">Smart Analytics</h3>
                    <p class="text-gray-300 leading-relaxed text-sm sm:text-base">
                        Get insights into your spending patterns with beautiful charts and detailed reports.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-12 sm:py-16 md:py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10 sm:mb-12 md:mb-16" data-aos="fade-up">
                <h2
                    class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-3 sm:mb-4 section-title">
                    Powerful <span class="gradient-text">Features</span></h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-300 max-w-3xl mx-auto px-2">
                    Everything you need to manage your finances effectively in one place with real-time insights.
                </p>
            </div>

            <div class="space-y-12 sm:space-y-16 md:space-y-24">
                <!-- Feature 1: Budgets -->
                <div class="grid md:grid-cols-2 gap-6 sm:gap-8 md:gap-12 items-center" data-aos="fade-right">
                    <div class="order-2 md:order-1">
                        <div
                            class="inline-block glass-strong text-purple-300 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4 feature-badge">
                            <i class="fas fa-piggy-bank mr-1 sm:mr-2"></i>Budget Management
                        </div>
                        <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 feature-title">
                            Smart Budget Tracking</h3>
                        <p class="text-base sm:text-lg text-gray-300 mb-4 sm:mb-6">
                            Set monthly budgets for different categories and track your spending in real-time. Get
                            visual progress indicators and detailed breakdowns to stay on track with your financial
                            goals.
                        </p>
                        <ul class="space-y-2 sm:space-y-3">
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Category-wise budget allocation</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Real-time spending alerts</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Visual progress tracking</span>
                            </li>
                        </ul>
                    </div>
                    <div class="order-1 md:order-2">
                        <div class="relative feature-image-wrapper">
                            <img src="{{ asset('img/budgets.png') }}" alt="Budget Management"
                                class="relative rounded-xl sm:rounded-2xl shadow-2xl w-full border border-white/10"
                                loading="lazy" decoding="async">
                        </div>
                    </div>
                </div>

                <!-- Feature 2: Goals -->
                <div class="grid md:grid-cols-2 gap-6 sm:gap-8 md:gap-12 items-center" data-aos="fade-left">
                    <div class="order-1">
                        <div class="relative feature-image-wrapper">
                            <img src="{{ asset('img/goals.png') }}" alt="Financial Goals"
                                class="relative rounded-xl sm:rounded-2xl shadow-2xl w-full border border-white/10"
                                loading="lazy" decoding="async">
                        </div>
                    </div>
                    <div class="order-2">
                        <div
                            class="inline-block glass-strong text-green-300 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4 feature-badge">
                            <i class="fas fa-bullseye mr-1 sm:mr-2"></i>Financial Goals
                        </div>
                        <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 feature-title">
                            Achieve Your Dreams</h3>
                        <p class="text-base sm:text-lg text-gray-300 mb-4 sm:mb-6">
                            Set savings goals for vacations, emergency funds, or any financial milestone. Track your
                            progress with intuitive visualizations and stay motivated to reach your targets.
                        </p>
                        <ul class="space-y-2 sm:space-y-3">
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Multiple goal tracking</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Progress visualization</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Target date reminders</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 3: Reports -->
                <div class="grid md:grid-cols-2 gap-6 sm:gap-8 md:gap-12 items-center" data-aos="fade-right">
                    <div class="order-2 md:order-1">
                        <div
                            class="inline-block glass-strong text-orange-300 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4 feature-badge">
                            <i class="fas fa-chart-line mr-1 sm:mr-2"></i>Analytics & Reports
                        </div>
                        <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 feature-title">
                            Detailed Financial Insights</h3>
                        <p class="text-base sm:text-lg text-gray-300 mb-4 sm:mb-6">
                            Generate comprehensive financial reports with income vs expenses analysis, category
                            breakdowns, cash flow tracking, and budget performance metrics.
                        </p>
                        <ul class="space-y-2 sm:space-y-3">
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Income vs Expense analysis</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Category spending breakdown</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Monthly summary reports</span>
                            </li>
                        </ul>
                    </div>
                    <div class="order-1 md:order-2">
                        <div class="relative feature-image-wrapper">
                            <img src="{{ asset('img/reports.png') }}" alt="Financial Reports"
                                class="relative rounded-xl sm:rounded-2xl shadow-2xl w-full border border-white/10"
                                loading="lazy" decoding="async">
                        </div>
                    </div>
                </div>

                <!-- Feature 4: Group Expenses -->
                <div class="grid md:grid-cols-2 gap-6 sm:gap-8 md:gap-12 items-center" data-aos="fade-left">
                    <div class="order-1">
                        <div class="relative feature-image-wrapper">
                            <img src="{{ asset('img/groupexpense.png') }}" alt="Group Expenses"
                                class="relative rounded-xl sm:rounded-2xl shadow-2xl w-full border border-white/10"
                                loading="lazy" decoding="async">
                        </div>
                    </div>
                    <div class="order-2">
                        <div
                            class="inline-block glass-strong text-indigo-300 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4 feature-badge">
                            <i class="fas fa-users mr-1 sm:mr-2"></i>Group Sharing
                        </div>
                        <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 feature-title">
                            Share Expenses Easily</h3>
                        <p class="text-base sm:text-lg text-gray-300 mb-4 sm:mb-6">
                            Split bills with roommates, friends, or family. Create groups, add members, track
                            contributions, and settle balances effortlessly with unique group codes.
                        </p>
                        <ul class="space-y-2 sm:space-y-3">
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Easy group creation with codes</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Member contribution tracking</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Balance sheet calculations</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 5: Notifications -->
                <div class="grid md:grid-cols-2 gap-6 sm:gap-8 md:gap-12 items-center" data-aos="fade-right">
                    <div class="order-2 md:order-1">
                        <div
                            class="inline-block glass-strong text-blue-300 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4 feature-badge">
                            <i class="fas fa-bell mr-1 sm:mr-2"></i>Smart Alerts
                        </div>
                        <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 feature-title">
                            Stay Informed</h3>
                        <p class="text-base sm:text-lg text-gray-300 mb-4 sm:mb-6">
                            Receive intelligent notifications for budget alerts, goal progress, negative balances, and
                            important financial milestones. Never miss a payment or overspend again.
                        </p>
                        <ul class="space-y-2 sm:space-y-3">
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Budget limit warnings</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Goal achievement alerts</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Negative balance notifications</span>
                            </li>
                        </ul>
                    </div>
                    <div class="order-1 md:order-2">
                        <div class="relative feature-image-wrapper">
                            <img src="{{ asset('img/notifications.png') }}" alt="Notifications"
                                class="relative rounded-xl sm:rounded-2xl shadow-2xl w-full border border-white/10"
                                loading="lazy" decoding="async">
                        </div>
                    </div>
                </div>

                <!-- Feature 6: Transactions -->
                <div class="grid md:grid-cols-2 gap-6 sm:gap-8 md:gap-12 items-center" data-aos="fade-left">
                    <div class="order-1">
                        <div class="relative feature-image-wrapper">
                            <img src="{{ asset('img/expensetracking.png') }}" alt="Transaction Management"
                                class="relative rounded-xl sm:rounded-2xl shadow-2xl w-full border border-white/10"
                                loading="lazy" decoding="async">
                        </div>
                    </div>
                    <div class="order-2">
                        <div
                            class="inline-block glass-strong text-red-300 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4 feature-badge">
                            <i class="fas fa-exchange-alt mr-1 sm:mr-2"></i>Transaction Tracking
                        </div>
                        <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 feature-title">
                            Complete Transaction Control</h3>
                        <p class="text-base sm:text-lg text-gray-300 mb-4 sm:mb-6">
                            Track all your financial transactions with detailed categorization, search functionality,
                            and filtering options. Edit, delete, or export your transaction history anytime.
                        </p>
                        <ul class="space-y-2 sm:space-y-3">
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Advanced search & filters</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Category management</span>
                            </li>
                            <li class="flex items-start">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                <span class="text-gray-300 text-sm sm:text-base">Export transaction data</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 sm:py-16 md:py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute inset-0 hero-pattern opacity-50"></div>
        <div class="max-w-4xl mx-auto text-center relative z-10" data-aos="fade-up">
            <h2
                class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 sm:mb-6 section-title px-2">
                Ready to Take Control of Your Finances?
            </h2>
            <p class="text-base sm:text-lg md:text-xl text-gray-200 mb-6 sm:mb-8 px-2">
                Join thousands of users who are already managing their finances smarter with TrackFlow.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-2">
                <a href="{{ route('register') }}"
                    class="bg-white text-purple-600 px-6 sm:px-8 md:px-10 py-3 sm:py-4 rounded-lg font-bold text-base sm:text-lg hover:bg-gray-100 transition transform hover:scale-105">
                    Start Free Today
                </a>
                <a href="#contact"
                    class="glass-strong border-2 border-white/30 text-white px-6 sm:px-8 md:px-10 py-3 sm:py-4 rounded-lg font-bold text-base sm:text-lg hover:bg-white/10 transition transform hover:scale-105">
                    Contact Sales
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-12 sm:py-16 md:py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10 sm:mb-12 md:mb-16" data-aos="fade-up">
                <h2
                    class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-3 sm:mb-4 section-title">
                    Get in <span class="gradient-text">Touch</span></h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-300 max-w-3xl mx-auto px-2">
                    Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-6 sm:gap-8 md:gap-12 contact-grid">
                <!-- Contact Form -->
                <div class="glass p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-lg" data-aos="fade-right">
                    <form id="contactForm" class="space-y-4 sm:space-y-6 contact-form">
                        <div>
                            <label class="block text-sm font-medium text-gray-200 mb-1.5 sm:mb-2">Your Name</label>
                            <input type="text" name="name" required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white/5 border border-white/10 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400 text-sm sm:text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-200 mb-1.5 sm:mb-2">Email Address</label>
                            <input type="email" name="email" required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white/5 border border-white/10 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400 text-sm sm:text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-200 mb-1.5 sm:mb-2">Subject</label>
                            <input type="text" name="subject" required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white/5 border border-white/10 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400 text-sm sm:text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-200 mb-1.5 sm:mb-2">Message</label>
                            <textarea name="message" rows="4" required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white/5 border border-white/10 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400 text-sm sm:text-base"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full btn-primary text-white py-3 sm:py-4 rounded-lg font-semibold text-base sm:text-lg">
                            <i class="fas fa-paper-plane mr-2"></i> Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Info & Map -->
                <div class="space-y-4 sm:space-y-6 md:space-y-8" data-aos="fade-left">
                    <!-- Contact Info -->
                    <div class="glass p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl shadow-lg space-y-4 sm:space-y-6">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div
                                class="w-10 h-10 sm:w-12 sm:h-12 gradient-bg rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white text-base sm:text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-white mb-1 text-sm sm:text-base">Our Location</h3>
                                <p class="text-gray-300 text-xs sm:text-sm md:text-base">Recreation Club,
                                    Sikhdeshpukuria, Bamangacchi<br>Barasat, West
                                    Bengal 700124,
                                    India</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 sm:gap-4">
                            <div
                                class="w-10 h-10 sm:w-12 sm:h-12 gradient-bg rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-white text-base sm:text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-white mb-1 text-sm sm:text-base">Phone Number</h3>
                                <p class="text-gray-300 text-xs sm:text-sm md:text-base">+91 8653021830<br>Mon-Fri, 9AM
                                    - 6PM IST</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 sm:gap-4">
                            <div
                                class="w-10 h-10 sm:w-12 sm:h-12 gradient-bg rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-white text-base sm:text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-white mb-1 text-sm sm:text-base">Email Address</h3>
                                <p class="text-gray-300 text-xs sm:text-sm md:text-base">
                                    support@trackflow.com<br>info@trackflow.com</p>
                            </div>
                        </div>

                        <div class="pt-4 sm:pt-6 border-t border-white/10">
                            <h3 class="font-bold text-white mb-3 sm:mb-4 text-sm sm:text-base">Follow Us</h3>
                            <div class="flex gap-3 sm:gap-4">
                                <a href="#"
                                    class="w-9 h-9 sm:w-10 sm:h-10 glass-strong hover:bg-purple-600 rounded-lg flex items-center justify-center transition group">
                                    <i
                                        class="fab fa-facebook-f text-gray-300 group-hover:text-white text-sm sm:text-base"></i>
                                </a>
                                <a href="#"
                                    class="w-9 h-9 sm:w-10 sm:h-10 glass-strong hover:bg-purple-600 rounded-lg flex items-center justify-center transition group">
                                    <i
                                        class="fab fa-twitter text-gray-300 group-hover:text-white text-sm sm:text-base"></i>
                                </a>
                                <a href="#"
                                    class="w-9 h-9 sm:w-10 sm:h-10 glass-strong hover:bg-purple-600 rounded-lg flex items-center justify-center transition group">
                                    <i
                                        class="fab fa-instagram text-gray-300 group-hover:text-white text-sm sm:text-base"></i>
                                </a>
                                <a href="#"
                                    class="w-9 h-9 sm:w-10 sm:h-10 glass-strong hover:bg-purple-600 rounded-lg flex items-center justify-center transition group">
                                    <i
                                        class="fab fa-linkedin-in text-gray-300 group-hover:text-white text-sm sm:text-base"></i>
                                </a>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 glass-strong hover:bg-purple-600 rounded-lg flex items-center justify-center transition group">
                                    <i class="fab fa-linkedin-in text-gray-300 group-hover:text-white"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="glass p-3 sm:p-4 rounded-xl sm:rounded-2xl shadow-lg overflow-hidden">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1307.7372887320992!2d88.52358456177086!3d22.744767580150814!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39f8a3001fe7330d%3A0x14932cfed95c47df!2sTapan%20Ghosh%20House!5e1!3m2!1sen!2sin!4v1765250474354!5m2!1sen!2sin"
                            width="100%" height="200" class="sm:h-[250px] md:h-[300px]" style="border:0;"
                            allowfullscreen="" loading="lazy" class="rounded-lg"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="glass-strong text-white py-8 sm:py-10 md:py-12 px-4 sm:px-6 lg:px-8 border-t border-white/10">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 mb-6 sm:mb-8 footer-grid">
                <!-- Company Info -->
                <div class="col-span-2 sm:col-span-2 md:col-span-1">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-3 sm:mb-4">
                        <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow Logo"
                            class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
                        <span class="text-lg sm:text-xl font-bold">TrackFlow</span>
                    </div>
                    <p class="text-gray-300 text-xs sm:text-sm leading-relaxed">
                        Smart financial management platform helping you take control of your finances.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Quick Links</h3>
                    <ul class="space-y-1.5 sm:space-y-2 text-gray-300 text-xs sm:text-sm">
                        <li><a href="#about" class="hover:text-white transition">About Us</a></li>
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#contact" class="hover:text-white transition">Contact</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Login</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h3 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Resources</h3>
                    <ul class="space-y-1.5 sm:space-y-2 text-gray-300 text-xs sm:text-sm">
                        <li><a href="#" onclick="openBlog(event)"
                                class="hover:text-white transition cursor-pointer">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" onclick="openPrivacyPolicy(event)"
                                class="hover:text-white transition cursor-pointer">Privacy Policy</a></li>
                        <li><a href="#" onclick="openTermsOfService(event)"
                                class="hover:text-white transition cursor-pointer">Terms of Service</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="col-span-2 sm:col-span-2 md:col-span-1">
                    <h3 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Newsletter</h3>
                    <p class="text-gray-300 text-xs sm:text-sm mb-3 sm:mb-4">Subscribe to get updates and news.</p>
                    <form class="space-y-2">
                        <input type="email" placeholder="Your email"
                            class="w-full px-3 sm:px-4 py-2 rounded-lg bg-white/5 border border-white/10 focus:border-purple-500 focus:outline-none text-white placeholder-gray-400 text-xs sm:text-sm">
                        <button type="submit"
                            class="w-full btn-primary text-white py-2 rounded-lg font-medium text-xs sm:text-sm">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-white/10 pt-6 sm:pt-8 text-center text-gray-300 text-xs sm:text-sm">
                <p>&copy; {{ date('Y') }} TrackFlow. All rights reserved. Made with <i
                        class="fas fa-heart text-red-500"></i> for better financial management.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop"
        class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 md:bottom-8 md:right-8 w-10 h-10 sm:w-12 sm:h-12 gradient-bg text-white rounded-full shadow-lg opacity-0 pointer-events-none transition-all duration-300 hover:scale-110 z-40">
        <i class="fas fa-arrow-up text-sm sm:text-base"></i>
    </button>

    <script>
        // Redirect authenticated users to dashboard
        @if(session('user_id'))
            window.location.href = '{{ route('dashboard') }}';
        @endif

        // Totally disable backward button navigation
        (function () {
            // Clear browser history and prevent back button
            history.pushState(null, document.title, location.href);

            window.addEventListener('popstate', function (event) {
                history.pushState(null, document.title, location.href);
            });

            // Prevent back navigation completely
            window.onpopstate = function (event) {
                history.go(1);
            };

            // Additional layer to block back button
            if (window.history && window.history.pushState) {
                window.history.replaceState(null, null, window.location.href);
                window.history.pushState(null, null, window.location.href);

                window.addEventListener('popstate', function () {
                    window.history.go(1);
                });
            }
        })();

        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Scroll to Top Button
        const scrollToTopBtn = document.getElementById('scrollToTop');

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                scrollToTopBtn.classList.add('opacity-0', 'pointer-events-none');
            }
        });

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Contact Form Submission
        document.getElementById('contactForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // Show success message (you can replace this with actual API call)
            alert('Thank you for your message! We will get back to you soon.');
            this.reset();
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Privacy Policy Modal
        function openPrivacyPolicy(e) {
            e.preventDefault();
            const modal = document.createElement('div');
            modal.id = 'privacyModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4';
            modal.innerHTML = `
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-4xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-hidden animate-scale-in modal-content-custom">
                    <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-blue-600 text-white p-4 sm:p-6 flex items-center justify-between">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold">Privacy Policy</h2>
                        <button onclick="closeModal('privacyModal')" class="text-white hover:text-gray-200 transition p-1">
                            <i class="fas fa-times text-xl sm:text-2xl"></i>
                        </button>
                    </div>
                    <div class="p-4 sm:p-6 md:p-8 overflow-y-auto max-h-[calc(95vh-70px)] sm:max-h-[calc(90vh-88px)]">
                        <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">Last updated: December 8, 2025</p>
                        
                        <div class="space-y-6 text-gray-700">
                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">1. Information We Collect</h3>
                                <p class="mb-3">We collect information that you provide directly to us, including:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Account information (name, email address, phone number)</li>
                                    <li>Financial data (income, expenses, budgets, goals)</li>
                                    <li>Profile information and preferences</li>
                                    <li>Communication data when you contact us</li>
                                </ul>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">2. How We Use Your Information</h3>
                                <p class="mb-3">We use the information we collect to:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Provide, maintain, and improve our services</li>
                                    <li>Process transactions and send related information</li>
                                    <li>Send technical notices, updates, and support messages</li>
                                    <li>Respond to your comments and questions</li>
                                    <li>Detect, prevent, and address technical issues and fraud</li>
                                </ul>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">3. Data Security</h3>
                                <p class="mb-3">We implement industry-standard security measures to protect your personal information:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Bank-level encryption (256-bit SSL/TLS)</li>
                                    <li>Secure data storage and transmission</li>
                                    <li>Regular security audits and updates</li>
                                    <li>Access controls and authentication</li>
                                </ul>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">4. Data Sharing</h3>
                                <p>We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4 mt-3">
                                    <li>With your explicit consent</li>
                                    <li>To comply with legal obligations</li>
                                    <li>To protect our rights and prevent fraud</li>
                                    <li>With service providers who assist in our operations</li>
                                </ul>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">5. Your Rights</h3>
                                <p class="mb-3">You have the right to:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Access your personal data</li>
                                    <li>Correct inaccurate data</li>
                                    <li>Request deletion of your data</li>
                                    <li>Export your data</li>
                                    <li>Opt-out of marketing communications</li>
                                </ul>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">6. Cookies and Tracking</h3>
                                <p>We use cookies and similar technologies to enhance your experience, analyze usage, and provide personalized content. You can control cookies through your browser settings.</p>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">7. Contact Us</h3>
                                <p>If you have questions about this Privacy Policy, please contact us at:</p>
                                <ul class="list-none space-y-2 mt-3">
                                    <li><strong>Email:</strong> privacy@trackflow.com</li>
                                    <li><strong>Phone:</strong> +1 (555) 123-4567</li>
                                    <li><strong>Address:</strong> 123 Financial Street, New York, NY 10001</li>
                                </ul>
                            </section>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        // Terms of Service Modal
        function openTermsOfService(e) {
            e.preventDefault();
            const modal = document.createElement('div');
            modal.id = 'termsModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4';
            modal.innerHTML = `
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-4xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-hidden animate-scale-in modal-content-custom">
                    <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 sm:p-6 flex items-center justify-between">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold">Terms of Service</h2>
                        <button onclick="closeModal('termsModal')" class="text-white hover:text-gray-200 transition p-1">
                            <i class="fas fa-times text-xl sm:text-2xl"></i>
                        </button>
                    </div>
                    <div class="p-4 sm:p-6 md:p-8 overflow-y-auto max-h-[calc(95vh-70px)] sm:max-h-[calc(90vh-88px)]">
                        <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">Last updated: December 8, 2025</p>
                        
                        <div class="space-y-6 text-gray-700">
                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">1. Acceptance of Terms</h3>
                                <p>By accessing and using TrackFlow, you accept and agree to be bound by the terms and provisions of this agreement. If you do not agree to these terms, please do not use our service.</p>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">2. Use License</h3>
                                <p class="mb-3">Permission is granted to temporarily access and use TrackFlow for personal, non-commercial purposes. This license shall automatically terminate if you violate any of these restrictions:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>You may not modify or copy the materials</li>
                                    <li>You may not use the materials for commercial purposes</li>
                                    <li>You may not attempt to reverse engineer any software</li>
                                    <li>You may not remove any copyright or proprietary notations</li>
                                </ul>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">3. User Account</h3>
                                <p class="mb-3">When you create an account with us, you agree to:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Provide accurate, current, and complete information</li>
                                    <li>Maintain the security of your password</li>
                                    <li>Accept responsibility for all activities under your account</li>
                                    <li>Notify us immediately of any unauthorized use</li>
                                </ul>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">4. Prohibited Uses</h3>
                                <p class="mb-3">You agree not to use TrackFlow:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>For any unlawful purpose or to violate any laws</li>
                                    <li>To harm, threaten, or harass others</li>
                                    <li>To transmit viruses or malicious code</li>
                                    <li>To interfere with the service or servers</li>
                                    <li>To collect user information without permission</li>
                                </ul>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">5. Service Availability</h3>
                                <p>We strive to provide continuous service but do not guarantee uninterrupted access. We reserve the right to modify, suspend, or discontinue any part of the service with or without notice.</p>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">6. Intellectual Property</h3>
                                <p>The service and its original content, features, and functionality are owned by TrackFlow and are protected by international copyright, trademark, and other intellectual property laws.</p>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">7. Disclaimer</h3>
                                <p>TrackFlow is provided "as is" without warranties of any kind. We do not guarantee accuracy, reliability, or suitability for any particular purpose. Use of the service is at your own risk.</p>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">8. Limitation of Liability</h3>
                                <p>TrackFlow shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use or inability to use the service.</p>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">9. Termination</h3>
                                <p>We may terminate or suspend your account and access to the service immediately, without prior notice, for any breach of these Terms of Service.</p>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">10. Changes to Terms</h3>
                                <p>We reserve the right to modify these terms at any time. Continued use of the service after changes constitutes acceptance of the new terms.</p>
                            </section>

                            <section>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">11. Contact Information</h3>
                                <p>For questions about these Terms of Service, contact us at:</p>
                                <ul class="list-none space-y-2 mt-3">
                                    <li><strong>Email:</strong> legal@trackflow.com</li>
                                    <li><strong>Phone:</strong> +1 (555) 123-4567</li>
                                    <li><strong>Address:</strong> 123 Financial Street, New York, NY 10001</li>
                                </ul>
                            </section>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        // Close Modal Function
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('animate-fade-out');
                setTimeout(() => modal.remove(), 300);
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function (e) {
            if (e.target.id === 'privacyModal' || e.target.id === 'termsModal' || e.target.id === 'blogModal') {
                closeModal(e.target.id);
            }
        });

        // Blog Modal
        function openBlog(e) {
            e.preventDefault();
            const modal = document.createElement('div');
            modal.id = 'blogModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4';
            modal.innerHTML = `
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-5xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-hidden animate-scale-in modal-content-custom">
                    <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-4 sm:p-6 flex items-center justify-between z-10">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-newspaper text-xl sm:text-2xl"></i>
                            <h2 class="text-xl sm:text-2xl md:text-3xl font-bold">TrackFlow Blog</h2>
                        </div>
                        <button onclick="closeModal('blogModal')" class="text-white hover:text-gray-200 transition p-1">
                            <i class="fas fa-times text-xl sm:text-2xl"></i>
                        </button>
                    </div>
                    <div class="p-4 sm:p-6 md:p-8 overflow-y-auto max-h-[calc(95vh-70px)] sm:max-h-[calc(90vh-88px)] bg-gray-50">
                        
                        <!-- Featured Article -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                            <div class="relative">
                                <img src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=1200&h=400&fit=crop" 
                                     alt="Financial Freedom" 
                                     class="w-full h-48 sm:h-64 object-cover">
                                <div class="absolute top-4 left-4">
                                    <span class="bg-purple-600 text-white px-3 py-1 rounded-full text-xs sm:text-sm font-semibold">Featured</span>
                                </div>
                            </div>
                            <div class="p-5 sm:p-6">
                                <div class="flex items-center gap-4 text-gray-500 text-xs sm:text-sm mb-3">
                                    <span><i class="fas fa-calendar mr-1"></i> Feb 10, 2026</span>
                                    <span><i class="fas fa-clock mr-1"></i> 8 min read</span>
                                    <span><i class="fas fa-tag mr-1"></i> Financial Planning</span>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3">The Ultimate Guide to Financial Freedom in 2026</h3>
                                <p class="text-gray-600 mb-4 text-sm sm:text-base leading-relaxed">
                                    Financial freedom isn't just about having money—it's about having control over your financial destiny. In this comprehensive guide, we explore proven strategies that have helped thousands of TrackFlow users achieve their financial goals. From budgeting basics to advanced investment strategies, discover how small daily habits can lead to significant long-term wealth building.
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=667eea&color=fff" alt="Author" class="w-10 h-10 rounded-full">
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm">Sarah Johnson</p>
                                            <p class="text-gray-500 text-xs">Financial Expert</p>
                                        </div>
                                    </div>
                                    <span class="text-purple-600 font-semibold text-sm cursor-pointer hover:text-purple-700">Read More <i class="fas fa-arrow-right ml-1"></i></span>
                                </div>
                            </div>
                        </div>

                        <!-- Blog Grid -->
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                            
                            <!-- Article 1 -->
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                                <div class="relative overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=250&fit=crop" 
                                         alt="Budgeting Tips" 
                                         class="w-full h-36 sm:h-44 object-cover group-hover:scale-105 transition duration-300">
                                </div>
                                <div class="p-4">
                                    <span class="text-purple-600 text-xs font-semibold">BUDGETING</span>
                                    <h4 class="font-bold text-gray-900 mt-1 mb-2 text-sm sm:text-base line-clamp-2">10 Budgeting Mistakes You're Probably Making</h4>
                                    <p class="text-gray-600 text-xs sm:text-sm line-clamp-2 mb-3">Learn the common pitfalls that derail most budgets and how to avoid them for better financial health.</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span><i class="fas fa-calendar mr-1"></i> Feb 8, 2026</span>
                                        <span><i class="fas fa-clock mr-1"></i> 5 min</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Article 2 -->
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                                <div class="relative overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=250&fit=crop" 
                                         alt="Expense Tracking" 
                                         class="w-full h-36 sm:h-44 object-cover group-hover:scale-105 transition duration-300">
                                </div>
                                <div class="p-4">
                                    <span class="text-green-600 text-xs font-semibold">EXPENSE TRACKING</span>
                                    <h4 class="font-bold text-gray-900 mt-1 mb-2 text-sm sm:text-base line-clamp-2">How to Track Every Rupee Without Going Crazy</h4>
                                    <p class="text-gray-600 text-xs sm:text-sm line-clamp-2 mb-3">Discover smart automation techniques that make expense tracking effortless and actually enjoyable.</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span><i class="fas fa-calendar mr-1"></i> Feb 5, 2026</span>
                                        <span><i class="fas fa-clock mr-1"></i> 6 min</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Article 3 -->
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                                <div class="relative overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1553729459-efe14ef6055d?w=400&h=250&fit=crop" 
                                         alt="Savings Goals" 
                                         class="w-full h-36 sm:h-44 object-cover group-hover:scale-105 transition duration-300">
                                </div>
                                <div class="p-4">
                                    <span class="text-blue-600 text-xs font-semibold">SAVINGS</span>
                                    <h4 class="font-bold text-gray-900 mt-1 mb-2 text-sm sm:text-base line-clamp-2">Setting Achievable Financial Goals That Actually Work</h4>
                                    <p class="text-gray-600 text-xs sm:text-sm line-clamp-2 mb-3">The science-backed approach to goal setting that has helped our users save 40% more money.</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span><i class="fas fa-calendar mr-1"></i> Feb 2, 2026</span>
                                        <span><i class="fas fa-clock mr-1"></i> 7 min</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Article 4 -->
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                                <div class="relative overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=400&h=250&fit=crop" 
                                         alt="Group Expenses" 
                                         class="w-full h-36 sm:h-44 object-cover group-hover:scale-105 transition duration-300">
                                </div>
                                <div class="p-4">
                                    <span class="text-orange-600 text-xs font-semibold">GROUP SHARING</span>
                                    <h4 class="font-bold text-gray-900 mt-1 mb-2 text-sm sm:text-base line-clamp-2">Split Bills Like a Pro: The Complete Guide</h4>
                                    <p class="text-gray-600 text-xs sm:text-sm line-clamp-2 mb-3">Master the art of splitting expenses with roommates, friends, and family without awkward conversations.</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span><i class="fas fa-calendar mr-1"></i> Jan 28, 2026</span>
                                        <span><i class="fas fa-clock mr-1"></i> 5 min</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Article 5 -->
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                                <div class="relative overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=250&fit=crop" 
                                         alt="Financial Reports" 
                                         class="w-full h-36 sm:h-44 object-cover group-hover:scale-105 transition duration-300">
                                </div>
                                <div class="p-4">
                                    <span class="text-red-600 text-xs font-semibold">ANALYTICS</span>
                                    <h4 class="font-bold text-gray-900 mt-1 mb-2 text-sm sm:text-base line-clamp-2">Understanding Your Financial Reports</h4>
                                    <p class="text-gray-600 text-xs sm:text-sm line-clamp-2 mb-3">Turn data into actionable insights with our guide to reading and analyzing your financial reports.</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span><i class="fas fa-calendar mr-1"></i> Jan 25, 2026</span>
                                        <span><i class="fas fa-clock mr-1"></i> 8 min</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Article 6 -->
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                                <div class="relative overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1434626881859-194d67b2b86f?w=400&h=250&fit=crop" 
                                         alt="Emergency Fund" 
                                         class="w-full h-36 sm:h-44 object-cover group-hover:scale-105 transition duration-300">
                                </div>
                                <div class="p-4">
                                    <span class="text-teal-600 text-xs font-semibold">EMERGENCY FUND</span>
                                    <h4 class="font-bold text-gray-900 mt-1 mb-2 text-sm sm:text-base line-clamp-2">Building Your Emergency Fund: A Step-by-Step Plan</h4>
                                    <p class="text-gray-600 text-xs sm:text-sm line-clamp-2 mb-3">Why every financial expert recommends an emergency fund and how to build one that protects you.</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span><i class="fas fa-calendar mr-1"></i> Jan 20, 2026</span>
                                        <span><i class="fas fa-clock mr-1"></i> 6 min</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Newsletter CTA -->
                        <div class="mt-8 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-6 sm:p-8 text-center text-white">
                            <h3 class="text-xl sm:text-2xl font-bold mb-2">Stay Updated with Financial Tips</h3>
                            <p class="text-purple-100 mb-4 text-sm sm:text-base">Get the latest articles, tips, and insights delivered straight to your inbox.</p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center max-w-md mx-auto">
                                <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white text-sm">
                                <button class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition text-sm">
                                    Subscribe
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
    </script>

    <style>
        @keyframes scale-in {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
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

        .animate-scale-in {
            animation: scale-in 0.3s ease-out;
        }

        .animate-fade-out {
            animation: fade-out 0.3s ease-out;
        }
    </style>
</body>

</html>