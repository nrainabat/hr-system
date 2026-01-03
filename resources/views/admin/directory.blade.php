@extends('layouts.app')

@section('title', 'Employee Directory')

@section('content')
<div class="container-fluid px-4">
    
    {{-- 1. Header & Actions --}}
    <div class="row align-items-center my-3"> {{-- Reduced margin --}}
        <div class="col-md-6">
            <h4 class="fw-bold mb-0" style="color: #123456;">
                <i class="bi bi-person-lines-fill me-2"></i>Employee Directory
            </h4>
        </div>
        <div class="col-md-6 mt-2 mt-md-0">
            <div class="d-flex gap-2 justify-content-md-end">
                {{-- Search Form --}}
                <form action="{{ route('admin.directory') }}" method="GET" class="flex-grow-1" style="max-width: 250px;"> {{-- Smaller width --}}
                    <div class="input-group input-group-sm"> {{-- Small Input Group --}}
                        <input type="text" class="form-control border-end-0" name="search" placeholder="Search..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary border-start-0" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                {{-- Add New Button --}}
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.users.create') }}" class="btn btn-sm text-white shadow-sm d-flex align-items-center" style="background-color: #873260;">
                        <i class="bi bi-plus-lg me-1"></i> Add New
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. User Grid Layout (More Compact) --}}
    <div class="row g-3"> {{-- Reduced Gutter --}}
        @forelse($users as $user)
            {{-- CHANGED: col-xl-3 (4 cards per row) and col-md-4 (3 cards per row) --}}
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-body text-center p-3"> {{-- Reduced Padding p-3 --}}
                        
                        {{-- Profile Picture (Smaller) --}}
                        <div class="mb-2">
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px; border: 2px solid #873260;">
                                <i class="bi bi-person-fill display-6" style="color: #123456;"></i>
                            </div>
                        </div>

                        {{-- Name & Role --}}
                        <h6 class="fw-bold mb-1 text-truncate" title="{{ $user->name }}">{{ $user->name }}</h6>
                        <div class="mb-2">
                            @if($user->role == 'admin') <span class="badge bg-danger" style="font-size: 0.7rem;">Admin</span>
                            @elseif($user->role == 'supervisor') <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Supervisor</span>
                            @elseif($user->role == 'intern') <span class="badge bg-info text-dark" style="font-size: 0.7rem;">Intern</span>
                            @else <span class="badge bg-primary" style="font-size: 0.7rem;">Employee</span>
                            @endif
                        </div>
                        <p class="text-muted mb-2 small text-truncate">{{ $user->position ?? 'No Position' }}</p>

                        <hr class="my-2 opacity-25">

                        {{-- Compact Contact Details --}}
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
                    
                    {{-- Compact Footer --}}
                    <div class="card-footer bg-white border-top-0 p-3 pt-0 d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-pill" style="font-size: 0.75rem;">Profile</a>
                        <button class="btn btn-sm btn-light flex-grow-1 rounded-pill text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-chat-dots-fill"></i>
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

    {{-- 3. Pagination --}}
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $users->onEachSide(1)->links() }}
        </div>
    </div>
</div>
@endsection