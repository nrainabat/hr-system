@extends('layouts.app')

@section('content')
<style>
    /* Reuse corporate styles from Admin or main CSS */
    :root { --corp-navy: #0f172a; --corp-border: #e2e8f0; }
    .card-corp { background: #fff; border: 1px solid var(--corp-border); box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .card-kpi { padding: 20px; text-align: center; border-left: 4px solid var(--corp-navy); }
    .table-corp thead th { background: var(--corp-navy); color: #fff; font-weight: normal; letter-spacing: 1px; }
</style>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark border-bottom pb-2">Team Performance Report</h3>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-corp card-kpi">
                <div class="text-uppercase text-muted small mb-1">Present Today</div>
                <div class="display-5 fw-bold text-dark">{{ $present }} <span class="fs-6 text-muted">/ {{ $myTeam->count() }}</span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-corp card-kpi" style="border-left-color: #c29d59;">
                <div class="text-uppercase text-muted small mb-1">Late Arrivals</div>
                <div class="display-5 fw-bold text-dark">{{ $late }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-corp card-kpi" style="border-left-color: #ef4444;">
                <div class="text-uppercase text-muted small mb-1">Absent</div>
                <div class="display-5 fw-bold text-dark">{{ $absent }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-corp h-100">
                <div class="p-3 border-bottom bg-light">
                    <h6 class="mb-0 fw-bold text-uppercase text-secondary">Employee Productivity (This Month)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Employee</th>
                                <th>Role</th>
                                <th class="text-center">Days Worked</th>
                                <th class="text-center">Punctuality</th>
                                <th class="text-end">Overtime (Hrs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teamStats as $member)
                            <tr>
                                <td class="fw-bold">{{ $member['name'] }}</td>
                                <td class="small text-muted">{{ $member['position'] }}</td>
                                <td class="text-center">{{ $member['days_worked'] }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $member['punctuality_score'] }}</span>
                                </td>
                                <td class="text-end fw-bold">{{ number_format($member['overtime'], 1) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-3">No team members found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-corp h-100">
                <div class="p-3 border-bottom bg-light">
                    <h6 class="mb-0 fw-bold text-uppercase text-secondary">Absence Forecast (7 Days)</h6>
                </div>
                <div class="p-3">
                    @if($upcomingLeaves->count() > 0)
                        <ul class="list-group list-group-flush">
                        @foreach($upcomingLeaves as $leave)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <div class="fw-bold text-dark">{{ $leave->user->name }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d') }}</small>
                                </div>
                                <span class="badge bg-secondary rounded-0">{{ $leave->leave_type }}</span>
                            </li>
                        @endforeach
                        </ul>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="far fa-calendar-check fa-2x mb-2"></i>
                            <p class="small">Full attendance expected.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection