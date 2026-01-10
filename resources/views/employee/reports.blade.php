@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 border-bottom border-2 border-dark pb-2">
            <h4 class="text-uppercase fw-bold text-dark" style="letter-spacing: 1px;">My Performance Record</h4>
            <span class="text-muted small">Analytics for {{ date('F Y') }}</span>
        </div>
    </div>

    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="p-4 bg-white border shadow-sm h-100 d-flex flex-column justify-content-between">
                <small class="text-uppercase text-secondary fw-bold">Attendance Rate</small>
                <div class="mt-2">
                    <h1 class="display-4 fw-bold text-dark mb-0">{{ $daysWorked }}</h1>
                    <span class="text-muted small">Days worked this month</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-white border shadow-sm h-100 d-flex flex-column justify-content-between">
                <small class="text-uppercase text-secondary fw-bold">Overtime Output</small>
                <div class="mt-2">
                    <h1 class="display-4 fw-bold text-dark mb-0">{{ $totalOvertime }}</h1>
                    <span class="text-muted small">Total Hours accumulated</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-navy text-white shadow-sm h-100 d-flex flex-column justify-content-between" style="background-color: #0f172a;">
                <small class="text-uppercase text-white-50 fw-bold">Avg. Daily Hours</small>
                <div class="mt-2">
                    <h1 class="display-4 fw-bold mb-0">{{ $avgHours }}</h1>
                    <span class="text-white-50 small">Hours / Day</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold text-uppercase border-bottom">Leave Balance Utilization</div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Leave Type</th>
                                <th class="text-end pe-4">Used (YTD)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leavesTaken as $leave)
                            <tr>
                                <td class="ps-4">{{ $leave->leave_type }}</td>
                                <td class="text-end pe-4 fw-bold">{{ $leave->count }} Days</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-muted">No leave taken this year.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold text-uppercase border-bottom">Recent Attendance Logs</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($recentAttendance as $log)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($log->date)->format('d M, Y') }}</span>
                                <br>
                                <small class="text-muted">In: {{ $log->clock_in ?? '--:--' }} | Out: {{ $log->clock_out ?? '--:--' }}</small>
                            </div>
                            @if($log->status == 'Present')
                                <span class="badge bg-success rounded-0">Present</span>
                            @else
                                <span class="badge bg-warning text-dark rounded-0">{{ $log->status }}</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection