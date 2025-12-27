@extends('layouts.app')

@section('Dashboard')

@section('content')
<div class="container">
    {{-- 1. Welcome Section --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card-body p-4">
                <h2 class="fw-bold">
                    Welcome, {{ Auth::user()->name }}!
                </h2>
                <p class="mb-0 text-muted">{{ date('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- 2. Top Cards Row --}}
    <div class="row mb-4">
        {{-- Attendance Card --}}
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100 border-0">
               <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-4"> 
                    <h5 class="text-muted mb-3">Today's Attendance</h5>
                    <div class="mb-3">
                        <h5 class="fw-bold mb-2">
                            Status: 
                            @if(!$todayAttendance || $todayAttendance->clock_out)
                                <span class="badge bg-warning text-dark">Not Clocked In</span>
                            @else
                                <span class="badge bg-success">Working Now</span>
                            @endif
                        </h5>
                        <div class="text-muted fs-5">
                            @if($todayAttendance)
                                <span><i class="bi bi-box-arrow-in-right"></i> {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('h:i A') }}</span>
                                @if($todayAttendance->clock_out)
                                    <span class="mx-2">|</span>
                                    <span><i class="bi bi-box-arrow-right"></i> {{ \Carbon\Carbon::parse($todayAttendance->clock_out)->format('h:i A') }}</span>
                                @endif
                            @else
                                <span>Please clock in to start.</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if(!$todayAttendance || $todayAttendance->clock_out)
                            <form action="{{ route('attendance.clockIn') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">CLOCK IN</button>
                            </form>
                        @else
                            <form action="{{ route('attendance.clockOut') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger px-5 fw-bold shadow-sm">CLOCK OUT</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="text-muted mb-3">Quick Actions</h5>
                    <div class="d-grid gap-2 w-100">
                        <a href="/employee/attendance" class="btn btn-outline-dark"><i class="bi bi-qr-code-scan"></i> Attendance History</a>
                        <a href="/employee/leave" class="btn btn-outline-danger"><i class="bi bi-calendar-plus"></i> Apply Leave</a>
                        
                        @if(Auth::user()->role == 'intern')
                            <a href="{{ route('intern.documents.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-cloud-upload"></i> Upload Document
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

       {{-- My Status --}}
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5 class="text-muted mb-3">My Status</h5>
                    <ul class="list-group list-group-flush">
                        
                        {{-- Department --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Department 
                            <span class="fw-semibold">{{ Auth::user()->department ?? 'Not Assigned' }}</span> 
                        </li>

                        {{-- ADDED: Position --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Position 
                            <span class="fw-semibold">{{ Auth::user()->position ?? 'Not Assigned' }}</span>
                        </li>

                        {{-- Role --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Role 
                            @if(Auth::user()->role == 'intern')
                                <span class="badge bg-info text-dark">Intern</span>
                            @else
                                <span class="badge bg-primary">Employee</span>
                            @endif
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Leave Statistics --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header text-white fw-bold py-2" style="background-color: #873260;">
            LEAVE STATISTICS - {{ date('Y') }}
        </div>
        <div class="card-body">
            <div class="row g-3 text-center text-white">
                <div class="col-md-3"><div class="p-4 rounded shadow-sm bg-primary"><h5 class="fw-bold">Total</h5><h2>{{ $totalLeaves ?? 0 }}</h2></div></div>
                <div class="col-md-3"><div class="p-4 rounded shadow-sm bg-success"><h5 class="fw-bold">Approved</h5><h2>{{ $approvedCount ?? 0 }}</h2></div></div>
                <div class="col-md-3"><div class="p-4 rounded shadow-sm bg-danger"><h5 class="fw-bold">Rejected</h5><h2>{{ $rejectedCount ?? 0 }}</h2></div></div>
                <div class="col-md-3"><div class="p-4 rounded shadow-sm bg-secondary"><h5 class="fw-bold">Cancelled</h5><h2>{{ $cancelledCount ?? 0 }}</h2></div></div>
            </div>
        </div>
    </div>

    {{-- 5. Announcements --}}
    <div class="card shadow-sm border-0">
        <div class="card-header text-white fw-bold py-2" style="background-color: #873260;">
            RECENT ANNOUNCEMENTS
        </div>
        <div class="card-body py-4">
            <p class="text-muted mb-0">No new announcements.</p>
        </div>
    </div>
</div>
@endsection