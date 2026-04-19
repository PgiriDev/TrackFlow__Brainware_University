<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - TrackFlow</title>
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
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            background: rgba(15, 23, 42, 0.6);
            cursor: pointer;
            transition: all 0.2s ease;
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

        <!-- Login Card -->
        <div class="glass rounded-3xl shadow-2xl overflow-hidden fade-in" style="animation-delay: 0.1s;">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-600 to-teal-500 px-8 py-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <i class="fas fa-sign-in-alt text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Welcome Back</h1>
                        <p class="text-teal-100 text-sm">Sign in to your account</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <!-- Alerts -->
                @if($errors->any() && !$errors->has('oauth'))
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl flex items-start gap-3 fade-in">
                        <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                        <p class="text-red-300 text-sm">{{ $errors->first() }}</p>
                    </div>
                @endif

                @if($errors->has('oauth'))
                    <div
                        class="mb-6 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-xl flex items-start gap-3 fade-in">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="text-yellow-300 text-sm font-semibold">Authentication provider not configured</p>
                            <p class="text-yellow-200/80 text-xs mt-1">{{ $errors->first('oauth') }}</p>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="text-yellow-400 hover:text-yellow-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('success'))
                    <div
                        class="mb-6 p-4 bg-teal-500/10 border border-teal-500/30 rounded-xl flex items-start gap-3 fade-in">
                        <i class="fas fa-check-circle text-teal-400 mt-0.5"></i>
                        <p class="text-teal-300 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                    @csrf

                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-envelope mr-2 text-teal-400"></i>Email Address
                        </label>
                        <div class="input-group relative">
                            <span class="input-icon"><i class="fas fa-at"></i></span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="glass-input w-full pl-12 pr-4 py-4 rounded-xl text-white"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2 text-teal-400"></i>Password
                        </label>
                        <div class="input-group relative">
                            <span class="input-icon"><i class="fas fa-key"></i></span>
                            <input type="password" id="password" name="password" required
                                class="glass-input w-full pl-12 pr-12 py-4 rounded-xl text-white"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-400 transition-colors">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="checkbox-custom">
                            <span
                                class="ml-3 text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Remember
                                me</span>
                        </label>
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-teal-400 hover:text-teal-300 font-medium transition-colors">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" id="loginBtn"
                        class="w-full btn-primary text-white font-semibold py-4 rounded-xl flex items-center justify-center gap-2 text-lg">
                        <i class="fas fa-arrow-right-to-bracket"></i>
                        <span>Sign In</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-slate-800/80 text-gray-400">or continue with</span>
                    </div>
                </div>

                <!-- Google Login Only -->
                <a href="{{ url('/auth/google/redirect') }}"
                    class="w-full btn-google text-white font-medium py-4 rounded-xl flex items-center justify-center gap-3 hover:shadow-lg">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#EA4335"
                            d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z" />
                        <path fill="#34A853"
                            d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2936293 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z" />
                        <path fill="#4A90E2"
                            d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5272727 23.1818182,9.81818182 L12,9.81818182 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z" />
                        <path fill="#FBBC05"
                            d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.2393662,17.3388335 L5.27698177,14.2678769 Z" />
                    </svg>
                    <span>Continue with Google</span>
                </a>

                <!-- Register Link -->
                <p class="text-center text-gray-400 mt-8">
                    Don't have an account?
                    <a href="{{ route('register') }}"
                        class="text-teal-400 hover:text-teal-300 font-semibold transition-colors ml-1">
                        Create one free
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
        // Prevent back navigation after login
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

        function togglePassword() {
            const field = document.getElementById('password');
            const icon = document.getElementById('password-eye');

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

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Signing in...</span>';
        });
    </script>
    @include('partials.pwa-install-prompt')
</body>

</html>