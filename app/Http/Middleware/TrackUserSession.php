<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

class TrackUserSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track authenticated users with session
        if ($userId = session('user_id')) {
            $this->trackSession($request, $userId);
        }

        return $next($request);
    }

    /**
     * Track or update user session
     */
    protected function trackSession(Request $request, $userId)
    {
        $sessionId = session()->getId();
        $agent = new Agent();
        $agent->setUserAgent($request->header('User-Agent'));

        // Detect device, platform, and browser
        $platform = $agent->platform() ? $agent->platform() . ' ' . $agent->version($agent->platform()) : 'Unknown';
        $browser = $agent->browser() ? $agent->browser() . ' ' . $agent->version($agent->browser()) : 'Unknown Browser';
        $device = $this->getDeviceType($agent);
        $ipAddress = $request->ip();

        // Check if session exists
        $existingSession = DB::table('user_sessions')
            ->where('session_id', $sessionId)
            ->where('user_id', $userId)
            ->first();

        $now = now();

        if ($existingSession) {
            // Update existing session
            DB::table('user_sessions')
                ->where('id', $existingSession->id)
                ->update([
                    'last_activity' => $now,
                    'ip_address' => $ipAddress,
                    'updated_at' => $now
                ]);
        } else {
            // Generate device fingerprint
            $fingerprint = $this->generateFingerprint($request, $agent);

            // Create new session
            DB::table('user_sessions')->insert([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'platform' => $platform,
                'browser' => $browser,
                'device' => $device,
                'ip_address' => $ipAddress,
                'device_fingerprint' => $fingerprint,
                'is_trusted' => true, // Session is trusted since user is already logged in
                'requires_2fa' => false,
                'trusted_at' => $now,
                'location' => $this->getLocation($ipAddress),
                'login_time' => $now,
                'last_activity' => $now,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        // Clean up old sessions (older than 60 days)
        DB::table('user_sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '<', now()->subDays(60))
            ->delete();
    }

    /**
     * Generate a device fingerprint
     */
    protected function generateFingerprint(Request $request, Agent $agent): string
    {
        $components = [
            $agent->platform() ?: 'Unknown',
            $agent->browser() ?: 'Unknown',
            $request->header('Accept-Language', 'en'),
            $this->getDeviceType($agent),
        ];

        return hash('sha256', implode('|', $components));
    }

    /**
     * Get device type from agent
     */
    protected function getDeviceType(Agent $agent): string
    {
        if ($agent->isMobile()) {
            return 'Mobile';
        } elseif ($agent->isTablet()) {
            return 'Tablet';
        } elseif ($agent->isDesktop()) {
            return 'Desktop';
        }

        return 'Unknown Device';
    }

    /**
     * Get location from IP address (basic implementation)
     */
    protected function getLocation(string $ipAddress): ?string
    {
        // For localhost/private IPs
        if (in_array($ipAddress, ['127.0.0.1', '::1']) || str_starts_with($ipAddress, '192.168.') || str_starts_with($ipAddress, '10.')) {
            return 'Local Network';
        }

        // You can integrate with IP geolocation services here
        // For now, return null
        return null;
    }
}
