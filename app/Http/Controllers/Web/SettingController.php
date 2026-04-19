<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Jobs\DeleteAccountOtpJob;

class SettingController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $user = \App\Models\User::find($userId);
        $currentSessionId = session()->getId();

        // Get active sessions for the user (active in last 30 days)
        $sessions = DB::table('user_sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>', now()->subDays(30))
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                $session->is_current = ($session->session_id === $currentSessionId);
                $session->is_active = now()->diffInMinutes($session->last_activity) < 30;
                return $session;
            });

        return view('settings.index', compact('user', 'sessions', 'currentSessionId'));
    }

    public function profile()
    {
        $userId = session('user_id');
        $user = \App\Models\User::find($userId);

        // Get currency configuration - use cached singleton
        $currencyConfig = config('currency.currencies');
        $userSetting = app('user.settings');
        $userCurrency = $userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR');
        $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? $currencyConfig[config('currency.default')]['symbol'] ?? '₹';
        $currencyName = $currencyConfig[$userCurrency]['name'] ?? $currencyConfig[config('currency.default')]['name'] ?? 'Indian Rupee';

        // Get user's groups with member details
        $userGroups = \App\Models\GroupMember::where('user_id', $userId)
            ->with([
                'group' => function ($query) {
                    $query->withCount('members');
                }
            ])
            ->where('status', 'active')
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->group->id,
                    'name' => $member->group->name,
                    'description' => $member->group->description,
                    'group_code' => $member->group->group_code,
                    'role' => $member->role,
                    'members_count' => $member->group->members_count,
                    'joined_at' => $member->created_at->format('M d, Y'),
                    'is_leader' => $member->role === 'leader'
                ];
            });

        $groupsCount = $userGroups->count();

        // Get primary UPI for the user
        $primaryUpi = \App\Models\UserUpi::where('user_id', $userId)
            ->where('is_primary', true)
            ->where('is_active', true)
            ->first();

        // Get user's community data
        $communityReputation = \App\Models\CommunityReputation::getOrCreate($userId);

        // Get user's community posts with stats
        $communityPosts = \App\Models\CommunityPost::where('user_id', $userId)
            ->with(['tags', 'reactions'])
            ->withCount(['comments', 'reactions', 'reports'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get community stats
        $communityStats = [
            'total_posts' => \App\Models\CommunityPost::where('user_id', $userId)->count(),
            'total_comments' => \App\Models\CommunityComment::where('user_id', $userId)->count(),
            'total_views' => \App\Models\CommunityPost::where('user_id', $userId)->sum('view_count'),
            'total_upvotes' => \App\Models\CommunityPost::where('user_id', $userId)->sum('vote_score'),
            'points' => $communityReputation->points,
            'level' => $communityReputation->level,
        ];

        return view('profile', compact('user', 'currencySymbol', 'currencyName', 'userCurrency', 'userGroups', 'groupsCount', 'primaryUpi', 'communityPosts', 'communityStats', 'communityReputation'));
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'currency' => 'nullable|string|max:3',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get user instance
            $user = \App\Models\User::find($userId);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Check if currency is changing - need to convert large transaction threshold
            $oldCurrency = $user->currency ?? 'INR';
            $newCurrency = $request->currency ?? 'INR';

            if ($oldCurrency !== $newCurrency) {
                // Convert large transaction threshold from old currency to new currency
                $userPrefs = DB::table('user_preferences')->where('user_id', $userId)->first();
                if ($userPrefs && isset($userPrefs->large_transaction_threshold)) {
                    $currencyService = app(\App\Services\CurrencyService::class);
                    $oldThreshold = floatval($userPrefs->large_transaction_threshold);

                    // Convert: old currency -> INR -> new currency
                    $newThreshold = $currencyService->convert($oldThreshold, $oldCurrency, $newCurrency);

                    // Update the threshold with converted value
                    DB::table('user_preferences')
                        ->where('user_id', $userId)
                        ->update([
                            'large_transaction_threshold' => round($newThreshold, 2),
                            'updated_at' => now()
                        ]);
                }
            }

            // Update user data
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->currency = $newCurrency;
            $user->bio = $request->bio;
            $user->save();

            // Persist display currency into user_settings as well
            \Illuminate\Support\Facades\DB::table('user_settings')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'base_currency' => 'INR',
                    'display_currency' => $newCurrency,
                    'currency_updated_at' => now()
                ]
            );

            // Clear cached user settings and preferences to pick up new values
            cache()->forget("user_settings:{$userId}");
            cache()->forget("user_preferences:{$userId}");

            // Update session data
            session([
                'user_name' => $request->name,
                'user_email' => $request->email,
                'user_currency' => $request->currency ?? 'INR'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'currency' => $user->currency,
                    'bio' => $user->bio
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateTheme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'theme' => 'required|string|in:light,dark,auto',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid theme value',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Update or insert theme preference
            DB::table('user_preferences')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'theme' => $request->theme,
                    'updated_at' => now()
                ]
            );

            // Clear cached user preferences to pick up new values
            cache()->forget("user_preferences:{$userId}");

            // Also store in session for quick access
            session(['user_theme' => $request->theme]);

            return response()->json([
                'success' => true,
                'message' => 'Theme preference saved successfully',
                'theme' => $request->theme
            ]);

        } catch (\Exception $e) {
            Log::error('Theme update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save theme: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get current user
            $user = DB::table('users')->where('id', $userId)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Update password
            DB::table('users')
                ->where('id', $userId)
                ->update(['password' => Hash::make($request->new_password)]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }

    // Verify Current Password
    public function verifyCurrentPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password is required'
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get current user
            $user = DB::table('users')->where('id', $userId)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Password verified'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify password: ' . $e->getMessage()
            ], 500);
        }
    }

    // Send OTP for Password Change
    public function sendPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get current user
            $user = DB::table('users')->where('id', $userId)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Generate 8-digit OTP
            $otp = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

            // Delete any existing OTPs for this user with same purpose
            DB::table('otps')
                ->where('user_id', $userId)
                ->where('purpose', 'password_change')
                ->delete();

            // Store OTP in unified otps table
            DB::table('otps')->insert([
                'user_id' => $userId,
                'email' => $user->email,
                'otp' => $otp,
                'purpose' => 'password_change',
                'extra_data' => json_encode(['new_password' => Hash::make($request->new_password)]),
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create notification in database for OTP
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'type' => 'password_otp',
                'title' => 'Password Change OTP',
                'message' => 'Your OTP is: ' . $otp . ' (valid for 10 min)',
                'icon' => 'fa-key',
                'color' => 'primary',
                'data' => json_encode(['otp' => $otp]),
                'action_url' => null,
                'is_read' => 0,
                'priority' => 'high',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send OTP via email to registered email using template
            try {
                \Illuminate\Support\Facades\Mail::send('email-template.password-change-otp', [
                    'userName' => $user->name ?? 'User',
                    'otp' => $otp,
                    'title' => 'Password Change Verification'
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('🔐 Password Change OTP - TrackFlow');
                });
            } catch (\Exception $e) {
                // If email fails, still return success but log the error
                \Illuminate\Support\Facades\Log::error('Failed to send OTP email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your email address',
                'show_otp_popup' => true, // Flag for frontend to show popup
                'debug_otp' => config('app.debug') ? $otp : null // Only show in debug mode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    // Change Password with OTP
    public function changePasswordWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:8',
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get current user
            $user = DB::table('users')->where('id', $userId)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Delete expired OTPs
            DB::table('otps')
                ->where('expires_at', '<', now())
                ->delete();

            // Get OTP from unified otps table
            $otpRecord = DB::table('otps')
                ->where('user_id', $userId)
                ->where('purpose', 'password_change')
                ->where('expires_at', '>', now())
                ->first();

            // Check if OTP exists
            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'No OTP found. Please request a new one.'
                ], 422);
            }

            // Verify OTP
            if ($request->otp !== $otpRecord->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP'
                ], 422);
            }

            // Verify current password one more time
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Get new password from extra_data
            $extraData = json_decode($otpRecord->extra_data, true);
            $newPasswordHash = $extraData['new_password'] ?? Hash::make($request->new_password);

            // Update password
            DB::table('users')
                ->where('id', $userId)
                ->update(['password' => $newPasswordHash]);

            // Clear OTP from database
            DB::table('otps')
                ->where('id', $otpRecord->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password: ' . $e->getMessage()
            ], 500);
        }
    }

    // Step 1: Verify Current Password and Send OTP
    public function verifyPasswordAndSendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password is required'
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = DB::table('users')->where('id', $userId)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Generate 8-digit OTP
            $otp = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

            // Delete any existing OTPs for this user with same purpose
            DB::table('otps')
                ->where('user_id', $userId)
                ->where('purpose', 'password_change')
                ->delete();

            // Store OTP in unified otps table
            DB::table('otps')->insert([
                'user_id' => $userId,
                'email' => $user->email,
                'otp' => $otp,
                'purpose' => 'password_change',
                'extra_data' => null,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Send OTP via email to registered email using template
            try {
                \Illuminate\Support\Facades\Mail::send('email-template.password-change-otp', [
                    'userName' => $user->name ?? 'User',
                    'otp' => $otp,
                    'title' => 'Password Change Verification'
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('🔐 Password Change OTP - TrackFlow');
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send OTP email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your email address',
                'show_otp_popup' => true,
                'debug_otp' => config('app.debug') ? $otp : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    // Step 2: Verify OTP
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Valid 8-digit OTP is required'
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = DB::table('users')->where('id', $userId)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Delete expired OTPs
            DB::table('otps')
                ->where('expires_at', '<', now())
                ->delete();

            // Get OTP from unified otps table
            $otpRecord = DB::table('otps')
                ->where('user_id', $userId)
                ->where('purpose', 'password_change')
                ->where('expires_at', '>', now())
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP not found or expired'
                ], 404);
            }

            // Verify OTP
            if ($request->otp !== $otpRecord->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP'
                ], 422);
            }

            // Mark OTP as verified by updating extra_data
            DB::table('otps')
                ->where('id', $otpRecord->id)
                ->update(['extra_data' => json_encode(['verified' => true])]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    // Step 3: Update Password
    public function updatePasswordFinal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = DB::table('users')->where('id', $userId)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Check if OTP was verified
            $otpRecord = DB::table('otps')
                ->where('user_id', $userId)
                ->where('purpose', 'password_change')
                ->where('expires_at', '>', now())
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP verification required'
                ], 422);
            }

            // Check if OTP is verified
            $extraData = json_decode($otpRecord->extra_data, true);
            if (!isset($extraData['verified']) || $extraData['verified'] !== true) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP verification required'
                ], 422);
            }

            // Verify current password one more time
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Update password
            DB::table('users')
                ->where('id', $userId)
                ->update(['password' => Hash::make($request->new_password)]);

            // Clear OTP from database
            DB::table('otps')
                ->where('id', $otpRecord->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePreferences(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Update user preferences in database
            $updateData = [];

            if ($request->has('theme')) {
                $updateData['theme'] = $request->theme;
            }

            if ($request->has('language')) {
                $updateData['language'] = $request->language;
            }

            if ($request->has('compact_mode')) {
                $updateData['compact_mode'] = $request->compact_mode ? 1 : 0;
            }

            if ($request->has('show_decimals')) {
                $updateData['show_decimals'] = $request->show_decimals ? 1 : 0;
            }

            if (!empty($updateData)) {
                $updateData['updated_at'] = now();
                DB::table('users')
                    ->where('id', $userId)
                    ->update($updateData);
            }

            // Update user_preferences table for date format and other settings
            if ($request->has('date_format')) {
                DB::table('user_preferences')->updateOrInsert(
                    ['user_id' => $userId],
                    [
                        'date_format' => $request->date_format,
                        'updated_at' => now()
                    ]
                );
                // Clear cached user preferences to pick up new values
                cache()->forget("user_preferences:{$userId}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Preferences updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update preferences: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeLanguage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'required|string|in:en,hi,es',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid language selection',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Update user's language preference in database
            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'language' => $request->language,
                    'updated_at' => now()
                ]);

            // Update session
            session(['user_language' => $request->language]);

            // Set application locale
            app()->setLocale($request->language);

            return response()->json([
                'success' => true,
                'message' => 'Language changed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change language: ' . $e->getMessage()
            ], 500);
        }
    }

    // Two-Factor Authentication Methods
    public function enable2FA(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = \App\Models\User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Generate secret key
            $google2fa = app(\PragmaRX\Google2FA\Google2FA::class);
            $secret = $google2fa->generateSecretKey();

            // Store secret temporarily in session
            session(['temp_2fa_secret' => $secret]);

            // Generate QR code with TrackFlow as the issuer
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                'TrackFlow',
                $user->email,
                $secret
            );

            $qrCodeRenderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($qrCodeRenderer);
            $qrCodeSvg = $writer->writeString($qrCodeUrl);

            return response()->json([
                'success' => true,
                'secret' => $secret,
                'qr_code' => $qrCodeSvg
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enable 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    // Send OTP for Account Deletion (Step 1)
    public function sendDeleteAccountOtp(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            // Generate 8-digit OTP
            $otp = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

            // Delete any existing OTPs for this user with same purpose
            DB::table('otps')
                ->where('user_id', $userId)
                ->where('purpose', 'account_deletion')
                ->delete();

            // Store OTP in unified otps table
            $otpId = DB::table('otps')->insertGetId([
                'user_id' => $userId,
                'email' => $user->email,
                'otp' => $otp,
                'purpose' => 'account_deletion',
                'extra_data' => null,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert into notifications so it appears in the notification center
            DB::table('notifications')->insert([
                'user_id' => $userId,
                'type' => 'account_delete_otp',
                'title' => 'Account Deletion OTP',
                'message' => 'Your account deletion OTP is: ' . $otp . ' (valid for 10 min)',
                'icon' => 'fa-exclamation-triangle',
                'color' => 'danger',
                'data' => json_encode(['otp' => $otp]),
                'action_url' => null,
                'is_read' => 0,
                'priority' => 'high',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send OTP via email to registered email using template
            try {
                Mail::send('email-template.account-deletion-otp', [
                    'userName' => $user->name ?? 'User',
                    'otp' => $otp,
                    'title' => 'Account Deletion Confirmation'
                ], function ($message) use ($user) {
                    $message->to($user->email)->subject('⚠️ Account Deletion OTP - TrackFlow');
                });
            } catch (\Exception $e) {
                Log::error('Failed to send account deletion OTP email: ' . $e->getMessage());
            }

            // Dispatch cleanup job to remove OTP after 10 minutes (update to use new table)
            try {
                DeleteAccountOtpJob::dispatch($otpId)->delay(now()->addMinutes(10));
            } catch (\Exception $e) {
                // If queue not configured, ignore - cleanup will occur on next run
                Log::warning('Failed to dispatch DeleteAccountOtpJob: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your email address',
                'show_otp_popup' => true,
                'debug_otp' => config('app.debug') ? $otp : null
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP: ' . $e->getMessage()], 500);
        }
    }

    // Verify OTP and delete account if valid
    public function verifyDeleteAccountOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Valid 8-digit OTP is required', 'errors' => $validator->errors()], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            // Remove expired OTPs
            DB::table('otps')->where('expires_at', '<', now())->delete();

            // Get OTP from unified otps table
            $otpRecord = DB::table('otps')
                ->where('user_id', $userId)
                ->where('purpose', 'account_deletion')
                ->where('otp', $request->otp)
                ->where('expires_at', '>', now())
                ->first();

            if (!$otpRecord) {
                return response()->json(['success' => false, 'message' => 'OTP not found or expired'], 422);
            }

            // OTP valid — delete the OTP immediately
            DB::table('otps')->where('id', $otpRecord->id)->delete();

            // Call existing deleteAccount flow to remove user data
            return $this->deleteAccount($request);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to verify OTP: ' . $e->getMessage()], 500);
        }
    }

    public function verify2FA(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code format',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Prefer the framework auth user, but fall back to session-based user id used throughout the app.
            $user = auth()->user();
            if (!$user) {
                $sessionUserId = session('user_id');
                if ($sessionUserId) {
                    $user = \App\Models\User::find($sessionUserId);
                    \Log::debug('verify2FA: auth()->user() missing, falling back to session user id: ' . var_export($sessionUserId, true));
                }
            }

            if (!$user) {
                \Log::warning('verify2FA: User not authenticated - no auth user and no session user_id');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get the secret from session (used for QR and verification)
            $secret = session('temp_2fa_secret');
            if (!$secret) {
                return response()->json([
                    'success' => false,
                    'message' => '2FA setup not initiated. Please start again.'
                ], 422);
            }

            // Verify the code
            $google2fa = app(\PragmaRX\Google2FA\Google2FA::class);
            $valid = $google2fa->verifyKey($secret, $request->code);

            if (!$valid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to verify code. Please try again.'
                ], 422);
            }

            // Generate recovery codes
            $recoveryCodes = [];
            for ($i = 0; $i < 8; $i++) {
                $recoveryCodes[] = strtoupper(substr(md5(random_bytes(10)), 0, 10));
            }

            // Save secret and recovery codes to database only after successful verification
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'two_factor_secret' => encrypt($secret),
                    'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
                    'two_factor_enabled' => true,
                    'updated_at' => now()
                ]);

            // Clear temporary secret
            session()->forget('temp_2fa_secret');

            return response()->json([
                'success' => true,
                'message' => '2FA enabled successfully',
                'recovery_codes' => $recoveryCodes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    public function disable2FA(Request $request)
    {
        try {
            // Prefer the framework auth user, but fall back to session-based user id used elsewhere in the app.
            $user = auth()->user();
            if (!$user) {
                $sessionUserId = session('user_id');
                if ($sessionUserId) {
                    $user = \App\Models\User::find($sessionUserId);
                    \Log::debug('disable2FA: auth()->user() missing, falling back to session user id: ' . var_export($sessionUserId, true));
                }
            }

            \Log::debug('disable2FA called, resolved user: ' . var_export($user ? $user->id : null, true));
            if (!$user) {
                \Log::error('disable2FA: User not authenticated (no auth user and no session user_id)');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Disable 2FA
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'two_factor_secret' => null,
                    'two_factor_recovery_codes' => null,
                    'two_factor_enabled' => false,
                    'updated_at' => now()
                ]);

            \Log::info('disable2FA: 2FA disabled for user_id ' . $user->id);
            return response()->json([
                'success' => true,
                'message' => '2FA disabled successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('disable2FA: Exception - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to disable 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRecoveryCodes(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = DB::table('users')->where('id', $userId)->first();

            if (!$user || !$user->two_factor_recovery_codes) {
                return response()->json([
                    'success' => false,
                    'message' => '2FA is not enabled'
                ], 422);
            }

            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

            return response()->json([
                'success' => true,
                'recovery_codes' => $recoveryCodes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recovery codes: ' . $e->getMessage()
            ], 500);
        }
    }

    // Email OTP 2FA Methods
    public function sendEmailOtp2FA(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Delete any existing OTPs for this user with same purpose
            DB::table('otps')
                ->where('user_id', $userId)
                ->where('purpose', '2fa_email')
                ->delete();

            // Store OTP in unified otps table
            DB::table('otps')->insert([
                'user_id' => $userId,
                'email' => $user->email,
                'otp' => $otp,
                'purpose' => '2fa_email',
                'extra_data' => null,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Send OTP email to registered email using template
            try {
                \Illuminate\Support\Facades\Mail::send('email-template.2fa-verification-otp', [
                    'userName' => $user->name ?? 'User',
                    'otp' => $otp,
                    'title' => '2FA Verification'
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('🛡️ 2FA Verification Code - TrackFlow');
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send 2FA OTP email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email',
                'show_otp_popup' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyEmail2FA(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code format'
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Delete expired OTPs
            DB::table('otps')->where('expires_at', '<', now())->delete();

            // Get OTP from unified otps table
            $otpRecord = DB::table('otps')
                ->where('user_id', $userId)
                ->where('purpose', '2fa_email')
                ->where('expires_at', '>', now())
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'No verification code found. Please request a new one.'
                ], 422);
            }

            // Verify OTP
            if ($otpRecord->otp !== $request->code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code'
                ], 422);
            }

            // Delete used OTP
            DB::table('otps')->where('id', $otpRecord->id)->delete();

            // Generate 8 recovery codes
            $recoveryCodes = [];
            for ($i = 0; $i < 8; $i++) {
                $recoveryCodes[] = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
            }

            // Enable 2FA with email method
            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'two_factor_enabled' => true,
                    'two_factor_secret' => 'email_otp', // Mark as email method
                    'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Email OTP 2FA enabled successfully',
                'recovery_codes' => $recoveryCodes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify code: ' . $e->getMessage()
            ], 500);
        }
    }

    // Session Management Methods
    public function getActiveSessions(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $currentSessionId = session()->getId();

            // Get active sessions (active in last 30 days)
            $sessions = DB::table('user_sessions')
                ->where('user_id', $userId)
                ->where('last_activity', '>', now()->subDays(30))
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) use ($currentSessionId) {
                    return [
                        'id' => $session->id,
                        'browser' => $session->browser,
                        'device' => $session->device,
                        'platform' => $session->platform,
                        'ip_address' => $session->ip_address,
                        'last_activity' => $session->last_activity,
                        'is_current' => $session->session_id === $currentSessionId,
                        'is_active' => now()->diffInMinutes($session->last_activity) < 30
                    ];
                });

            return response()->json([
                'success' => true,
                'sessions' => $sessions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sessions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function revokeSession(Request $request, $session_id)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Validate session_id
            if (!is_numeric($session_id) || $session_id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session ID'
                ], 422);
            }

            $currentSessionId = session()->getId();
            $sessionToRevoke = DB::table('user_sessions')
                ->where('id', $session_id)
                ->where('user_id', $userId)
                ->first();

            if (!$sessionToRevoke) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            // Don't allow revoking current session
            if ($sessionToRevoke->session_id === $currentSessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot revoke current session. Use logout instead.'
                ], 422);
            }

            // Mark this device fingerprint as requiring 2FA for future logins
            $trustedDeviceService = new \App\Services\TrustedDeviceService();
            $trustedDeviceService->markDeviceRequires2FA($userId, $sessionToRevoke->session_id);

            // Delete from Laravel's sessions table to invalidate the session
            DB::table('sessions')
                ->where('id', $sessionToRevoke->session_id)
                ->delete();

            // Delete from user_sessions table
            DB::table('user_sessions')
                ->where('id', $session_id)
                ->where('user_id', $userId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Session revoked successfully. The device will require 2FA on next login.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke session: ' . $e->getMessage()
            ], 500);
        }
    }

    public function revokeAllSessions(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $currentSessionId = session()->getId();

            // Get all sessions to revoke (except current) with their fingerprints
            $sessionsToRevoke = DB::table('user_sessions')
                ->where('user_id', $userId)
                ->where('session_id', '!=', $currentSessionId)
                ->get();

            // Mark all device fingerprints as requiring 2FA
            $trustedDeviceService = new \App\Services\TrustedDeviceService();
            foreach ($sessionsToRevoke as $session) {
                if ($session->session_id) {
                    $trustedDeviceService->markDeviceRequires2FA($userId, $session->session_id);
                }
            }

            // Get session IDs for deletion
            $sessionIds = $sessionsToRevoke->pluck('session_id')->toArray();

            // Delete from Laravel's sessions table to invalidate all other sessions
            if (!empty($sessionIds)) {
                DB::table('sessions')
                    ->whereIn('id', $sessionIds)
                    ->delete();
            }

            // Delete all user_sessions except current
            DB::table('user_sessions')
                ->where('user_id', $userId)
                ->where('session_id', '!=', $currentSessionId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'All other sessions have been revoked. Those devices will require 2FA on next login.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke sessions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateNotifications(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Update or create user_preferences record
            $updateData = [
                'updated_at' => now()
            ];

            // Transaction alerts
            if ($request->has('transaction_alerts')) {
                $updateData['transaction_alerts'] = $request->transaction_alerts ? 1 : 0;
            }

            // Budget alerts
            if ($request->has('budget_alerts')) {
                $updateData['budget_alerts'] = $request->budget_alerts ? 1 : 0;
            }

            // Push notifications
            if ($request->has('push_notifications')) {
                $updateData['push_notifications'] = $request->push_notifications ? 1 : 0;
            }

            // Large transaction alerts
            if ($request->has('large_transaction_alerts')) {
                $updateData['large_transaction_alerts'] = $request->large_transaction_alerts ? 1 : 0;
            }

            // Large transaction threshold
            if ($request->has('large_transaction_threshold')) {
                $updateData['large_transaction_threshold'] = floatval($request->large_transaction_threshold);
            }

            // Weekly summary
            if ($request->has('weekly_summary')) {
                $updateData['weekly_summary'] = $request->weekly_summary ? 1 : 0;
            }

            // Goal progress
            if ($request->has('goal_progress')) {
                $updateData['goal_progress'] = $request->goal_progress ? 1 : 0;
            }

            // Group expense
            if ($request->has('group_expense')) {
                $updateData['group_expense'] = $request->group_expense ? 1 : 0;
            }

            // Login alerts
            if ($request->has('login_alerts')) {
                $updateData['login_alerts'] = $request->login_alerts ? 1 : 0;
            }

            // New device alerts
            if ($request->has('new_device_alerts')) {
                $updateData['new_device_alerts'] = $request->new_device_alerts ? 1 : 0;
            }

            DB::table('user_preferences')->updateOrInsert(
                ['user_id' => $userId],
                $updateData
            );

            // Clear cached user preferences to pick up new values
            cache()->forget("user_preferences:{$userId}");

            return response()->json([
                'success' => true,
                'message' => 'Notification preferences updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|string', // Base64 image data
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get current user
            $user = DB::table('users')->where('id', $userId)->first();

            // Delete old profile picture if exists
            if ($user && $user->profile_picture) {
                $oldPath = public_path($user->profile_picture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Decode base64 image
            $imageData = $request->profile_picture;

            // Remove data URI scheme if present
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image format'
                ], 422);
            }

            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to decode image'
                ], 422);
            }

            // Create uploads directory if not exists
            $uploadDir = public_path('uploads/profile-pictures');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate hashed filename for security and uniqueness
            $hash = md5($userId . time() . uniqid('', true));
            $filename = 'profile_' . $hash . '.' . $type;
            $filepath = $uploadDir . '/' . $filename;

            // Save image
            file_put_contents($filepath, $imageData);

            // Update database with relative path
            $relativePath = '/uploads/profile-pictures/' . $filename;
            DB::table('users')
                ->where('id', $userId)
                ->update(['profile_picture' => $relativePath]);

            // Update session
            session(['user_profile_picture' => $relativePath]);

            return response()->json([
                'success' => true,
                'message' => 'Profile picture uploaded successfully',
                'data' => [
                    'profile_picture' => $relativePath
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload profile picture: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProfilePicture(Request $request)
    {
        // Same as upload - handles both upload and update
        return $this->uploadProfilePicture($request);
    }

    public function deleteProfilePicture(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get current user
            $user = DB::table('users')->where('id', $userId)->first();

            // Delete profile picture file if exists
            if ($user && $user->profile_picture) {
                $filepath = public_path($user->profile_picture);
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }

            // Update database
            DB::table('users')
                ->where('id', $userId)
                ->update(['profile_picture' => null]);

            // Update session
            session()->forget('user_profile_picture');

            return response()->json([
                'success' => true,
                'message' => 'Profile picture removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove profile picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user bio
     */
    public function updateBio(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Validate bio
            $request->validate([
                'bio' => 'nullable|string|max:500'
            ]);

            // Update database
            DB::table('users')
                ->where('id', $userId)
                ->update(['bio' => $request->input('bio')]);

            return response()->json([
                'success' => true,
                'message' => 'Bio updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bio: ' . $e->getMessage()
            ], 500);
        }
    }


    public function deleteAccount(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = \App\Models\User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Delete profile picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Delete all user data
            DB::beginTransaction();

            try {
                // Delete related data
                \App\Models\Transaction::where('user_id', $userId)->delete();
                \App\Models\Category::where('user_id', $userId)->delete();
                \App\Models\Budget::where('user_id', $userId)->delete();
                \App\Models\Goal::where('user_id', $userId)->delete();
                \App\Models\Report::where('user_id', $userId)->delete();
                \App\Models\SyncLog::where('user_id', $userId)->delete();
                \App\Models\AuditLog::where('user_id', $userId)->delete();
                \App\Models\UserPreferences::where('user_id', $userId)->delete();

                // Delete the user
                $user->delete();

                DB::commit();

                // Clear session
                session()->flush();

                return response()->json([
                    'success' => true,
                    'message' => 'Account deleted successfully',
                    'redirect' => route('login')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account: ' . $e->getMessage()
            ], 500);
        }
    }
}
