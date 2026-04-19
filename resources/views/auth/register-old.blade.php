<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TrackFlow</title>
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
            <p class="text-gray-300 mt-2">Create your account</p>
        </div>

        <!-- Register Card -->
        <div class="glass rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-white mb-6">Get Started</h2>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-200 mb-2">
                        Full Name
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="glass-input w-full px-4 py-3 rounded-lg" placeholder="John Doe">
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-200 mb-2">
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="glass-input w-full px-4 py-3 rounded-lg" placeholder="you@example.com">
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-200 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="glass-input w-full px-4 py-3 pr-12 rounded-lg" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-200 mb-2">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="glass-input w-full px-4 py-3 pr-12 rounded-lg" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                            <i class="fas fa-eye" id="password_confirmation-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms -->
                <div class="mb-6">
                    <label class="flex items-start">
                        <input type="checkbox" required
                            class="w-4 h-4 mt-1 text-blue-600 border-gray-400 rounded focus:ring-blue-500 bg-white/10">
                        <span class="ml-2 text-sm text-gray-300">
                            I agree to the <a href="#" class="text-blue-400 hover:text-blue-300">Terms of Service</a>
                            and <a href="#" class="text-blue-400 hover:text-blue-300">Privacy Policy</a>
                        </span>
                    </label>
                </div>

                <!-- Register Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    Create Account
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 glass text-gray-300">Or sign up with</span>
                    </div>
                </div>

                <!-- Social Register -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <button type="button" onclick="window.location.href='{{ url('/auth/google/redirect') }}'"
                        class="flex items-center justify-center px-4 py-3 glass-input border border-white/10 rounded-lg hover:bg-white/10 transition-colors">
                        <i class="fab fa-google text-red-400 mr-2"></i>
                        <span class="text-sm font-medium text-gray-300">Google</span>
                    </button>
                    <button type="button" onclick="window.location.href='{{ url('/auth/github/redirect') }}'"
                        class="flex items-center justify-center px-4 py-3 glass-input border border-white/10 rounded-lg hover:bg-white/10 transition-colors">
                        <i class="fab fa-github text-gray-300 mr-2"></i>
                        <span class="text-sm font-medium text-gray-300">GitHub</span>
                    </button>
                </div>
            </form>

            <!-- Login Link -->
            <p class="text-center text-sm text-gray-300 mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium">
                    Sign in
                </a>
            </p>
        </div>
    </div>

    <script>
        // Prevent authenticated users from accessing register page via back button
        (function () {
            @if(session('user_id'))
                // User is already logged in, redirect immediately
                window.location.href = '{{ route('dashboard') }}';
                return;
            @endif

            // Prevent back button from accessing this page after registration
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
    </script>
</body>

</html>