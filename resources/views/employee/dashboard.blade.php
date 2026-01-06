@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-2">
    {{-- 1. Header Section --}}
    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-0">DASHBOARD</h2>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }} !</p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-white text-dark border shadow-sm px-3 py-2">
                <i class="bi bi-calendar3 me-2 text-muted"></i> {{ date('l, d F Y') }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT COLUMN: Main Operations --}}
        <div class="col-lg-8">
            
            {{-- 1. ATTENDANCE SECTION --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold" style="color: #123456;">
                        <i class="bi bi-clock me-2"></i> TODAY'S ATTENDANCE
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        {{-- Status Indicator --}}
                        <div class="col-md-6 mb-3 mb-md-0 text-center text-md-start border-end-md">
                            <p class="text-uppercase text-muted small fw-bold mb-1">Current Status</p>
                            @if(!$todayAttendance || $todayAttendance->clock_out)
                                <h4 class="fw-bold text-muted mb-0"><i class="bi bi-circle-fill text-secondary me-2 small"></i>Not Clocked In</h4>
                            @else
                                <h4 class="fw-bold text-success mb-0"><i class="bi bi-circle-fill text-success me-2 small"></i>Working Now</h4>
                            @endif
                            
                            <div class="mt-3">
                                @if($todayAttendance)
                                    <span class="me-3">
                                        <i class="bi bi-box-arrow-in-right text-muted me-1"></i> 
                                        <strong>{{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('h:i A') }}</strong>
                                    </span>
                                    @if($todayAttendance->clock_out)
                                        <span>
                                            <i class="bi bi-box-arrow-right text-muted me-1"></i> 
                                            <strong>{{ \Carbon\Carbon::parse($todayAttendance->clock_out)->format('h:i A') }}</strong>
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted small">No activity recorded today.</span>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-md-6 text-center">
                            @if(!$todayAttendance || $todayAttendance->clock_out)
                                <form action="{{ route('attendance.clockIn') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg px-5 w-100 shadow-sm" style="background-color: #123456; border-color: #123456;">
                                        <i class="bi bi-fingerprint me-2"></i> CLOCK IN
                                    </button>
                                </form>
                                <small class="text-muted d-block mt-2">Shift starts at 9:00 AM</small>
                            @else
                                <form action="{{ route('attendance.clockOut') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-lg px-5 w-100">
                                        <i class="bi bi-power me-2"></i> CLOCK OUT
                                    </button>
                                </form>
                                <small class="text-muted d-block mt-2">Don't forget to clock out!</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. LEAVE OVERVIEW --}}
            <h6 class="text-muted text-uppercase fw-bold mb-3 small">Leave Overview ({{ date('Y') }})</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                        <div class="card-body p-3">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Total</small>
                            <h3 class="fw-bold mb-0 text-dark">{{ $totalLeaves ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                        <div class="card-body p-3">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Approved</small>
                            <h3 class="fw-bold mb-0 text-dark">{{ $approvedCount ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                        <div class="card-body p-3">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Rejected</small>
                            <h3 class="fw-bold mb-0 text-dark">{{ $rejectedCount ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-secondary">
                        <div class="card-body p-3">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Cancelled</small>
                            <h3 class="fw-bold mb-0 text-dark">{{ $cancelledCount ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. ROLE SPECIFIC CONTENT --}}
            
            {{-- === SUPERVISOR SECTION === --}}
            @if(Auth::user()->role === 'supervisor')
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold" style="color: #123456;">
                            <i class="bi bi-kanban me-2"></i> SUPERVISOR TASKS
                        </h6>
                        <span class="badge bg-warning text-dark">{{ $pendingReviewCount ?? 0 }} Pending</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Intern Name</th>
                                        <th>Document</th>
                                        <th>Date</th>
                                        <th class="text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingInternDocuments ?? [] as $doc)
                                        <tr>
                                            <td class="ps-4 fw-bold">{{ $doc->user->name }}</td>
                                            <td>{{ $doc->filename }}</td>
                                            <td class="text-muted small">{{ $doc->created_at->format('d M Y') }}</td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('supervisor.documents.review', $doc->id) }}" class="btn btn-sm btn-primary px-3">Review</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="bi bi-check2-all d-block fs-4 mb-2"></i>
                                                All documents reviewed.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-center py-2">
                        <a href="{{ route('supervisor.documents.index') }}" class="text-decoration-none small fw-bold">View All Tasks &rarr;</a>
                    </div>
                </div>
            @endif

            {{-- === INTERN SECTION === --}}
            @if(Auth::user()->role === 'intern')
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold" style="color: #123456;">
                            <i class="bi bi-file-earmark-text me-2"></i> RECENT UPLOADS
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">File Name</th>
                                        <th>Date Uploaded</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentDocuments ?? [] as $doc)
                                        <tr>
                                            <td class="ps-4">{{ $doc->filename }}</td>
                                            <td class="text-muted small">{{ $doc->created_at->format('d M Y') }}</td>
                                            <td>
                                                @if($doc->status == 'signed') <span class="badge bg-success">Signed</span>
                                                @elseif($doc->status == 'rejected') <span class="badge bg-secondary">Rejected</span>
                                                @else <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                @if($doc->status == 'signed' && $doc->signed_file_path)
                                                    <a href="{{ asset('storage/' . $doc->signed_file_path) }}" class="btn btn-sm btn-outline-success" download>
                                                        <i class="bi bi-download me-1"></i> Download
                                                    </a>
                                                @elseif($doc->supervisor_comment)
                                                    <span class="text-muted small" title="{{ $doc->supervisor_comment }}"><i class="bi bi-chat-left-text"></i> Note</span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No documents uploaded yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 4. ANNOUNCEMENTS --}}
            <div class="card border-0 shadow-sm bg-light mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3" style="color: #123456;">
                        <i class="bi bi-megaphone-fill text-warning me-2"></i> ANNOUNCEMENTS
                    </h6>
                    <div class="d-flex align-items-start bg-white p-3 rounded border">
                        <div class="me-3">
                            <span class="badge bg-primary">NEW</span>
                        </div>
                        <div>
                            <small class="text-dark fw-bold d-block">System Update 1.0</small>
                            <small class="text-muted">The HR system has been updated. Please report any issues to IT support.</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Profile & Actions --}}
        <div class="col-lg-4">
            
            {{-- 1. PROFILE SUMMARY CARD (UPDATED WITH IMAGE) --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        @if(Auth::user()->profile_image)
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                 alt="Profile" 
                                 class="rounded-circle border" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-flex justify-content-center align-items-center mx-auto border" style="width: 100px; height: 100px;">
                                <span class="fs-1 text-secondary fw-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted mb-3 small">{{ Auth::user()->email }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-light text-dark border">{{ Auth::user()->department ?? 'No Dept' }}</span>
                        <span class="badge bg-light text-dark border">{{ Auth::user()->position ?? 'No Position' }}</span>
                    </div>

                    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary btn-sm w-100">View Full Profile</a>
                </div>
            </div>

            {{-- 2. QUICK ACTIONS --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold" style="color: #123456;">QUICK ACTIONS</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('attendance.index') }}" class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                            <i class="bi bi-qr-code-scan fs-5 text-primary me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-semibold">Attendance Log</h6>
                                <small class="text-muted">View your clock-in history</small>
                            </div>
                        </a>
                        
                        @if(Auth::user()->role === 'supervisor')
                            <a href="{{ route('supervisor.documents.index') }}" class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                                <i class="bi bi-file-earmark-check fs-5 text-success me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-semibold">Review Documents</h6>
                                    <small class="text-muted">Approve intern files</small>
                                </div>
                            </a>
                        @elseif(Auth::user()->role === 'intern')
                            <a href="{{ route('intern.documents.create') }}" class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                                <i class="bi bi-cloud-upload fs-5 text-success me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-semibold">Upload Document</h6>
                                    <small class="text-muted">Submit files for signing</small>
                                </div>
                            </a>
                        @else
                            <a href="{{ route('leave.create') }}" class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                                <i class="bi bi-calendar-plus fs-5 text-danger me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-semibold">Apply for Leave</h6>
                                    <small class="text-muted">Request time off</small>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection