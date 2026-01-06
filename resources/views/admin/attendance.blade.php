@extends('layouts.app')

@section('title', 'Employee Attendance')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold" style="color: #123456;">
            <i class="bi bi-clock-history me-2"></i> Employee Attendance Log
        </h4>
        {{-- Optional: Add Date Filter Here --}}
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Employee</th>
                            <th>Date</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceRecords as $record)
                        <tr>
                            <td class="ps-4 fw-bold">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-2 border" style="width: 35px; height: 35px;">
                                        <i class="bi bi-person text-muted"></i>
                                    </div>
                                    <div>
                                        {{ $record->user->name }}
                                        <div class="small text-muted fw-normal">{{ $record->user->department ?? 'No Dept' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                            <td class="text-success fw-bold">
                                {{ \Carbon\Carbon::parse($record->clock_in)->format('h:i A') }}
                            </td>
                            <td class="text-danger fw-bold">
                                @if($record->clock_out)
                                    {{ \Carbon\Carbon::parse($record->clock_out)->format('h:i A') }}
                                @else
                                    <span class="badge bg-warning text-dark">Active</span>
                                @endif
                            </td>
                            <td>
                                @if($record->status == 'Present' && !$record->clock_out)
                                    <span class="badge bg-success">Working</span>
                                @elseif($record->status == 'Completed')
                                    <span class="badge bg-secondary">Completed</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ $record->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x display-4 d-block mb-3 opacity-25"></i>
                                No attendance records found.
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