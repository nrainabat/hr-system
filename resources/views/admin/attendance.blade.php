@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold" style="color: #123456;">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i> Employee Attendance Log
        </h4>
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
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceRecords as $record)
                            {{-- Calculate Duration Logic --}}
                            @php
                                $totalDuration = 'In Progress';
                                if($record->clock_out) {
                                    $start = \Carbon\Carbon::parse($record->clock_in);
                                    $end = \Carbon\Carbon::parse($record->clock_out);
                                    $totalDuration = $start->diff($end)->format('%h hrs %i mins');
                                }
                            @endphp

                        <tr>
                            <td class="ps-4 fw-bold">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-2 border" style="width: 35px; height: 35px;">
                                        <span class="text-muted fw-bold">{{ substr($record->user->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        {{ $record->user->name ?? 'Unknown' }}
                                        <div class="small text-muted fw-normal">{{ $record->user->department ?? '-' }}</div>
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
                                    <span class="badge bg-light text-dark border">Active</span>
                                @endif
                            </td>
                            {{-- Status Column --}}
                            <td>
                                @if($record->status == 'Present')
                                    <span class="badge bg-success">Present</span>
                                @elseif($record->status == 'Late')
                                    <span class="badge bg-warning text-dark">Late</span>
                                @elseif($record->status == 'Overtime')
                                    <span class="badge bg-primary">Overtime</span>
                                @elseif($record->status == 'Half Day')
                                    <span class="badge bg-info text-dark">Half Day</span>
                                @else
                                    <span class="badge bg-secondary">{{ $record->status }}</span>
                                @endif
                            </td>
                            
                            {{-- Action Column --}}
                            <td class="text-end pe-4">
                                <button type="button" 
                                    class="btn btn-sm btn-outline-primary preview-btn"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#attendanceModal"
                                    data-name="{{ $record->user->name ?? 'Unknown' }}"
                                    data-dept="{{ $record->user->department ?? 'N/A' }}"
                                    data-date="{{ \Carbon\Carbon::parse($record->date)->format('d F Y') }}"
                                    data-clockin="{{ \Carbon\Carbon::parse($record->clock_in)->format('h:i A') }}"
                                    data-clockout="{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('h:i A') : '--' }}"
                                    data-status="{{ $record->status }}"
                                    data-overtime="{{ $record->overtime_hours ?? 'None' }}"
                                    data-duration="{{ $totalDuration }}"> {{-- Pass Duration Here --}}
                                    <i class="bi bi-eye"></i> Preview
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No attendance records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #123456;">
                <h5 class="modal-title fw-bold"><i class="bi bi-clock-history me-2"></i> Attendance Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                {{-- Employee Info --}}
                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded border">
                    <div class="rounded-circle bg-white d-flex justify-content-center align-items-center me-3 border" style="width: 50px; height: 50px;">
                        <i class="bi bi-person-fill fs-3 text-muted"></i>
                    </div>
                    <div>
                        <h5 id="modal-name" class="fw-bold mb-0"></h5>
                        <small id="modal-dept" class="text-muted"></small>
                    </div>
                </div>

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
                    
                    {{-- NEW: Total Working Hours --}}
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

{{-- Script to Populate Modal --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('attendanceModal');
        modal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;

            // Extract info from data-* attributes
            const name = button.getAttribute('data-name');
            const dept = button.getAttribute('data-dept');
            const date = button.getAttribute('data-date');
            const clockin = button.getAttribute('data-clockin');
            const clockout = button.getAttribute('data-clockout');
            const status = button.getAttribute('data-status');
            const overtime = button.getAttribute('data-overtime');
            const duration = button.getAttribute('data-duration'); // <--- New Data

            // Update the modal's content
            modal.querySelector('#modal-name').textContent = name;
            modal.querySelector('#modal-dept').textContent = dept;
            modal.querySelector('#modal-date').textContent = date;
            modal.querySelector('#modal-clockin').textContent = clockin;
            modal.querySelector('#modal-clockout').textContent = clockout;
            modal.querySelector('#modal-overtime').textContent = overtime;
            modal.querySelector('#modal-duration').textContent = duration; // <--- Update Text
            
            // Handle Overtime Visibility (Optional clean up)
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