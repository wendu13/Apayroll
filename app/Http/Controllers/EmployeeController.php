<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Department;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('contact', 'like', "%{$request->search}%")
                  ->orWhere('department', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $employees   = $query->orderBy('last_name')->paginate(20);
        $departments = Employee::select('department')->distinct()->pluck('department');

        return view('hr.employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Employee::select('department')->distinct()->pluck('department');
        return view('hr.employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'employee_number' => 'required|unique:employees',
            'department'      => 'required|string',
            'photo'           => 'nullable|image|max:2048',
        ]);

        $employee = new Employee($request->except('photo'));

        if ($request->hasFile('photo')) {
            $employee->photo = $request->file('photo')->store('photos', 'public');
        }

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['schedules.cutoff']);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Employee::select('department')->distinct()->pluck('department');
        return view('hr.employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'photo'      => 'nullable|image|max:2048',
    
            'basic_rate' => 'nullable|numeric|min:0',
            'rate_type'  => 'required|in:monthly,daily',
            'allowance'  => 'nullable|numeric|min:0',
            'other_pay'  => 'nullable|numeric|min:0',
        ]);
    
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }
    
        $employee->update($validated);
    
        return redirect()->route('employees.show', $employee->id)
                         ->with('success', 'Employee updated successfully');
    }    

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('hr.employees.index')->with('success', 'Employee deleted successfully.');
    }

}
