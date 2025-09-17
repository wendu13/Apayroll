@extends('layouts.auth-layout')

@section('title', 'Login')

@section('content')
    <h4 class="text-center fw-bold mb-3">Login</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    
    <div class="form-wrapper">
        <form action="/login" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email"
                autocomplete="new-email" readonly onfocus="this.removeAttribute('readonly');" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password"
                autocomplete="new-password" required>
            </div>

            <div class="mb-4 form-check custom-check">
                <input class="form-check-input" type="checkbox" id="showPassword">
                <label class="form-check-label" for="showPassword">Show password</label>
            </div>

            <div style="display: flex; justify-content: center;">
                <button type="submit" class="btn btn-primary custom-btn">Login</button>
            </div>

            <div class="d-flex justify-content-between mt-2">
                <a href="/forgot-password" class="login-link">Forgot Password?</a>
                <a href="{{ route('register') }}" class="login-link">Register here</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('showPassword').addEventListener('change', function () {
        const passwordInput = document.querySelector('input[name="password"]');
        passwordInput.type = this.checked ? 'text' : 'password';
    });

    window.addEventListener('load', function () {
        document.querySelectorAll('input[type="text"], input[type="email"]').forEach(input => {
            input.value = '';
        });
    });
</script>
@endpush
