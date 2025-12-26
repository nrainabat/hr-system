@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold" style="color: #873260;">My Profile</h5>
                </div>
                
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="text-center mb-4">
                            <div class="mb-3">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                         class="rounded-circle shadow-sm" 
                                         width="150" height="150" 
                                         style="object-fit: cover; border: 3px solid #873260;"
                                         alt="Profile Picture">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=873260&color=fff&size=150" 
                                         class="rounded-circle shadow-sm" 
                                         alt="Default Profile">
                                @endif
                            </div>
                            
                            <label class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-camera"></i> Change Picture
                                <input type="file" name="profile_image" class="d-none" onchange="this.form.submit()">
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control bg-light" value="{{ ucfirst(Auth::user()->role) }}" readonly>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn text-white px-4" style="background-color: #873260;">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection