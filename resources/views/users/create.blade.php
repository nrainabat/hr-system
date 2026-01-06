@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white fw-bold py-3" style="background-color: #123456;">
                    <i class="bi bi-person-plus-fill me-2"></i> Register New Employee
                </div>
                
                <div class="card-body p-4">
                    
                    {{-- 1. Display Validation Errors (If any) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
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
                            {{-- Basic Info --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>

                            {{-- Phone & Gender --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" placeholder="+60..." value="{{ old('phone_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="" selected disabled>Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            {{-- About & Address --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">About User (Bio)</label>
                                <textarea name="about" class="form-control" rows="2">{{ old('about') }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Residential Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                            </div>

                            <hr class="my-4">

                            {{-- Role & Dept --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">System Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="" selected disabled>Select a Role</option>
                                    <option value="employee">Employee</option>
                                    <option value="intern">Intern</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Department</label>
                                <select name="department" class="form-select" required>
                                    <option value="" selected disabled>Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Position</label>
                                <select name="position" class="form-select">
                                <option value="" selected disabled>Select Position</option>
                                @foreach($positions as $job)
                                    <option value="{{ $job->title }}" {{ old('position') == $job->title ? 'selected' : '' }}>
                                        {{ $job->title }}
                                    </option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 gap-2">
                            <a href="/admin/dashboard" class="btn btn-secondary px-4">Cancel</a>
                            <button type="submit" class="btn text-white px-4" style="background-color: #123456;">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. Success Modal HTML --}}
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4 border-0 shadow">
            <div class="modal-body">
                <div class="mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-check-lg text-success display-3"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2 text-dark">Registration Successful!</h4>
                <p class="text-muted mb-4">{{ session('success') }}</p>
                <button type="button" class="btn btn-dark px-5 py-2 fw-bold" data-bs-dismiss="modal" style="background-color: #123456;">Okay</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- 3. Script to trigger Modal --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success'))
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @endif
    });
</script>
@endpush

@endsection