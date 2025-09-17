<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use Illuminate\Http\Request;

class EmployeeDeductionController extends Controller
{
    public function index(Employee $employee)
    {
        $deductions = $employee->deductions()
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.employees.partials.deduction', compact('employee', 'deductions'));
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'deduction_type' => 'required|string',
            'custom_type'    => 'nullable|string',
            'amount'         => 'required|numeric|min:0.01', // monthly
            'term'           => 'required|integer|min:1|max:120',
            'cut_off'        => 'required|string|in:1st_half,2nd_half',
            'start_date'     => 'required|date',
            'notes'          => 'nullable|string',
        ]);
    
        $deduction = new EmployeeDeduction();
        $deduction->employee_id = $employee->id;
        $deduction->deduction_type = $validated['deduction_type'];
        $deduction->custom_type = $validated['deduction_type'] === 'other' ? $validated['custom_type'] : null;
        $deduction->amount = $validated['amount']; // monthly
        $deduction->term = $validated['term'];
        $deduction->cut_off = $validated['cut_off'];
        $deduction->start_date = $validated['start_date'];
        $deduction->notes = $validated['notes'] ?? null;
    
        // Computed
        $deduction->remaining_balance = $validated['amount'] * $validated['term'];
        $deduction->payments_made = 0;
        $deduction->is_active = true;
    
        $deduction->save();
    
        return redirect()->back()->with('success', 'Deduction added successfully.');
    }
    
    public function show(Employee $employee, EmployeeDeduction $deduction)
    {
        // Verify the deduction belongs to this employee
        if ($deduction->employee_id !== $employee->id) {
            abort(404);
        }
    
        return response()->json([
            'id' => $deduction->id,
            'type' => $deduction->formatted_deduction_type,
            'amount' => $deduction->amount,
            'term' => $deduction->term,
            'per_payment_amount' => $deduction->amount, // monthly amount
            'start_date' => $deduction->start_date->format('M d, Y'),
            'notes' => $deduction->notes,
            'file_path' => $deduction->file_path ?? null,
            'is_active' => $deduction->is_active,
        ]);
    }
    
    public function edit(Employee $employee, EmployeeDeduction $deduction)
    {
        return view('hr.employees.deductions.edit', compact('employee', 'deduction'));
    }

    public function update(Request $request, Employee $employee, EmployeeDeduction $deduction)
    {
        $validated = $request->validate([
            'deduction_type' => 'required|in:company_loan,cash_advance,sss_loan,hdmf_loan,other',
            'custom_type' => 'required_if:deduction_type,other|nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'term' => 'required|integer|min:1|max:120',
            'cut_off' => 'required|in:1st_half,2nd_half',
            'start_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean'
        ]);

        // Recalculate remaining balance if amount or term changed
        if ($deduction->amount != $validated['amount'] || $deduction->term != $validated['term']) {
            $perPayment = $validated['amount'] / $validated['term'];
            $validated['remaining_balance'] = max(0, $validated['amount'] - ($perPayment * $deduction->payments_made));
        }

        $deduction->update($validated);

        return redirect()
            ->route('employees.deductions.index', $employee)
            ->with('success', 'Employee deduction updated successfully.');
    }

    public function destroy(Employee $employee, EmployeeDeduction $deduction)
    {
        $deduction->delete();

        return redirect()
            ->route('employees.deductions.index', $employee)
            ->with('success', 'Employee deduction deleted successfully.');
    }

    // Toggle active status
    public function toggleStatus(Employee $employee, EmployeeDeduction $deduction)
    {
        $deduction->update(['is_active' => !$deduction->is_active]);

        $status = $deduction->is_active ? 'activated' : 'deactivated';
        
        return redirect()
            ->route('employees.deductions.index', $employee)
            ->with('success', "Deduction {$status} successfully.");
    }

    // Process single payment
    public function processPayment(Employee $employee, EmployeeDeduction $deduction)
    {
        if ($deduction->payments_made >= $deduction->term) {
            return redirect()
                ->route('employees.deductions.index', $employee)
                ->with('error', 'This deduction is already completed.');
        }

        $deduction->processPayment();

        return redirect()
            ->route('employees.deductions.index', $employee)
            ->with('success', 'Payment processed successfully.');
    }
}