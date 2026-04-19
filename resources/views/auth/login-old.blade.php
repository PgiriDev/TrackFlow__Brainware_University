<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TrackFlow</title>
    <link rel="icon" type="image/png" href="{{ asset('trackflow-main/fav-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
        }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(102, 126, 234, 0.5);
            outline: none;
            ring: 2px;
            ring-color: rgba(102, 126, 234, 0.3);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl shadow-lg mb-4">
                <i class="fas fa-chart-line text-2xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">TrackFlow</h1>
            <p class="text-gray-300 mt-2">Financial Management System</p>
        </div>

        <!-- Login Card -->
        <div class="glass rounded-2xl shadow-xl p-8">
            <!-- Face Login Dynamic Island -->
            <div id="faceLoginIsland"
                class="fixed top-8 left-1/2 -translate-x-1/2 z-50 hidden flex-col items-center justify-center">
                <div
                    class="bg-white/90 dark:bg-gray-900/90 rounded-2xl shadow-2xl px-6 py-4 flex flex-col items-center border-2 border-blue-500 relative animate-fade-in">
                    <div class="relative flex items-center justify-center mb-2">
                        <div class="absolute z-10 w-20 h-20 rounded-full border-4 border-blue-500 animate-pulse"></div>
                        <video id="faceLoginVideo" autoplay muted playsinline
                            class="rounded-full w-20 h-20 object-cover bg-black"></video>
                        <div id="faceLoginCheckmark"
                            class="absolute z-20 w-20 h-20 flex items-center justify-center hidden">
                            <div
                                class="rounded-full bg-green-500/80 w-20 h-20 flex items-center justify-center animate-pulse">
                                <i class="fas fa-check text-white text-3xl animate-bounce"></i>
                            </div>
                        </div>
                    </div>
                    <div class="w-40 h-1 bg-gray-300 rounded-full overflow-hidden mb-2">
                        <div id="faceLoginProgress" class="h-1 bg-blue-500 rounded-full transition-all duration-200"
                            style="width:0%"></div>
                    </div>
                    <div id="faceLoginMsg" class="text-xs text-gray-700 dark:text-gray-200 mb-1 text-center">Align your
                        face in the circle</div>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-white mb-6">Welcome Back</h2>

            @if($errors->any() && !$errors->has('oauth'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $errors->first() }}
                    </p>
                </div>
            @endif

            @if($errors->has('oauth'))
                <div id="oauthError"
                    class="mb-4 p-4 bg-yellow-50 border border-yellow-300 rounded-lg flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                        <div>
                            <p class="text-sm text-yellow-900 font-semibold">Authentication provider not configured</p>
                            <p class="text-sm text-yellow-800">{{ $errors->first('oauth') }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('oauthError').remove()"
                        class="text-yellow-700 ml-4">Dismiss</button>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-200 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="glass-input w-full pl-10 pr-4 py-3 rounded-lg" placeholder="you@example.com">
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-200 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                            class="glass-input w-full pl-10 pr-12 py-3 rounded-lg" placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 text-blue-600 border-gray-400 rounded focus:ring-blue-500 bg-white/10">
                        <span class="ml-2 text-sm text-gray-300">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}"
                        class="text-sm text-blue-400 hover:text-blue-300 font-medium">
                        Forgot password?
                    </a>
                </div>

                <!-- Login Button -->
                <!-- Face Login Button -->
                <button type="button" onclick="openFaceLoginIsland()"
                    class="w-full mt-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i class="fas fa-user-lock"></i> Login with Face
                </button>
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 glass text-gray-300">Or continue with</span>
                </div>
            </div>

            <!-- Social Login -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Progressive anchor fallback: show under-construction page for social providers -->
                <a id="googleBtn" href="{{ route('under.construction') }}"
                    class="flex items-center justify-center px-4 py-3 glass-input border border-white/10 rounded-lg hover:bg-white/10 transition-colors">
                    <i class="fab fa-google text-red-400 mr-2"></i>
                    <span class="text-sm font-medium text-gray-300">Google</span>
                </a>
                <button type="button" onclick="window.location.href='{{ route('under.construction') }}'"
                    class="flex items-center justify-center px-4 py-3 glass-input border border-white/10 rounded-lg hover:bg-white/10 transition-colors">
                    <i class="fab fa-github text-gray-300 mr-2"></i>
                    <span class="text-sm font-medium text-gray-300">GitHub</span>
                </button>
            </div>

            <!-- Register Link -->
            <p class="text-center text-sm text-gray-300 mt-6">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-medium">
                    Sign up for free
                </a>
            </p>
        </div>

        <!-- Demo Notice -->
        <div class="mt-6 p-4 glass rounded-lg border border-white/10">
            <p class="text-sm text-gray-300">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Demo Mode:</strong> Register a new account or use existing credentials to login.
            </p>
        </div>
    </div>

    <script>
        // Face Login Dynamic Island Logic
        // (The actual script is included below, not as raw HTML)

        // Force reload if page loaded from cache
        window.onpageshow = function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        };
            }) ();

        // Simple Google OAuth navigation (no fetch, no dynamic script injection)
        document.addEventListener('DOMContentLoaded', function () {
            const googleBtn = document.getElementById('googleBtn');
            if (!googleBtn) return;

            // Anchor already points to the server route; if JS is enabled we'll ensure navigation
            googleBtn.addEventListener('click', function (e) {
                // Let the anchor behave naturally; this handler is a no-op to avoid interference
                // but keeps parity if other code expects an element with id 'googleBtn'.
                return true;
            });
        });

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
    </script>
</body>

</html>