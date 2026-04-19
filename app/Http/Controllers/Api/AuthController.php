<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPreferences;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'currency' => 'nullable|string|size:3',
            'timezone' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'currency' => $validated['currency'] ?? config('currency.default', 'INR'),
            'timezone' => $validated['timezone'] ?? 'UTC',
        ]);

        // Send welcome email to new user
        $emailService = new \App\Services\EmailNotificationService();
        $emailService->sendWelcomeEmail($user->id);

        // Create default preferences
        UserPreferences::createDefault($user->id);

        // Create API token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Persist user_settings display_currency
        \Illuminate\Support\Facades\DB::table('user_settings')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'base_currency' => 'INR',
                'display_currency' => $validated['currency'] ?? config('currency.default', 'INR'),
                'currency_updated_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'currency' => $user->currency,
                    'display_currency' => (\Illuminate\Support\Facades\DB::table('user_settings')->where('user_id', $user->id)->first())->display_currency ?? $user->currency,
                ],
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rate limiting
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60); // Block for 1 minute after failed attempt

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($key);

        // Check if 2FA is enabled
        if ($user->two_factor_enabled) {
            // Generate temporary token for 2FA verification
            $tempToken = $user->createToken('2fa-temp', ['verify-2fa'])->plainTextToken;

            return response()->json([
                'success' => true,
                'requires_2fa' => true,
                'temp_token' => $tempToken,
                'message' => 'Please verify with 2FA code',
            ]);
        }

        // Create API token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Load relationships
        $user->load('preferences');

        $displayCurrency = (\Illuminate\Support\Facades\DB::table('user_settings')->where('user_id', $user->id)->first())->display_currency ?? $user->currency;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'currency' => $user->currency,
                    'display_currency' => $displayCurrency,
                    'timezone' => $user->timezone,
                    'two_factor_enabled' => $user->two_factor_enabled,
                    'preferences' => $user->preferences,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices',
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load(['preferences']);

        $displayCurrency = (\Illuminate\Support\Facades\DB::table('user_settings')->where('user_id', $user->id)->first())->display_currency ?? $user->currency;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'currency' => $user->currency,
                'display_currency' => $displayCurrency,
                'timezone' => $user->timezone,
                'two_factor_enabled' => $user->two_factor_enabled,
                'created_at' => $user->created_at,
                'preferences' => $user->preferences,
                'stats' => [
                    'total_balance' => 0,
                    'linked_accounts' => 0,
                    'active_accounts' => 0,
                ],
            ],
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
            'currency' => 'sometimes|string|size:3',
            'timezone' => 'sometimes|string',
        ]);

        $request->user()->update($validated);

        // Persist display currency if provided
        if (isset($validated['currency'])) {
            \Illuminate\Support\Facades\DB::table('user_settings')->updateOrInsert(
                ['user_id' => $request->user()->id],
                [
                    'base_currency' => 'INR',
                    'display_currency' => $validated['currency'],
                    'currency_updated_at' => now()
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $request->user()->fresh(),
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        if (!Hash::check($validated['current_password'], $request->user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 400);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Revoke all other tokens for security
        $request->user()->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully. You have been logged out from other devices.',
        ]);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to send reset link',
        ], 400);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();

                // Revoke all tokens
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to reset password',
        ], 400);
    }

    /**
     * Enable 2FA
     */
    public function enable2FA(Request $request): JsonResponse
    {
        if ($request->user()->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => '2FA is already enabled',
            ], 400);
        }

        // Generate secret
        $secret = $this->generate2FASecret();
        $recoveryCodes = $this->generateRecoveryCodes();

        $request->user()->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ]);

        return response()->json([
            'success' => true,
            'message' => '2FA enabled successfully',
            'data' => [
                'secret' => $secret,
                'recovery_codes' => $recoveryCodes,
                'qr_code_url' => $this->generate2FAQrCode($request->user()->email, $secret),
            ],
        ]);
    }

    /**
     * Disable 2FA
     */
    public function disable2FA(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, $request->user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password is incorrect',
            ], 400);
        }

        $request->user()->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => '2FA disabled successfully',
        ]);
    }

    /**
     * Verify 2FA code
     */
    public function verify2FA(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => '2FA is not enabled',
            ], 400);
        }

        $secret = decrypt($user->two_factor_secret);

        // Verify code (implement TOTP verification here)
        $isValid = $this->verify2FACode($secret, $request->code);

        if (!$isValid) {
            // Check recovery codes
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            if (in_array($request->code, $recoveryCodes)) {
                // Remove used recovery code
                $recoveryCodes = array_diff($recoveryCodes, [$request->code]);
                $user->update([
                    'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
                ]);
                $isValid = true;
            }
        }

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid 2FA code',
            ], 400);
        }

        // Delete temp token and create full access token
        $user->tokens()->where('name', '2fa-temp')->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => '2FA verified successfully',
            'data' => [
                'token' => $token,
            ],
        ]);
    }

    /**
     * Generate 2FA secret
     */
    protected function generate2FASecret(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Generate recovery codes
     */
    protected function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        }
        return $codes;
    }

    /**
     * Generate QR code URL for 2FA
     */
    protected function generate2FAQrCode(string $email, string $secret): string
    {
        $appName = config('app.name', 'TrackFlow');
        return "otpauth://totp/{$appName}:{$email}?secret={$secret}&issuer={$appName}";
    }

    /**
     * Verify 2FA code (basic implementation - use a proper TOTP library in production)
     */
    protected function verify2FACode(string $secret, string $code): bool
    {
        // Implement proper TOTP verification here
        // For now, return true for demonstration
        // In production, use a library like spomky-labs/otphp
        return strlen($code) === 6 && is_numeric($code);
    }
}
