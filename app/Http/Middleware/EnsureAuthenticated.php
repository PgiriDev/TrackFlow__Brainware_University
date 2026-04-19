<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAuthenticated
{
    /**
     * Handle an incoming request.
     * Ensures user is authenticated before accessing protected routes.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!session('user_id')) {
            // Store intended URL for redirect after login
            session(['url.intended' => $request->url()]);

            // Redirect to login page
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        return $next($request);
    }
}
