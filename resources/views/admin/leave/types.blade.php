@extends('layouts.app')
@section('title', 'Leave Types')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- ADD FORM --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header text-white fw-bold" style="background-color: #123456;">
                    Add Leave Type
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.leave.types.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Type Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Annual Leave" required>
                        </div>
                        <button type="submit" class="btn text-white w-100" style="background-color: #123456;">
                        <i class="bi bi-plus-lg"></i> Add Leave Types
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- LIST --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold text-dark">Current Leave Types</div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($types as $type)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $type->name }}</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        {{-- Edit Button (Triggers Modal) --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTypeModal{{ $type->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.leave.types.delete', $type->id) }}" method="POST" onsubmit="return confirm('Delete this leave type?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- EDIT MODAL (POP-UP) --}}
                            <div class="modal fade" id="editTypeModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        {{-- Modal Header --}}
                                        <div class="modal-header text-white" style="background-color: #123456;">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-pencil-square me-2"></i> Edit Leave Type
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        
                                        <div class="modal-body">
                                            <form action="{{ route('admin.leave.types.update', $type->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Type Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $type->name }}" required>
                                                </div>

                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    {{-- "UPDATE" BUTTON IS GREEN AND BOLD --}}
                                                    <button type="submit" class="btn btn-success">
                                                        Update Leave Type
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End Modal --}}

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection