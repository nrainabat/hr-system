@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">Admin Dashboard</h2>
            <p class="text-muted">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white" style="background-color: #873260;">
                <div class="card-body p-4">
                    <h5 class="fw-bold opacity-75">Total Users</h5>
                    <h1 class="fw-bold mb-0">{{ $totalUsers }}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-primary">
                <div class="card-body p-4">
                    <h5 class="fw-bold opacity-75">Employees</h5>
                    <h1 class="fw-bold mb-0">{{ $totalEmployees }}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-success">
                <div class="card-body p-4">
                    <h5 class="fw-bold opacity-75">Supervisors</h5>
                    <h1 class="fw-bold mb-0">{{ $totalSupervisors }}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-warning">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark opacity-75">Interns</h5>
                    <h1 class="fw-bold text-dark mb-0">{{ $totalInterns }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-lightning-charge me-2"></i> Quick Actions</h5>
        </div>
        <div class="card-body p-4">
            <div class="d-flex gap-3 flex-wrap">
                <a href="{{ route('admin.users.create') }}" class="btn btn-dark px-4 py-2">
                    <i class="bi bi-person-plus-fill me-2"></i> Register New User
                </a>
                <button class="btn btn-outline-secondary px-4 py-2">
                    <i class="bi bi-people-fill me-2"></i> Manage Employees
                </button>
                <button class="btn btn-outline-secondary px-4 py-2">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i> View Reports
                </button>
            </div>
        </div>
    </div>
</div>
@endsection