@extends('layouts.auth-layout')

@section('title', 'Registration')

@section('content')
    <h4 class="text-center fw-bold mb-3">Registration</h4>

    @if(session('success'))
        <div id="successBox" class="position-fixed top-50 start-50 translate-middle bg-light border border-success rounded shadow-sm p-3 text-center" style="z-index:1050; width: 280px;">
            <p class="text-success mb-2">{{ session('success') }}</p>
            <button class="btn btn-sm btn-success" onclick="document.getElementById('successBox').remove()">OK</button>
        </div>
    @endif

    <form action="{{ route('register.store') }}" method="POST">
        @csrf

        {{-- Row 1 --}}
        <div class="row mb-2">
            <div class="col-md-4">
                <label class="form-label">Employee Number</label>
                <input type="text" class="form-control form-control-sm" name="employee_number" placeholder="Employee Number" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Department</label>
                <input type="text" class="form-control form-control-sm" name="department" placeholder="Department" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Position</label>
                <input type="text" class="form-control form-control-sm" name="position" placeholder="Position" required>
            </div>
        </div>

        {{-- Row 2 --}}
        <div class="row mb-2">
            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" class="form-control form-control-sm" name="email" placeholder="Email" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control form-control-sm" name="first_name" placeholder="First Name" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control form-control-sm" name="last_name" placeholder="Last Name" required>
            </div>
        </div>

        {{-- Row 3 --}}
        <div class="row mb-2">
            <div class="col-md-6">
                <label class="form-label">Security Question</label>
                <select name="security_question" class="form-control form-control-sm" required>
                    <option value="">-- Select a question --</option>
                    <option value="What is your favorite color?">What is your favorite color?</option>
                    <option value="What is your pet’s name?">What is your pet’s name?</option>
                    <option value="What city were you born in?">What city were you born in?</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Answer</label>
                <input type="text" class="form-control form-control-sm" name="security_answer" placeholder="Answer" required>
            </div>
        </div>

        {{-- Row 4 --}}
        <div class="row mb-2">
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" id="password" class="form-control form-control-sm" name="password" placeholder="New Password" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" class="form-control form-control-sm" name="password_confirmation" placeholder="Confirm Password" required>
            </div>
        </div>

        {{-- Show password checkbox --}}
        <div class="mb-2 form-check custom-check">
            <input class="form-check-input" type="checkbox" id="showPassword">
            <label class="form-check-label" for="showPassword">Show password</label>
        </div>

        {{-- Back to Login --}}
        <div class="d-flex justify-content-between align-items-center mt-2">
            <a href="{{ route('login') }}" class="login-link">Back to Login</a>
            <button type="submit" class="btn btn-primary custom-btn">Register</button>
        </div>
    </form>

    <script>
        document.getElementById('showPassword').addEventListener('change', function () {
            const type = this.checked ? 'text' : 'password';
            document.getElementById('password').type = type;
            document.getElementById('confirm_password').type = type;
        });
    </script>
@endsection
