@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    {{-- 1. Welcome Section --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card-body p-4">
                <h2 class="fw-bold">Welcome, {{ Auth::user()->name }}!</h2>
                <p class="mb-0 text-muted">{{ date('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- 2. Top Cards Row (Attendance, Quick Actions, Status) --}}
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
                        
                        {{-- 2. Role Specific Buttons --}}
                        @if(Auth::user()->role === 'supervisor')
                            {{-- Supervisor: Can Review Documents AND Apply for Leave --}}
                            <a href="{{ route('supervisor.documents.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-file-earmark-check"></i> Review Documents
                            </a>
                            <a href="/employee/leave" class="btn btn-outline-danger">
                                <i class="bi bi-calendar-plus"></i> Apply for Leave
                            </a>

                        @elseif(Auth::user()->role === 'intern')
                            {{-- Intern: Can Upload Document --}}
                            <a href="{{ route('intern.documents.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-cloud-upload"></i> Upload Document
                            </a>

                        @else
                            {{-- Regular Employee: Can Apply for Leave --}}
                            <a href="/employee/leave" class="btn btn-outline-danger">
                                <i class="bi bi-calendar-plus"></i> Apply for Leave
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
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Department <span class="fw-semibold">{{ Auth::user()->department ?? 'Not Assigned' }}</span> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Position <span class="fw-semibold">{{ Auth::user()->position ?? 'Not Assigned' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Role
                            @php $role = Auth::user()->role; @endphp
                            @if($role === 'admin') <span class="badge bg-danger">Admin</span>
                            @elseif($role === 'supervisor') <span class="badge bg-warning text-dark">Supervisor</span>
                            @elseif($role === 'intern') <span class="badge bg-info text-dark">Intern</span>
                            @else <span class="badge bg-primary">Employee</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. ROLE SPECIFIC SECTIONS --}}

    {{-- === SUPERVISOR SECTION === --}}
    @if(Auth::user()->role === 'supervisor')
        {{-- A. Supervisor Stats Row --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-primary text-white h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-people-fill display-4 mb-2"></i>
                        <h5 class="fw-bold">My Interns</h5>
                        <p class="fs-4 mb-0 fw-bold">{{ $myInternsCount ?? 0 }}</p>
                        <small class="text-white-50">Assigned in {{ Auth::user()->department }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-warning text-dark h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-hourglass-split display-4 mb-2"></i>
                        <h5 class="fw-bold">Pending Reviews</h5>
                        <p class="fs-4 mb-0 fw-bold">{{ $pendingReviewCount ?? 0 }}</p>
                        <small class="text-dark-50">Documents waiting action</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-success text-white h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-check2-circle display-4 mb-2"></i>
                        <h5 class="fw-bold">Total Signed</h5>
                        <p class="fs-4 mb-0 fw-bold">{{ $signedCount ?? 0 }}</p>
                        <small class="text-white-50">Documents processed</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- B. Supervisor Document Table --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header text-white fw-bold py-2" style="background-color: #2D3748;">
                <i class="bi bi-inbox me-2"></i> RECENT DOCUMENT REQUESTS
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Intern</th>
                                <th>Document</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingInternDocuments ?? [] as $doc)
                                <tr>
                                    <td>{{ $doc->user->name }}</td>
                                    <td>{{ $doc->filename }}</td>
                                    <td>{{ $doc->created_at->format('d M') }}</td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                    <td>
                                        <a href="{{ route('supervisor.documents.review', $doc->id) }}" class="btn btn-sm btn-primary">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No pending documents.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('supervisor.documents.index') }}" class="text-decoration-none fw-bold">View All Documents &rarr;</a>
                </div>
            </div>
        </div>
    @endif

    {{-- === INTERN SECTION === --}}
    @if(Auth::user()->role === 'intern')
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header text-white fw-bold py-2" style="background-color: #4A5568;">
                <i class="bi bi-file-earmark-text me-2"></i> RECENT UPLOADS
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Date</th><th>File</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($recentDocuments ?? [] as $doc)
                            <tr>
                                <td>{{ $doc->created_at->format('d M') }}</td>
                                <td>{{ $doc->filename }}</td>
                                <td>
                                    @if($doc->status == 'signed') <span class="badge bg-success">Signed</span>
                                    @elseif($doc->status == 'rejected') <span class="badge bg-secondary">Rejected</span>
                                    @else <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">No documents uploaded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- 5. Announcements (Visible to All) --}}
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