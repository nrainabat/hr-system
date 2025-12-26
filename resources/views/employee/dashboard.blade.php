@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
                <div class="card-body p-4">
                    <h2 class="fw-bold">Welcome, {{ Auth::user()->name }}!</h2>
                    <p class="mb-0">{{ date('l, d F Y') }}</p>
                </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100 border-0">
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-4">
                
                <div class="mb-3">
                    <h5 class="fw-bold mb-2">
                        Status: 
                        @if(!$todayAttendance)
                            <span class="badge bg-warning text-dark">Not Clocked In</span>
                        @elseif($todayAttendance->clock_in && is_null($todayAttendance->clock_out))
                            <span class="badge bg-success">Working Now</span>
                        @else
                            <span class="badge bg-secondary">Completed</span>
                        @endif
                    </h5>

                    <div class="text-muted fs-5">
                        @if($todayAttendance)
                            <span>
                                <i class="bi bi-box-arrow-in-right"></i> In: 
                                <span class="fw-bold text-dark">
                                    {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('h:i A') }}
                                </span>
                            </span>

                            @if($todayAttendance->clock_out)
                                <span class="mx-2">|</span>
                                <span>
                                    <i class="bi bi-box-arrow-right"></i> Out: 
                                    <span class="fw-bold text-dark">
                                        {{ \Carbon\Carbon::parse($todayAttendance->clock_out)->format('h:i A') }}
                                    </span>
                                </span>
                            @endif
                        @else
                            <span>Please clock in to start your shift.</span>
                        @endif
                    </div>
                </div>

                <div>
                    @if(!$todayAttendance)
                        <form action="{{ route('attendance.clockIn') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
                                <i class="bi bi-play-circle-fill me-2"></i> CLOCK IN
                            </button>
                        </form>
                    
                    @elseif($todayAttendance && is_null($todayAttendance->clock_out))
                        <form action="{{ route('attendance.clockOut') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger px-5 fw-bold shadow-sm">
                                <i class="bi bi-stop-circle-fill me-2"></i> CLOCK OUT
                            </button>
                        </form>

                    @else
                        <button class="btn btn-secondary px-5 fw-bold" disabled>
                            <i class="bi bi-check-all me-2"></i> DONE
                        </button>
                    @endif
                </div>
            </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="text-muted mb-3">Quick Actions</h5>
                    <div class="d-grid gap-2 w-100">
                        <a href="/employee/attendance" class="btn btn-outline-dark">
                            <i class="bi bi-qr-code-scan"></i> View Attendance History
                        </a>
                        <a href="/employee/leave" class="btn btn-outline-danger">
                            <i class="bi bi-calendar-plus"></i> Apply for Leave
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5 class="text-muted mb-3">My Status</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Department
                            <span class="fw-semibold">IT Department</span> </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Position
                            <span class="fw-semibold">Intern</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Status
                            <span class="badge bg-success">Active</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm border-0">
    <div class="card-header text-white fw-bold py-2" style="background-color: #873260;">
        LEAVE STATISTICS - {{ date('Y') }}
    </div>
    
    <div class="card-body">
        <div class="row g-3 text-center text-white">
            
            <div class="col-md-3">
                <div class="p-4 rounded shadow-sm" style="background-color: #0d6efd;"> <h5 class="fw-bold mb-1">Total Applied</h5>
                    <h2 class="fw-bold mb-0">{{ $totalLeaves ?? 0 }}</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 rounded shadow-sm" style="background-color: #198754;">
                    <h5 class="fw-bold mb-1">Approved</h5>
                    <h2 class="fw-bold mb-0">{{ $approvedCount ?? 0 }}</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 rounded shadow-sm" style="background-color: #dc3545;">
                    <h5 class="fw-bold mb-1">Not Approved</h5>
                    <h2 class="fw-bold mb-0">{{ $rejectedCount ?? 0 }}</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 rounded shadow-sm" style="background-color: #6c757d;">
                    <h5 class="fw-bold mb-1">Cancelled</h5>
                    <h2 class="fw-bold mb-0">{{ $cancelledCount ?? 0 }}</h2>
                </div>
            </div>

        </div>
    </div>
</div>

    <div class="card shadow-sm border-0">
        <div class="card-header text-white fw-bold py-2" style="background-color: #873260;">
            RECENT ANNOUNCEMENTS
        </div>
        <div class="card-body py-4">
            <p class="text-muted mb-0">No new announcements from HR.</p>
        </div>
    </div>
</div>
@endsection