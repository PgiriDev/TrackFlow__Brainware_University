<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\TransactionController;
use App\Http\Controllers\Web\BudgetController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\GoalController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\SettingController;
use App\Http\Controllers\Web\GroupExpenseController;
use App\Http\Controllers\Web\UpiController;
use App\Http\Controllers\Web\NotificationController;
use App\Services\EmailNotificationService;
use App\Services\RememberMeService;
use Laravel\Socialite\Facades\Socialite;

// ============================================
// TEST EMAIL ROUTE - REMOVE AFTER TESTING
// ============================================
Route::get('/test-email', function () {
    try {
        $testEmail = 'trackflow.info@gmail.com';
        $otp = '12345678'; // Test OTP

        // Send test email using the new template
        \Illuminate\Support\Facades\Mail::send('email-template.password-change-otp', [
            'otp' => $otp,
            'userName' => 'Test User',
            'title' => 'Test Email - Password Change OTP',
            'subject' => 'Test Email - TrackFlow'
        ], function ($message) use ($testEmail) {
            $message->to($testEmail)
                ->subject('Test Email - Password Change OTP - TrackFlow');
        });

        return response()->json([
            'success' => true,
            'message' => 'Test email sent successfully to ' . $testEmail,
            'note' => 'Check your inbox (and spam folder) for the test email.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to send email: ' . $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
});
// ============================================

// ============================================
// PUBLIC PAYMENT REQUEST PAGE (No Auth Required)
// ============================================
Route::get('/pay/{token}', [GroupExpenseController::class, 'showPaymentRequest'])->name('payment.request');

// Home route - Show landing page or redirect to dashboard
Route::get('/', function () {
    // If user is logged in, redirect to dashboard
    if (session('user_id')) {
        return redirect()->route('dashboard');
    }
    // Otherwise show landing page
    return view('landing');
})->name('home');

// Authentication routes
Route::get('/login', function () {
    if (session('user_id')) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');


Route::post('/login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        // Initialize trusted device service
        $trustedDeviceService = new \App\Services\TrustedDeviceService();

        // Check if this is a new/untrusted device that requires 2FA
        $isDeviceTrusted = $trustedDeviceService->isDeviceTrusted($user->id, $request);
        $wasDeviceRevoked = $trustedDeviceService->wasDeviceRevoked($user->id, $request);

        // Determine if 2FA verification is needed
        // 2FA is required if:
        // 1. User has 2FA enabled AND device is not trusted (new device)
        // 2. OR device was previously revoked (requires re-authentication)
        $requires2FA = false;

        if ($user->two_factor_enabled && $user->two_factor_secret) {
            // If device is not trusted or was revoked, require 2FA
            if (!$isDeviceTrusted || $wasDeviceRevoked) {
                $requires2FA = true;
            }
        }

        // If 2FA is required
        if ($requires2FA) {
            // Store user ID temporarily for 2FA verification
            session()->put('2fa_user_id', $user->id);
            session()->put('2fa_remember', $request->has('remember'));
            session()->put('2fa_new_device', !$isDeviceTrusted);
            session()->put('2fa_revoked_device', $wasDeviceRevoked);

            // Check 2FA method type
            if ($user->two_factor_secret === 'email_otp') {
                // Email OTP method - send code and redirect
                $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                // Store OTP in database
                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'token' => $otp,
                        'created_at' => now()
                    ]
                );

                // Send OTP email using template
                try {
                    \Illuminate\Support\Facades\Mail::send('email-template.login-verification-otp', [
                        'userName' => $user->name ?? 'User',
                        'otp' => $otp,
                        'title' => 'Login Verification',
                        'ipAddress' => request()->ip(),
                        'browser' => request()->header('User-Agent')
                    ], function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('🔐 Login Verification Code - TrackFlow');
                    });
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send login OTP email: ' . $e->getMessage());
                }

                session()->put('2fa_method', 'email');
                return redirect()->route('2fa.verify')->with('info', 'Verification code sent to your email');
            } else {
                // Authenticator app method
                session()->put('2fa_method', 'app');
                return redirect()->route('2fa.verify');
            }
        }

        // Regular login without 2FA (or device is already trusted)
        session()->put('user_id', $user->id);
        session()->put('user_name', $user->name);
        session()->put('user_email', $user->email);

        // Mark this device/session as trusted
        $trustedDeviceService->createTrustedSession($user->id, $request);

        // Handle Remember Me functionality
        $rememberCookie = null;
        if ($request->has('remember')) {
            $rememberMeService = new RememberMeService();
            $rememberCookie = $rememberMeService->createToken($user, $request);
        }

        // Send security alert email for new login
        $emailService = new EmailNotificationService();
        $userAgent = request()->header('User-Agent');
        $deviceType = 'Unknown';
        if (str_contains($userAgent, 'Mobile'))
            $deviceType = 'Mobile';
        elseif (str_contains($userAgent, 'Windows'))
            $deviceType = 'Windows PC';
        elseif (str_contains($userAgent, 'Macintosh'))
            $deviceType = 'Mac';
        elseif (str_contains($userAgent, 'Linux'))
            $deviceType = 'Linux';

        $emailService->sendSecurityAlert($user->id, [
            'alert_type' => 'login',
            'alert_title' => 'New Login Detected',
            'alert_message' => 'A new login was detected on your TrackFlow account.',
            'details' => [
                'Time' => now()->format('M d, Y h:i A'),
                'IP Address' => request()->ip(),
                'Device' => $deviceType,
            ],
            'action_required' => false,
            'action_url' => url('/settings/security'),
            'action_text' => 'Review Security Settings',
        ]);

        // Link user to any pending group memberships with matching email
        $linkedGroups = \App\Services\GroupMemberLinkService::linkUserToGroupMembers($user);

        // Redirect to intended URL or dashboard
        $intendedUrl = session()->pull('url.intended', route('dashboard'));
        $message = 'Login successful!';
        if (count($linkedGroups) > 0) {
            $message .= ' You have been added to ' . count($linkedGroups) . ' new group(s).';
        }

        // Return redirect with remember cookie if set
        $redirect = redirect($intendedUrl)->with('success', $message);
        if ($rememberCookie) {
            $redirect = $redirect->withCookie($rememberCookie);
        }
        return $redirect;
    }

    return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
})->name('login.post');

// 2FA Verification Routes
Route::get('/2fa/verify', function () {
    if (!session('2fa_user_id')) {
        return redirect()->route('login');
    }
    return view('auth.2fa-verify');
})->name('2fa.verify');

Route::post('/2fa/verify', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'code' => 'required|string|size:6'
    ]);

    $userId = session('2fa_user_id');
    $method = session('2fa_method', 'app'); // Default to app if not set

    if (!$userId) {
        return redirect()->route('login')->withErrors(['error' => 'Session expired']);
    }

    $user = \App\Models\User::find($userId);
    if (!$user || !$user->two_factor_secret) {
        return redirect()->route('login')->withErrors(['error' => 'Invalid session']);
    }

    $valid = false;

    // Check 2FA method
    if ($method === 'email' || $user->two_factor_secret === 'email_otp') {
        // Email OTP verification
        $otpRecord = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        if ($otpRecord) {
            // Check if OTP is expired (10 minutes)
            $createdAt = new \DateTime($otpRecord->created_at);
            $now = new \DateTime();
            $diff = $now->getTimestamp() - $createdAt->getTimestamp();

            if ($diff <= 600 && $otpRecord->token === $request->code) {
                $valid = true;
                // Delete used OTP
                DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            }
        }
    } else {
        // Authenticator app verification
        $google2fa = app(\PragmaRX\Google2FA\Google2FA::class);
        $secret = decrypt($user->two_factor_secret);
        $valid = $google2fa->verifyKey($secret, $request->code);
    }

    if (!$valid) {
        return back()->withErrors(['error' => 'Invalid or expired verification code']);
    }

    // Check if Remember Me was checked during login
    $shouldRemember = session('2fa_remember', false);
    $wasRememberedLogin = session('2fa_remembered_login', false);

    // Login successful - clear 2FA session data
    session()->forget(['2fa_user_id', '2fa_remember', '2fa_method', '2fa_remembered_login', '2fa_new_device', '2fa_revoked_device']);
    session()->put('user_id', $user->id);
    session()->put('user_name', $user->name);
    session()->put('user_email', $user->email);

    // Mark this device/session as trusted after successful 2FA verification
    $trustedDeviceService = new \App\Services\TrustedDeviceService();
    $trustedDeviceService->createTrustedSession($user->id, $request);

    // Handle Remember Me functionality
    $rememberCookie = null;
    if ($shouldRemember) {
        $rememberMeService = new RememberMeService();
        if ($wasRememberedLogin) {
            // User came from remembered login - rotate the existing token
            $rememberCookie = $rememberMeService->rotateToken($user, $request);
        } else {
            // Fresh login with remember me checked - create new token
            $rememberCookie = $rememberMeService->createToken($user, $request);
        }
    }

    // Send security alert email for 2FA login
    $emailService = new EmailNotificationService();
    $userAgent = request()->header('User-Agent');
    $deviceType = 'Unknown';
    if (str_contains($userAgent, 'Mobile'))
        $deviceType = 'Mobile';
    elseif (str_contains($userAgent, 'Windows'))
        $deviceType = 'Windows PC';
    elseif (str_contains($userAgent, 'Macintosh'))
        $deviceType = 'Mac';
    elseif (str_contains($userAgent, 'Linux'))
        $deviceType = 'Linux';

    $emailService->sendSecurityAlert($user->id, [
        'alert_type' => 'login',
        'alert_title' => 'New 2FA Login Detected',
        'alert_message' => 'A new login with 2FA verification was detected on your TrackFlow account.',
        'details' => [
            'Time' => now()->format('M d, Y h:i A'),
            'IP Address' => request()->ip(),
            'Device' => $deviceType,
            '2FA Method' => session('2fa_method', 'app') === 'email' ? 'Email OTP' : 'Authenticator App',
        ],
        'action_required' => false,
        'action_url' => url('/settings/security'),
        'action_text' => 'Review Security Settings',
    ]);

    // Link user to any pending group memberships with matching email
    $linkedGroups = \App\Services\GroupMemberLinkService::linkUserToGroupMembers($user);

    // Redirect to intended URL or dashboard
    $intendedUrl = session()->pull('url.intended', route('dashboard'));
    $message = 'Login successful!';
    if (count($linkedGroups) > 0) {
        $message .= ' You have been added to ' . count($linkedGroups) . ' new group(s).';
    }

    // Return redirect with remember cookie if set
    $redirect = redirect($intendedUrl)->with('success', $message);
    if ($rememberCookie) {
        $redirect = $redirect->withCookie($rememberCookie);
    }
    return $redirect;
})->name('2fa.verify.post');

Route::post('/2fa/recovery', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'recovery_code' => 'required|string'
    ]);

    $userId = session('2fa_user_id');
    if (!$userId) {
        return redirect()->route('login')->withErrors(['error' => 'Session expired']);
    }

    $user = \App\Models\User::find($userId);
    if (!$user || !$user->two_factor_recovery_codes) {
        return redirect()->route('login')->withErrors(['error' => 'Invalid session']);
    }

    // Check recovery code
    $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
    $code = strtoupper(trim($request->recovery_code));

    if (!in_array($code, is_array($recoveryCodes) ? $recoveryCodes : [])) {
        return back()->withErrors(['error' => 'Invalid recovery code']);
    }

    // Remove used recovery code
    $recoveryCodes = array_values(array_diff($recoveryCodes, [$code]));

    \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $userId)
        ->update([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'updated_at' => now()
        ]);

    // Check if Remember Me was checked during login
    $shouldRemember = session('2fa_remember', false);
    $wasRememberedLogin = session('2fa_remembered_login', false);

    // Login successful - clear 2FA session data
    session()->forget(['2fa_user_id', '2fa_remember', '2fa_remembered_login', '2fa_new_device', '2fa_revoked_device', '2fa_method']);
    session()->put('user_id', $user->id);
    session()->put('user_name', $user->name);
    session()->put('user_email', $user->email);

    // Mark this device/session as trusted after successful recovery code verification
    $trustedDeviceService = new \App\Services\TrustedDeviceService();
    $trustedDeviceService->createTrustedSession($user->id, $request);

    // Handle Remember Me functionality
    $rememberCookie = null;
    if ($shouldRemember) {
        $rememberMeService = new RememberMeService();
        if ($wasRememberedLogin) {
            // User came from remembered login - rotate the existing token
            $rememberCookie = $rememberMeService->rotateToken($user, $request);
        } else {
            // Fresh login with remember me checked - create new token
            $rememberCookie = $rememberMeService->createToken($user, $request);
        }
    }

    // Send security alert for recovery code login (high priority)
    $emailService = new EmailNotificationService();
    $userAgent = request()->header('User-Agent');
    $deviceType = 'Unknown';
    if (str_contains($userAgent, 'Mobile'))
        $deviceType = 'Mobile';
    elseif (str_contains($userAgent, 'Windows'))
        $deviceType = 'Windows PC';
    elseif (str_contains($userAgent, 'Macintosh'))
        $deviceType = 'Mac';
    elseif (str_contains($userAgent, 'Linux'))
        $deviceType = 'Linux';

    $emailService->sendSecurityAlert($user->id, [
        'alert_type' => 'warning',
        'alert_title' => 'Recovery Code Used',
        'alert_message' => 'A recovery code was used to access your TrackFlow account. If this wasn\'t you, please secure your account immediately.',
        'details' => [
            'Time' => now()->format('M d, Y h:i A'),
            'IP Address' => request()->ip(),
            'Device' => $deviceType,
            'Recovery Codes Remaining' => count($recoveryCodes),
        ],
        'action_required' => true,
        'action_url' => url('/settings/security'),
        'action_text' => 'Review & Secure Account',
    ]);

    // Link user to any pending group memberships with matching email
    $linkedGroups = \App\Services\GroupMemberLinkService::linkUserToGroupMembers($user);

    // Redirect to intended URL or dashboard
    $intendedUrl = session()->pull('url.intended', route('dashboard'));
    $message = 'Login successful using recovery code!';
    if (count($linkedGroups) > 0) {
        $message .= ' You have been added to ' . count($linkedGroups) . ' new group(s).';
    }

    // Return redirect with remember cookie if set
    $redirect = redirect($intendedUrl)->with('success', $message);
    if ($rememberCookie) {
        $redirect = $redirect->withCookie($rememberCookie);
    }
    return $redirect;
})->name('2fa.recovery');

// 2FA Resend OTP Route (for email method)
Route::post('/2fa/resend', function () {
    $userId = session('2fa_user_id');
    $method = session('2fa_method');

    if (!$userId || $method !== 'email') {
        return response()->json([
            'success' => false,
            'message' => 'Invalid session or method'
        ], 400);
    }

    $user = \App\Models\User::find($userId);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    // Generate new OTP
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // Store OTP in database
    \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $user->email],
        [
            'token' => $otp,
            'created_at' => now()
        ]
    );

    // Send OTP email
    try {
        \Illuminate\Support\Facades\Mail::send('email-template.login-verification-otp', [
            'userName' => $user->name ?? 'User',
            'otp' => $otp,
            'title' => 'Login Verification',
            'ipAddress' => request()->ip(),
            'browser' => request()->header('User-Agent')
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('🔐 Login Verification Code - TrackFlow');
        });

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent successfully'
        ]);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Failed to resend login OTP: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to send verification code'
        ], 500);
    }
})->name('2fa.resend');

Route::get('/register', function () {
    if (session('user_id')) {
        return redirect()->route('dashboard');
    }
    return view('auth.register');
})->name('register');

