<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>HR Dashboard</title>
    
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/fixed-nav.css') }}">
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    
    <script src="https://unpkg.com/lucide@latest"></script>
    
</head>
<body class="d-flex flex-column" style="height: 100vh; margin: 0;">
    <!-- Logo Header -->
    <div class="background-wrapper" style="height: 60px;">
        <div class="left-logo">
            <img src="{{ asset('images/technologo.png') }}" alt="Technopark Logo">
        </div>
        <div class="right-logos">
            <img src="{{ asset('images/rw.png') }}" alt="RW Logo">
            <img src="{{ asset('images/cvsu.png') }}" alt="CvSU Logo">
        </div>
    </div>

    <!-- Top Navigation Bar -->
    <div class="topbar">
        <div class="logo-title">
            <h3>HR Department</h3>
        </div>
        <div class="top-right d-flex align-items-center gap-3">
            <span class="bell-icon">🔔</span>

            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: none; border: none; color: inherit;">
                    <span class="user-icon">👤</span>
                    <span class="username">{{ auth('hr')->user()->first_name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

<!-- Sidebar + Main Content -->
<div class="layout-container">
    <div id="sidebar" class="sidebar">
        @php
            $currentRoute = Route::currentRouteName();
            $pageTitle = match($currentRoute) {
                'dashboard' => 'Dashboard',
                'employee' => 'Employee',
                default => 'HR Portal'
            };
        @endphp

        <div class="sidebar-header">
            <h4>{{ $pageTitle }}</h4>
            <span id="sidebarToggle" class="toggle-icon">☰</span>
            </div>

            <ul class="sidebar-nav">
                <li class="{{ $currentRoute == 'hr.dashboard' ? 'active' : '' }}">
                    <a href="{{ route('hr.dashboard') }}">
                        <i data-lucide="layout-dashboard"></i><span>Dashboard</span>
                    </a>
                </li>

                <li class="{{ $currentRoute == 'employees.index' ? 'active' : '' }}">
                    <a href="{{ route('employees.index') }}">
                        <i data-lucide="users"></i><span>Employee</span>
                    </a>
                </li>

                <li class="{{ $currentRoute == 'schedule.index' ? 'active' : '' }}">
                    <a href="{{ route('schedule.index') }}">
                        <i data-lucide="calendar-clock"></i>
                        <span>Schedule</span>
                    </a>
                </li>
                
                <li class="{{ $currentRoute == 'deductions.index' ? 'active' : '' }}">
                    <a href="{{ route('deductions.index') }}">
                        <i data-lucide="percent"></i><span>Deduction</span>
                    </a>
                </li>

                <li class="{{ $currentRoute == 'premium.index' ? 'active' : '' }}">
                    <a href="{{ route('premium.index') }}">
                        <i data-lucide="dollar-sign"></i><span>Premium Setup</span>
                    </a>
                </li>

                <li><a href="#"><i data-lucide="file-text"></i><span>Payroll</span></a></li>
                <li><a href="#"><i data-lucide="wallet"></i><span>Pay Slip</span></a></li>
                
                <li class="{{ $currentRoute == 'calendar.index' ? 'active' : '' }}">
                    <a href="{{ route('calendar.index') }}">
                        <i data-lucide="calendar"></i><span>Calendar</span>
                    </a>
                </li>

                <li><a href="#"><i data-lucide="file-warning"></i><span>Report</span></a></li>
                <li><a href="#"><i data-lucide="archive"></i><span>Archive</span></a></li>
            </ul>
            
        </div>

        <ul class="sidebar-nav">
            <li class="{{ $currentRoute == 'hr.dashboard' ? 'active' : '' }}">
                <a href="{{ route('hr.dashboard') }}">
                    <i data-lucide="layout-dashboard"></i><span>Dashboard</span>
                </a>
            </li>

            <li class="{{ $currentRoute == 'employees.index' ? 'active' : '' }}">
                <a href="{{ route('employees.index') }}">
                    <i data-lucide="users"></i><span>Employee</span>
                </a>
            </li>
            <li>
            <a href="{{ route('schedule.index') }}">
                <i data-lucide="calendar-clock"></i>
                <span>Schedule</span>
            </a>
            </li>
            <li class="{{ $currentRoute == 'deductions.index' ? 'active' : '' }}">
                <a href="{{ route('deductions.index') }}">
                    <i data-lucide="percent"></i><span>Deduction</span>
                </a>
            </li>
            <li> <i data-lucide="badge-percent"></i><span>Premium</span></li>
            <li><i data-lucide="file-text"></i><span>Payroll</span></li>
            <li><i data-lucide="wallet"></i><span>Pay Slip</span></li>
            
            <li class="{{ $currentRoute == 'calendar.index' ? 'active' : '' }}">
                <a href="{{ route('calendar.index') }}">
                    <i data-lucide="users"></i><span>Calendar</span>
                </a>
            </li>

            <li><i data-lucide="file-warning"></i><span>Report</span></li>
            <li><i data-lucide="archive"></i><span>Archive</span></li>
        </ul>
        </div>

        <div class="main-content">
        <!-- Fixed Page Header -->
        <div class="page-header">
            <h4>@yield('page-title')</h4>
        </div>

        <!-- Scrollable Page Body -->
        <div class="page-body">
            @yield('content')
        </div>
    </div>
    </div>

    <!-- Footer -->
    <footer class="text-white text-center mt-auto" style="background-color: #032260; height: 20px; font-size: 9px; line-height: 20px;">
        <p>RW RealWorks • CvSU–Silang Campus • &copy; 2025</p>
    </div>

    <script>
        const sidebar = document.querySelector('.sidebar');
        const toggleIcon = document.getElementById('sidebarToggle');

        toggleIcon.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');

            // Toggle icon symbol
            if (sidebar.classList.contains('collapsed')) {
                toggleIcon.textContent = '☰';
            } else {
                toggleIcon.textContent = '☰';
            }
        });
    </script>

    <script>
        lucide.createIcons();

        document.getElementById('department').addEventListener('change', function () {
            this.form.submit();
        });
    </script>
    

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    @stack('scripts')

</body>
</html>
