<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LegacySessionAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // If already logged in via Laravel auth, proceed
        if (Auth::check()) {
            return $next($request);
        }

        // Support legacy session('user_id') used throughout the app
        $userId = $request->session()->get('user_id');
        if ($userId) {
            \Log::info('LegacySessionAuth: found session user_id', ['user_id' => $userId]);
            Auth::loginUsingId($userId);
            \Log::info('LegacySessionAuth: logged in user', ['auth_id' => Auth::id()]);
            return $next($request);
        }

        // Not authenticated - return JSON 401 for AJAX/API requests, otherwise redirect to login
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        return redirect()->route('login');
    }
}
