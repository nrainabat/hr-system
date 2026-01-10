@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                
                {{-- Card Header --}}
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color: #123456;">
                        <i class="bi bi-person-circle me-2"></i> My Profile
                    </h5>
                    
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-warning fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#passwordModal">
                            <i class="bi bi-key-fill"></i> Change Password
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" id="editBtn" onclick="enableEdit()">
                            <i class="bi bi-pencil-square"></i> Edit Profile
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- LEFT COLUMN: Profile Image & Summary --}}
                            <div class="col-md-4 text-center border-end">
                                <div class="mb-3 position-relative d-inline-block">
                                    {{-- Profile Image Logic --}}
                                    @if(Auth::user()->profile_image)
                                        <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                             class="rounded-circle shadow-sm" 
                                             width="180" height="180" 
                                             style="object-fit: cover; border: 4px solid #123456;"
                                             alt="Profile">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=123456&color=fff&size=180" 
                                             class="rounded-circle shadow-sm" 
                                             alt="Default Profile">
                                    @endif
                                    
                                    {{-- Upload Button (Hidden initially) --}}
                                    <label class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow p-2" 
                                           id="photoUploadBtn" style="cursor: pointer; display: none;">
                                        <i class="bi bi-camera-fill text-dark fs-5"></i>
                                        <input type="file" name="profile_image" class="d-none" onchange="this.form.submit()">
                                    </label>
                                </div>
                                <h4 class="fw-bold mt-2">{{ Auth::user()->name }}</h4>
                                <p class="text-muted mb-0">{{ Auth::user()->position ?? 'No Position' }}</p>
                                <small class="text-muted">{{ Auth::user()->department ?? 'No Department' }}</small>
                            </div>

                            {{-- RIGHT COLUMN: User Details Form --}}
                            <div class="col-md-8 ps-md-5">
                                <fieldset disabled id="profileFields">
                                    <div class="row g-3">
                                        
                                        {{-- 1. Basic Info --}}
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Full Name</label>
                                            <input type="text" name="name" class="form-control editable" value="{{ Auth::user()->name }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Username</label>
                                            <input type="text" name="username" class="form-control editable" value="{{ Auth::user()->username }}" required>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Email Address</label>
                                            <input type="email" name="email" class="form-control editable" value="{{ Auth::user()->email }}" required>
                                        </div>

                                        {{-- 2. Additional Details: Phone & Gender --}}
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Phone Number</label>
                                            <input type="text" name="phone_number" class="form-control editable" 
                                                   value="{{ Auth::user()->phone_number }}" placeholder="e.g. +60123456789">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Gender</label>
                                            <select name="gender" class="form-select editable">
                                                <option value="" disabled {{ !Auth::user()->gender ? 'selected' : '' }}>Select Gender</option>
                                                <option value="Male" {{ Auth::user()->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ Auth::user()->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>

                                        {{-- 3. About Me --}}
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">About Me</label>
                                            <textarea name="about" class="form-control editable" rows="3" 
                                                      placeholder="Brief bio about yourself...">{{ Auth::user()->about }}</textarea>
                                        </div>

                                        {{-- 4. Address --}}
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Residential Address</label>
                                            <textarea name="address" class="form-control editable" rows="2" 
                                                      placeholder="Full residential address...">{{ Auth::user()->address }}</textarea>
                                        </div>

                                        <hr class="my-4">

                                        {{-- 5. Read-Only System Fields --}}
                                        
                                        {{-- NEW: Employment Information Header --}}
                                        <div class="col-12">
                                            <h6 class="text-muted text-uppercase fw-bold small border-bottom pb-2">Employment Information</h6>
                                        </div>

                                        {{-- NEW: Start Date --}}
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted">Start Date</label>
                                            <input type="text" class="form-control bg-light" 
                                                   value="{{ Auth::user()->start_date ? \Carbon\Carbon::parse(Auth::user()->start_date)->format('d M Y') : 'N/A' }}" 
                                                   readonly>
                                        </div>

                                        {{-- NEW: End Date --}}
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted">End Date</label>
                                            <input type="text" class="form-control bg-light" 
                                                   value="{{ Auth::user()->end_date ? \Carbon\Carbon::parse(Auth::user()->end_date)->format('d M Y') : 'Permanent' }}" 
                                                   readonly>
                                        </div>

                                        {{-- Existing Read-Only Fields --}}
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted">Department</label>
                                            <input type="text" class="form-control bg-light" value="{{ Auth::user()->department }}" readonly>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted">Position</label>
                                            <input type="text" class="form-control bg-light" value="{{ Auth::user()->position }}" readonly>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted">Supervisor Assigned</label>
                                            <input type="text" class="form-control bg-light" 
                                                   value="{{ Auth::user()->supervisor ? Auth::user()->supervisor->name : 'Not Assigned' }}" 
                                                   readonly>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted">Role</label>
                                            <input type="text" class="form-control bg-light" value="{{ ucfirst(Auth::user()->role) }}" readonly>
                                        </div>
                                    </div>
                                </fieldset>

                                {{-- Action Buttons (Hidden by default) --}}
                                <div id="actionButtons" class="text-end mt-4" style="display: none;">
                                    <button type="button" class="btn btn-secondary px-4 me-2" onclick="cancelEdit()">Cancel</button>
                                    <button type="submit" class="btn text-white px-4" style="background-color: #123456;">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Password Change Modal --}}
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #123456;">
                <h5 class="modal-title fw-bold"><i class="bi bi-shield-lock me-2"></i> Change Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-medium">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function enableEdit() {
        // 1. Enable the Fieldset (unlocks everything temporarily)
        const fieldset = document.getElementById('profileFields');
        fieldset.disabled = false;

        // 2. Lock fields that should NOT be editable (those without 'editable' class)
        const readOnlyInputs = fieldset.querySelectorAll('input:not(.editable), select:not(.editable), textarea:not(.editable)');
        readOnlyInputs.forEach(input => {
            input.readOnly = true; 
            if(input.tagName === 'SELECT') input.disabled = true; // Selects need disabled, not readOnly
            input.classList.add('bg-light');
        });

        // 3. Show Buttons
        document.getElementById('actionButtons').style.display = 'block';
        document.getElementById('photoUploadBtn').style.display = 'block';
        document.getElementById('editBtn').style.display = 'none';
    }

    function cancelEdit() {
        location.reload();
    }

    // Automatically re-open modal if there are password errors
    @if($errors->has('current_password') || $errors->has('new_password'))
        var myModal = new bootstrap.Modal(document.getElementById('passwordModal'));
        myModal.show();
    @endif
</script>
@endsection