Route::post('/register', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'currency' => 'required|string|size:3'
    ]);

    // Get selected currency (default to INR if invalid)
    $selectedCurrency = $request->currency;
    $validCurrencies = array_keys(config('currency.currencies', []));
    if (!in_array($selectedCurrency, $validCurrencies)) {
        $selectedCurrency = config('currency.default', 'INR');
    }

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'currency' => $selectedCurrency,
        'timezone' => 'UTC'
    ]);

    // Store display_currency in user_settings table
    \Illuminate\Support\Facades\DB::table('user_settings')->updateOrInsert(
        ['user_id' => $user->id],
        [
            'base_currency' => 'INR',
            'display_currency' => $selectedCurrency,
            'currency_updated_at' => now()
        ]
    );

    // Send welcome email to new user
    $emailService = new EmailNotificationService();
    $emailService->sendWelcomeEmail($user->id);

    // Link user to any pending group memberships with matching email
    $linkedGroups = \App\Services\GroupMemberLinkService::linkUserToGroupMembers($user);

    session()->put('user_id', $user->id);
    session()->put('user_name', $user->name);
    session()->put('user_email', $user->email);

    // Mark this device/session as trusted for the new user (no 2FA needed for first login)
    $trustedDeviceService = new \App\Services\TrustedDeviceService();
    $trustedDeviceService->createTrustedSession($user->id, $request);

    $message = 'Registration successful!';
    if (count($linkedGroups) > 0) {
        $message .= ' You have been added to ' . count($linkedGroups) . ' group(s) that were waiting for you.';
    }

    return redirect()->route('dashboard')->with('success', $message);
})->name('register.post');

// OAuth routes for Google and GitHub (minimal, safe integration)
Route::get('/auth/{provider}/redirect', function ($provider) {
    if (!in_array($provider, ['google', 'github'])) {
        abort(404);
    }
    // Validate provider configuration before redirecting to avoid provider errors like missing client_id
    $clientId = config("services.{$provider}.client_id");
    $clientSecret = config("services.{$provider}.client_secret");
    if (empty($clientId) || empty($clientSecret)) {
        \Illuminate\Support\Facades\Log::warning("OAuth redirect attempted for {$provider} but client_id/secret missing");
        return redirect()->route('login')->withErrors([
            'oauth' => ucfirst($provider) . ' OAuth is not configured on this server. Please set ' . strtoupper($provider) . '_CLIENT_ID and ' . strtoupper($provider) . '_CLIENT_SECRET in your .env'
        ]);
    }

    // Prefer the explicit redirect configured in config/services.php (via env).
    $request = request();
    $configuredRedirect = config("services.{$provider}.redirect");
    $fallbackRedirect = 'https://trackflow.mooo.com/auth/' . $provider . '/callback';
    $redirectUrl = $configuredRedirect ?: $fallbackRedirect;

    // If caller requested a preview, return the provider redirect URL as JSON instead of redirecting.
    if ($request->query('preview')) {
        try {
            // Let Socialite use the configured redirect in config/services.php (env) when available.
            $driver = Socialite::driver($provider)->stateless();
            $response = $driver->redirect();
            $target = method_exists($response, 'getTargetUrl') ? $response->getTargetUrl() : (string) $response;
            return response()->json(['success' => true, 'url' => $target]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OAuth preview error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to build provider URL: ' . $e->getMessage()], 500);
        }
    }

    // Use Socialite default redirect which respects config/services.php redirect setting
    return Socialite::driver($provider)->redirect();
})->name('oauth.redirect');

// Controller-based routes for Google (friendly named routes)
use App\Http\Controllers\Auth\GoogleController;

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::get('/auth/{provider}/callback', function (\Illuminate\Http\Request $request, $provider) {
    if (!in_array($provider, ['google', 'github'])) {
        abort(404);
    }

    // Validate provider configuration before attempting callback handling
    $clientId = config("services.{$provider}.client_id");
    $clientSecret = config("services.{$provider}.client_secret");
    if (empty($clientId) || empty($clientSecret)) {
        \Illuminate\Support\Facades\Log::warning("OAuth callback invoked for {$provider} but client_id/secret missing");
        return redirect()->route('login')->withErrors([
            'oauth' => ucfirst($provider) . ' OAuth is not configured on this server. Please set ' . strtoupper($provider) . '_CLIENT_ID and ' . strtoupper($provider) . '_CLIENT_SECRET in your .env'
        ]);
    }

    try {
        // Use stateless() on the callback; Socialite will use the redirect configured in config/services.php
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $email = $socialUser->getEmail();
        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Unable to retrieve email from provider']);
        }

        $name = $socialUser->getName() ?: ($socialUser->getNickname() ?? '');
        $avatar = method_exists($socialUser, 'getAvatar') ? $socialUser->getAvatar() : ($socialUser->avatar ?? null);

        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            // Create user with name, email and profile picture. Keep defaults consistent with register route.
            $user = \App\Models\User::create([
                'name' => $name ?: 'User',
                'email' => $email,
                'password' => null,
                'email_verified_at' => now(),
                'currency' => 'INR',
                'timezone' => 'UTC',
                'profile_picture' => $avatar,
            ]);
        } else {
            // Update existing user name/profile picture if provider has newer data
            $needsUpdate = false;
            if ($name && $user->name !== $name) {
                $user->name = $name;
                $needsUpdate = true;
            }
            if ($avatar && $user->profile_picture !== $avatar) {
                $user->profile_picture = $avatar;
                $needsUpdate = true;
            }
            if ($needsUpdate) {
                $user->save();
            }
        }

        // Log the user in via session (consistent with existing login flow)
        session()->put('user_id', $user->id);
        session()->put('user_name', $user->name);
        session()->put('user_email', $user->email);

        // Link user to any pending group memberships with matching email
        \App\Services\GroupMemberLinkService::linkUserToGroupMembers($user);

        return redirect()->route('dashboard');
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('OAuth callback error: ' . $e->getMessage());
        return redirect()->route('login')->withErrors(['oauth' => 'Failed to authenticate with provider']);
    }
})->name('oauth.callback');

// One-Tap / ID token verification endpoint (optional - used by Google Identity Services)
Route::post('/auth/google/one-tap', [\App\Http\Controllers\Auth\GoogleOneTapController::class, 'handleOneTap']);

// Under Construction page (public)
Route::get('/under-construction', function () {
    return view('errors.under-construction');
})->name('under.construction');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (\Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Email not found in database']);
    }

    $otp = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
    $expiry = now()->addMinutes(15);

    \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $otp, 'created_at' => now()]
    );

    try {
        \Illuminate\Support\Facades\Mail::send('email-template.password-reset-otp', [
            'otp' => $otp,
            'title' => 'Password Reset'
        ], function ($message) use ($request) {
            $message->to($request->email)
                ->subject('🔓 Password Reset OTP - TrackFlow');
        });
        return response()->json(['success' => true, 'message' => 'OTP sent to your email']);
    } catch (\Exception $e) {
        return response()->json(['success' => true, 'message' => 'OTP generated (Check console in dev mode): ' . $otp]);
    }
})->name('password.email');

Route::post('/verify-otp', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|string|size:8'
    ]);

    $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (!$record || $record->token !== $request->otp) {
        return response()->json(['success' => false, 'message' => 'Invalid OTP']);
    }

    if (now()->diffInMinutes($record->created_at) > 15) {
        return response()->json(['success' => false, 'message' => 'OTP expired']);
    }

    return response()->json(['success' => true, 'message' => 'OTP verified']);
})->name('password.verify');

