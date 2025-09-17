<?php

namespace App\Http\Controllers;

use App\Models\TaxBracket;
use App\Models\DeductionSetting;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxBrackets = TaxBracket::orderBy('from')->get();
        return response()->json($taxBrackets);
    }

    public function update(Request $request)
    {
        $brackets = $request->input('brackets', []);
    
        try {
            // Clear old tax brackets
            TaxBracket::truncate();
    
            // Create new brackets
            foreach ($brackets as $b) {
                TaxBracket::create([
                    'from' => floatval($b['from']),
                    'to' => floatval($b['to']),
                    'percentage' => floatval($b['percentage']),
                    'fixed_amount' => floatval($b['fixed_amount']),
                ]);
            }
    
            // NO MORE deduction_settings interaction
    
            return redirect()
                ->route('deductions.index')
                ->with('success', 'Tax brackets updated successfully!');
    
        } catch (\Exception $e) {
            \Log::error('Error updating tax brackets: ' . $e->getMessage());
    
            return redirect()
                ->back()
                ->with('error', 'Failed to update tax brackets')
                ->withInput();
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|numeric|min:0',
            'to' => 'required|numeric|min:0|gte:from',
            'percentage' => 'required|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
        ]);

        TaxBracket::create($validated);

        return redirect()
            ->route('deductions.index')
            ->with('success', 'Tax bracket created successfully');
    }

    public function destroy($id)
    {
        try {
            $taxBracket = TaxBracket::findOrFail($id);
            $taxBracket->delete();

            return response()->json(['success' => true, 'message' => 'Tax bracket deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Error deleting tax bracket: ' . $e->getMessage());
            
            return response()->json(['error' => 'Failed to delete tax bracket'], 500);
        }
    }
}