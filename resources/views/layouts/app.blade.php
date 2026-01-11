<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'iManageHR')</title>

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
            background-color: #123456; 
            color: white; 
            width: 280px !important; 
        }
        .offcanvas .btn-close { 
            filter: invert(1) grayscale(100%) brightness(200%); 
        }
        .offcanvas-body .nav-link { 
            color: rgba(255, 255, 255, 0.85); 
            font-size: 1rem; padding: 12px 15px; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.05); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .offcanvas-body .nav-link:hover { 
            color: #fff; 
            background-color: rgba(255, 255, 255, 0.1); 
            transition: all 0.3s; 
        }
        .sub-menu { 
            background-color: rgba(0, 0, 0, 0.2); 
        }
        .sub-menu .nav-link { 
            padding-left: 3rem; 
            font-size: 0.95rem; 
            border-bottom: none; 
        }
        .nav-link[aria-expanded="true"] .bi-chevron-right { 
            transform: rotate(90deg); 
            transition: transform 0.3s; 
        }
        .bi-chevron-right { 
            transition: transform 0.3s; 
            font-size: 0.8rem; 
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- TOP NAVBAR --}}
<nav class="navbar fixed-top shadow-sm" style="background-color:#123456;">
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
                    <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">{{ Auth::user()->username }}</a>
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
    
    <div class="offcanvas-body p-0">
        <nav class="nav flex-column">
            @auth
                {{-- DASHBOARD --}}
                @if(Auth::user()->role === 'admin')
                    <a class="nav-link" href="/admin/dashboard"><span><i class="bi bi-speedometer2 me-2"></i> Dashboard</span></a>
                @elseif(Auth::user()->role === 'supervisor')
                    <a class="nav-link" href="/supervisor/dashboard"><span><i class="bi bi-speedometer2 me-2"></i> Dashboard</span></a>
                @else
                    <a class="nav-link" href="{{ route('dashboard') }}"><span><i class="bi bi-speedometer2 me-2"></i> Dashboard</span></a>
                @endif

                {{-- ADMIN LINKS --}}
                @if(Auth::user()->role === 'admin')
                    <a class="nav-link" data-bs-toggle="collapse" href="#adminUsersMenu" role="button" aria-expanded="false">
                        <span><i class="bi bi-people me-2"></i> Employee</span><i class="bi bi-chevron-right"></i>
                    </a>
                    <div class="collapse sub-menu" id="adminUsersMenu">
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('admin.users.create') }}">New Employee</a>
                            <a class="nav-link" href="{{ route('admin.directory') }}">Employee Directory</a>
                        </nav>
                    </div>

                    <a class="nav-link" data-bs-toggle="collapse" href="#orgMenu" role="button" aria-expanded="false">
                        <span><i class="bi bi-building me-2"></i> Organization</span><i class="bi bi-chevron-right"></i>
                    </a>
                    <div class="collapse sub-menu" id="orgMenu">
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('admin.org.departments.index') }}">Departments</a>
                            <a class="nav-link" href="{{ route('admin.org.jobs.index') }}">Job Positions</a>
                            {{-- UPDATED: Changed link to Team Department --}}
                            <a class="nav-link" href="{{ route('admin.org.structure.teams') }}">Structure</a>
                        </nav>
                    </div>

                    <a class="nav-link" data-bs-toggle="collapse" href="#adminLeaveMenu" role="button" aria-expanded="false">
                        <span><i class="bi bi-calendar-week me-2"></i> Leave Management</span><i class="bi bi-chevron-right"></i>
                    </a>
                    <div class="collapse sub-menu" id="adminLeaveMenu">
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('admin.leave.requests') }}">Leave Requests</a>
                            <a class="nav-link" href="{{ route('admin.leave.balances') }}">Leave Count</a>
                            <a class="nav-link" href="{{ route('admin.leave.types') }}">Leave Types</a>
                        </nav>
                    </div>

                    <a class="nav-link" href="{{ route('admin.attendance') }}">
                        <span><i class="bi bi-clock me-2"></i> Attendance Log</span>
                    </a>

                    <a class="nav-link" href="{{ route('admin.performance.index') }}">
                        <span><i class="bi bi-trophy me-2"></i> Performance Review</span>
                    </a>

                    <a class="nav-link" href="{{ route('admin.reports') }}">
                        <span><i class="bi bi-graph-up-arrow me-2"></i> Analytics</span>
                    </a>
                @endif

                {{-- EMPLOYEE & INTERN --}}
                @if(Auth::user()->role === 'employee' || Auth::user()->role === 'intern' || Auth::user()->role === 'supervisor')
                    <a class="nav-link" href="{{ route('attendance.index') }}"><span><i class="bi bi-calendar-check me-2"></i> Attendance</span></a>
                    <a class="nav-link" data-bs-toggle="collapse" href="#leaveMenu" role="button" aria-expanded="false">
                        <span><i class="bi bi-briefcase me-2"></i> Leave</span><i class="bi bi-chevron-right"></i>
                    </a>
                    <div class="collapse sub-menu" id="leaveMenu">
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('leave.create') }}">Apply Leave</a>
                            <a class="nav-link" href="{{ route('leave.history') }}">Application History</a>
                        </nav>
                    </div>
                @endif

                {{-- INTERN --}}
                @if(Auth::user()->role === 'intern')
                    <a class="nav-link" data-bs-toggle="collapse" href="#internDocsMenu" role="button" aria-expanded="false">
                        <span><i class="bi bi-file-earmark-text me-2"></i> My Documents</span><i class="bi bi-chevron-right"></i>
                    </a>
                    <div class="collapse sub-menu" id="internDocsMenu">
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('intern.documents.create') }}">Upload New</a>
                            <a class="nav-link" href="{{ route('intern.documents.index') }}">View History</a>
                        </nav>
                    </div>
                @endif

                {{-- SUPERVISOR --}}
                @if(Auth::user()->role === 'supervisor')
                    <a class="nav-link" data-bs-toggle="collapse" href="#supervisorDocsMenu" role="button" aria-expanded="false">
                        <span><i class="bi bi-file-earmark-check me-2"></i> Document Reviews</span><i class="bi bi-chevron-right"></i>
                    </a>
                    <div class="collapse sub-menu" id="supervisorDocsMenu">
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('supervisor.documents.index') }}">Pending Reviews</a>
                        </nav>
                    </div>
                    
                    <a class="nav-link" href="{{ route('supervisor.reports') }}">
                        <span><i class="bi bi-bar-chart-line me-2"></i> Team Reports</span>
                    </a>

                    {{-- Supervisor Performance Dropdown --}}
                    <a class="nav-link" data-bs-toggle="collapse" href="#supervisorPerformanceMenu" role="button" aria-expanded="false">
                        <span><i class="bi bi-award me-2"></i> Performance</span><i class="bi bi-chevron-right"></i>
                    </a>
                    <div class="collapse sub-menu" id="supervisorPerformanceMenu">
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('performance.index') }}">Evaluate Teams</a>
                            <a class="nav-link" href="{{ route('performance.myReview') }}">Performance Review</a>
                        </nav>
                    </div>
                @endif

                {{-- Employee/Intern/Supervisor Personal Reports --}}
                @if(in_array(Auth::user()->role, ['employee', 'intern']))
                    <a class="nav-link" href="{{ route('performance.index') }}">
                        <span><i class="bi bi-clipboard-check me-2"></i> My Performance</span>
                    </a>
                     <a class="nav-link" href="{{ route('employee.reports') }}">
                        <span><i class="bi bi-pie-chart me-2"></i> My Stats</span>
                    </a>
                @endif
            @endauth
        </nav>
    </div>
</div>

<main class="container-fluid px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>