Route::post('/reset-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|string|size:8',
        'password' => 'required|min:8|confirmed'
    ]);

    $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->where('token', $request->otp)
        ->first();

    if (!$record) {
        return response()->json(['success' => false, 'message' => 'Invalid OTP']);
    }

    $user = \App\Models\User::where('email', $request->email)->first();
    $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
    $user->save();

    \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return response()->json(['success' => true, 'message' => 'Password reset successful']);
})->name('password.update');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    // Get user ID before clearing session
    $userId = session('user_id');

    // Delete all remember tokens for this user
    $rememberCookie = null;
    if ($userId) {
        $rememberMeService = new RememberMeService();
        $rememberMeService->deleteAllTokensForUser($userId);
        $rememberCookie = $rememberMeService->forgetCookie();
    }

    // Clear all session data
    session()->flush();
    session()->invalidate();
    session()->regenerateToken();

    // Redirect to landing page with cleared remember cookie
    $redirect = redirect()->route('home')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate, post-check=0, pre-check=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

    if ($rememberCookie) {
        $redirect = $redirect->withCookie($rememberCookie);
    }

    return $redirect;
})->name('logout');

// Add Transaction Route (simple without API)
Route::post('/transactions/store', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'type' => 'required|in:debit,credit',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:255',
        'category_id' => 'required|string',
        'date' => 'required|date',
        'merchant' => 'nullable|string|max:255',
        'notes' => 'nullable|string'
    ]);

    $userId = session('user_id');
    $user = \App\Models\User::find($userId);
    $userCurrency = $user->currency ?? 'INR';

    // Ensure we store amounts consistently in base currency (INR) to avoid display rounding
    $currencyService = app(\App\Services\CurrencyService::class);
    $amountInBase = $currencyService->toBase($request->amount, $userCurrency);

    $transaction = \App\Models\Transaction::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'user_id' => $userId,
        'bank_account_id' => null,
        // Store category id properly so frontend can resolve the name
        'category_id' => $request->category_id ?: null,
        'date' => $request->date,
        'description' => $request->description,
        'merchant' => $request->merchant,
        // Store amount in USD (base) to match update behavior
        'amount' => round($amountInBase, 2),
        'currency' => 'INR',
        // Persist the user-entered amount and currency so display remains identical after edit
        'entered_amount' => $request->amount,
        'entered_currency' => $userCurrency,
        'type' => $request->type,
        'status' => 'completed',
        // Keep notes as provided (do not duplicate category info into notes)
        'notes' => $request->notes ?: '',
        'is_recurring' => false,
        'is_duplicate' => false,
        'is_excluded' => false
    ]);

    // Create notifications
    $notificationService = app(\App\Services\NotificationService::class);

    // Always notify about transaction being added
    $notificationService->transactionAdded(
        $userId,
        $transaction,
        $request->amount,
        $userCurrency
    );

    // Check if it's a large transaction and notify
    $threshold = $notificationService->getLargeTransactionThreshold($userId, $userCurrency);
    if ($request->amount >= $threshold) {
        $notificationService->largeTransaction(
            $userId,
            $transaction,
            $request->amount,
            $userCurrency,
            $threshold
        );
    }

    // Check if request expects JSON (AJAX) or HTML
    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Transaction added successfully',
            'data' => $transaction
        ]);
    }

    // For form submissions, redirect with session flash (no auto-reload needed)
    return redirect()->route('transactions.index')
        ->with('success', 'Transaction added successfully!');
})->name('transactions.store');

// Dashboard Stats Route
Route::get('/dashboard/stats', function () {
    $userId = session('user_id');

    // If no user in session, return zeros
    if (!$userId) {
        return response()->json([
            'success' => false,
            'message' => 'No user session',
            'data' => [
                'total_balance' => 0,
                'monthly_income' => 0,
                'monthly_expenses' => 0
            ]
        ]);
    }

    // Get user's currency preference
    $user = \App\Models\User::find($userId);
    $userCurrency = $user->currency ?? 'INR';
    $currencyService = app(\App\Services\CurrencyService::class);

    // Get all transactions for the user
    $transactions = \App\Models\Transaction::where('user_id', $userId)->get();

    // Convert transaction amounts to user's currency
    $convertedTransactions = $transactions->map(function ($tx) use ($currencyService, $userCurrency) {
        $storedCurrency = $tx->currency ?? 'INR';
        $tx->converted_amount = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
        return $tx;
    });

    // Calculate total balance (income - expenses) in user's currency
    $totalIncome = $convertedTransactions->where('type', 'credit')->sum('converted_amount');
    $totalExpenses = $convertedTransactions->where('type', 'debit')->sum('converted_amount');
    $totalBalance = $totalIncome - $totalExpenses;

    // Calculate monthly income and expenses (current month)
    $currentMonth = now()->format('Y-m');
    $monthlyTransactions = $convertedTransactions->filter(function ($tx) use ($currentMonth) {
        return \Carbon\Carbon::parse($tx->date)->format('Y-m') === $currentMonth;
    });

    $monthlyIncome = $monthlyTransactions->where('type', 'credit')->sum('converted_amount');
    $monthlyExpenses = $monthlyTransactions->where('type', 'debit')->sum('converted_amount');

    // Calculate previous month for comparison
    $previousMonth = now()->subMonth()->format('Y-m');
    $previousMonthTransactions = $convertedTransactions->filter(function ($tx) use ($previousMonth) {
        return \Carbon\Carbon::parse($tx->date)->format('Y-m') === $previousMonth;
    });

    $previousMonthIncome = $previousMonthTransactions->where('type', 'credit')->sum('converted_amount');
    $previousMonthExpenses = $previousMonthTransactions->where('type', 'debit')->sum('converted_amount');
    $previousTotalIncome = $convertedTransactions->where('type', 'credit')
        ->filter(function ($tx) use ($currentMonth) {
            return \Carbon\Carbon::parse($tx->date)->format('Y-m') < $currentMonth;
        })->sum('converted_amount');
    $previousTotalExpenses = $convertedTransactions->where('type', 'debit')
        ->filter(function ($tx) use ($currentMonth) {
            return \Carbon\Carbon::parse($tx->date)->format('Y-m') < $currentMonth;
        })->sum('converted_amount');
    $previousBalance = $previousTotalIncome - $previousTotalExpenses;

    // Calculate percentage changes
    // For balance: compare current total balance with previous total balance
    $balanceChange = $previousBalance > 0
        ? (($totalBalance - $previousBalance) / $previousBalance * 100)
        : ($totalBalance > 0 ? 100 : 0);

    // For income: compare current month with previous month
    $incomeChange = $previousMonthIncome > 0
        ? (($monthlyIncome - $previousMonthIncome) / $previousMonthIncome * 100)
        : ($monthlyIncome > 0 ? 100 : 0);

    // For expenses: compare current month with previous month
    $expenseChange = $previousMonthExpenses > 0
        ? (($monthlyExpenses - $previousMonthExpenses) / $previousMonthExpenses * 100)
        : ($monthlyExpenses > 0 ? 100 : 0);

    // Calculate savings rate
    $savingsRate = $monthlyIncome > 0 ? (($monthlyIncome - $monthlyExpenses) / $monthlyIncome * 100) : 0;
    $savingsStatus = $savingsRate > 20 ? 'Good' : ($savingsRate > 10 ? 'Fair' : 'Low');

    return response()->json([
        'success' => true,
        'data' => [
            'total_balance' => round($totalBalance, 2),
            'monthly_income' => round($monthlyIncome, 2),
            'monthly_expenses' => round($monthlyExpenses, 2),
            'balance_change' => round($balanceChange, 1),
            'income_change' => round($incomeChange, 1),
            'expense_change' => round($expenseChange, 1),
            'savings_rate' => round($savingsRate, 1),
            'savings_status' => $savingsStatus,
            'user_id' => $userId,
            'transaction_count' => $transactions->count(),
            'currency' => $userCurrency
        ]
    ]);
})->name('dashboard.stats');

