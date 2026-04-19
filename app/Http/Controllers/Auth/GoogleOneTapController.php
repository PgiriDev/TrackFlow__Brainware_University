<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Carbon\Carbon;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleOneTapController extends Controller
{
    /**
     * Endpoint: POST /auth/google/one-tap
     * Body: { id_token: "<google-id-token>" }
     */
    public function handleOneTap(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
            'purpose' => 'nullable|string',
        ]);

        $idToken = $request->input('id_token');

        // verify token
        $payload = $this->verifyIdToken($idToken);

        if (!$payload) {
            return response()->json(['message' => 'Invalid Google ID token'], 401);
        }

        // payload contains 'sub' (Google user id), 'email', 'name', 'picture', 'email_verified' etc.
        $providerUserId = $payload['sub'];
        $email = $payload['email'] ?? null;
        $name = $payload['name'] ?? null;
        $picture = $payload['picture'] ?? null;
        $emailVerified = $payload['email_verified'] ?? false;

        // find existing social account
        $social = SocialAccount::where('provider', 'google')->where('provider_user_id', $providerUserId)->first();

        if ($social) {
            $user = $social->user;
        } else {
            // if no social, try to find by email
            $user = null;
            if ($email) {
                $user = User::where('email', $email)->first();
            }

            if (!$user) {
                // create new user
                $user = User::create([
                    'name' => $name ?? 'Google User',
                    'email' => $email,
                    // set a random password - user can reset later
                    'password' => Hash::make(Str::random(40)),
                    'profile_picture' => $picture,
                ]);

                // Send welcome email to new user
                $emailService = new \App\Services\EmailNotificationService();
                $emailService->sendWelcomeEmail($user->id);
            }

            // create social account record
            $social = SocialAccount::create([
                'user_id' => $user->id,
                'provider' => 'google',
                'provider_user_id' => $providerUserId,
                'provider_token' => null,
                'provider_refresh_token' => null,
                'meta' => $payload,
                'expires_at' => null,
            ]);
        }

        // login user
        Auth::login($user, true);

        // Link user to any pending group memberships with matching email
        \App\Services\GroupMemberLinkService::linkUserToGroupMembers($user);

        // return success
        return response()->json([
            'message' => 'Authenticated',
            'user' => $user,
        ]);
    }

    protected function verifyIdToken(string $idToken)
    {
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);

        try {
            $payload = $client->verifyIdToken($idToken);
            return $payload ? $payload : null;
        } catch (\Exception $e) {
            \Log::error('Google ID token verify error: ' . $e->getMessage());
            return null;
        }
    }
}
