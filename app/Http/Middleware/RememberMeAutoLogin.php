<?php

namespace App\Http\Middleware;

use App\Services\RememberMeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RememberMeAutoLogin
{
    /**
     * Handle an incoming request.
     * Automatically logs in users with valid remember tokens.
     * 
     * For users with 2FA enabled:
     * - Skip email/password but still require 2FA code
     * - Set session flag to indicate remembered user needs 2FA
     *
     * Security features:
     * - MANDATORY user agent matching (no bypass)
     * - Token rotation on successful auto-login
     * - Rate limiting on validation attempts
     * - Secure token validation with constant-time comparison
     * - 2FA is ALWAYS required if enabled (never bypassed)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if user is already authenticated
        if (session('user_id')) {
            return $next($request);
        }

        // Skip if already in 2FA verification flow
        if (session('2fa_user_id')) {
            return $next($request);
        }

        // Skip if no remember token cookie exists
        $rememberToken = $request->cookie(RememberMeService::getCookieName());
        if (!$rememberToken) {
            return $next($request);
        }

        // Attempt to validate the remember token
        $rememberMeService = new RememberMeService();
        $user = $rememberMeService->validateToken($request);

        if ($user) {
            // Check if user has 2FA enabled
            if ($user->two_factor_enabled && $user->two_factor_secret) {
                // User has 2FA - set up 2FA verification flow (skip email/password)
                session()->put('2fa_user_id', $user->id);
                session()->put('2fa_remember', true); // They already had remember me
                session()->put('2fa_remembered_login', true); // Flag for skipped email/password

                // Check 2FA method type and send OTP if email method
                if ($user->two_factor_secret === 'email_otp') {
                    // Email OTP method - send code
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
                            'ipAddress' => $request->ip(),
                            'browser' => $request->header('User-Agent')
                        ], function ($message) use ($user) {
                            $message->to($user->email)
                                ->subject('🔐 Login Verification Code - TrackFlow');
                        });
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send login OTP email: ' . $e->getMessage());
                    }

                    session()->put('2fa_method', 'email');
                } else {
                    // Authenticator app method
                    session()->put('2fa_method', 'app');
                }

                \Illuminate\Support\Facades\Log::info('RememberMe: User has 2FA, redirecting to 2FA verify', [
                    'user_id' => $user->id,
                    'ip' => $request->ip(),
                ]);

                // Redirect to 2FA verification page
                return redirect()->route('2fa.verify')->with('info', 'Please enter your 2FA code to continue');
            }

            // No 2FA - proceed with full auto-login
            session()->put('user_id', $user->id);
            session()->put('user_name', $user->name);
            session()->put('user_email', $user->email);

            // Rotate the token for enhanced security
            $newCookie = $rememberMeService->rotateToken($user, $request);

            // Log the auto-login for security auditing
            \Illuminate\Support\Facades\Log::info('RememberMe: Auto-login via middleware (no 2FA)', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'url' => $request->url(),
            ]);

            // Continue with the request and attach the new rotated cookie
            $response = $next($request);

            // Attach the new rotated cookie to the response
            if ($response instanceof Response) {
                $response->headers->setCookie($newCookie);
            }

            return $response;
        }

        // Token validation failed - clear the invalid cookie
        $response = $next($request);

        if ($response instanceof Response) {
            $response->headers->setCookie($rememberMeService->forgetCookie());
        }

        return $response;
    }
}
