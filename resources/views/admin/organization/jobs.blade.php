@extends('layouts.app')

@section('content')
<div class="container-fluid p-4"> 
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-bold" style="background-color: #e9ecef; color: #333;">
                    <i class="bi bi bi-briefcase-fill me-2"></i> Add Job Position
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.org.jobs.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold small text-uppercase">Position Name</label>
                            <input type="text" name="title" class="form-control p-2" placeholder="e.g. Staff" required>
                        </div>

                        <button type="submit" class="btn w-100 text-white py-2" style="background-color: #0d2e4e;">
                            <i class="bi bi-plus-lg me-1"></i> Add Job Position
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
             <h5 class="fw-bold mb-3" style="color: #123456;">
                <i class="bi bi-list-ul me-2"></i> Available Job Position 
            </h5>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Job Title</th>
                                <th>Created Date</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $item)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $item->title }}</td>
                                <td class="text-muted small">
                                    {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm me-1 edit-btn"
                                            data-id="{{ $item->id }}"
                                            data-title="{{ $item->title }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editJobModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <form action="{{ route('admin.org.jobs.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job position?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No job positions found. Start by adding one on the left!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editJobForm" method="POST">
                @csrf
                @method('PUT') 
                <div class="modal-header" style="background-color: #0d2e4e; color: white;">
                    <h5 class="modal-title">Edit Job Position</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Job Title</label>
                        <input type="text" name="title" id="modal_job_title" class="form-control" required>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background-color: #0d2e4e;">Update Job</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editForm = document.getElementById('editJobForm');
        const titleInput = document.getElementById('modal_job_title');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');

                // Update the form action URL dynamically
                editForm.action = `/admin/organization/jobs/${id}`;
                titleInput.value = title;
            });
        });
    });
</script>

@endsection