<?php

namespace App\Http\Controllers;

use App\Models\FaceAuthentication;
use App\Services\FaceCryptoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaceLoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'face_vector' => 'required|array|min:100'
        ]);
        $inputVector = $request->face_vector;

        foreach (FaceAuthentication::whereNull('revoked_at')->get() as $face) {
            $stored = FaceCryptoService::decryptVector($face->face_vector);
            if ($this->cosineSimilarity($inputVector, $stored) >= 0.90) {
                Auth::loginUsingId($face->user_id);
                session()->save();
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false], 401);
    }

    private function cosineSimilarity($a, $b)
    {
        $dot = $normA = $normB = 0;
        for ($i = 0; $i < count($a); $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] ** 2;
            $normB += $b[$i] ** 2;
        }
        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
