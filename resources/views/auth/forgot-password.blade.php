<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - TrackFlow</title>
    <link rel="icon" type="image/png" href="{{ asset('trackflow-main/fav-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

        .btn-primary {
            background: linear-gradient(135deg, var(--teal-600), var(--teal-500));
            transition: all 0.3s ease;
        }

        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--teal-500), var(--teal-400));
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(20, 184, 166, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
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

        .slide-in {
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
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

        /* Step indicator styles */
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .step-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .step-dot.active {
            background: var(--teal-500);
            box-shadow: 0 0 10px rgba(20, 184, 166, 0.5);
        }

        .step-dot.completed {
            background: var(--teal-400);
        }

        .step-line {
            width: 40px;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .step-line.active {
            background: var(--teal-500);
        }

        /* OTP Input styling */
        .otp-input {
            letter-spacing: 0.5em;
            font-family: 'Courier New', monospace;
        }

        /* Alert styles */
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .alert-success {
            background: rgba(20, 184, 166, 0.1);
            border: 1px solid rgba(20, 184, 166, 0.3);
        }

        /* Spinner animation */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
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
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
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
        <div class="text-center mb-8 fade-in">
            <a href="{{ url('/') }}" class="inline-block">
                <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow"
                    class="h-12 mx-auto mb-4 hover:scale-105 transition-transform">
            </a>
            <p class="text-gray-400 text-sm">Financial Management System</p>
        </div>

        <!-- Forgot Password Card -->
        <div class="glass rounded-3xl shadow-2xl overflow-hidden fade-in" style="animation-delay: 0.1s;">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-600 to-teal-500 px-8 py-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <i class="fas fa-key text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Reset Password</h1>
                        <p class="text-teal-100 text-sm">Recover your account access</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step-dot active" id="stepDot1"></div>
                    <div class="step-line" id="stepLine1"></div>
                    <div class="step-dot" id="stepDot2"></div>
                    <div class="step-line" id="stepLine2"></div>
                    <div class="step-dot" id="stepDot3"></div>
                </div>

                <!-- Alert Box -->
                <div id="alertBox" class="hidden mb-6 p-4 rounded-xl flex items-start gap-3 fade-in">
                    <i id="alertIcon" class="mt-0.5"></i>
                    <p id="alertMessage" class="text-sm flex-1"></p>
                    <button type="button" onclick="hideAlert()"
                        class="text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Step 1: Email Input -->
                <div id="step1" class="slide-in">
                    <p class="text-gray-400 mb-6 text-sm">
                        <i class="fas fa-info-circle text-teal-400 mr-2"></i>
                        Enter your email address and we'll send you an 8-digit OTP to reset your password.
                    </p>

                    <form id="emailForm">
                        @csrf
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fas fa-envelope mr-2 text-teal-400"></i>Email Address
                            </label>
                            <div class="input-group relative">
                                <span class="input-icon"><i class="fas fa-at"></i></span>
                                <input type="email" id="email" name="email" required
                                    class="glass-input w-full pl-12 pr-4 py-4 rounded-xl text-white"
                                    placeholder="you@example.com">
                            </div>
                        </div>

                        <button type="submit" id="sendOtpBtn"
                            class="w-full btn-primary text-white font-semibold py-4 rounded-xl flex items-center justify-center gap-2 text-lg">
                            <i class="fas fa-paper-plane"></i>
                            <span>Send OTP</span>
                        </button>
                    </form>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="step2" class="hidden">
                    <p class="text-gray-400 mb-6 text-sm">
                        <i class="fas fa-shield-halved text-teal-400 mr-2"></i>
                        Enter the 8-digit OTP sent to your email.
                    </p>

                    <form id="otpForm">
                        @csrf
                        <input type="hidden" id="email2" name="email">

                        <div class="mb-6">
                            <label for="otp" class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fas fa-hashtag mr-2 text-teal-400"></i>OTP Code
                            </label>
                            <div class="input-group relative">
                                <span class="input-icon"><i class="fas fa-lock"></i></span>
                                <input type="text" id="otp" name="otp" maxlength="8" required
                                    class="glass-input w-full pl-12 pr-4 py-4 rounded-xl text-white text-center text-xl otp-input"
                                    placeholder="00000000">
                            </div>
                            <p class="text-xs text-gray-500 mt-2 text-center">
                                <i class="fas fa-clock mr-1"></i>OTP expires in 10 minutes
                            </p>
                        </div>

                        <button type="submit" id="verifyOtpBtn"
                            class="w-full btn-primary text-white font-semibold py-4 rounded-xl flex items-center justify-center gap-2 text-lg">
                            <i class="fas fa-check-circle"></i>
                            <span>Verify OTP</span>
                        </button>
                    </form>

                    <button onclick="backToStep1()"
                        class="w-full mt-4 btn-secondary text-gray-300 font-medium py-3 rounded-xl flex items-center justify-center gap-2 hover:text-white">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to email</span>
                    </button>
                </div>

                <!-- Step 3: Reset Password -->
                <div id="step3" class="hidden">
                    <p class="text-gray-400 mb-6 text-sm">
                        <i class="fas fa-lock text-teal-400 mr-2"></i>
                        Create a new secure password for your account.
                    </p>

                    <form id="resetForm">
                        @csrf
                        <input type="hidden" id="email3" name="email">
                        <input type="hidden" id="otp3" name="otp">

                        <div class="mb-5">
                            <label for="new_password" class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fas fa-lock mr-2 text-teal-400"></i>New Password
                            </label>
                            <div class="input-group relative">
                                <span class="input-icon"><i class="fas fa-key"></i></span>
                                <input type="password" id="new_password" name="password" required minlength="8"
                                    class="glass-input w-full pl-12 pr-12 py-4 rounded-xl text-white"
                                    placeholder="••••••••" oninput="checkPasswordStrength()">
                                <button type="button" onclick="togglePasswordField('new_password')"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-400 transition-colors">
                                    <i class="fas fa-eye" id="new_password-eye"></i>
                                </button>
                            </div>
                            <!-- Password Strength Indicator -->
                            <div class="strength-indicator" id="strengthIndicator" style="display: none;">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strengthFill"></div>
                                </div>
                                <span class="strength-text strength-blink" id="strengthText">Poor</span>
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

                        <div class="mb-6">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fas fa-check-double mr-2 text-teal-400"></i>Confirm Password
                            </label>
                            <div class="input-group relative">
                                <span class="input-icon"><i class="fas fa-key"></i></span>
                                <input type="password" id="confirm_password" name="password_confirmation" required
                                    minlength="8" class="glass-input w-full pl-12 pr-12 py-4 rounded-xl text-white"
                                    placeholder="••••••••" oninput="checkPasswordMatch()">
                                <button type="button" onclick="togglePasswordField('confirm_password')"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-400 transition-colors">
                                    <i class="fas fa-eye" id="confirm_password-eye"></i>
                                </button>
                            </div>
                            <!-- Password Match Indicator -->
                            <div class="requirement-item mt-2" id="passwordMatchIndicator" style="display: none;">
                                <i class="fas fa-circle"></i>
                                <span id="passwordMatchText">Passwords match</span>
                            </div>
                        </div>

                        <button type="submit" id="resetPasswordBtn"
                            class="w-full btn-primary text-white font-semibold py-4 rounded-xl flex items-center justify-center gap-2 text-lg">
                            <i class="fas fa-shield-halved"></i>
                            <span>Reset Password</span>
                        </button>
                    </form>
                </div>

                <!-- Back to Login -->
                <p class="text-center text-gray-400 mt-8">
                    Remember your password?
                    <a href="{{ route('login') }}"
                        class="text-teal-400 hover:text-teal-300 font-semibold transition-colors ml-1">
                        Back to login
                    </a>
                </p>
            </div>
        </div>

        <!-- Security Badge -->
        <div class="mt-8 text-center fade-in" style="animation-delay: 0.3s;">
            <div class="glass rounded-xl p-4 inline-flex items-center gap-3">
                <i class="fas fa-shield-halved text-teal-400"></i>
                <p class="text-gray-400 text-sm">
                    Secured with 256-bit encryption
                </p>
            </div>
        </div>
    </div>

    <script>
        // Prevent back button navigation and cache
        (function () {
            @if(session('user_id'))
                // User is already logged in, redirect to dashboard
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

            // Force reload if page loaded from cache
            window.onpageshow = function (event) {
                if (event.persisted) {
                    window.location.reload();
                }
            };
        })();

        function showAlert(message, type) {
            const alertBox = document.getElementById('alertBox');
            const alertMessage = document.getElementById('alertMessage');
            const alertIcon = document.getElementById('alertIcon');

            alertBox.classList.remove('hidden', 'alert-error', 'alert-success');

            if (type === 'error') {
                alertBox.classList.add('alert-error');
                alertIcon.className = 'fas fa-exclamation-circle text-red-400 mt-0.5';
                alertMessage.className = 'text-red-300 text-sm flex-1';
            } else {
                alertBox.classList.add('alert-success');
                alertIcon.className = 'fas fa-check-circle text-teal-400 mt-0.5';
                alertMessage.className = 'text-teal-300 text-sm flex-1';
            }

            alertMessage.textContent = message;
        }

        function hideAlert() {
            document.getElementById('alertBox').classList.add('hidden');
        }

        function togglePasswordField(fieldId) {
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

        function updateStepIndicator(step) {
            // Reset all
            for (let i = 1; i <= 3; i++) {
                document.getElementById('stepDot' + i).classList.remove('active', 'completed');
            }
            document.getElementById('stepLine1').classList.remove('active');
            document.getElementById('stepLine2').classList.remove('active');

            // Set active and completed states
            for (let i = 1; i <= step; i++) {
                if (i < step) {
                    document.getElementById('stepDot' + i).classList.add('completed');
                } else {
                    document.getElementById('stepDot' + i).classList.add('active');
                }
            }
            if (step >= 2) document.getElementById('stepLine1').classList.add('active');
            if (step >= 3) document.getElementById('stepLine2').classList.add('active');
        }

        function setButtonLoading(btnId, isLoading, loadingText, originalHtml) {
            const btn = document.getElementById(btnId);
            if (isLoading) {
                btn.disabled = true;
                btn.innerHTML = `<i class="fas fa-spinner animate-spin"></i><span>${loadingText}</span>`;
            } else {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        function backToStep1() {
            document.getElementById('step1').classList.remove('hidden');
            document.getElementById('step1').classList.add('slide-in');
            document.getElementById('step2').classList.add('hidden');
            hideAlert();
            updateStepIndicator(1);
        }

        // Step 1: Send OTP
        document.getElementById('emailForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const originalHtml = '<i class="fas fa-paper-plane"></i><span>Send OTP</span>';

            setButtonLoading('sendOtpBtn', true, 'Sending OTP...', originalHtml);
            hideAlert();

            try {
                const response = await fetch('{{ route("password.email") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    document.getElementById('email2').value = email;
                    document.getElementById('step1').classList.add('hidden');
                    document.getElementById('step2').classList.remove('hidden');
                    document.getElementById('step2').classList.add('slide-in');
                    updateStepIndicator(2);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
            } finally {
                setButtonLoading('sendOtpBtn', false, '', originalHtml);
            }
        });

        // Step 2: Verify OTP
        document.getElementById('otpForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email2').value;
            const otp = document.getElementById('otp').value;
            const originalHtml = '<i class="fas fa-check-circle"></i><span>Verify OTP</span>';

            setButtonLoading('verifyOtpBtn', true, 'Verifying...', originalHtml);
            hideAlert();

            try {
                const response = await fetch('{{ route("password.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ email, otp })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    document.getElementById('email3').value = email;
                    document.getElementById('otp3').value = otp;
                    document.getElementById('step2').classList.add('hidden');
                    document.getElementById('step3').classList.remove('hidden');
                    document.getElementById('step3').classList.add('slide-in');
                    updateStepIndicator(3);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
            } finally {
                setButtonLoading('verifyOtpBtn', false, '', originalHtml);
            }
        });

        // Step 3: Reset Password
        document.getElementById('resetForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email3').value;
            const otp = document.getElementById('otp3').value;
            const password = document.getElementById('new_password').value;
            const password_confirmation = document.getElementById('confirm_password').value;
            const originalHtml = '<i class="fas fa-shield-halved"></i><span>Reset Password</span>';

            if (password !== password_confirmation) {
                showAlert('Passwords do not match', 'error');
                return;
            }

            setButtonLoading('resetPasswordBtn', true, 'Resetting...', originalHtml);
            hideAlert();

            try {
                const response = await fetch('{{ route("password.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ email, otp, password, password_confirmation })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message + ' Redirecting to login...', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("login") }}';
                    }, 2000);
                } else {
                    showAlert(data.message, 'error');
                    setButtonLoading('resetPasswordBtn', false, '', originalHtml);
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
                setButtonLoading('resetPasswordBtn', false, '', originalHtml);
            }
        });

        // Auto-format OTP input (numbers only)
        document.getElementById('otp').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Password Strength Checker
        function checkPasswordStrength() {
            const password = document.getElementById('new_password').value;
            const strengthIndicator = document.getElementById('strengthIndicator');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');

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
            checkPasswordMatch();
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

        function checkPasswordMatch() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchIndicator = document.getElementById('passwordMatchIndicator');
            const matchText = document.getElementById('passwordMatchText');
            const icon = matchIndicator.querySelector('i');

            if (confirmPassword.length > 0) {
                matchIndicator.style.display = 'flex';
                matchIndicator.classList.remove('valid', 'invalid', 'neutral');
                icon.classList.remove('fa-circle', 'fa-check-circle', 'fa-times-circle');

                if (password === confirmPassword) {
                    matchIndicator.classList.add('valid');
                    icon.classList.add('fa-check-circle');
                    matchText.textContent = 'Passwords match';
                } else {
                    matchIndicator.classList.add('invalid');
                    icon.classList.add('fa-times-circle');
                    matchText.textContent = 'Passwords do not match';
                }
            } else {
                matchIndicator.style.display = 'none';
            }
        }

        // Initialize step indicator
        updateStepIndicator(1);
    </script>
</body>

</html>