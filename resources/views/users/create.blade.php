@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white fw-bold py-3" style="background-color: #873260;">
                    <i class="bi bi-person-plus-fill me-2"></i> Register New Employee
                </div>
                
                <div class="card-body p-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Ahmad Ali" value="{{ old('name') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="e.g. ahmad.ali" value="{{ old('username') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="e.g. ahmad@company.com" value="{{ old('email') }}" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" required>
                                <small class="text-muted">Must be at least 6 characters.</small>
                            </div>

                            <hr class="my-4">

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">System Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="" selected disabled>Select a Role</option>
                                    <option value="employee">Employee (Standard User)</option>
                                    <option value="intern">Intern</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Department</label>
                                <select name="department" class="form-select" required>
                                    <option value="" selected disabled>Select a Department</option>
                                    {{-- Loop through departments passed from Controller --}}
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}" {{ old('department') == $dept->name ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Position</label>
                                <input type="text" name="position" class="form-control" placeholder="e.g. Senior Developer" value="{{ old('position') }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 gap-2">
                            <a href="/admin/dashboard" class="btn btn-secondary px-4">Cancel</a>
                            <button type="submit" class="btn text-white px-4" style="background-color: #873260;">
                                <i class="bi bi-check-circle me-1"></i> Create User
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection