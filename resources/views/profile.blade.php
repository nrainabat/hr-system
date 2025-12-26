@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center g-4">
        
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color: #873260;">
                        <i class="bi bi-person-circle me-2"></i> My Profile
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="editBtn" onclick="enableEdit()">
                        <i class="bi bi-pencil-square"></i> Edit Profile
                    </button>
                </div>
                
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="text-center mb-4">
                            <div class="mb-3 position-relative d-inline-block">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                         class="rounded-circle shadow-sm" 
                                         width="150" height="150" 
                                         style="object-fit: cover; border: 3px solid #873260;"
                                         alt="Profile">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=873260&color=fff&size=150" 
                                         class="rounded-circle shadow-sm" 
                                         alt="Default Profile">
                                @endif
                                
                                <label class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow p-2" 
                                       id="photoUploadBtn" style="cursor: pointer; display: none;">
                                    <i class="bi bi-camera-fill text-dark"></i>
                                    <input type="file" name="profile_image" class="d-none" onchange="previewImage(this)">
                                </label>
                            </div>
                        </div>

                        <fieldset id="profileFields" disabled>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Department</label>
                                    <input type="text" name="department" class="form-control" value="{{ Auth::user()->department }}" placeholder="e.g. IT Department">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Position</label>
                                    <input type="text" name="position" class="form-control" value="{{ Auth::user()->position }}" placeholder="e.g. Senior Developer">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-medium">Role</label>
                                    <input type="text" class="form-control bg-light" value="{{ ucfirst(Auth::user()->role) }}" readonly>
                                    <small class="text-muted">Role cannot be changed.</small>
                                </div>
                            </div>
                        </fieldset>

                        <div id="actionButtons" class="text-end mt-3" style="display: none;">
                            <button type="button" class="btn btn-secondary px-4 me-2" onclick="cancelEdit()">Cancel</button>
                            <button type="submit" class="btn text-white px-4" style="background-color: #873260;">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold" style="color: #873260;">
                        <i class="bi bi-shield-lock me-2"></i> Change Password
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

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

                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-dark">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function enableEdit() {
        // Enable inputs
        document.getElementById('profileFields').disabled = false;
        // Show Save/Cancel buttons
        document.getElementById('actionButtons').style.display = 'block';
        // Show Photo Upload button
        document.getElementById('photoUploadBtn').style.display = 'block';
        // Hide Edit button
        document.getElementById('editBtn').style.display = 'none';
    }

    function cancelEdit() {
        // Reload page to reset data
        location.reload();
    }
</script>
@endsection