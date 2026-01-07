@extends('layouts.app')
@section('title', 'Leave Types')
@section('content')
<div class="container py-4">
    
    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Add Form --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white fw-bold" style="background-color: #123456;">
                    Add Leave Type
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.leave.types.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>Type Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Annual Leave" required>
                        </div>
                        <button class="btn w-100 text-white" style="background-color: #123456;">Add</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- List --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Current Leave Types</div>
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($types as $type)
                        <tr>
                            <td>{{ $type->name }}</td>
                            <td class="text-end">
                                {{-- Edit Button --}}
                                <button type="button" 
                                        class="btn btn-sm btn-outline-warning me-1 edit-btn"
                                        data-id="{{ $type->id }}"
                                        data-name="{{ $type->name }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editTypeModal">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                {{-- Delete Form --}}
                                <form action="{{ route('admin.leave.types.delete', $type->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark">Edit Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTypeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Type Name</label>
                        <input type="text" name="name" id="modal-type-name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editTypeModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            
            // Update the modal's content.
            const inputName = editModal.querySelector('#modal-type-name');
            const form = editModal.querySelector('#editTypeForm');
            
            inputName.value = name;
            // Update form action URL dynamically
            form.action = `{{ url('admin/leave/types') }}/${id}`;
        });
    });
</script>
@endpush

@endsection