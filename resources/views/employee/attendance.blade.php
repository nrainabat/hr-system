@extends('layouts.app')

@section('title', 'Attendance History')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">My Attendance History</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/employee/dashboard" class="text-decoration-none" style="color: #873260;">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Attendance</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header text-white fw-bold py-3" style="background-color: #123456;">
            <i class="bi bi-calendar-check me-2"></i> Attendance Records
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Working Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceHistory as $record)
                        <tr>
                            <td class="ps-4">
                                {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($record->date)->format('l') }}</small>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ \Carbon\Carbon::parse($record->clock_in)->format('h:i A') }}
                                </span>
                            </td>

                            <td>
                                @if($record->clock_out)
                                    <span class="badge bg-light text-dark border">
                                        {{ \Carbon\Carbon::parse($record->clock_out)->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">-- : --</span>
                                @endif
                            </td>
                            
                            <td>
                                @if($record->clock_out)
                                    @php
                                        $in = \Carbon\Carbon::parse($record->clock_in);
                                        $out = \Carbon\Carbon::parse($record->clock_out);
                                        // Calculate total minutes
                                        $minutes = $in->diffInMinutes($out);
                                        // Convert to hours with decimals
                                        $hours = $minutes / 60;
                                    @endphp
                                    <span class="text-dark">
                                        {{ number_format($hours, 2) }} hrs
                                    </span>
                                @else
                                    <span class="text-warning">Ongoing</span>
                                @endif
                            </td>

                            <td>
                                @if(is_null($record->clock_out))
                                    <span class="badge bg-success">Working</span>
                                @else
                                    <span class="badge bg-secondary">Completed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x display-4 d-block mb-3"></i>
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