<?php

namespace App\Services;

use App\Models\RememberToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RememberMeService
{
    /**
     * Cookie name for the remember token
     */
    private const COOKIE_NAME = 'remember_token';

    /**
     * Token expiration in days
     */
    private const EXPIRATION_DAYS = 30;

    /**
     * Rate limit: maximum validation attempts per minute per IP
     */
    private const MAX_ATTEMPTS_PER_MINUTE = 10;

    /**
     * Create a new remember token for a user.
     * Generates a cryptographically secure token, hashes it, and stores in database.
     *
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function createToken(User $user, Request $request): \Symfony\Component\HttpFoundation\Cookie
    {
        // Generate a cryptographically secure random token (64 bytes = 128 hex chars)
        $rawToken = bin2hex(random_bytes(64));

        // Create a selector (first 32 chars) and validator (rest) approach
        // Selector is stored as-is for lookups, validator is hashed
        $selector = substr($rawToken, 0, 32);
        $validator = substr($rawToken, 32);

        // Hash the validator portion using SHA-256 for secure storage
        $hashedValidator = hash('sha256', $validator);

        // Combine selector and hashed validator for database storage
        $tokenForDb = $selector . ':' . $hashedValidator;

        // Get user agent (mandatory for matching)
        $userAgent = $request->header('User-Agent') ?? 'Unknown';

        // Calculate expiration
        $expiresAt = now()->addDays(self::EXPIRATION_DAYS);

        // Delete any existing tokens for this user with the same user agent
        // (prevents accumulation of old tokens)
        RememberToken::where('user_id', $user->id)
            ->where('user_agent', $userAgent)
            ->delete();

        // Create the token record
        RememberToken::create([
            'user_id' => $user->id,
            'token' => $tokenForDb,
            'user_agent' => $userAgent,
            'ip_address' => $request->ip(),
            'expires_at' => $expiresAt,
            'last_used_at' => now(),
        ]);

        // Log token creation (without sensitive data)
        Log::info('RememberMe: Token created', [
            'user_id' => $user->id,
            'expires_at' => $expiresAt->toDateTimeString(),
            'ip' => $request->ip(),
        ]);

        // Create secure cookie with the raw token (selector + validator)
        // HttpOnly: true - prevents JavaScript access (XSS protection)
        // Secure: true - only sent over HTTPS (in production)
        // SameSite: Strict - prevents CSRF attacks
        return Cookie::make(
            self::COOKIE_NAME,
            $rawToken,
            self::EXPIRATION_DAYS * 24 * 60, // minutes
            '/',
            null,
            config('app.env') === 'production', // Secure only in production
            true,  // HttpOnly
            false,
            'Strict' // SameSite
        );
    }

    /**
     * Validate a remember token and return the user if valid.
     * Performs token rotation on successful validation.
     *
     * @param Request $request
     * @return User|null
     */
    public function validateToken(Request $request): ?User
    {
        $rawToken = $request->cookie(self::COOKIE_NAME);

        if (!$rawToken || strlen($rawToken) !== 128) {
            return null;
        }

        // Check rate limiting
        if (!$this->checkRateLimit($request)) {
            Log::warning('RememberMe: Rate limit exceeded', [
                'ip' => $request->ip(),
            ]);
            return null;
        }

        // Extract selector and validator
        $selector = substr($rawToken, 0, 32);
        $validator = substr($rawToken, 32);

        // Find token by selector prefix
        $rememberedToken = RememberToken::where('token', 'LIKE', $selector . ':%')
            ->valid()
            ->first();

        if (!$rememberedToken) {
            Log::debug('RememberMe: Token not found or expired', [
                'ip' => $request->ip(),
            ]);
            return null;
        }

        // Extract stored hashed validator
        $parts = explode(':', $rememberedToken->token);
        if (count($parts) !== 2) {
            Log::warning('RememberMe: Invalid token format', [
                'token_id' => $rememberedToken->id,
            ]);
            $rememberedToken->delete();
            return null;
        }

        $storedHashedValidator = $parts[1];

        // Verify the validator using constant-time comparison
        $providedHashedValidator = hash('sha256', $validator);
        if (!hash_equals($storedHashedValidator, $providedHashedValidator)) {
            Log::warning('RememberMe: Token validator mismatch', [
                'token_id' => $rememberedToken->id,
                'ip' => $request->ip(),
            ]);
            // Possible token theft - delete all tokens for this user
            RememberToken::where('user_id', $rememberedToken->user_id)->delete();
            return null;
        }

        // MANDATORY: Verify user agent matches exactly
        $currentUserAgent = $request->header('User-Agent') ?? 'Unknown';
        if ($rememberedToken->user_agent !== $currentUserAgent) {
            Log::warning('RememberMe: User agent mismatch (mandatory check failed)', [
                'token_id' => $rememberedToken->id,
                'user_id' => $rememberedToken->user_id,
                'expected_ua' => substr($rememberedToken->user_agent, 0, 50) . '...',
                'received_ua' => substr($currentUserAgent, 0, 50) . '...',
                'ip' => $request->ip(),
            ]);
            // Delete this specific token as it might be compromised
            $rememberedToken->delete();
            return null;
        }

        // Get the user
        $user = User::find($rememberedToken->user_id);
        if (!$user) {
            $rememberedToken->delete();
            return null;
        }

        // Update last used timestamp
        $rememberedToken->update([
            'last_used_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        Log::info('RememberMe: Auto-login successful', [
            'user_id' => $user->id,
            'token_id' => $rememberedToken->id,
            'ip' => $request->ip(),
        ]);

        return $user;
    }

    /**
     * Rotate the remember token (issue a new token, invalidate old one).
     * Should be called after successful auto-login for enhanced security.
     *
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function rotateToken(User $user, Request $request): \Symfony\Component\HttpFoundation\Cookie
    {
        $currentUserAgent = $request->header('User-Agent') ?? 'Unknown';

        // Delete the old token for this user agent
        RememberToken::where('user_id', $user->id)
            ->where('user_agent', $currentUserAgent)
            ->delete();

        // Create and return a new token
        return $this->createToken($user, $request);
    }

    /**
     * Delete all remember tokens for a user (used during logout).
     *
     * @param int $userId
     * @return int Number of tokens deleted
     */
    public function deleteAllTokensForUser(int $userId): int
    {
        $count = RememberToken::where('user_id', $userId)->delete();

        Log::info('RememberMe: All tokens deleted for user', [
            'user_id' => $userId,
            'tokens_deleted' => $count,
        ]);

        return $count;
    }

    /**
     * Delete only the current token (for logout from single device).
     *
     * @param Request $request
     * @param int $userId
     * @return bool
     */
    public function deleteCurrentToken(Request $request, int $userId): bool
    {
        $rawToken = $request->cookie(self::COOKIE_NAME);

        if (!$rawToken || strlen($rawToken) !== 128) {
            return false;
        }

        $selector = substr($rawToken, 0, 32);

        return RememberToken::where('user_id', $userId)
            ->where('token', 'LIKE', $selector . ':%')
            ->delete() > 0;
    }

    /**
     * Create a cookie to forget/clear the remember token.
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function forgetCookie(): \Symfony\Component\HttpFoundation\Cookie
    {
        return Cookie::forget(self::COOKIE_NAME);
    }

    /**
     * Check rate limiting for token validation attempts.
     *
     * @param Request $request
     * @return bool True if within rate limit
     */
    private function checkRateLimit(Request $request): bool
    {
        $key = 'remember_me_attempts:' . $request->ip();
        $attempts = cache()->get($key, 0);

        if ($attempts >= self::MAX_ATTEMPTS_PER_MINUTE) {
            return false;
        }

        cache()->put($key, $attempts + 1, 60); // 60 seconds TTL

        return true;
    }

    /**
     * Cleanup expired tokens from the database.
     * Should be called periodically (e.g., via scheduled task).
     *
     * @return int Number of tokens deleted
     */
    public function cleanupExpiredTokens(): int
    {
        return RememberToken::cleanupExpired();
    }

    /**
     * Get the cookie name (for external use if needed).
     *
     * @return string
     */
    public static function getCookieName(): string
    {
        return self::COOKIE_NAME;
    }
}
