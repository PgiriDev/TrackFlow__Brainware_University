<?php

namespace App\Http\Controllers;

use App\Models\FaceAuthentication;
use App\Services\FaceHashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FaceEnrollController extends Controller
{
    public function enroll(Request $request)
    {
        $request->validate([
            'face_vector' => 'required|array|min:64'
        ]);

        try {
            $hash = FaceHashService::hash($request->face_vector);

            \Log::info('Face enroll - request', [
                'user_id' => auth()->id(),
                'vector_length' => is_array($request->face_vector) ? count($request->face_vector) : 0,
            ]);

            $faceId = null;

            DB::transaction(function () use ($hash, &$faceId) {
                FaceAuthentication::where('user_id', auth()->id())
                    ->whereNull('revoked_at')
                    ->update(['revoked_at' => now()]);

                $face = FaceAuthentication::create([
                    'user_id' => auth()->id(),
                    'face_hash' => $hash,
                    // Ensure we explicitly set null for the old column so DB won't reject the insert
                    'face_vector' => null,
                ]);

                $faceId = $face->id;

                // Use direct DB update to avoid session invalidation
                \App\Models\User::where('id', auth()->id())->update([
                    'face_registered' => true
                ]);
            });

            \Log::info('Face enroll - success', ['user_id' => auth()->id(), 'face_id' => $faceId, 'hash' => $hash]);

            return response()->json(['success' => true, 'face_id' => $faceId]);

        } catch (\Throwable $e) {
            \Log::error('Face enroll failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request' => ['len' => is_array($request->face_vector) ? count($request->face_vector) : 0]
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Face enroll failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete (revoke) the current user's face authentications.
     */
    public function destroy(Request $request)
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            DB::transaction(function () use ($userId) {
                // Soft-revoke existing face authentications
                \App\Models\FaceAuthentication::where('user_id', $userId)
                    ->whereNull('revoked_at')
                    ->update(['revoked_at' => now()]);

                // Mark user as not registered
                \App\Models\User::where('id', $userId)->update(['face_registered' => false]);
            });

            \Log::info('Face deleted', ['user_id' => auth()->id()]);

            return response()->json(['success' => true, 'message' => 'Face deleted']);
        } catch (\Throwable $e) {
            \Log::error('Face delete failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete face: ' . $e->getMessage()], 500);
        }
    }
}
