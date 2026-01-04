@extends('layouts.app')
@section('title', 'Edit Department')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white fw-bold" style="background-color: #123456;">
                    <i class="bi bi-pencil-square me-2"></i> Edit Department
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.org.departments.update', $department->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Department Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.org.departments') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn text-white" style="background-color: #123456;">Update Department</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection