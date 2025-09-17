<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Technopark Hotel Auth')</title>
    <!-- Blade Layout (e.g., app.blade.php or login.blade.php) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your CSS must come AFTER Bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    @stack('styles')
</head>
<body>

    <!-- Fixed Logo Area -->
    <div class="logo-wrapper">
        <div class="left-logo">
            <img src="{{ asset('images/technologo.png') }}" alt="Technopark Logo">
        </div>
        <div class="right-logos">
            <img src="{{ asset('images/rw.png') }}" alt="RW Logo">
            <img src="{{ asset('images/cvsu.png') }}" alt="CvSU Logo">
        </div>
    </div>

    <!-- Main Auth Section -->
    <div class="auth-page">
        <div class="auth-box">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        RW RealWorks • CvSU–Silang Campus • © 2025
    </div>

    @stack('scripts')
</body>
</html>
