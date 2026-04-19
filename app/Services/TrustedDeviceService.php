<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class TrustedDeviceService
{
    /**
     * Generate a device fingerprint based on browser/device characteristics
     */
    public function generateFingerprint(Request $request): string
    {
        $agent = new Agent();
        $agent->setUserAgent($request->header('User-Agent'));

        // Create fingerprint from relatively stable device characteristics
        $components = [
            $agent->platform() ?: 'Unknown',
            $agent->browser() ?: 'Unknown',
            $request->header('Accept-Language', 'en'),
            $this->getDeviceType($agent),
        ];

        return hash('sha256', implode('|', $components));
    }

    /**
     * Get device type (Desktop, Mobile, Tablet)
     */
    private function getDeviceType(Agent $agent): string
    {
        if ($agent->isTablet()) {
            return 'Tablet';
        } elseif ($agent->isMobile()) {
            return 'Mobile';
        }
        return 'Desktop';
    }

    /**
     * Check if the current device/session is trusted for the user
     */
    public function isDeviceTrusted(int $userId, Request $request): bool
    {
        $fingerprint = $this->generateFingerprint($request);

        // Check if there's an existing trusted session with this fingerprint
        $trustedSession = DB::table('user_sessions')
            ->where('user_id', $userId)
            ->where('device_fingerprint', $fingerprint)
            ->where('is_trusted', true)
            ->where('requires_2fa', false)
            ->first();

        return $trustedSession !== null;
    }

    /**
     * Check if device fingerprint was revoked recently (requires re-authentication with 2FA)
     */
    public function wasDeviceRevoked(int $userId, Request $request): bool
    {
        $fingerprint = $this->generateFingerprint($request);

        // Check if there's a session with this fingerprint that requires 2FA
        // This happens when a session was revoked
        $revokedSession = DB::table('user_sessions')
            ->where('user_id', $userId)
            ->where('device_fingerprint', $fingerprint)
            ->where('requires_2fa', true)
            ->first();

        return $revokedSession !== null;
    }

    /**
     * Mark the current session as trusted
     */
    public function trustCurrentSession(int $userId, Request $request): void
    {
        $sessionId = session()->getId();
        $fingerprint = $this->generateFingerprint($request);

        DB::table('user_sessions')
            ->where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->update([
                'device_fingerprint' => $fingerprint,
                'is_trusted' => true,
                'requires_2fa' => false,
                'trusted_at' => now(),
                'updated_at' => now(),
            ]);

        // Also clear any requires_2fa flag for this fingerprint
        DB::table('user_sessions')
            ->where('user_id', $userId)
            ->where('device_fingerprint', $fingerprint)
            ->where('requires_2fa', true)
            ->update([
                'requires_2fa' => false,
                'updated_at' => now(),
            ]);
    }

    /**
     * Create a new trusted session record
     */
    public function createTrustedSession(int $userId, Request $request): void
    {
        $agent = new Agent();
        $agent->setUserAgent($request->header('User-Agent'));

        $sessionId = session()->getId();
        $fingerprint = $this->generateFingerprint($request);
        $platform = $agent->platform() ? $agent->platform() . ' ' . $agent->version($agent->platform()) : 'Unknown';
        $browser = $agent->browser() ? $agent->browser() . ' ' . $agent->version($agent->browser()) : 'Unknown Browser';
        $device = $this->getDeviceType($agent);
        $ipAddress = $request->ip();

        // Check if session already exists
        $existingSession = DB::table('user_sessions')
            ->where('session_id', $sessionId)
            ->first();

        if ($existingSession) {
            // Update existing session
            DB::table('user_sessions')
                ->where('session_id', $sessionId)
                ->update([
                    'device_fingerprint' => $fingerprint,
                    'is_trusted' => true,
                    'requires_2fa' => false,
                    'trusted_at' => now(),
                    'last_activity' => now(),
                    'updated_at' => now(),
                ]);
        } else {
            // Create new session
            DB::table('user_sessions')->insert([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'platform' => $platform,
                'browser' => $browser,
                'device' => $device,
                'ip_address' => $ipAddress,
                'device_fingerprint' => $fingerprint,
                'is_trusted' => true,
                'requires_2fa' => false,
                'trusted_at' => now(),
                'location' => $this->getLocation($ipAddress),
                'login_time' => now(),
                'last_activity' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Clear any requires_2fa flag for this fingerprint
        DB::table('user_sessions')
            ->where('user_id', $userId)
            ->where('device_fingerprint', $fingerprint)
            ->where('requires_2fa', true)
            ->update([
                'requires_2fa' => false,
                'updated_at' => now(),
            ]);
    }

    /**
     * Mark a device fingerprint as requiring 2FA (used when session is revoked)
     */
    public function markDeviceRequires2FA(int $userId, string $sessionId): void
    {
        // Get the fingerprint of the session being revoked
        $session = DB::table('user_sessions')
            ->where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->first();

        if ($session && $session->device_fingerprint) {
            // Mark this fingerprint as requiring 2FA for future logins
            // Insert a marker record that will be checked during login
            DB::table('user_sessions')
                ->where('user_id', $userId)
                ->where('device_fingerprint', $session->device_fingerprint)
                ->update([
                    'requires_2fa' => true,
                    'is_trusted' => false,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Get location from IP (simplified version)
     */
    private function getLocation(string $ip): string
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Local';
        }
        return 'Unknown';
    }
}
