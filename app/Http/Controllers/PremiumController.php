<?php
// app/Http/Controllers/PremiumController.php

namespace App\Http\Controllers;

use App\Models\PremiumCategory;
use App\Models\PremiumType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PremiumController extends Controller
{
    public function index()
    {
        $categories = PremiumCategory::active()
            ->ordered()
            ->with(['premiumTypes' => function($query) {
                $query->active()->ordered();
            }])
            ->get();

        return view('hr.premium.index', compact('categories'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'premium_types' => 'required|array',
            'premium_types.*.id' => 'required|exists:premium_types,id',
            'premium_types.*.regular_rate' => 'required|numeric|min:0|max:999.99',
            'premium_types.*.special_rate' => 'nullable|numeric|min:0|max:999.99',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->premium_types as $typeData) {
                $premiumType = PremiumType::findOrFail($typeData['id']);
                $premiumType->update([
                    'regular_rate' => $typeData['regular_rate'],
                    'special_rate' => $typeData['special_rate'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Premium rates updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating premium rates: ' . $e->getMessage());
        }
    }

    public function updateCategory(Request $request, $categoryId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $category = PremiumCategory::findOrFail($categoryId);
            $category->update($request->only(['name', 'description']));

            return response()->json(['success' => true, 'message' => 'Category updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating category: ' . $e->getMessage()]);
        }
    }

    public function toggleActive(Request $request, $id)
    {
        try {
            $premiumType = PremiumType::findOrFail($id);
            $premiumType->update(['is_active' => !$premiumType->is_active]);

            return response()->json([
                'success' => true, 
                'message' => 'Premium type status updated successfully!',
                'is_active' => $premiumType->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
        }
    }
}