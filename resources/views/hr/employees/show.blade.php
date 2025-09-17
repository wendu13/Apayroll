@extends('layouts.hr')

{{-- Fixed header title --}}
@section('page-title', 'Human Resource Department')

@section('hr-content')
<div class="container">

    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary mb-3">← Back</a>
    <p>{{ $employee->employee_number }} | {{ $employee->last_name }}, {{ $employee->first_name }}</p>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="employeeTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#personal">Personal Info</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#schedule">Schedules</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#deductions">Deductions</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#payslip">Payslips</a>
        </li>
    </ul>

    <div class="tab-content" id="employeeTabContent">
        {{-- Personal Info --}}
        @include('hr.employees.partials.personal-info')

        {{-- Schedule --}}
        @include('hr.employees.partials.schedule')

        {{--Deduction --}}
        @include('hr.employees.partials.deduction')

        {{-- Payslip --}}
        @include('hr.employees.partials.payslip')
    </div>

</div>
@endsection
