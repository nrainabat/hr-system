@extends('layouts.app')

@section('title', 'My Leave History')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">My Leave Applications</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/employee/dashboard" class="text-decoration-none" style="color: #123456;">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Leave History</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header text-white fw-bold py-3" style="background-color: #2D3748;">
            <i class="bi bi-list-task me-2"></i> Application List
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Applied Date</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                        <tr>
                            <td class="ps-4">
                                {{ $leave->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <span class="fw-bold">{{ ucfirst($leave->leave_type) }}</span>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }} 
                                <span class="text-muted mx-1">to</span> 
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">
                                    ({{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }} Days)
                                </small>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $leave->reason }}">
                                    {{ $leave->reason }}
                                </span>
                            </td>
                            <td>
                                @if($leave->status === 'approved')
                                    <span class="badge bg-success">APPROVED</span>
                                @elseif($leave->status === 'rejected')
                                    <span class="badge bg-danger">REJECTED</span>
                                @else
                                    <span class="badge bg-warning text-dark">PENDING</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No leave applications found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection