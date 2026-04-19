<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google OAuth page
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['profile', 'email'])
            ->redirect();
    }

    /**
     * Handle the callback from Google
     */
    public function callback(Request $request)
    {
        // If Google did not return an authorization code, bail to login (prevents double invocations)
        if (!$request->has('code')) {
            return redirect('/login');
        }

        try {
            // Use stateless() to avoid invalid_state issues on local dev / when cookies are blocked
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Exception $e) {
            \Log::error('Google OAuth Error (callback): ' . $e->getMessage());
            return redirect('/login')->withErrors(['oauth' => 'Failed to authenticate with Google. Please try again.']);
        }

        // Validate email was retrieved
        $email = $googleUser->getEmail();
        if (!$email) {
            \Log::error('Google OAuth Error: No email returned from Google');
            return redirect('/login')->withErrors(['oauth' => 'Unable to retrieve email from Google. Please try again.']);
        }

        $providerId = $googleUser->getId();
        $name = $googleUser->getName();
        $avatar = $googleUser->getAvatar();

        try {
            // Find SocialAccount (if user previously linked)
            $social = SocialAccount::where('provider', 'google')
                ->where('provider_user_id', $providerId)
                ->first();

            if ($social) {
                $user = $social->user;
            } else {
                // If not linked, try find user by email
                $user = User::where('email', $email)->first();

                if (!$user) {
                    // Create user with Google-provided data
                    $user = User::create([
                        'name' => $name ?: 'User',
                        'email' => $email,
                        'password' => Hash::make(Str::random(40)),
                        'profile_photo' => $avatar,
                        'email_verified_at' => now(),
                    ]);

                    // Send welcome email to new user
                    try {
                        $emailService = new \App\Services\EmailNotificationService();
                        $emailService->sendWelcomeEmail($user->id);
                    } catch (Exception $e) {
                        \Log::warning('Failed to send welcome email: ' . $e->getMessage());
                    }
                } else {
                    // Existing user: update profile_photo if empty
                    if (empty($user->profile_photo) && $avatar) {
                        $user->profile_photo = $avatar;
                        $user->save();
                    }
                }

                // Create social link
                $user->socialAccounts()->create([
                    'provider' => 'google',
                    'provider_user_id' => $providerId,
                    'provider_token' => isset($googleUser->token) ? encrypt($googleUser->token) : null,
                    'provider_refresh_token' => isset($googleUser->refreshToken) ? encrypt($googleUser->refreshToken) : null,
                    'meta' => $googleUser->user,
                    'expires_at' => isset($googleUser->expiresIn) ? now()->addSeconds($googleUser->expiresIn) : null,
                ]);
            }

            // Persist login via Laravel Auth AND session variables
            // The app uses session('user_id') for authentication checks
            Auth::login($user, true);

            // Set session variables that the EnsureAuthenticated middleware checks
            session()->put('user_id', $user->id);
            session()->put('user_name', $user->name);
            session()->put('user_email', $user->email);
            session()->save();

            // Link user to any pending group memberships with matching email
            \App\Services\GroupMemberLinkService::linkUserToGroupMembers($user);

            // Redirect to intended (dashboard)
            return redirect()->intended('/dashboard');

        } catch (Exception $e) {
            \Log::error('Google OAuth Error (user creation/login): ' . $e->getMessage());
            return redirect('/login')->withErrors(['oauth' => 'Failed to complete authentication. Please try again.']);
        }
    }
}
