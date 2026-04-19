<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserUpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpiController extends Controller
{
    /**
     * Get all UPIs for the authenticated user.
     */
    public function index(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $upis = UserUpi::where('user_id', $userId)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($upi) {
                return [
                    'id' => $upi->id,
                    'name' => $upi->name,
                    'upi_id' => $upi->upi_id,
                    'qr_code_url' => $upi->qr_code_url,
                    'is_primary' => $upi->is_primary,
                    'is_active' => $upi->is_active,
                    'created_at' => $upi->created_at->format('M d, Y'),
                ];
            });

        return response()->json(['success' => true, 'upis' => $upis]);
    }

    /**
     * Store a new UPI.
     */
    public function store(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'upi_id' => 'required|string|max:255|regex:/^[\w.-]+@[\w]+$/',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_primary' => 'nullable|boolean',
        ], [
            'upi_id.regex' => 'Please enter a valid UPI ID (e.g., yourname@paytm)',
        ]);

        // Handle QR code upload
        $qrCodePath = null;
        if ($request->hasFile('qr_code')) {
            $file = $request->file('qr_code');
            $filename = 'upi_' . $userId . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $qrCodePath = $file->storeAs('upi-qrcodes', $filename, 'public');
        }

        // If setting as primary, unset other primaries first
        if ($request->boolean('is_primary')) {
            UserUpi::where('user_id', $userId)->update(['is_primary' => false]);
        }

        // If this is the first UPI, make it primary
        $isFirstUpi = UserUpi::where('user_id', $userId)->count() === 0;

        $upi = UserUpi::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'upi_id' => $validated['upi_id'],
            'qr_code_path' => $qrCodePath,
            'is_primary' => $request->boolean('is_primary') || $isFirstUpi,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'UPI added successfully',
            'data' => [
                'id' => $upi->id,
                'name' => $upi->name,
                'upi_id' => $upi->upi_id,
                'qr_code_url' => $upi->qr_code_url,
                'is_primary' => $upi->is_primary,
                'is_active' => $upi->is_active,
            ]
        ], 201);
    }

    /**
     * Update a UPI.
     */
    public function update(Request $request, $id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $upi = UserUpi::where('user_id', $userId)->where('id', $id)->first();

        if (!$upi) {
            return response()->json(['success' => false, 'message' => 'UPI not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'upi_id' => 'sometimes|string|max:255|regex:/^[\w.-]+@[\w]+$/',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_primary' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'upi_id.regex' => 'Please enter a valid UPI ID (e.g., yourname@paytm)',
        ]);

        // Handle QR code upload
        if ($request->hasFile('qr_code')) {
            // Delete old QR code
            if ($upi->qr_code_path) {
                Storage::disk('public')->delete($upi->qr_code_path);
            }

            $file = $request->file('qr_code');
            $filename = 'upi_' . $userId . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $validated['qr_code_path'] = $file->storeAs('upi-qrcodes', $filename, 'public');
        }

        // If setting as primary, unset other primaries first
        if ($request->boolean('is_primary') && !$upi->is_primary) {
            UserUpi::where('user_id', $userId)->where('id', '!=', $id)->update(['is_primary' => false]);
            $validated['is_primary'] = true;
        }

        $upi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'UPI updated successfully',
            'data' => [
                'id' => $upi->id,
                'name' => $upi->name,
                'upi_id' => $upi->upi_id,
                'qr_code_url' => $upi->qr_code_url,
                'is_primary' => $upi->is_primary,
                'is_active' => $upi->is_active,
            ]
        ]);
    }

    /**
     * Delete a UPI.
     */
    public function destroy($id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $upi = UserUpi::where('user_id', $userId)->where('id', $id)->first();

        if (!$upi) {
            return response()->json(['success' => false, 'message' => 'UPI not found'], 404);
        }

        // Delete QR code file
        if ($upi->qr_code_path) {
            Storage::disk('public')->delete($upi->qr_code_path);
        }

        $wasPrimary = $upi->is_primary;
        $upi->delete();

        // If deleted was primary, make another one primary
        if ($wasPrimary) {
            $nextUpi = UserUpi::where('user_id', $userId)->first();
            if ($nextUpi) {
                $nextUpi->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'UPI deleted successfully'
        ]);
    }

    /**
     * Set a UPI as primary.
     */
    public function setPrimary($id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $upi = UserUpi::where('user_id', $userId)->where('id', $id)->first();

        if (!$upi) {
            return response()->json(['success' => false, 'message' => 'UPI not found'], 404);
        }

        // Unset all primaries
        UserUpi::where('user_id', $userId)->update(['is_primary' => false]);

        // Set this as primary
        $upi->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'UPI set as primary'
        ]);
    }

    /**
     * Delete QR code only.
     */
    public function deleteQrCode($id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $upi = UserUpi::where('user_id', $userId)->where('id', $id)->first();

        if (!$upi) {
            return response()->json(['success' => false, 'message' => 'UPI not found'], 404);
        }

        if ($upi->qr_code_path) {
            Storage::disk('public')->delete($upi->qr_code_path);
            $upi->update(['qr_code_path' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'QR code deleted successfully'
        ]);
    }
}
