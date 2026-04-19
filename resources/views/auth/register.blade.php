<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - TrackFlow</title>
    <link rel="icon" type="image/png" href="{{ asset('trackflow-main/fav-icon.png') }}">
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --teal-500: #14b8a6;
            --teal-600: #0d9488;
            --teal-400: #2dd4bf;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
        }

        .glass {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .glass-input {
            background: rgba(15, 23, 42, 0.6);
            border: 2px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .glass-input:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: var(--teal-500);
            outline: none;
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.15);
        }

        .glass-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
        }

        .glass-input.success {
            border-color: #22c55e;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--teal-600), var(--teal-500));
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--teal-500), var(--teal-400));
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(20, 184, 166, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-google {
            background: linear-gradient(135deg, rgba(234, 67, 53, 0.1), rgba(234, 67, 53, 0.05));
            border: 2px solid rgba(234, 67, 53, 0.3);
            transition: all 0.3s ease;
        }

        .btn-google:hover {
            background: linear-gradient(135deg, rgba(234, 67, 53, 0.2), rgba(234, 67, 53, 0.1));
            border-color: rgba(234, 67, 53, 0.5);
            transform: translateY(-2px);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .checkbox-custom {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            background: rgba(15, 23, 42, 0.6);
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .checkbox-custom:checked {
            background: var(--teal-500);
            border-color: var(--teal-500);
        }

        .checkbox-custom:checked::after {
            content: '✓';
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        /* Select dropdown styles */
        select.glass-input {
            background-image: none;
        }

        select.glass-input option {
            background: #1e293b;
            color: white;
            padding: 12px;
        }

        select.glass-input option:hover,
        select.glass-input option:focus {
            background: #0d9488;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
            transition: color 0.3s ease;
        }

        .input-group:focus-within .input-icon {
            color: var(--teal-400);
        }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .strength-weak {
            background: #ef4444;
            width: 25%;
        }

        .strength-fair {
            background: #f59e0b;
            width: 50%;
        }

        .strength-good {
            background: #84cc16;
            width: 75%;
        }

        .strength-strong {
            background: #22c55e;
            width: 100%;
        }

        /* Password Strength Indicator Styles */
        .strength-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }

        .strength-bar {
            flex: 1;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .strength-fill.poor {
            width: 33%;
            background: #ef4444;
        }

        .strength-fill.good {
            width: 66%;
            background: #f59e0b;
        }

        .strength-fill.strong {
            width: 100%;
            background: #10b981;
        }

        .strength-text {
            font-size: 12px;
            font-weight: 600;
            min-width: 50px;
            text-align: right;
        }

        .strength-text.poor {
            color: #ef4444;
        }

        .strength-text.good {
            color: #f59e0b;
        }

        .strength-text.strong {
            color: #10b981;
        }

        /* Blinking animation for strength indicator */
        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .strength-blink {
            animation: blink 1s ease-in-out infinite;
        }

        /* Password Requirements Styles */
        .password-requirements {
            margin-top: 12px;
            padding: 12px;
            background: rgba(15, 23, 42, 0.4);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .requirements-title {
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            padding: 4px 0;
            transition: all 0.3s ease;
        }

        .requirement-item i {
            width: 14px;
            font-size: 10px;
        }

        .requirement-item.valid {
            color: #10b981;
        }

        .requirement-item.invalid {
            color: #ef4444;
        }

        .requirement-item.neutral {
            color: rgba(255, 255, 255, 0.4);
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            max-width: 600px;
            width: 100%;
            max-height: 80vh;
            overflow: hidden;
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s ease;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .modal-overlay.active .modal-content {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--teal-600), var(--teal-500));
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            color: white;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 24px;
            max-height: calc(80vh - 140px);
            overflow-y: auto;
            color: #d1d5db;
            font-size: 14px;
            line-height: 1.7;
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: rgba(20, 184, 166, 0.5);
            border-radius: 3px;
        }

        .modal-body h4 {
            color: #14b8a6;
            font-size: 16px;
            font-weight: 600;
            margin: 20px 0 10px 0;
        }

        .modal-body h4:first-child {
            margin-top: 0;
        }

        .modal-body p {
            margin-bottom: 12px;
        }

        .modal-body ul {
            margin: 10px 0 15px 20px;
            list-style-type: disc;
        }

        .modal-body ul li {
            margin-bottom: 6px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: flex-end;
        }

        .modal-footer button {
            padding: 10px 24px;
            background: linear-gradient(135deg, var(--teal-600), var(--teal-500));
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal-footer button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(20, 184, 166, 0.4);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 py-8">
    <!-- Background decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl float-animation"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl float-animation"
            style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 right-1/3 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl float-animation"
            style="animation-delay: -1.5s;"></div>
    </div>

    <div class="max-w-md w-full relative z-10">
        <!-- Logo -->
        <div class="text-center mb-6 fade-in">
            <a href="{{ url('/') }}" class="inline-block">
                <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow"
                    class="h-10 mx-auto mb-3 hover:scale-105 transition-transform">
            </a>
            <p class="text-gray-400 text-sm">Start your financial journey</p>
        </div>

        <!-- Register Card -->
        <div class="glass rounded-3xl shadow-2xl overflow-hidden fade-in" style="animation-delay: 0.1s;">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-600 to-cyan-600 px-8 py-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-plus text-xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">Create Account</h1>
                        <p class="text-teal-100 text-sm">Join thousands managing their finances</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <!-- Alerts -->
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl flex items-start gap-3 fade-in">
                        <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                        <div class="text-red-300 text-sm">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}" id="registerForm">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-user mr-2 text-teal-400"></i>Full Name
                        </label>
                        <div class="input-group relative">
                            <span class="input-icon"><i class="fas fa-id-card"></i></span>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="glass-input w-full pl-12 pr-4 py-3.5 rounded-xl text-white"
                                placeholder="John Doe">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-envelope mr-2 text-teal-400"></i>Email Address
                        </label>
                        <div class="input-group relative">
                            <span class="input-icon"><i class="fas fa-at"></i></span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="glass-input w-full pl-12 pr-4 py-3.5 rounded-xl text-white"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2 text-teal-400"></i>Password
                        </label>
                        <div class="input-group relative">
                            <span class="input-icon"><i class="fas fa-key"></i></span>
                            <input type="password" id="password" name="password" required minlength="8"
                                class="glass-input w-full pl-12 pr-12 py-3.5 rounded-xl text-white"
                                placeholder="••••••••" oninput="checkPasswordStrengthNew()">
                            <button type="button" onclick="togglePassword('password')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-400 transition-colors">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        <!-- Password Strength Indicator -->
                        <div class="strength-indicator" id="strengthIndicator" style="display: none;">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <span class="strength-text strength-blink" id="strengthTextNew">Poor</span>
                        </div>
                        <!-- Password Requirements -->
                        <div class="password-requirements" id="passwordRequirements">
                            <div class="requirements-title"><i class="fas fa-shield-check mr-1"></i>Password
                                Requirements</div>
                            <div class="requirement-item neutral" id="req-length">
                                <i class="fas fa-circle"></i>
                                <span>Minimum 8 characters</span>
                            </div>
                            <div class="requirement-item neutral" id="req-uppercase">
                                <i class="fas fa-circle"></i>
                                <span>At least one uppercase letter (A-Z)</span>
                            </div>
                            <div class="requirement-item neutral" id="req-lowercase">
                                <i class="fas fa-circle"></i>
                                <span>At least one lowercase letter (a-z)</span>
                            </div>
                            <div class="requirement-item neutral" id="req-number">
                                <i class="fas fa-circle"></i>
                                <span>At least one number (0-9)</span>
                            </div>
                            <div class="requirement-item neutral" id="req-special">
                                <i class="fas fa-circle"></i>
                                <span>At least one special character (!@#$%^&*)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-5">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-shield-check mr-2 text-teal-400"></i>Confirm Password
                        </label>
                        <div class="input-group relative">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="glass-input w-full pl-12 pr-12 py-3.5 rounded-xl text-white"
                                placeholder="••••••••" oninput="checkPasswordMatchNew()">
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-400 transition-colors">
                                <i class="fas fa-eye" id="password_confirmation-eye"></i>
                            </button>
                        </div>
                        <!-- Password Match Indicator -->
                        <div class="requirement-item mt-2" id="passwordMatchIndicator" style="display: none;">
                            <i class="fas fa-circle"></i>
                            <span id="passwordMatchTextNew">Passwords match</span>
                        </div>
                    </div>

                    <!-- Currency Selection -->
                    <div class="mb-5">
                        <label for="currency" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-coins mr-2 text-teal-400"></i>Default Currency
                        </label>
                        <div class="input-group relative">
                            <span class="input-icon"><i class="fas fa-money-bill-wave"></i></span>
                            <select id="currency" name="currency" required
                                class="glass-input w-full pl-12 pr-4 py-3.5 rounded-xl text-white appearance-none cursor-pointer">
                                @php
                                    $currencies = config('currency.currencies', []);
                                @endphp
                                @foreach($currencies as $code => $currency)
                                    <option value="{{ $code }}" {{ old('currency', 'INR') == $code ? 'selected' : '' }}>
                                        {{ $currency['symbol'] }} - {{ $currency['name'] }} ({{ $code }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>This will be your default currency for transactions
                        </p>
                    </div>

                    <!-- Terms -->
                    <div class="mb-6">
                        <label class="flex items-start cursor-pointer group">
                            <input type="checkbox" required class="checkbox-custom mt-0.5">
                            <span
                                class="ml-3 text-sm text-gray-400 group-hover:text-gray-300 transition-colors leading-relaxed">
                                I agree to the
                                <a href="javascript:void(0)" onclick="openModal('termsModal')"
                                    class="text-teal-400 hover:text-teal-300 underline">Terms of Service</a>
                                and
                                <a href="javascript:void(0)" onclick="openModal('privacyModal')"
                                    class="text-teal-400 hover:text-teal-300 underline">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Register Button -->
                    <button type="submit" id="registerBtn"
                        class="w-full btn-primary text-white font-semibold py-4 rounded-xl flex items-center justify-center gap-2 text-lg">
                        <i class="fas fa-rocket"></i>
                        <span>Create Account</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-slate-800/80 text-gray-400">or sign up with</span>
                    </div>
                </div>

                <!-- Google Sign Up Only -->
                <a href="{{ url('/auth/google/redirect') }}"
                    class="w-full btn-google text-white font-medium py-3.5 rounded-xl flex items-center justify-center gap-3 hover:shadow-lg">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#EA4335"
                            d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z" />
                        <path fill="#34A853"
                            d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2936293 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z" />
                        <path fill="#4A90E2"
                            d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5272727 23.1818182,9.81818182 L12,9.81818182 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z" />
                        <path fill="#FBBC05"
                            d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z" />
                    </svg>
                    <span>Continue with Google</span>
                </a>

                <!-- Login Link -->
                <p class="text-center text-gray-400 mt-6">
                    Already have an account?
                    <a href="{{ route('login') }}"
                        class="text-teal-400 hover:text-teal-300 font-semibold transition-colors ml-1">
                        Sign in
                    </a>
                </p>
            </div>
        </div>

        <!-- Features Badge -->
        <div class="mt-6 text-center fade-in" style="animation-delay: 0.3s;">
            <div class="glass rounded-xl p-4 inline-flex items-center gap-4 flex-wrap justify-center">
                <div class="flex items-center gap-2 text-gray-400 text-xs">
                    <i class="fas fa-check-circle text-teal-400"></i>
                    <span>Free Forever</span>
                </div>
                <div class="flex items-center gap-2 text-gray-400 text-xs">
                    <i class="fas fa-check-circle text-teal-400"></i>
                    <span>No Credit Card</span>
                </div>
                <div class="flex items-center gap-2 text-gray-400 text-xs">
                    <i class="fas fa-check-circle text-teal-400"></i>
                    <span>Cancel Anytime</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prevent back navigation after registration
        (function () {
            @if(session('user_id'))
                window.location.href = '{{ route('dashboard') }}';
                return;
            @endif

            if (window.history && window.history.pushState) {
                window.history.replaceState(null, null, window.location.href);
                window.history.pushState(null, null, window.location.href);
                window.addEventListener('popstate', function (event) {
                    window.history.pushState(null, null, window.location.href);
                });
            }

            window.onpageshow = function (event) {
                if (event.persisted) {
                    window.location.reload();
                }
            };
        })();

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-eye');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function checkPasswordStrengthNew() {
            const password = document.getElementById('password').value;
            const strengthIndicator = document.getElementById('strengthIndicator');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthTextNew');

            // Show/hide strength indicator
            if (password.length > 0) {
                strengthIndicator.style.display = 'flex';
            } else {
                strengthIndicator.style.display = 'none';
                resetRequirements();
                return;
            }

            // Check each requirement
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

            // Update requirement indicators
            updateRequirement('req-length', hasLength);
            updateRequirement('req-uppercase', hasUppercase);
            updateRequirement('req-lowercase', hasLowercase);
            updateRequirement('req-number', hasNumber);
            updateRequirement('req-special', hasSpecial);

            // Calculate strength score
            let score = 0;
            if (hasLength) score++;
            if (hasUppercase) score++;
            if (hasLowercase) score++;
            if (hasNumber) score++;
            if (hasSpecial) score++;

            // Extra points for longer passwords
            if (password.length >= 12) score++;
            if (password.length >= 16) score++;

            // Update strength indicator
            strengthFill.classList.remove('poor', 'good', 'strong');
            strengthText.classList.remove('poor', 'good', 'strong');

            if (score <= 2) {
                strengthFill.classList.add('poor');
                strengthText.classList.add('poor');
                strengthText.textContent = 'Poor';
            } else if (score <= 4) {
                strengthFill.classList.add('good');
                strengthText.classList.add('good');
                strengthText.textContent = 'Good';
            } else {
                strengthFill.classList.add('strong');
                strengthText.classList.add('strong');
                strengthText.textContent = 'Strong';
            }

            // Also check password match if confirm field has value
            checkPasswordMatchNew();
        }

        function updateRequirement(reqId, isValid) {
            const reqElement = document.getElementById(reqId);
            const icon = reqElement.querySelector('i');

            reqElement.classList.remove('valid', 'invalid', 'neutral');
            icon.classList.remove('fa-circle', 'fa-check-circle', 'fa-times-circle');

            if (isValid) {
                reqElement.classList.add('valid');
                icon.classList.add('fa-check-circle');
            } else {
                reqElement.classList.add('invalid');
                icon.classList.add('fa-times-circle');
            }
        }

        function resetRequirements() {
            const requirements = ['req-length', 'req-uppercase', 'req-lowercase', 'req-number', 'req-special'];
            requirements.forEach(reqId => {
                const reqElement = document.getElementById(reqId);
                const icon = reqElement.querySelector('i');
                reqElement.classList.remove('valid', 'invalid');
                reqElement.classList.add('neutral');
                icon.classList.remove('fa-check-circle', 'fa-times-circle');
                icon.classList.add('fa-circle');
            });
        }

        function checkPasswordMatchNew() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const matchIndicator = document.getElementById('passwordMatchIndicator');
            const matchText = document.getElementById('passwordMatchTextNew');
            const icon = matchIndicator.querySelector('i');
            const confirmInput = document.getElementById('password_confirmation');

            if (confirmPassword.length > 0) {
                matchIndicator.style.display = 'flex';
                matchIndicator.classList.remove('valid', 'invalid', 'neutral');
                icon.classList.remove('fa-circle', 'fa-check-circle', 'fa-times-circle');
                confirmInput.classList.remove('success', 'error');

                if (password === confirmPassword) {
                    matchIndicator.classList.add('valid');
                    icon.classList.add('fa-check-circle');
                    matchText.textContent = 'Passwords match';
                    confirmInput.classList.add('success');
                } else {
                    matchIndicator.classList.add('invalid');
                    icon.classList.add('fa-times-circle');
                    matchText.textContent = 'Passwords do not match';
                    confirmInput.classList.add('error');
                }
            } else {
                matchIndicator.style.display = 'none';
                confirmInput.classList.remove('success', 'error');
            }
        }

        // Keep old functions for backward compatibility (not used but safe to keep)
        function checkPasswordStrength(password) {
            checkPasswordStrengthNew();
        }

        function checkPasswordMatch() {
            checkPasswordMatchNew();
        }

        // Form submission with loading state
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const btn = document.getElementById('registerBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Creating account...</span>';
        });

        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function (e) {
                if (e.target === this) {
                    this.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                    modal.classList.remove('active');
                });
                document.body.style.overflow = '';
            }
        });
    </script>

    <!-- Terms of Service Modal -->
    <div class="modal-overlay" id="termsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-file-contract"></i> Terms of Service</h3>
                <button class="modal-close" onclick="closeModal('termsModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <h4>1. Acceptance of Terms</h4>
                <p>By accessing and using TrackFlow ("the Service"), you acknowledge that you have read, understood, and
                    agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use
                    our Service.</p>

                <h4>2. Description of Service</h4>
                <p>TrackFlow is a personal finance management application that helps users track their income, expenses,
                    budgets, and financial goals. The Service is provided "as is" and we reserve the right to modify or
                    discontinue any aspect of the Service at any time.</p>

                <h4>3. User Accounts</h4>
                <ul>
                    <li>You must provide accurate and complete information when creating an account</li>
                    <li>You are responsible for maintaining the confidentiality of your account credentials</li>
                    <li>You must be at least 18 years old to use this Service</li>
                    <li>One person may not maintain more than one account</li>
                </ul>

                <h4>4. User Responsibilities</h4>
                <p>You agree to use the Service only for lawful purposes and in accordance with these Terms. You agree
                    not to:</p>
                <ul>
                    <li>Use the Service for any illegal or unauthorized purpose</li>
                    <li>Attempt to gain unauthorized access to any part of the Service</li>
                    <li>Interfere with or disrupt the integrity or performance of the Service</li>
                    <li>Upload or transmit any malicious code or content</li>
                </ul>

                <h4>5. Data and Privacy</h4>
                <p>Your use of the Service is also governed by our Privacy Policy. By using the Service, you consent to
                    the collection and use of your information as described in our Privacy Policy.</p>

                <h4>6. Financial Information Disclaimer</h4>
                <p>TrackFlow is a financial tracking tool and does not provide financial, investment, tax, or legal
                    advice. Any financial decisions you make based on information from the Service are your sole
                    responsibility.</p>

                <h4>7. Limitation of Liability</h4>
                <p>TrackFlow and its developers shall not be liable for any indirect, incidental, special,
                    consequential, or punitive damages resulting from your use of or inability to use the Service.</p>

                <h4>8. Changes to Terms</h4>
                <p>We reserve the right to modify these Terms at any time. We will notify users of any material changes
                    via email or through the Service. Your continued use of the Service after such modifications
                    constitutes acceptance of the updated Terms.</p>

                <h4>9. Termination</h4>
                <p>We may terminate or suspend your account and access to the Service at our sole discretion, without
                    prior notice, for conduct that we believe violates these Terms or is harmful to other users, us, or
                    third parties.</p>

                <h4>10. Contact Information</h4>
                <p>If you have any questions about these Terms, please contact us at support@trackflow.com</p>

                <p style="margin-top: 20px; color: #9ca3af; font-size: 12px;"><strong>Last Updated:</strong> January
                    2026</p>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('termsModal')">I Understand</button>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div class="modal-overlay" id="privacyModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-shield-halved"></i> Privacy Policy</h3>
                <button class="modal-close" onclick="closeModal('privacyModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <h4>1. Information We Collect</h4>
                <p>We collect information you provide directly to us, including:</p>
                <ul>
                    <li><strong>Account Information:</strong> Name, email address, and password when you create an
                        account</li>
                    <li><strong>Financial Data:</strong> Transaction details, income, expenses, budgets, and financial
                        goals you enter into the Service</li>
                    <li><strong>Usage Information:</strong> How you interact with our Service, including features used
                        and time spent</li>
                    <li><strong>Device Information:</strong> Browser type, operating system, and device identifiers</li>
                </ul>

                <h4>2. How We Use Your Information</h4>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide, maintain, and improve our Service</li>
                    <li>Process transactions and send related information</li>
                    <li>Send you technical notices, updates, and support messages</li>
                    <li>Respond to your comments, questions, and requests</li>
                    <li>Analyze usage patterns to enhance user experience</li>
                    <li>Detect, investigate, and prevent fraudulent transactions and other illegal activities</li>
                </ul>

                <h4>3. Data Security</h4>
                <p>We implement industry-standard security measures to protect your personal information, including:</p>
                <ul>
                    <li>256-bit SSL encryption for all data transmission</li>
                    <li>Encrypted storage of sensitive financial data</li>
                    <li>Regular security audits and vulnerability assessments</li>
                    <li>Secure password hashing using bcrypt algorithm</li>
                </ul>

                <h4>4. Data Sharing</h4>
                <p>We do not sell, trade, or rent your personal information to third parties. We may share your
                    information only in the following circumstances:</p>
                <ul>
                    <li>With your explicit consent</li>
                    <li>To comply with legal obligations or respond to lawful requests</li>
                    <li>To protect the rights, property, or safety of TrackFlow, our users, or others</li>
                    <li>With service providers who assist in operating our Service (under strict confidentiality
                        agreements)</li>
                </ul>

                <h4>5. Data Retention</h4>
                <p>We retain your personal information for as long as your account is active or as needed to provide you
                    services. You can request deletion of your account and associated data at any time through your
                    account settings.</p>

                <h4>6. Your Rights</h4>
                <p>You have the right to:</p>
                <ul>
                    <li>Access and receive a copy of your personal data</li>
                    <li>Rectify any inaccurate personal data</li>
                    <li>Request deletion of your personal data</li>
                    <li>Object to processing of your personal data</li>
                    <li>Export your data in a portable format</li>
                </ul>

                <h4>7. Cookies and Tracking</h4>
                <p>We use cookies and similar tracking technologies to track activity on our Service and hold certain
                    information. You can instruct your browser to refuse all cookies or to indicate when a cookie is
                    being sent.</p>

                <h4>8. Third-Party Links</h4>
                <p>Our Service may contain links to third-party websites. We are not responsible for the privacy
                    practices or content of these external sites.</p>

                <h4>9. Children's Privacy</h4>
                <p>Our Service is not intended for children under 18 years of age. We do not knowingly collect personal
                    information from children under 18.</p>

                <h4>10. Changes to This Policy</h4>
                <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the
                    new Privacy Policy on this page and updating the "Last Updated" date.</p>

                <h4>11. Contact Us</h4>
                <p>If you have any questions about this Privacy Policy, please contact us at privacy@trackflow.com</p>

                <p style="margin-top: 20px; color: #9ca3af; font-size: 12px;"><strong>Last Updated:</strong> January
                    2026</p>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('privacyModal')">I Understand</button>
            </div>
        </div>
    </div>
    @include('partials.pwa-install-prompt')
</body>

</html>