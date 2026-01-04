@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-3" style="color: #123456;">
        <i class="bi bi-envelope-exclamation me-2"></i> Pending Leave Requests
    </h4>
    
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Employee</th>
                        <th>Type</th>
                        <th>Dates</th>
                        <th>Duration</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $leave)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px;">
                                    <i class="bi bi-person text-muted"></i>
                                </div>
                                <div>
                                    {{ $leave->user->name }}
                                    <div class="small text-muted fw-normal">{{ $leave->user->department ?? 'No Dept' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-secondary">{{ ucfirst($leave->leave_type) }}</span></td>
                        <td class="small">
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} 
                            <i class="bi bi-arrow-right mx-1 text-muted"></i> 
                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                        </td>
                        <td class="small text-muted">
                            {{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }} Days
                        </td>
                        <td class="text-end pe-4">
                            {{-- PREVIEW BUTTON --}}
                            <button type="button" 
                                class="btn btn-sm btn-outline-primary preview-btn"
                                data-bs-toggle="modal" 
                                data-bs-target="#leaveDetailsModal"
                                data-id="{{ $leave->id }}"
                                data-name="{{ $leave->user->name }}"
                                data-dept="{{ $leave->user->department ?? 'N/A' }}"
                                data-type="{{ ucfirst($leave->leave_type) }}"
                                data-start="{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}"
                                data-end="{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}"
                                data-days="{{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }}"
                                data-reason="{{ $leave->reason }}"
                                data-attachment="{{ $leave->attachment ? asset('storage/'.$leave->attachment) : '' }}"
                                title="View Details">
                                <i class="bi bi-eye me-1"></i> Preview
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle display-4 d-block mb-3 opacity-25 text-success"></i>
                            No pending leave requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ==================== LEAVE DETAILS MODAL ==================== --}}
<div class="modal fade" id="leaveDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            {{-- Header --}}
            <div class="modal-header text-white" style="background-color: #123456;">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-text me-2"></i> Leave Application Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- Body --}}
            <div class="modal-body p-4">
                {{-- Employee Info --}}
                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                    <div class="rounded-circle bg-white d-flex justify-content-center align-items-center me-3 border" style="width: 50px; height: 50px;">
                        <i class="bi bi-person-fill fs-3 text-muted"></i>
                    </div>
                    <div>
                        <h5 id="modal-user-name" class="fw-bold mb-0"></h5>
                        <small id="modal-user-dept" class="text-muted"></small>
                    </div>
                </div>

                {{-- Leave Details Grid --}}
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Leave Type</label>
                        <p id="modal-leave-type" class="fw-bold text-dark"></p>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Duration</label>
                        <p id="modal-duration" class="fw-bold text-dark"></p>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Date Range</label>
                        <p class="fw-bold text-dark">
                            <span id="modal-start-date"></span> 
                            <i class="bi bi-arrow-right mx-2 text-muted"></i> 
                            <span id="modal-end-date"></span>
                        </p>
                    </div>
                </div>

                {{-- Reason --}}
                <div class="mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Reason</label>
                    <div class="p-3 bg-light rounded border-start border-4 border-primary">
                        <p id="modal-reason" class="mb-0 text-dark fst-italic"></p>
                    </div>
                </div>

                {{-- Attachment Link (Hidden by default) --}}
                <div id="modal-attachment-section" class="d-none mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Attachment</label>
                    <a href="#" id="modal-attachment-link" target="_blank" class="d-block mt-1 text-decoration-none">
                        <i class="bi bi-paperclip me-1"></i> View Attached Document
                    </a>
                </div>
            </div>

            {{-- Footer (Actions) --}}
            <div class="modal-footer bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                
                <div class="d-flex gap-2">
                    {{-- Reject Button Form --}}
                    <form id="form-reject" action="" method="POST" onsubmit="return confirm('Are you sure you want to REJECT this request?');">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-x-circle me-1"></i> Reject
                        </button>
                    </form>

                    {{-- Approve Button Form --}}
                    <form id="form-approve" action="" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-circle me-1"></i> Approve
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewBtns = document.querySelectorAll('.preview-btn');
        
        previewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // 1. Get Data from data attributes
                const id = this.dataset.id;
                const name = this.dataset.name;
                const dept = this.dataset.dept;
                const type = this.dataset.type;
                const start = this.dataset.start;
                const end = this.dataset.end;
                const days = this.dataset.days;
                const reason = this.dataset.reason;
                const attachment = this.dataset.attachment;

                // 2. Populate Modal Fields
                document.getElementById('modal-user-name').textContent = name;
                document.getElementById('modal-user-dept').textContent = dept;
                document.getElementById('modal-leave-type').textContent = type;
                document.getElementById('modal-start-date').textContent = start;
                document.getElementById('modal-end-date').textContent = end;
                document.getElementById('modal-duration').textContent = days + ' Days';
                document.getElementById('modal-reason').textContent = reason;

                // 3. Handle Attachment
                const attachSection = document.getElementById('modal-attachment-section');
                const attachLink = document.getElementById('modal-attachment-link');
                
                if(attachment) {
                    attachSection.classList.remove('d-none');
                    attachLink.href = attachment;
                } else {
                    attachSection.classList.add('d-none');
                }

                // 4. Update Form Actions (Approve/Reject)
                // Base URL for leave status update
                const baseUrl = "{{ url('admin/leave/requests') }}"; 
                document.getElementById('form-approve').action = `${baseUrl}/${id}`;
                document.getElementById('form-reject').action = `${baseUrl}/${id}`;
            });
        });
    });
</script>
@endpush

@endsection