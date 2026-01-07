@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0 pt-3 pb-2">
                    <h5 class="card-title text-dark">
                        <i class="bi bi-buildings me-2"></i> Add Department
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.org.departments.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold small text-uppercase">Department Name</label>
                            <input type="text" name="name" class="form-control p-2" placeholder="e.g. Human Resources" required>
                        </div>

                        <button type="submit" class="btn w-100 text-white fw-bold py-2" style="background-color: #0d2e4e;">
                            <i class="bi bi-plus-lg me-1"></i> Add Department
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-2">
                    <h5 class="card-title text-dark">
                        <i class="bi bi-list-ul me-2"></i> Current Departments
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Created Date</th>
                                <th class="text-center">Employees</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments as $dept)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $dept->name }}</td>
                                <td class="text-muted small">
                                    {{ $dept->created_at ? $dept->created_at->format('d M Y') : '-' }}
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border border-secondary rounded-pill px-3">
                                        {{ $dept->employees_count ?? 0 }}
                                    </span>
                                </td>

                                <td class="text-end pe-4">
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm me-1 edit-btn"
                                            data-id="{{ $dept->id }}"
                                            data-name="{{ $dept->name }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editDepartmentModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <form action="{{ route('admin.org.departments.destroy', $dept->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
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
                                    No departments found. Start by adding one!
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

<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editDepartmentForm" method="POST">
                @csrf
                @method('PUT') 
                
                <div class="modal-header" style="background-color: #0d2e4e; color: white;">
                    <h5 class="modal-title fw-bold">Edit Department</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" name="name" id="modal_department_name" class="form-control" required>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn fw-bold text-white" style="background-color: #0d2e4e;">Update Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editForm = document.getElementById('editDepartmentForm');
        const nameInput = document.getElementById('modal_department_name');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                // Update the form action URL dynamically
                // Ensure this matches your route prefix (e.g., /admin/organization/departments/)
                editForm.action = `/admin/organization/departments/${id}`;
                nameInput.value = name;
            });
        });
    });
</script>

@endsection