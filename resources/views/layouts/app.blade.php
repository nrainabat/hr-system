<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'iManageHR')</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding-top: 70px; 
            padding-bottom: 70px;
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }
        .offcanvas {
            background-color: #873260; 
            color: white;
            width: 280px !important;
        }
        .offcanvas .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        .offcanvas-body .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .offcanvas-body .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            padding-left: 10px;
            transition: all 0.3s;
        }
    </style>
    @stack('styles')
</head>

<body>

{{-- TOP NAVBAR --}}
<nav class="navbar fixed-top shadow-sm" style="background-color:#873260;">
    <div class="container-fluid px-3">
        <div class="d-flex align-items-center">
            <button class="btn border-0 text-white me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                <i class="bi bi-list" style="font-size: 1.5rem;"></i>
            </button>
            <a class="navbar-brand text-white" href="#">iManageHR</a>
        </div>

        <ul class="navbar-nav ms-auto flex-row">
            @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                        {{ Auth::user()->username }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end position-absolute">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="/logout">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
        </ul>
    </div>
</nav>

{{-- SIDEBAR --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title text-white" id="sidebarMenuLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    
    <div class="offcanvas-body">
        <nav class="nav flex-column">
            @auth
                {{-- ADMIN LINKS --}}
                @if(Auth::user()->role === 'admin')
                    <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                    <a class="nav-link" href="/admin/users">Users</a>
                    <a class="nav-link" href="/admin/employees">Employees</a>
                    <a class="nav-link" href="/admin/interns">Interns</a>
                @endif

                {{-- EMPLOYEE & INTERN SHARED LINKS --}}
                @if(Auth::user()->role === 'employee' || Auth::user()->role === 'intern' || Auth::user()->role === 'supervisor')
                    
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    
                    <a class="nav-link" href="{{ route('attendance.index') }}">
                        <i class="bi bi-calendar-check me-2"></i> Attendance
                    </a>

                    {{-- LEAVE DROPDOWN --}}
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-briefcase me-2"></i> Leave
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm" style="background-color: rgba(255, 255, 255, 0.9);">
                            <li>
                                <a class="dropdown-item text-dark" href="{{ route('leave.create') }}">
                                    <i class="bi bi-pencil-square me-2"></i> Apply Form
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-dark" href="{{ route('leave.history') }}">
                                    <i class="bi bi-list-ul me-2"></i> List of Application
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif

                {{-- SUPERVISOR LINKS --}}
                @if(Auth::user()->role === 'supervisor')
                    {{-- NEW: Navigation for Document Review --}}
                    <a class="nav-link" href="{{ route('supervisor.documents.index') }}">
                        <i class="bi bi-file-earmark-check me-2"></i> Review Documents
                    </a>

                    {{-- Optional: Keep or Remove 'My Interns' if not used --}}
                    {{-- <a class="nav-link" href="/supervisor/interns">My Interns</a> --}}
                @endif

                {{-- INTERN --}}
                @if(Auth::user()->role === 'intern')
                    <a class="nav-link" href="{{ route('intern.documents.index') }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Documents
                    </a>
                @endif

            @endauth
        </nav>
    </div>
</div>

<main class="container-fluid px-4">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

</body>
</html>