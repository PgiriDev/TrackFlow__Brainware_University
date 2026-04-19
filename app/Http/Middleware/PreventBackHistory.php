<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     * Prevents browser back button from accessing authenticated pages after logout
     * and prevents accessing login/register pages when already authenticated.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Check if response is StreamedResponse (for file downloads)
        if ($response instanceof StreamedResponse) {
            // For streamed responses, headers are already set in the controller
            return $response;
        }

        // Add cache control headers to prevent page caching for regular responses
        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
