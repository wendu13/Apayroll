{{-- resources/views/layouts/hr.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="page-body">
        @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif
        @yield('hr-content')
    </div>
@endsection
