@extends('layouts.app')

@section('title', 'Apply for Leave - iManageHR')

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                
                <div class="card-header text-white fw-bold py-3" style="background-color: #123456;">
                    <i class="bi bi-calendar-plus me-2"></i> Apply for Leave / WFH
                </div>

                <div class="card-body p-4">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="/employee/leave/store" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="leave_type" class="form-label fw-medium">Leave Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="leave_type" name="leave_type" required>
                                <option value="" selected disabled>Select leave type...</option>
                                @foreach($leaveTypes as $type)
                                <option value="{{ $type->name }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-medium">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-medium">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label fw-medium">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Please state your reason..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="attachment" class="form-label fw-medium">Attachment (Optional)</label>
                            <input class="form-control" type="file" id="attachment" name="attachment">
                            <small class="text-muted">Supported formats: JPG, PNG, PDF (Max: 2MB)</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/employee/dashboard" class="btn btn-secondary px-4">Cancel</a>
                            <button type="submit" class="btn text-white px-4" style="background-color: #3b6d28ff;">
                                Submit Application
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple logic to ensure End Date is not before Start Date
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    startDate.addEventListener('change', function() {
        endDate.min = this.value;
    });
</script>
@endpush

@endsection