@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    {{-- 1. Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-body py-3">
                <h2 class="fw-bold" style="color: #123456;">Admin Dashboard</h2>
                <p class="text-muted mb-0">Overview of system statistics and user management.</p>
            </div>
        </div>
    </div>

    {{-- 2. Statistics Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Users --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100" style="background-color: #123456; color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase opacity-75 mb-1">Total Users</h6>
                            <h2 class="fw-bold mb-0">{{ $totalUsers }}</h2>
                        </div>
                        <i class="bi bi-people-fill display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Employees --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase opacity-75 mb-1">Employees</h6>
                            <h2 class="fw-bold mb-0">{{ $totalEmployees }}</h2>
                        </div>
                        <i class="bi bi-briefcase-fill display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Supervisors --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase opacity-75 mb-1">Supervisors</h6>
                            <h2 class="fw-bold mb-0">{{ $totalSupervisors }}</h2>
                        </div>
                        <i class="bi bi-person-badge-fill display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Interns --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-warning text-dark">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase opacity-75 mb-1">Interns</h6>
                            <h2 class="fw-bold mb-0">{{ $totalInterns }}</h2>
                        </div>
                        <i class="bi bi-mortarboard-fill display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 3. Recent Users Table --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header text-white fw-bold py-3" style="background-color: #2D3748;">
                    <i class="bi bi-clock-history me-2"></i> RECENTLY JOINED USERS
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Name</th>
                                    <th>Role</th>
                                    <th>Date Joined</th>
                                    <th class="text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $user)
                                    <tr>
                                        <td class="ps-4 fw-semibold">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px;">
                                                    <i class="bi bi-person text-muted"></i>
                                                </div>
                                                {{ $user->name }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->role == 'admin') <span class="badge bg-danger">Admin</span>
                                            @elseif($user->role == 'supervisor') <span class="badge bg-success">Supervisor</span>
                                            @elseif($user->role == 'intern') <span class="badge bg-warning text-dark">Intern</span>
                                            @else <span class="badge bg-primary">Employee</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                                        <td class="text-end pe-4">
                                            <span class="badge bg-light text-success border border-success">Active</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No users found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <a href="{{ route('admin.users.create') }}" class="text-decoration-none fw-bold text-dark">View All Users &rarr;</a>
                </div>
            </div>
        </div>

        {{-- 4. Quick Actions --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">Quick Actions</h5>
                </div>
                <div class="card-body p-4 d-flex flex-column gap-3">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-dark w-100 py-3 d-flex align-items-center justify-content-center shadow-sm">
                        <i class="bi bi-person-plus-fill me-2 fs-5"></i> Register New User
                    </a>
                    
                    <button class="btn btn-outline-secondary w-100 py-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-people-fill me-2 fs-5"></i> Manage Employees
                    </button>

                    <button class="btn btn-outline-secondary w-100 py-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-bar-graph me-2 fs-5"></i> View Reports
                    </button>
                    
                    <div class="mt-auto text-center pt-3">
                        <small class="text-muted">System Version 1.0.0</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection