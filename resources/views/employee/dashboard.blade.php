@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-2">
    {{-- Header Section --}}
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

                        <div class="col-md-6 text-center">
                            @if(!$todayAttendance || $todayAttendance->clock_out)
                                <form action="{{ route('attendance.clockIn') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg px-5 w-100 shadow-sm" style="background-color: #123456; border-color: #123456;">
                                        <i class="bi bi-fingerprint me-2"></i> CLOCK IN
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('attendance.clockOut') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-lg px-5 w-100">
                                        <i class="bi bi-power me-2"></i> CLOCK OUT
                                    </button>
                                </form>
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

            {{-- 3. SUPERVISOR TASKS (Pending Reviews) --}}
            @if(Auth::user()->role === 'supervisor')
                
                {{-- HEADER --}}
                <div class="mb-3 mt-4">
                     <h4 class="fw-bold" style="color: #123456;">
                        <i class="bi bi-shield-lock-fill me-2"></i> Supervisor Management
                    </h4>
                </div>

                {{-- Pending Reviews Table --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold" style="color: #123456;">
                            <i class="bi bi-kanban me-2"></i> PENDING REVIEWS
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
                                            <td class="text-muted small">{{ $doc->created_at->format('d M') }}</td>
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

            {{-- 4. INTERN RECENT UPLOADS --}}
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
                                                    <a href="{{ asset('storage/' . $doc->signed_file_path) }}" class="btn btn-sm btn-outline-success" download><i class="bi bi-download"></i></a>
                                                @elseif($doc->supervisor_comment)
                                                    <span class="text-muted small" title="{{ $doc->supervisor_comment }}"><i class="bi bi-chat-left-text"></i> Note</span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No documents uploaded yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 5. ANNOUNCEMENTS --}}
            <div class="card border-0 shadow-sm bg-light mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3" style="color: #123456;">
                        <i class="bi bi-megaphone-fill text-warning me-2"></i> ANNOUNCEMENTS
                    </h6>
                    @forelse($announcements ?? [] as $ann)
                        <div class="d-flex align-items-start bg-white p-3 rounded border mb-2">
                            <div class="me-3">
                                <span class="badge bg-primary">NEW</span>
                            </div>
                            <div>
                                <h6 class="text-dark fw-bold mb-1">{{ $ann->title }}</h6>
                                <p class="text-muted small mb-1">{{ $ann->content }}</p>
                                <small class="text-secondary" style="font-size: 0.7rem;">
                                    Posted {{ $ann->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="d-flex align-items-center text-muted">
                            <i class="bi bi-info-circle me-2"></i> No announcements at this time.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            
            {{-- 1. PROFILE SUMMARY CARD --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        @if(Auth::user()->profile_image)
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                 alt="Profile" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover;">
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

            {{-- 3. SUPERVISOR WIDGETS --}}
            @if(Auth::user()->role === 'supervisor')
                
                {{-- A. TOTAL EMPLOYEES CARD (With Modal Trigger) --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center p-4">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3">Total Assigned Employees</h6>
                        <h1 class="display-4 fw-bold mb-3 text-dark">{{ $totalTeam ?? 0 }}</h1>
                        <button type="button" class="btn btn-outline-dark btn-sm w-100" data-bs-toggle="modal" data-bs-target="#teamListModal">
                            <i class="bi bi-list-ul me-2"></i> View Details
                        </button>
                    </div>
                </div>

                {{-- B. ATTENDANCE CHART CARD --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold" style="color: #123456;">ATTENDANCE OVERVIEW</h6>
                    </div>
                    <div class="card-body pt-4">
                        <div style="height: 180px; position: relative;" class="mb-3">
                            <canvas id="supervisorTeamChart"></canvas>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-2">
                            <div class="text-center w-100 border-end">
                                <h5 class="fw-bold text-success mb-0">{{ $teamPresent ?? 0 }}</h5>
                                <small class="text-muted" style="font-size: 0.65rem;">PRESENT</small>
                            </div>
                            <div class="text-center w-100 border-end">
                                <h5 class="fw-bold text-warning mb-0">{{ $teamLate ?? 0 }}</h5>
                                <small class="text-muted" style="font-size: 0.65rem;">LATE</small>
                            </div>
                            <div class="text-center w-100">
                                <h5 class="fw-bold text-danger mb-0">{{ $teamAbsent ?? 0 }}</h5>
                                <small class="text-muted" style="font-size: 0.65rem;">ABSENT</small>
                            </div>
                        </div>
                    </div>
                </div>

            @endif

        </div>
    </div>
</div>

@if(Auth::user()->role === 'supervisor')
<div class="modal fade" id="teamListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title fw-bold" style="color: #123456;"><i class="bi bi-people-fill me-2"></i> My Team Members</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($myTeam ?? [] as $member)
                        <div class="list-group-item p-3 d-flex align-items-center">
                            <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-3 border" style="width: 45px; height: 45px;">
                                <span class="fw-bold text-secondary">{{ substr($member->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $member->name }}</h6>
                                <div class="small text-muted">
                                    <span class="badge bg-light text-dark border me-1">{{ ucfirst($member->role) }}</span>
                                    {{ $member->position ?? 'No Position' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-person-x display-4 mb-2 d-block"></i>
                            No employees assigned to your department yet.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('supervisorTeamChart');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Late', 'Absent'],
                    datasets: [{
                        data: [
                            {{ $teamPresent ?? 0 }}, 
                            {{ $teamLate ?? 0 }}, 
                            {{ $teamAbsent ?? 0 }}
                        ],
                        backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    cutout: '70%'
                }
            });
        }
    });
</script>
@endpush
@endsection