@extends('layouts.app')

@section('title', 'Employee Directory')

@section('content')
<div class="container-fluid px-4">
    
    {{-- 1. Header & Actions --}}
    <div class="row align-items-center my-3">
        <div class="col-md-6">
            <h4 class="fw-bold mb-0" style="color: #123456;">
                <i class="bi bi-person-lines-fill me-2"></i>Employee Directory
            </h4>
        </div>
        <div class="col-md-6 mt-2 mt-md-0">
            <div class="d-flex gap-2 justify-content-md-end">
                <form action="{{ route('admin.directory') }}" method="GET" class="flex-grow-1" style="max-width: 250px;">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control border-end-0" name="search" placeholder="Search..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary border-start-0" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.users.create') }}" class="btn btn-sm text-white shadow-sm d-flex align-items-center" style="background-color: #123456;">
                        <i class="bi bi-plus-lg me-1"></i> Add New
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- SUCCESS MESSAGE ALERT --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- 2. User Grid --}}
    <div class="row g-3">
        @forelse($users as $user)
            {{-- ... existing user card code ... --}}
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-body text-center p-3">
                        
                        {{-- Grid Profile Picture --}}
                        <div class="mb-2">
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center shadow-sm overflow-hidden" style="width: 70px; height: 70px; border: 2px solid #123456;">
                                @if($user->profile_image)
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" class="w-100 h-100 object-fit-cover" alt="Profile">
                                @else
                                    <i class="bi bi-person-fill display-6" style="color: #123456;"></i>
                                @endif
                            </div>
                        </div>

                        <h6 class="fw-bold mb-1 text-truncate" title="{{ $user->name }}">{{ $user->name }}</h6>
                        <p class="text-muted mb-2 small text-truncate">{{ $user->position ?? 'No Position' }}</p>

                        <hr class="my-2 opacity-25">

                        <div class="text-start px-1">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-envelope text-muted me-2 small"></i>
                                <span class="text-truncate small" style="max-width: 100%;" title="{{ $user->email }}">{{ $user->email }}</span>
                            </div>
                            
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-telephone text-muted me-2 small"></i>
                                <span class="small">{{ $user->phone_number ?? '-' }}</span>
                            </div>

                             <div class="d-flex align-items-center">
                                <i class="bi bi-building text-muted me-2 small"></i>
                                <span class="small text-truncate">{{ $user->department ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-top-0 p-3 pt-0 d-flex align-items-center justify-content-between">
                        <button type="button" 
                            class="btn btn-sm btn-outline-primary rounded-pill px-3 view-profile-btn" 
                            data-user-id="{{ $user->id }}"
                            style="font-size: 0.75rem;">
                            View Profile
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-center shadow-sm border-0 py-4">
                    <i class="bi bi-search fs-1 text-muted mb-2 d-block"></i>
                    <h6 class="text-muted">No employees found.</h6>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $users->onEachSide(1)->links() }}
        </div>
    </div>
</div>

{{-- ... existing modal code ... --}}
@include('admin.directory_modal') 
{{-- (Assuming you kept the modal code at the bottom of the file as before) --}}

@endsection