{{-- resources/views/layouts/hr.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="page-body">
        @if (session('success'))
            <div id="flash-message" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div id="flash-message" class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @yield('hr-content')
    </div>

    <script>
        // Auto-dismiss flash messages after 3 seconds
        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if (flash) {
                flash.classList.remove('show');
                flash.classList.add('fade');
                setTimeout(() => flash.remove(), 500); // wait for fade animation
            }
        }, 3000);
    </script>
@endsection

