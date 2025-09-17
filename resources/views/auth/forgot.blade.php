@extends('layouts.auth-layout')

@section('title', 'Forgot Password')

@section('content')
    <h4 class="text-center fw-bold mb-3">Forgot Password</h4>

    @if(session('success'))
        <div class="alert alert-success p-2 text-center">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger p-2">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li style="font-size: 13px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/forgot-password" method="POST">
        @csrf

        {{-- Row 1 --}}
        <div class="row mb-2">
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm" placeholder="Email" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Employee Number</label>
                <input type="text"  name="employee_number" value="{{ old('employee_number') }}" class="form-control form-control-sm" placeholder="Employee Number" required>
            </div>
        </div>

        {{-- Row 2 --}}
        <div class="row mb-2">
        <div class="col-md-7">
                <label class="form-label">Security Question</label>
                <select name="security_question" class="form-control form-control-sm" required>
                    <option value="">-- Select a question --</option>
                    <option value="What is your favorite color?" {{ old('security_question') == 'What is your favorite color?' ? 'selected' : '' }}>What is your favorite color?</option>
                    <option value="What is your pet’s name?" {{ old('security_question') == 'What is your pet’s name?' ? 'selected' : '' }}>What is your pet’s name?</option>
                    <option value="What city were you born in?" {{ old('security_question') == 'What city were you born in?' ? 'selected' : '' }}>What city were you born in?</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Answer</label>
                <input type="text" name="security_answer" class="form-control form-control-sm" value="{{ old('security_answer') }}" placeholder="Answer" required>
            </div>

        </div>

        {{-- Row 3 --}}
        <div class="row mb-2">
        <div class="col-md-6">
                <label class="form-label">New Password</label>
                <input type="password" name="password" id="password" class="form-control form-control-sm" placeholder="New Password" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="confirm_password" class="form-control form-control-sm" placeholder="Confirm Password" required>
            </div>
        </div>

        {{-- Show password --}}
        <div class="mb-2 form-check custom-check">
            <input class="form-check-input" type="checkbox" id="showPassword" onclick="togglePassword()">
            <label class="form-check-label" for="showPassword">Show password</label>
        </div>

        {{-- Back + Submit --}}
        <div class="d-flex justify-content-between align-items-center mt-2">
            <a href="{{ route('login') }}" class="login-link">Back to Login</a>
            <button type="submit" class="btn btn-primary custom-btn">Reset</button>
        </div>
    </form>

    <script>
        function togglePassword() {
            const pw1 = document.getElementById("password");
            const pw2 = document.getElementById("confirm_password");
            const type = pw1.type === "password" ? "text" : "password";
            pw1.type = type;
            if (pw2) pw2.type = type;
        }
    </script>
@endsection
