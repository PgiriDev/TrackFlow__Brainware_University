<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index');
    }

    public function list(Request $request)
    {
        $userId = session('user_id');
        \Log::info('CategoryController list() called', ['user_id' => $userId]);

        if (!$userId) {
            \Log::warning('No user_id in session');
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Get both user's custom categories AND system categories (where user_id is null)
        $categories = Category::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        })
            ->orderBy('is_system', 'desc') // System categories first
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        \Log::info('Categories found', ['count' => $categories->count()]);

        $categories = $categories->map(function ($category) {
            // Map type back to credit/debit for frontend
            $typeMapping = [
                'income' => 'credit',
                'expense' => 'debit'
            ];

            $category->display_type = $typeMapping[$category->type] ?? $category->type;
            return $category;
        });

        \Log::info('Returning categories response', ['count' => $categories->count()]);
        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function createAjax(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:credit,debit',
            'color' => 'required|string',
            'icon' => 'nullable|string',
            'description' => 'nullable|string|max:500',
        ]);

        // Map type from credit/debit to income/expense
        $typeMapping = [
            'credit' => 'income',
            'debit' => 'expense'
        ];

        $category = Category::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'type' => $typeMapping[$validated['type']],
            'color' => $validated['color'],
            'icon' => $validated['icon'] ?? 'fa-tag',
            'is_system' => false,
        ]);

        return response()->json(['success' => true, 'data' => $category], 201);
    }

    public function deleteAjax(Request $request, $id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $category = Category::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $category->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        // Handled via API
        return redirect()->route('categories.index')
            ->with('success', 'Created successfully');
    }

    public function edit($id)
    {
        return view('categories.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Handled via API
        return redirect()->route('categories.index')
            ->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        // Handled via API
        return redirect()->route('categories.index')
            ->with('success', 'Deleted successfully');
    }
}