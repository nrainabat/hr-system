@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                
                {{-- Card Header --}}
                <div class="card-header text-white fw-bold py-3" style="background-color: #123456;">
                    <i class="bi bi-pencil-square me-2"></i> Edit Employee Details
                </div>
                
                <div class="card-body p-4">
                    {{-- Display Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- MAIN FORM: User Details --}}
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            {{-- Basic Info --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>

                            {{-- Phone & Gender --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" placeholder="+60..." value="{{ old('phone_number', $user->phone_number) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="" disabled>Select Gender</option>
                                    <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            {{-- About & Address --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">About User (Bio)</label>
                                <textarea name="about" class="form-control" rows="2">{{ old('about', $user->about) }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Residential Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
                            </div>

                            <hr class="my-4">

                            {{-- Role & Dept --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">System Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Employee</option>
                                    <option value="intern" {{ old('role', $user->role) == 'intern' ? 'selected' : '' }}>Intern</option>
                                    <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Department</label>
                                <select name="department" class="form-select">
                                    <option value="" disabled>Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}" {{ old('department', $user->department) == $dept->name ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Position</label>
                                <select name="position" class="form-select">
                                <option value="" disabled>Select Position</option>
                                @foreach($positions as $job)
                                <option value="{{ $job->title }}" {{ old('position', $user->position) == $job->title ? 'selected' : '' }}>
                                    {{ $job->title }}
                                </option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- ACTION BUTTONS ROW --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            {{-- LEFT: Change Password Button --}}
                            <button type="button" class="btn btn-warning text-dark fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#passwordModal">
                                <i class="bi bi-key-fill me-1"></i> Change Password
                            </button>

                            {{-- RIGHT: Cancel & Update Buttons --}}
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.directory') }}" class="btn btn-secondary px-4">Cancel</a>
                                {{-- GREEN & BOLD BUTTON --}}
                                <button type="submit" class="btn btn-success px-4 fw-bold">Update Details</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CHANGE PASSWORD MODAL --}}
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #123456;">
                <h5 class="modal-title fw-bold"><i class="bi bi-shield-lock me-2"></i> Reset Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('admin.users.password', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body p-4">
                    <div class="alert alert-warning small">
                        <i class="bi bi-info-circle-fill me-1"></i> This will forcefully reset the user's password.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" required>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">Save New Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script to auto-open modal if there are password errors --}}
<script>
    @if($errors->has('password'))
        var myModal = new bootstrap.Modal(document.getElementById('passwordModal'));
        myModal.show();
    @endif
</script>
@endsection