// Dashboard Chart Data Routes
Route::get('/dashboard/income-expenses-chart', function () {
    $userId = session('user_id');
    if (!$userId) {
        return response()->json(['success' => false, 'data' => []]);
    }

    // Get user's currency preference
    $user = \App\Models\User::find($userId);
    $userCurrency = $user->currency ?? 'INR';
    $currencyService = app(\App\Services\CurrencyService::class);

    $transactions = \App\Models\Transaction::where('user_id', $userId)->get();

    // Convert transaction amounts to user's currency
    $convertedTransactions = $transactions->map(function ($tx) use ($currencyService, $userCurrency) {
        $storedCurrency = $tx->currency ?? 'INR';
        $tx->converted_amount = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
        return $tx;
    });

    // Get last 6 months data
    $months = [];
    $incomeData = [];
    $expenseData = [];

    for ($i = 5; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $monthKey = $month->format('Y-m');
        $monthLabel = $month->format('M');

        $monthTransactions = $convertedTransactions->filter(function ($tx) use ($monthKey) {
            return \Carbon\Carbon::parse($tx->date)->format('Y-m') === $monthKey;
        });

        $income = $monthTransactions->where('type', 'credit')->sum('converted_amount');
        $expenses = $monthTransactions->where('type', 'debit')->sum('converted_amount');

        $months[] = $monthLabel;
        $incomeData[] = round($income, 2);
        $expenseData[] = round($expenses, 2);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'labels' => $months,
            'income' => $incomeData,
            'expenses' => $expenseData,
            'currency' => $userCurrency
        ]
    ]);
})->name('dashboard.income-expenses-chart');

Route::get('/dashboard/category-chart', function () {
    $userId = session('user_id');
    if (!$userId) {
        return response()->json(['success' => false, 'data' => []]);
    }

    // Get user's currency preference
    $user = \App\Models\User::find($userId);
    $userCurrency = $user->currency ?? 'INR';
    $currencyService = app(\App\Services\CurrencyService::class);

    $currentMonth = now()->format('Y-m');
    $transactions = \App\Models\Transaction::where('user_id', $userId)
        ->where('type', 'debit') // Only expenses
        ->get()
        ->filter(function ($tx) use ($currentMonth) {
            return \Carbon\Carbon::parse($tx->date)->format('Y-m') === $currentMonth;
        });

    // Group by category and convert amounts
    $categoryData = [];
    foreach ($transactions as $tx) {
        // Prefer linked category record when available, otherwise fall back to legacy notes parsing
        $categoryName = 'Other';
        if ($tx->category_id) {
            $cat = \App\Models\Category::find($tx->category_id);
            if ($cat) {
                $categoryName = $cat->name;
            }
        } elseif ($tx->notes && preg_match('/\\[Category: (.+?)\\]/', $tx->notes, $matches)) {
            $categoryName = $matches[1];
            // Resolve numeric legacy category id to name
            if (is_numeric($categoryName)) {
                $resolved = \App\Models\Category::find((int) $categoryName);
                if ($resolved) {
                    $categoryName = $resolved->name;
                }
            }
        }

        if (!isset($categoryData[$categoryName])) {
            $categoryData[$categoryName] = 0;
        }

        // Convert amount to user's currency
        $storedCurrency = $tx->currency ?? 'INR';
        $convertedAmount = $currencyService->convert((float) $tx->amount, $storedCurrency, $userCurrency);
        $categoryData[$categoryName] += $convertedAmount;
    }

    // Round values
    $categoryData = array_map(function ($value) {
        return round($value, 2);
    }, $categoryData);

    return response()->json([
        'success' => true,
        'data' => [
            'labels' => array_keys($categoryData),
            'values' => array_values($categoryData),
            'currency' => $userCurrency
        ]
    ]);
})->name('dashboard.category-chart');

// Get Transactions Route (simple JSON response)
Route::get('/transactions/list', function (\Illuminate\Http\Request $request) {
    $userId = session('user_id');

    // Get user's currency preference
    $user = \App\Models\User::find($userId);
    $userCurrency = $user->currency ?? 'INR';

    // Get currency service for conversion
    $currencyService = app(\App\Services\CurrencyService::class);

    $transactions = \App\Models\Transaction::where('user_id', $userId)
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($tx) use ($currencyService, $userCurrency) {
            // Extract category from notes
            $categoryName = 'General';
            if ($tx->notes && preg_match('/\[Category: (.+?)\]/', $tx->notes, $matches)) {
                $categoryName = $matches[1];
                // If the legacy marker contains a numeric id, try to resolve it to the category name
                if (is_numeric($categoryName)) {
                    $resolvedCat = \App\Models\Category::find((int) $categoryName);
                    if ($resolvedCat) {
                        $categoryName = $resolvedCat->name;
                    }
                }
            }

            // Determine display amount: prefer the user-entered amount if it exists and matches user's current currency,
            // otherwise convert stored amount (base currency INR) to user's display currency.
            $baseCurrency = 'INR'; // All amounts are stored in INR as base currency
            $displayAmount = null;

            if (!is_null($tx->entered_amount) && !is_null($tx->entered_currency) && $tx->entered_currency === $userCurrency) {
                // User's currency hasn't changed since entry - use the original entered amount
                $displayAmount = round((float) $tx->entered_amount, 2);
            } else {
                // User's currency has changed or no entered_amount - convert from base currency (INR)
                $displayAmount = $currencyService->convert((float) $tx->amount, $baseCurrency, $userCurrency);
            }

            // Build category object: prefer linked category record, fall back to parsed name
            $categoryObj = ['name' => $categoryName, 'color' => null, 'icon' => null];
            if ($tx->category_id) {
                $catRec = \App\Models\Category::find($tx->category_id);
                if ($catRec) {
                    $categoryObj = ['name' => $catRec->name, 'color' => $catRec->color, 'icon' => $catRec->icon];
                }
            }

            return [
                'id' => $tx->id,
                'uuid' => $tx->uuid,
                'date' => $tx->date->format('Y-m-d'),
                'description' => $tx->description,
                'merchant' => $tx->merchant,
                'amount' => round($displayAmount, 2),
                'enteredAmount' => isset($tx->entered_amount) ? (float) $tx->entered_amount : null,
                'enteredCurrency' => $tx->entered_currency ?? null,
                'storedAmount' => (float) $tx->amount, // Original amount in base currency (INR)
                'storedCurrency' => $baseCurrency,
                'displayCurrency' => $userCurrency,
                'type' => $tx->type,
                'status' => $tx->status,
                'notes' => preg_replace('/\s*\[Category: .+?\]/', '', $tx->notes),
                'category' => $categoryObj,
                'budget_id' => $tx->budget_id,
                'budget_item_id' => $tx->budget_item_id,
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $transactions,
        'meta' => [
            'total' => $transactions->count(),
            'from' => 1,
            'to' => $transactions->count(),
            'userCurrency' => $userCurrency
        ]
    ]);
})->name('transactions.list');

// ============================================
// SCHEDULED TRANSACTIONS ROUTES
// ============================================

