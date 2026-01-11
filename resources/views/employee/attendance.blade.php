@extends('layouts.app')

@section('title', 'Attendance History')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">My Attendance History</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/employee/dashboard" class="text-decoration-none" style="color: #123456;">Dashboard</a></li>
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
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceHistory as $record)
                            @php
                                // Calculate Duration for Preview Modal
                                $totalDuration = 'In Progress';
                                if($record->clock_out) {
                                    $start = \Carbon\Carbon::parse($record->clock_in);
                                    $end = \Carbon\Carbon::parse($record->clock_out);
                                    $totalDuration = $start->diff($end)->format('%h hrs %i mins');
                                }
                            @endphp
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
                                    @if($record->status == 'Overtime')
                                        <span class="badge bg-primary">Overtime</span>
                                    @elseif($record->status == 'Late')
                                        <span class="badge bg-warning text-dark">Late</span>
                                    @else
                                        <span class="badge bg-secondary">Completed</span>
                                    @endif
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <button type="button" 
                                    class="btn btn-sm btn-outline-primary preview-btn"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#attendanceModal"
                                    data-date="{{ \Carbon\Carbon::parse($record->date)->format('d F Y') }}"
                                    data-clockin="{{ \Carbon\Carbon::parse($record->clock_in)->format('h:i A') }}"
                                    data-clockout="{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('h:i A') : '--' }}"
                                    data-status="{{ $record->status }}"
                                    data-overtime="{{ $record->overtime_hours ?? 'None' }}"
                                    data-duration="{{ $totalDuration }}">
                                    <i class="bi bi-eye"></i> Preview
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
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

{{-- Attendance Preview Modal --}}
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #123456;">
                <h5 class="modal-title fw-bold"><i class="bi bi-clock-history me-2"></i> Attendance Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                {{-- Details Grid --}}
                <div class="row g-4">
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Date</label>
                        <p id="modal-date" class="fw-bold text-dark fs-5"></p>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Status</label>
                        <p><span id="modal-status" class="badge bg-secondary fs-6"></span></p>
                    </div>

                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Clock In</label>
                        <p id="modal-clockin" class="fw-bold text-success fs-5"></p>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Clock Out</label>
                        <p id="modal-clockout" class="fw-bold text-danger fs-5"></p>
                    </div>
                    
                    {{-- Total Working Hours --}}
                    <div class="col-12">
                        <div class="p-3 bg-light rounded border">
                            <label class="small text-muted fw-bold text-uppercase mb-1">Total Working Hours</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hourglass-split fs-4 me-2 text-dark"></i>
                                <span id="modal-duration" class="fw-bold fs-5 text-dark"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Overtime Section --}}
                    <div class="col-12" id="modal-overtime-container">
                        <div class="p-3 bg-opacity-10 bg-primary rounded border border-primary">
                            <label class="small text-primary fw-bold text-uppercase mb-1">Overtime Duration</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-stopwatch fs-4 me-2 text-primary"></i>
                                <span id="modal-overtime" class="fw-bold fs-5 text-dark"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('attendanceModal');
        modal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;

            // Extract info from data-* attributes
            const date = button.getAttribute('data-date');
            const clockin = button.getAttribute('data-clockin');
            const clockout = button.getAttribute('data-clockout');
            const status = button.getAttribute('data-status');
            const overtime = button.getAttribute('data-overtime');
            const duration = button.getAttribute('data-duration');

            // Update the modal's content
            modal.querySelector('#modal-date').textContent = date;
            modal.querySelector('#modal-clockin').textContent = clockin;
            modal.querySelector('#modal-clockout').textContent = clockout;
            modal.querySelector('#modal-overtime').textContent = overtime;
            modal.querySelector('#modal-duration').textContent = duration;
            
            // Handle Overtime Visibility
            const otContainer = modal.querySelector('#modal-overtime-container');
            if(overtime === 'None' || overtime === '') {
                otContainer.style.display = 'none';
            } else {
                otContainer.style.display = 'block';
            }
            
            // Handle Status Badge Color
            const statusSpan = modal.querySelector('#modal-status');
            statusSpan.textContent = status;
            statusSpan.className = 'badge fs-6 ';
            
            if (status === 'Present') statusSpan.classList.add('bg-success');
            else if (status === 'Late') statusSpan.classList.add('bg-warning', 'text-dark');
            else if (status === 'Overtime') statusSpan.classList.add('bg-primary');
            else if (status === 'Half Day') statusSpan.classList.add('bg-info', 'text-dark');
            else statusSpan.classList.add('bg-secondary');
        });
    });
</script>
@endpush

@endsection