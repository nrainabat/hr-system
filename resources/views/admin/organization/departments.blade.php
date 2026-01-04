@extends('layouts.app')
@section('title', 'Departments')
@section('content')
<div class="container py-4">
    <div class="row">
        {{-- ADD FORM --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header text-white fw-bold" style="background-color: #123456;">
                    <i class="bi bi-building-add me-2"></i> Add Department
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.org.departments.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Department Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Human Resources" required>
                        </div>
                        <button type="submit" class="btn text-white w-100" style="background-color: #123456;">
                        <i class="bi bi-plus-lg"></i> Add Department
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- LIST --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-list-ul me-2"></i> Current Departments
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Manager</th>
                                <th class="text-center">Employees</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments as $dept)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $dept->name }}</td>
                                <td class="text-muted">{{ $dept->manager_name ?? '-' }}</td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $dept->user_count }}</span></td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.org.departments.delete', $dept->id) }}" method="POST" onsubmit="return confirm('Delete this department?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No departments found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection