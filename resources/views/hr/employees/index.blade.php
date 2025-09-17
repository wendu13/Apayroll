@extends('layouts.hr')

{{-- Fixed header title --}}
@section('page-title', 'Employee Management')

@section('hr-content')
<div class="container">

    {{-- Filters + Buttons --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('employees.index') }}" id="filterForm" class="d-flex align-items-center gap-2">
            {{-- Department Dropdown --}}
            <select class="form-select form-select-sm" name="department" style="width: 200px;" onchange="this.form.submit()">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                        {{ $department }}
                    </option>
                @endforeach
            </select>

            {{-- Search Input --}}
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Search..." 
                   class="form-control form-control-sm" 
                   style="width: 230px;" 
                   onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
        </form>

        {{-- Buttons --}}
        <div class="d-flex gap-2">
            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">Add Employee</a>
        </div>
    </div>

    {{-- Department Title --}}
    <h5>
        {{ isset($selectedDepartment) ? $selectedDepartment->name : 'All Employees' }}
    </h5>

    {{-- Employee Table --}}
    <div class="table-container">
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee Number</th>
                    <th>Name</th>
                    <th>Bank Account</th>
                    <th>Contact #</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $index => $employee)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $employee->employee_number }}</td>
                        <td>{{ $employee->last_name }}, {{ $employee->first_name }}</td>
                        <td>{{ $employee->bank_account }}</td>
                        <td>{{ $employee->contact }}</td>
                        <td>
                            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-info">View</a>

                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No employees found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
