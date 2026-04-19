<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Two-Factor Authentication - TrackFlow</title>
    <link rel="icon" type="image/png" href="{{ asset('trackflow-main/fav-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --teal-500: #14b8a6;
            --teal-600: #0d9488;
            --teal-400: #2dd4bf;
        }

        body {
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

        .otp-input {
            letter-spacing: 0.5em;
            text-indent: 0.5em;
            font-family: 'Courier New', monospace;
        }

        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
        }

        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.4);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(20, 184, 166, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(20, 184, 166, 0);
            }
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

        .countdown-ring {
            transform: rotate(-90deg);
        }

        .method-badge {
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.2), rgba(13, 148, 136, 0.1));
            border: 1px solid rgba(20, 184, 166, 0.3);
        }

        .method-badge-email {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(124, 58, 237, 0.1));
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Background decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-md w-full relative z-10">
        <!-- Logo -->
        <div class="text-center mb-8 fade-in">
            <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow" class="h-12 mx-auto mb-4">
        </div>

        <div class="glass rounded-3xl shadow-2xl p-8 fade-in">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6 pulse-ring
                    @if(session('2fa_method') === 'email')
                        bg-gradient-to-br from-purple-500/20 to-blue-500/20 border border-purple-500/30
                    @else
                        bg-gradient-to-br from-teal-500/20 to-green-500/20 border border-teal-500/30
                    @endif">
                    @if(session('2fa_method') === 'email')
                        <i class="fas fa-envelope text-4xl text-purple-400"></i>
                    @else
                        <i class="fas fa-mobile-screen text-4xl text-teal-400"></i>
                    @endif
                </div>

                <h1 class="text-2xl font-bold text-white mb-2">Two-Factor Authentication</h1>

                @if(session('2fa_remembered_login'))
                    <!-- Welcome back message for remembered users -->
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full mt-2 mb-2 bg-gradient-to-r from-green-500/20 to-teal-500/20 border border-green-500/30">
                        <i class="fas fa-user-check text-green-400 text-sm"></i>
                        <span class="text-green-300 text-sm font-medium">Welcome back! Just verify your identity</span>
                    </div>
                @endif

                <!-- Method Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mt-2
                    @if(session('2fa_method') === 'email')
                        method-badge-email
                    @else
                        method-badge
                    @endif">
                    @if(session('2fa_method') === 'email')
                        <i class="fas fa-envelope text-purple-400 text-sm"></i>
                        <span class="text-purple-300 text-sm font-medium">Email Verification</span>
                    @else
                        <i class="fas fa-mobile-alt text-teal-400 text-sm"></i>
                        <span class="text-teal-300 text-sm font-medium">Authenticator App</span>
                    @endif
                </div>

                <p class="text-gray-400 mt-4 text-sm leading-relaxed">
                    @if(session('2fa_remembered_login'))
                        Your device is remembered. Just enter your 2FA code to continue.
                    @elseif(session('2fa_method') === 'email')
                        Enter the 6-digit code we sent to your registered email address
                    @else
                        Enter the 6-digit code from your authenticator app (Google Authenticator, Authy, etc.)
                    @endif
                </p>
            </div>

            <!-- Alerts -->
            @if(session('info'))
                <div class="mb-6 p-4 bg-teal-500/10 border border-teal-500/30 rounded-xl flex items-start gap-3 fade-in">
                    <i class="fas fa-check-circle text-teal-400 mt-0.5"></i>
                    <p class="text-teal-300 text-sm">{{ session('info') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl flex items-start gap-3 fade-in">
                    <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                    <p class="text-red-300 text-sm">{{ $errors->first() }}</p>
                </div>
            @endif

            <!-- Verification Form -->
            <form id="verifyForm" action="{{ route('2fa.verify.post') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="code" class="block text-sm font-medium text-gray-300 mb-3">
                        <i class="fas fa-key mr-2 text-teal-400"></i>Verification Code
                    </label>
                    <input type="text" id="code" name="code" maxlength="6" required autofocus
                        autocomplete="one-time-code" inputmode="numeric" pattern="[0-9]*"
                        class="glass-input w-full px-4 py-4 rounded-xl text-center text-3xl font-bold tracking-[0.3em] otp-input"
                        placeholder="••••••">

                    <!-- Countdown Timer for Email OTP -->
                    @if(session('2fa_method') === 'email')
                        <div id="timerContainer" class="mt-4 text-center">
                            <div class="inline-flex items-center gap-3 bg-slate-800/50 px-4 py-2 rounded-lg">
                                <svg class="w-5 h-5 countdown-ring" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="16" fill="none" stroke="#334155" stroke-width="3"></circle>
                                    <circle id="timerRing" cx="18" cy="18" r="16" fill="none" stroke="#14b8a6"
                                        stroke-width="3" stroke-dasharray="100.53" stroke-dashoffset="0"
                                        stroke-linecap="round"></circle>
                                </svg>
                                <span id="countdown" class="text-gray-400 text-sm font-medium">Code expires in <span
                                        id="timerText" class="text-teal-400 font-bold">10:00</span></span>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-500 mt-3 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Codes refresh every 30 seconds
                        </p>
                    @endif
                </div>

                <button type="submit" id="verifyBtn"
                    class="w-full btn-primary text-white font-semibold py-4 rounded-xl transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-shield-check"></i>
                    <span>Verify & Sign In</span>
                </button>
            </form>

            <!-- Resend OTP (Email method only) -->
            @if(session('2fa_method') === 'email')
                <div class="mt-6 text-center">
                    <p class="text-gray-500 text-sm mb-2">Didn't receive the code?</p>
                    <button id="resendBtn" onclick="resendOtp()"
                        class="text-teal-400 hover:text-teal-300 text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-redo mr-1"></i>
                        <span id="resendText">Resend Code</span>
                    </button>
                </div>
            @endif

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-slate-800/80 text-gray-500">or</span>
                </div>
            </div>

            <!-- Recovery Code Option -->
            <div id="recoverySection">
                <button type="button" onclick="toggleRecoveryForm()"
                    class="w-full py-3 px-4 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:border-white/20 transition-all flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-key"></i>
                    <span>Use Recovery Code Instead</span>
                    <i id="recoveryArrow" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>

                <div id="recoveryForm" class="hidden mt-4 pt-4 border-t border-white/10 fade-in">
                    <form action="{{ route('2fa.recovery') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="recovery_code" class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fas fa-life-ring mr-2 text-yellow-400"></i>Recovery Code
                            </label>
                            <input type="text" id="recovery_code" name="recovery_code" maxlength="10" required
                                class="glass-input w-full px-4 py-3 rounded-xl text-center font-mono uppercase tracking-wider"
                                placeholder="XXXXXXXXXX">
                            <p class="text-xs text-gray-500 mt-2 text-center">Enter one of your saved recovery codes</p>
                        </div>
                        <button type="submit"
                            class="w-full bg-yellow-600/80 hover:bg-yellow-500 text-white font-semibold py-3 rounded-xl transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-unlock"></i>
                            <span>Verify Recovery Code</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Back to Login -->
            <div class="mt-8 text-center">
                <a href="{{ route('login') }}"
                    class="text-gray-400 hover:text-white text-sm transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Back to Login</span>
                </a>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-8 text-center">
            <div class="glass rounded-xl p-4 inline-flex items-center gap-3">
                <i class="fas fa-question-circle text-teal-400"></i>
                <p class="text-gray-400 text-sm">
                    Having trouble?
                    <a href="mailto:support@trackflow.app"
                        class="text-teal-400 hover:text-teal-300 transition-colors">Contact Support</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Prevent back button navigation
        (function () {
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

        // OTP Input handling
        const codeInput = document.getElementById('code');
        codeInput.addEventListener('input', function (e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');

            // Auto-submit when 6 digits are entered
            if (this.value.length === 6) {
                document.getElementById('verifyBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Verifying...';
                document.getElementById('verifyBtn').disabled = true;
                setTimeout(() => {
                    document.getElementById('verifyForm').submit();
                }, 300);
            }
        });

        // Toggle recovery form
        function toggleRecoveryForm() {
            const form = document.getElementById('recoveryForm');
            const arrow = document.getElementById('recoveryArrow');
            form.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }

        // Countdown timer for email OTP
        @if(session('2fa_method') === 'email')
            let timeLeft = 600; // 10 minutes
            const timerText = document.getElementById('timerText');
            const timerRing = document.getElementById('timerRing');
            const resendBtn = document.getElementById('resendBtn');
            const circumference = 100.53; // 2 * PI * 16

            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerText.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                // Update ring
                const offset = circumference - (timeLeft / 600) * circumference;
                timerRing.style.strokeDashoffset = offset;

                if (timeLeft <= 60) {
                    timerText.classList.remove('text-teal-400');
                    timerText.classList.add('text-red-400');
                    timerRing.style.stroke = '#ef4444';
                }

                if (timeLeft <= 0) {
                    timerText.textContent = 'Expired';
                    if (resendBtn) resendBtn.disabled = false;
                } else {
                    timeLeft--;
                    setTimeout(updateTimer, 1000);
                }
            }
            updateTimer();

            // Resend OTP
            let resendCooldown = 0;
            async function resendOtp() {
                if (resendCooldown > 0) return;

                const resendText = document.getElementById('resendText');
                resendText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sending...';
                resendBtn.disabled = true;

                try {
                    const response = await fetch('/2fa/resend', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Reset timer
                        timeLeft = 600;
                        timerText.classList.remove('text-red-400');
                        timerText.classList.add('text-teal-400');
                        timerRing.style.stroke = '#14b8a6';

                        // Show success message
                        showToast('New verification code sent!', 'success');

                        // Start cooldown
                        resendCooldown = 60;
                        updateResendCooldown();
                    } else {
                        showToast(data.message || 'Failed to resend code', 'error');
                        resendBtn.disabled = false;
                        resendText.innerHTML = 'Resend Code';
                    }
                } catch (error) {
                    showToast('Failed to resend code. Please try again.', 'error');
                    resendBtn.disabled = false;
                    resendText.innerHTML = 'Resend Code';
                }
            }

            function updateResendCooldown() {
                const resendText = document.getElementById('resendText');
                if (resendCooldown > 0) {
                    resendText.textContent = `Resend in ${resendCooldown}s`;
                    resendCooldown--;
                    setTimeout(updateResendCooldown, 1000);
                } else {
                    resendText.innerHTML = 'Resend Code';
                    resendBtn.disabled = false;
                }
            }
        @endif

        // Toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3 fade-in
                ${type === 'success' ? 'bg-teal-500 text-white' :
                    type === 'error' ? 'bg-red-500 text-white' :
                        'bg-blue-500 text-white'}`;
            toast.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-times-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>

</html>