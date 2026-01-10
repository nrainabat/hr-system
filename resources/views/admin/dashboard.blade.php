@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    {{-- 1. Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-body py-3">
                <h2 class="fw-bold" style="color: #123456;">DASHBOARD</h2>
                <p class="text-muted mb-0">Overview of system statistics and user management.</p>
            </div>
        </div>
    </div>

    {{-- 2. Organization Overview --}}
    <h5 class="fw-bold mb-3 ps-1" style="color: #123456;">Organization Overview</h5>
    <div class="row g-3 mb-4">
        {{-- Total Users --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 border-start border-4" style="border-color: #123456 !important;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3" style="background-color: rgba(18, 52, 86, 0.1);">
                        <i class="bi bi-people-fill fs-4" style="color: #123456;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Total Employees</h6>
                        <h3 class="fw-bold mb-0" style="color: #123456;">{{ $totalUsers }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Employees --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 border-start border-4 border-primary">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-briefcase-fill text-primary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Staff</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalEmployees }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Supervisors --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 border-start border-4 border-success">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-person-badge-fill text-success fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Supervisors</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalSupervisors }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Interns --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 border-start border-4 border-warning">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-mortarboard-fill text-warning fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Interns</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalInterns }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Today's Attendance --}}
    <h5 class="fw-bold mb-3 mt-4 ps-1" style="color: #123456;">Today's Attendance</h5>
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    {{-- Header with Rate --}}
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <div>
                            <h4 class="fw-bold mb-0" style="color: #123456;">
                                {{ $attendancePercentage }}% 
                            </h4>
                            <span class="text-muted small">Daily Attendance Rate</span>
                        </div>
                        <span class="text-muted small">{{ date('d M Y') }}</span>
                    </div>
                    
                    {{-- STACKED Progress Bar --}}
                    {{-- Calculates widths based on total users to ensure accurate proportions --}}
                    @php
                        $totalForCalc = $totalUsers > 0 ? $totalUsers : 1;
                        $presentPct = ($presentCount / $totalForCalc) * 100;
                        $latePct = ($lateCount / $totalForCalc) * 100;
                    @endphp

                    <div class="progress" style="height: 25px; border-radius: 15px; background-color: #f8f9fa; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                        {{-- 1. Present (Green) --}}
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $presentPct }}%" 
                             aria-valuenow="{{ $presentPct }}" aria-valuemin="0" aria-valuemax="100"
                             data-bs-toggle="tooltip" title="{{ $presentCount }} On Time">
                             @if($presentPct > 5) {{ $presentCount }} @endif
                        </div>

                        {{-- 2. Late (Yellow/Warning) --}}
                        <div class="progress-bar bg-warning text-dark" role="progressbar" 
                             style="width: {{ $latePct }}%" 
                             aria-valuenow="{{ $latePct }}" aria-valuemin="0" aria-valuemax="100"
                             data-bs-toggle="tooltip" title="{{ $lateCount }} Late">
                             @if($latePct > 5) {{ $lateCount }} @endif
                        </div>
                    </div>

                    {{-- Legend / Key --}}
                    <div class="row mt-4 text-center">
                        <div class="col-4 border-end">
                            <h5 class="fw-bold mb-0 text-success">{{ $presentCount }}</h5>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">On Time</small>
                        </div>
                        <div class="col-4 border-end">
                            <h5 class="fw-bold mb-0 text-warning">{{ $lateCount }}</h5>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Late</small>
                        </div>
                        <div class="col-4">
                            <h5 class="fw-bold mb-0 text-danger">{{ $absentCount }}</h5>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Absent</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Clock In Action (Remains unchanged) --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-dark text-white">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="bi bi-clock-history display-4 mb-3 text-warning"></i>
                    <h5 class="fw-light">Current Server Time</h5>
                    <h2 class="fw-bold" id="liveClock">{{ date('h:i A') }}</h2>
                </div>
            </div>
        </div>
    </div>

     {{-- 5. Pending Leave Requests & Quick Actions --}}
    <div class="row">
        {{-- LEFT: Pending Leave Requests Table --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header text-white fw-bold py-3 d-flex justify-content-between align-items-center" style="background-color: #d97706;">
                    <span><i class="bi bi-hourglass-split me-2"></i> Pending Leave Requests</span>
                    <span class="badge bg-white text-dark">{{ count($pendingLeaveRequests) }} New</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Employee</th>
                                    <th>Leave Type</th>
                                    <th>Dates</th>
                                    <th>Duration</th>
                                    <th class="text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingLeaveRequests as $leave)
                                    <tr>
                                        <td class="ps-4 fw-semibold">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px;">
                                                    <i class="bi bi-person text-muted"></i>
                                                </div>
                                                {{ $leave->user->name ?? 'Unknown' }}
                                            </div>
                                        </td>
                                        <td>{{ ucfirst($leave->leave_type) }}</td>
                                        <td class="small text-muted">
                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - 
                                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d M') }}
                                        </td>
                                        <td>{{ $leave->days }} Day(s)</td>
                                        <td class="text-end pe-4">
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-check-circle display-4 d-block mb-2 text-success opacity-50"></i>
                                            No pending leave requests.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Quick Actions (UPDATED) --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">Quick Actions</h5>
                </div>
                <div class="card-body p-4 d-flex flex-column gap-3">
                    {{-- 1. Register User --}}
                    <a href="{{ route('admin.users.create') }}" class="btn btn-dark w-100 py-3 d-flex align-items-center justify-content-center shadow-sm">
                        <i class="bi bi-person-plus-fill me-2 fs-5"></i> Register New User
                    </a>
                    
                    {{-- 2. Manage Employees (Links to Directory) --}}
                    <a href="{{ route('admin.directory') }}" class="btn btn-outline-secondary w-100 py-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-people-fill me-2 fs-5"></i> Manage Employees
                    </a>

                    {{-- 3. View Reports (Links to Attendance Log) --}}
                    <a href="{{ route('admin.attendance') }}" class="btn btn-outline-secondary w-100 py-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-bar-graph me-2 fs-5"></i> View Reports
                    </a>
                    
                    <div class="mt-auto text-center pt-3">
                        <small class="text-muted">System Version 1.0.0</small>
                    </div>
                </div>
            </div>
        </div>

    {{-- 4. Company Structure (Chart) --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">Employee Distribution by Department</h6>
                </div>
                <div class="card-body p-4">
                    {{-- Canvas for Chart.js --}}
                    <canvas id="departmentChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Live Clock
        setInterval(() => {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const clockElement = document.getElementById('liveClock');
            if(clockElement) clockElement.innerText = timeString;
        }, 60000);

        // Chart.js
        const ctx = document.getElementById('departmentChart');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($deptLabels) !!},
                    datasets: [{
                        label: 'Number of Employees',
                        data: {!! json_encode($deptCounts) !!},
                        backgroundColor: ['#123456', '#0d6efd', '#198754', '#ffc107', '#6c757d'],
                        borderRadius: 4,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { display: true } },
                        x: { grid: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>
@endpush
@endsection