// Get Scheduled Transactions List
Route::get('/transactions/scheduled/list', function () {
    $userId = session('user_id');
    if (!$userId) {
        return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
    }

    $user = \App\Models\User::find($userId);
    $userCurrency = $user->currency ?? 'INR';
    $currencyService = app(\App\Services\CurrencyService::class);
    $currencyConfig = config('currency.currencies');
    $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? '₹';

    $scheduledTransactions = \App\Models\ScheduledTransaction::where('user_id', $userId)
        ->with('category')
        ->orderBy('scheduled_date', 'asc')
        ->get()
        ->map(function ($tx) use ($currencyService, $userCurrency) {
            return [
                'id' => $tx->id,
                'scheduled_date' => $tx->scheduled_date->format('Y-m-d'),
                'description' => $tx->description,
                'merchant' => $tx->merchant,
                'amount' => round($tx->amount, 2),
                'type' => $tx->type,
                'status' => $tx->status,
                'notes' => $tx->notes,
                'category' => $tx->category ? [
                    'id' => $tx->category->id,
                    'name' => $tx->category->name,
                    'color' => $tx->category->color,
                    'icon' => $tx->category->icon,
                ] : null,
                'category_id' => $tx->category_id,
                'is_due_today' => $tx->scheduled_date->isToday(),
                'is_overdue' => $tx->scheduled_date->isPast() && $tx->status === 'pending',
                'days_until' => $tx->scheduled_date->diffInDays(now(), false) * -1,
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $scheduledTransactions,
        'meta' => [
            'total' => $scheduledTransactions->count(),
            'pending' => $scheduledTransactions->where('status', 'pending')->count(),
            'userCurrency' => $userCurrency,
            'currencySymbol' => $currencySymbol,
        ]
    ]);
})->name('transactions.scheduled.list');

// Store Scheduled Transaction
Route::post('/transactions/scheduled/store', function (\Illuminate\Http\Request $request) {
    $userId = session('user_id');
    if (!$userId) {
        return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
    }

    $request->validate([
        'type' => 'required|in:debit,credit',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'scheduled_date' => 'required|date|after:today',
        'merchant' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
    ]);

    $user = \App\Models\User::find($userId);
    $userCurrency = $user->currency ?? 'INR';
    $currencyService = app(\App\Services\CurrencyService::class);

    // Convert to base currency for storage
    $amountInBase = $currencyService->toBase($request->amount, $userCurrency);

    $scheduled = \App\Models\ScheduledTransaction::create([
        'user_id' => $userId,
        'type' => $request->type,
        'amount' => round($amountInBase, 2),
        'currency' => $currencyService->getBaseCurrency(),
        'description' => $request->description,
        'category_id' => $request->category_id,
        'scheduled_date' => $request->scheduled_date,
        'merchant' => $request->merchant,
        'notes' => $request->notes,
        'status' => 'pending',
    ]);

    // Send confirmation email
    try {
        $currencyConfig = config('currency.currencies');
        $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? '₹';

        \Illuminate\Support\Facades\Mail::send('emails.scheduled-transaction-confirmation', [
            'userName' => $user->name ?? 'User',
            'description' => $scheduled->description,
            'merchant' => $scheduled->merchant,
            'amount' => $request->amount, // Use original amount in user's currency
            'currency' => $currencySymbol,
            'type' => $scheduled->type,
            'category' => $scheduled->category->name ?? null,
            'notes' => $scheduled->notes,
            'scheduledDate' => $scheduled->scheduled_date,
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('📅 Transaction Scheduled - TrackFlow');
        });

        $scheduled->update([
            'confirmation_sent' => true,
            'confirmation_sent_at' => now(),
        ]);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Failed to send scheduled transaction confirmation email: ' . $e->getMessage());
    }

    return response()->json([
        'success' => true,
        'message' => 'Transaction scheduled successfully! A confirmation email has been sent.',
        'data' => $scheduled
    ]);
})->name('transactions.scheduled.store');

// Cancel Scheduled Transaction
Route::delete('/transactions/scheduled/{id}/cancel', function ($id) {
    $userId = session('user_id');
    if (!$userId) {
        return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
    }

    $scheduled = \App\Models\ScheduledTransaction::with('category')->where('id', $id)
        ->where('user_id', $userId)
        ->where('status', 'pending')
        ->firstOrFail();

    // Get user details for email
    $user = \App\Models\User::find($userId);
    $userCurrency = $user->currency ?? 'INR';
    $currencyConfig = config('currency.currencies');
    $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? '₹';

    // Store transaction details before cancelling
    $transactionDetails = [
        'userName' => $user->name ?? 'User',
        'description' => $scheduled->description,
        'merchant' => $scheduled->merchant,
        'amount' => $scheduled->amount,
        'currency' => $currencySymbol,
        'type' => $scheduled->type,
        'category' => $scheduled->category->name ?? null,
        'notes' => $scheduled->notes,
        'scheduledDate' => $scheduled->scheduled_date,
    ];

    // Mark as cancelled
    $scheduled->markCancelled();

    // Send cancellation email
    try {
        \Illuminate\Support\Facades\Mail::send('emails.scheduled-transaction-cancelled', $transactionDetails, function ($message) use ($user) {
            $message->to($user->email)
                ->subject('❌ Scheduled Transaction Cancelled - TrackFlow');
        });
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Failed to send scheduled transaction cancellation email: ' . $e->getMessage());
    }

    return response()->json([
        'success' => true,
        'message' => 'Scheduled transaction cancelled successfully. A confirmation email has been sent.'
    ]);
})->name('transactions.scheduled.cancel');

// Execute Scheduled Transaction (Convert to actual transaction)
Route::post('/transactions/scheduled/{id}/execute', function ($id) {
    $userId = session('user_id');
    if (!$userId) {
        return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
    }

    $scheduled = \App\Models\ScheduledTransaction::where('id', $id)
        ->where('user_id', $userId)
        ->where('status', 'pending')
        ->firstOrFail();

    // Create actual transaction
    $transaction = \App\Models\Transaction::create([
        'user_id' => $userId,
        'type' => $scheduled->type,
        'amount' => $scheduled->amount,
        'currency' => $scheduled->currency,
        'description' => $scheduled->description,
        'category_id' => $scheduled->category_id,
        'date' => now()->toDateString(),
        'merchant' => $scheduled->merchant,
        'notes' => $scheduled->notes . ' [Scheduled Transaction]',
        'status' => 'completed',
        'is_recurring' => false,
        'is_duplicate' => false,
    ]);

    // Mark scheduled transaction as completed
    $scheduled->markCompleted($transaction->id);

    return response()->json([
        'success' => true,
        'message' => 'Scheduled transaction executed successfully',
        'data' => $transaction
    ]);
})->name('transactions.scheduled.execute');

// Update Transaction Route
Route::put('/transactions/{id}/update', function (\Illuminate\Http\Request $request, $id) {
    $request->validate([
        'type' => 'required|in:debit,credit',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:255',
        'category_id' => 'required|string',
        'date' => 'required|date',
        'merchant' => 'nullable|string|max:255',
        'notes' => 'nullable|string'
    ]);

    $userId = session('user_id');
    $transaction = \App\Models\Transaction::where('id', $id)
        ->where('user_id', $userId)
        ->firstOrFail();

    // Get user's display currency and convert incoming amount -> base (INR) for storage
    $user = \App\Models\User::find($userId);
    $userCurrency = $user->currency ?? config('currency.default', 'INR');
    $currencyService = app(\App\Services\CurrencyService::class);
    $amountInBase = $currencyService->toBase($request->amount, $userCurrency);

    $transaction->update([
        'date' => $request->date,
        'description' => $request->description,
        'merchant' => $request->merchant,
        'amount' => round($amountInBase, 2), // Store in base currency (INR)
        'currency' => $currencyService->getBaseCurrency(), // Always store in configured base
        'type' => $request->type,
        // Store the category id and keep notes as provided
        'category_id' => $request->category_id ?: null,
        'notes' => $request->notes ?: '',
        // Update the stored user-entered amount so display stays consistent
        'entered_amount' => $request->amount,
        'entered_currency' => $userCurrency,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Transaction updated successfully',
        'data' => $transaction
    ]);
})->name('transactions.update');

// Get Single Transaction Route
Route::get('/transactions/{id}/show', function ($id) {
    $userId = session('user_id');
    $transaction = \App\Models\Transaction::where('id', $id)
        ->where('user_id', $userId)
        ->firstOrFail();

    // Get user's currency preference for conversion
    $user = \App\Models\User::find($userId);
    $userCurrency = $user->currency ?? 'INR';

    // Determine display amount: prefer the user-entered amount only when its currency
    // explicitly matches the user's display currency. For legacy rows where
    // entered_currency is null, fall back to converting the stored canonical amount.
    $currencyService = app(\App\Services\CurrencyService::class);
    $storedCurrency = $transaction->currency ?? $currencyService->getBaseCurrency();
    $displayAmount = null;
    if (!is_null($transaction->entered_amount) && isset($transaction->entered_currency) && $transaction->entered_currency === $userCurrency) {
        // entered_amount is already in user's display currency
        $displayAmount = round((float) $transaction->entered_amount, 2);
    } else {
        // Convert stored canonical amount (storedCurrency, usually INR) -> user's display currency
        $displayAmount = $currencyService->convert((float) $transaction->amount, $storedCurrency, $userCurrency);
    }

    // Resolve category id -> name when possible
    $categoryId = $transaction->category_id;
    $categoryName = null;
    if ($categoryId) {
        $cat = \App\Models\Category::find($categoryId);
        if ($cat) {
            $categoryName = $cat->name;
        }
    } elseif ($transaction->notes && preg_match('/\[Category: (.+?)\]/', $transaction->notes, $matches)) {
        // Fallback to legacy notes parsing if present
        $categoryName = $matches[1];
        // If it's a numeric id, try to resolve to a category record
        if (is_numeric($categoryName)) {
            $resolved = \App\Models\Category::find((int) $categoryName);
            if ($resolved) {
                $categoryName = $resolved->name;
            }
        }
    }

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => round($displayAmount, 2), // Display in user's currency
            'entered_amount' => $transaction->entered_amount,
            'entered_currency' => $transaction->entered_currency,
            'storedAmount' => (float) $transaction->amount,
            'storedCurrency' => $storedCurrency,
            'displayCurrency' => $userCurrency,
            'description' => $transaction->description,
            // Return category id for select population and category_name for display
            'category_id' => $categoryId,
            'category_name' => $categoryName,
            'date' => \Carbon\Carbon::parse($transaction->date)->format('Y-m-d'),
            'merchant' => $transaction->merchant,
            'notes' => preg_replace('/\s*\[Category: .+?\]/', '', $transaction->notes)
        ]
    ]);
})->name('transactions.show');

// Protected routes - Authentication required for all
Route::middleware(['auth.session'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions - specific routes must come before resource routes
    Route::delete('transactions/{id}/delete-ajax', [TransactionController::class, 'deleteAjax'])->name('transactions.deleteAjax');
    Route::resource('transactions', TransactionController::class)->except(['store', 'show', 'update']);

    // Budgets - AJAX routes must come before resource routes
    Route::get('budgets/list-ajax', [BudgetController::class, 'listAjax'])->name('budgets.listAjax');
    Route::post('budgets/create-ajax', [BudgetController::class, 'storeAjax'])->name('budgets.storeAjax');
    Route::post('budgets/{budgetId}/items/{itemId}/add-spent', [BudgetController::class, 'addSpentAjax'])->name('budgets.addSpentAjax');
    Route::delete('budgets/{id}/delete-ajax', [BudgetController::class, 'destroyAjax'])->name('budgets.destroyAjax');
    Route::resource('budgets', BudgetController::class);

    // Categories - specific routes must come before resource routes
    Route::get('categories/list', [CategoryController::class, 'list'])->name('categories.list');
    Route::post('categories/create-ajax', [CategoryController::class, 'createAjax'])->name('categories.createAjax');
    Route::delete('categories/{id}/delete-ajax', [CategoryController::class, 'deleteAjax'])->name('categories.deleteAjax');
    Route::resource('categories', CategoryController::class);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/list', [NotificationController::class, 'getNotifications'])->name('notifications.list');
    Route::get('notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('notifications.deleteAll');
    Route::delete('notifications/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');
    Route::get('notifications/preferences', [NotificationController::class, 'getPreferences'])->name('notifications.preferences');
    Route::post('notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.updatePreferences');

    // Goals
    Route::get('goals/list-ajax', [GoalController::class, 'listAjax'])->name('goals.listAjax');
    Route::resource('goals', GoalController::class);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/stats', [ReportController::class, 'getStats'])->name('reports.stats');
    Route::get('reports/data', [ReportController::class, 'getReportData'])->name('reports.data');
    Route::get('reports/specific', [ReportController::class, 'getSpecificReport'])->name('reports.specific');
    Route::get('reports/financial-health', [ReportController::class, 'getFinancialHealth'])->name('reports.financial-health');
    Route::get('reports/year-comparison', [ReportController::class, 'getYearComparison'])->name('reports.year-comparison');
    Route::match(['get', 'post'], 'reports/export', [ReportController::class, 'exportData'])->name('reports.export');
    Route::match(['get', 'post'], 'reports/consolidated-export', [ReportController::class, 'exportConsolidated'])->name('reports.consolidated.export');
    Route::get('reports/export-pdf', [ReportController::class, 'exportReportPDF'])->name('reports.export-pdf');
    Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/{id}/download', [ReportController::class, 'download'])->name('reports.download');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::post('settings/theme', [SettingController::class, 'updateTheme'])->name('settings.theme');
    Route::post('settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');
    Route::post('settings/verify-current-password', [SettingController::class, 'verifyCurrentPassword'])->name('settings.verify-current-password');
    Route::post('settings/verify-password-and-send-otp', [SettingController::class, 'verifyPasswordAndSendOtp'])->name('settings.verify-password-and-send-otp');
    Route::post('settings/verify-otp', [SettingController::class, 'verifyOtp'])->name('settings.verify-otp');
    Route::post('settings/update-password', [SettingController::class, 'updatePasswordFinal'])->name('settings.update-password');
    Route::post('settings/send-password-otp', [SettingController::class, 'sendPasswordOtp'])->name('settings.send-password-otp');
    Route::post('settings/change-password-with-otp', [SettingController::class, 'changePasswordWithOtp'])->name('settings.change-password-with-otp');
    Route::post('settings/change-language', [SettingController::class, 'changeLanguage'])->name('settings.change-language');
    Route::post('settings/preferences', [SettingController::class, 'updatePreferences'])->name('settings.preferences');
    Route::post('settings/notifications', [SettingController::class, 'updateNotifications'])->name('settings.notifications');
    Route::post('settings/2fa/enable', [SettingController::class, 'enable2FA'])->name('settings.2fa.enable');
    Route::post('settings/2fa/verify', [SettingController::class, 'verify2FA'])->name('settings.2fa.verify');
    Route::post('settings/2fa/disable', [SettingController::class, 'disable2FA'])->name('settings.2fa.disable');
    Route::get('settings/2fa/recovery-codes', [SettingController::class, 'getRecoveryCodes'])->name('settings.2fa.recovery-codes');
    Route::post('settings/2fa/send-email-otp', [SettingController::class, 'sendEmailOtp2FA'])->name('settings.2fa.send-email-otp');
    Route::post('settings/2fa/verify-email', [SettingController::class, 'verifyEmail2FA'])->name('settings.2fa.verify-email');
    Route::get('settings/sessions', [SettingController::class, 'getActiveSessions'])->name('settings.sessions');
    Route::delete('settings/sessions/{session_id}', [SettingController::class, 'revokeSession'])->name('settings.sessions.revoke');
    Route::post('settings/sessions/revoke-all', [SettingController::class, 'revokeAllSessions'])->name('settings.sessions.revoke-all');
    Route::post('settings/preferences', [SettingController::class, 'updatePreferences'])->name('settings.preferences');
    Route::post('settings/notifications', [SettingController::class, 'updateNotifications'])->name('settings.notifications');
    Route::delete('settings/delete-account', [SettingController::class, 'deleteAccount'])->name('settings.delete-account');
    Route::post('settings/delete-account/send-otp', [SettingController::class, 'sendDeleteAccountOtp'])->name('settings.delete-account.send-otp');
    Route::post('settings/delete-account/verify', [SettingController::class, 'verifyDeleteAccountOtp'])->name('settings.delete-account.verify');

    // UPI Management
    Route::get('settings/upi', [UpiController::class, 'index'])->name('settings.upi.index');
    Route::post('settings/upi', [UpiController::class, 'store'])->name('settings.upi.store');
    Route::put('settings/upi/{id}', [UpiController::class, 'update'])->name('settings.upi.update');
    Route::delete('settings/upi/{id}', [UpiController::class, 'destroy'])->name('settings.upi.destroy');
    Route::post('settings/upi/{id}/set-primary', [UpiController::class, 'setPrimary'])->name('settings.upi.set-primary');
    Route::delete('settings/upi/{id}/qr-code', [UpiController::class, 'deleteQrCode'])->name('settings.upi.delete-qr');

    // Profile routes
    Route::get('profile', [SettingController::class, 'profile'])->name('profile');
    Route::post('profile/upload-picture', [SettingController::class, 'uploadProfilePicture'])->name('profile.upload-picture');
    Route::post('profile/update-picture', [SettingController::class, 'updateProfilePicture'])->name('profile.update-picture');
    Route::delete('profile/delete-picture', [SettingController::class, 'deleteProfilePicture'])->name('profile.delete-picture');
    Route::post('profile/update-bio', [SettingController::class, 'updateBio'])->name('profile.update-bio');

    // API endpoints for profile page
    Route::get('api/user/stats', function () {
        $userId = session('user_id');

        $stats = [
            'success' => true,
            'stats' => [
                'transaction_count' => DB::table('transactions')->where('user_id', $userId)->count(),
                'account_count' => 0,
                'member_since' => DB::table('users')->where('id', $userId)->value('created_at'),
                'total_income' => DB::table('transactions')
                    ->where('user_id', $userId)
                    ->where('type', 'income')
                    ->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month)
                    ->sum('amount'),
                'total_expenses' => DB::table('transactions')
                    ->where('user_id', $userId)
                    ->where('type', 'expense')
                    ->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month)
                    ->sum('amount'),
            ]
        ];

        return response()->json($stats);
    });

    Route::get('api/transactions/recent', function () {
        $userId = session('user_id');
        $limit = request('limit', 5);

        $transactions = DB::table('transactions')
            ->where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'description' => $transaction->description,
                    'amount' => $transaction->amount,
                    'type' => $transaction->type,
                    'category' => $transaction->category,
                    'date' => date('M d, Y', strtotime($transaction->date)),
                ];
            });

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    });

    // Goals API endpoints
    Route::get('api/v1/goals', [App\Http\Controllers\API\GoalController::class, 'index']);
    Route::post('api/v1/goals', [App\Http\Controllers\API\GoalController::class, 'store']);
    Route::get('api/v1/goals/{id}', [App\Http\Controllers\API\GoalController::class, 'show']);
    Route::put('api/v1/goals/{id}', [App\Http\Controllers\API\GoalController::class, 'update']);
    Route::delete('api/v1/goals/{id}', [App\Http\Controllers\API\GoalController::class, 'destroy']);
    Route::post('api/v1/goals/{id}/contribute', [App\Http\Controllers\API\GoalController::class, 'addContribution']);

    // Help
    Route::get('help', function () {
        return view('help');
    })->name('help');

    // Contact Form Submission
    Route::post('contact/submit', [App\Http\Controllers\Web\ContactController::class, 'store'])->name('contact.submit');

    // Group Expense Sharing Routes
    Route::prefix('group-expense')->name('group-expense.')->group(function () {
        // Main dashboard
        Route::get('/', [GroupExpenseController::class, 'index'])->name('index');

        // Group management
        Route::post('/create', [GroupExpenseController::class, 'createGroup'])->name('create');
        Route::post('/join-by-code', [GroupExpenseController::class, 'joinByCode'])->name('join-by-code');
        Route::get('/{groupId}', [GroupExpenseController::class, 'showGroup'])->name('show');
        Route::delete('/{groupId}', [GroupExpenseController::class, 'deleteGroup'])->name('delete');

        // Member management
        Route::post('/{groupId}/members', [GroupExpenseController::class, 'addMember'])->name('members.add');
        Route::delete('/{groupId}/members/{memberId}', [GroupExpenseController::class, 'removeMember'])->name('members.remove');
        Route::post('/{groupId}/leave', [GroupExpenseController::class, 'leaveGroup'])->name('leave');
        Route::patch('/{groupId}/members/{memberId}/role', [GroupExpenseController::class, 'changeRole'])->name('members.change-role');
        Route::post('/{groupId}/members/{memberId}/activity', [GroupExpenseController::class, 'updateMemberActivity'])->name('members.update-activity');
        Route::post('/{groupId}/settle-up', [GroupExpenseController::class, 'settleUp'])->name('settle-up');

        // Transaction management
        Route::post('/{groupId}/transactions', [GroupExpenseController::class, 'addTransaction'])->name('transactions.add');
        Route::put('/{groupId}/transactions/{transactionId}', [GroupExpenseController::class, 'editTransaction'])->name('transactions.edit');
        Route::delete('/{groupId}/transactions/{transactionId}', [GroupExpenseController::class, 'deleteTransaction'])->name('transactions.delete');
        Route::patch('/{groupId}/transactions/{transactionId}/toggle-status', [GroupExpenseController::class, 'toggleTransactionStatus'])->name('transactions.toggle-status');

        // Member profile
        Route::get('/{groupId}/members/{memberId}/profile', [GroupExpenseController::class, 'getMemberProfile'])->name('members.profile');

        // Settlement payments
        Route::post('/{groupId}/settlements', [GroupExpenseController::class, 'createSettlement'])->name('settlements.create');
        Route::post('/{groupId}/settlements/{settlementId}/submit-proof', [GroupExpenseController::class, 'submitPaymentProof'])->name('settlements.submit-proof');
        Route::post('/{groupId}/settlements/{settlementId}/verify', [GroupExpenseController::class, 'verifyPayment'])->name('settlements.verify');
        Route::post('/{groupId}/settlements/{settlementId}/reject', [GroupExpenseController::class, 'rejectPayment'])->name('settlements.reject');
        Route::get('/{groupId}/settlements/pending', [GroupExpenseController::class, 'getPendingSettlements'])->name('settlements.pending');
    });

    // Community Hub Routes
    Route::prefix('community')->name('community.')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\CommunityController::class, 'index'])->name('index');
        Route::get('/post/{id}', [App\Http\Controllers\Web\CommunityController::class, 'show'])->name('show');
        Route::post('/posts', [App\Http\Controllers\Web\CommunityController::class, 'store'])->name('store');
        Route::delete('/posts/{id}', [App\Http\Controllers\Web\CommunityController::class, 'destroy'])->name('destroy');
        Route::post('/posts/{postId}/vote', [App\Http\Controllers\Web\CommunityController::class, 'vote'])->name('vote');
        Route::post('/posts/{postId}/react', [App\Http\Controllers\Web\CommunityController::class, 'react'])->name('react');
        Route::post('/posts/{postId}/comments', [App\Http\Controllers\Web\CommunityController::class, 'comment'])->name('comment');
        Route::patch('/posts/{postId}/status', [App\Http\Controllers\Web\CommunityController::class, 'updateStatus'])->name('status');
        Route::post('/reports', [App\Http\Controllers\Web\CommunityController::class, 'report'])->name('report');
        Route::get('/notifications', [App\Http\Controllers\Web\CommunityController::class, 'notifications'])->name('notifications');
        Route::patch('/notifications/{id}/read', [App\Http\Controllers\Web\CommunityController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/polls/{pollId}/vote', [App\Http\Controllers\Web\CommunityController::class, 'pollVote'])->name('poll.vote');
    });
});

// Error pages
Route::get('/403', function () {
    abort(403);
})->name('error.403');

Route::get('/404', function () {
    abort(404);
})->name('error.404');

// Temporary debug endpoint to inspect session/auth state from the browser.
Route::get('/debug/session', function (\Illuminate\Http\Request $request) {
    \Log::debug('debug/session called', ['cookies' => $request->cookies->all(), 'headers' => $request->headers->all(), 'session_id' => session()->getId(), 'session_user_id' => session('user_id'), 'auth_id' => auth()->id()]);
    return response()->json([
        'auth_id' => auth()->id(),
        'session_user_id' => session('user_id'),
        'session_id' => session()->getId(),
        'cookies' => $request->cookies->all(),
        'headers' => $request->headers->all()
    ]);
});

