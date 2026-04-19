<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Construction-TrackFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #eef2ff;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-4xl card p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="px-4 py-6">
            <h1 class="text-3xl font-bold text-slate-800">This feature under construction</h1>
            <p class="mt-3 text-slate-600">We're performing scheduled maintenance. Some features (like social login) are
                temporarily unavailable. You can register or login below — the rest of the app will be available soon.
            </p>

            <div class="mt-6 flex gap-3">
                <button id="showLogin" class="px-4 py-2 bg-blue-600 text-white rounded-md">Login</button>
                <button id="showRegister"
                    class="px-4 py-2 bg-transparent border border-slate-300 text-slate-700 rounded-md">Register</button>
            </div>

            <div id="forms" class="mt-6">
                <!-- Login Form -->
                <form id="loginForm" method="POST" action="{{ route('login.post') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm text-slate-600">Email</label>
                        <input name="email" type="email" required class="mt-1 w-full px-3 py-2 border rounded-md" />
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600">Password</label>
                        <input name="password" type="password" required
                            class="mt-1 w-full px-3 py-2 border rounded-md" />
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2"><input type="checkbox" name="remember"> <span
                                class="text-sm">Remember me</span></label>
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600">Forgot?</a>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md">Sign
                            in</button>
                    </div>
                </form>

                <!-- Register Form -->
                <form id="registerForm" method="POST" action="{{ route('register.post') }}" class="space-y-3 hidden">
                    @csrf
                    <div>
                        <label class="block text-sm text-slate-600">Name</label>
                        <input name="name" type="text" required class="mt-1 w-full px-3 py-2 border rounded-md" />
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600">Email</label>
                        <input name="email" type="email" required class="mt-1 w-full px-3 py-2 border rounded-md" />
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600">Password</label>
                        <input name="password" type="password" required
                            class="mt-1 w-full px-3 py-2 border rounded-md" />
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600">Confirm Password</label>
                        <input name="password_confirmation" type="password" required
                            class="mt-1 w-full px-3 py-2 border rounded-md" />
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md">Create
                            account</button>
                    </div>
                </form>
            </div>

            <div class="mt-6 text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} TrackFlow. All rights reserved.</p>
            </div>
        </div>

        <div class="px-4 py-6 border-l md:border-l-0 md:border-l md:border-slate-100">
            <div class="flex flex-col items-center justify-center h-full">
                <img src="https://icons.iconarchive.com/icons/paomedia/small-n-flat/1024/sign-warning-icon.png"
                    alt="construction" class="w-40 h-40 opacity-80" />
                <p class="mt-4 text-slate-600">You can also use social providers when available. For now this button
                    demonstrates the page.</p>

                <div class="mt-6 w-full grid grid-cols-1 gap-3">
                    <a href="{{ route('under.construction') }}"
                        class="flex items-center justify-center gap-3 px-4 py-3 border rounded-md hover:bg-slate-50">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-6"
                            alt="Google"> Continue with Google
                    </a>
                    <a href="{{ route('under.construction') }}"
                        class="flex items-center justify-center gap-3 px-4 py-3 border rounded-md hover:bg-slate-50">
                        <img src="https://cdn-icons-png.flaticon.com/512/25/25231.png" class="w-6" alt="GitHub">
                        Continue with GitHub
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('showLogin').addEventListener('click', function () {
            document.getElementById('loginForm').classList.remove('hidden');
            document.getElementById('registerForm').classList.add('hidden');
        });
        document.getElementById('showRegister').addEventListener('click', function () {
            document.getElementById('registerForm').classList.remove('hidden');
            document.getElementById('loginForm').classList.add('hidden');
        });
    </script>
</body>

</html>