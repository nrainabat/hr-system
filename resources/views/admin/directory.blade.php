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
                    <a href="{{ route('admin.users.create') }}" class="btn btn-sm text-white shadow-sm d-flex align-items-center" style="background-color: #123456;">
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
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px; border: 2px solid #123456;">
                                <i class="bi bi-person-fill display-6" style="color: #123456;"></i>
                            </div>
                        </div>

                        {{-- Name & Role --}}
                        <h6 class="fw-bold mb-1 text-truncate" title="{{ $user->name }}">{{ $user->name }}</h6>
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

    {{-- 3. Pagination --}}
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $users->onEachSide(1)->links() }}
        </div>
    </div>
</div>

{{-- ==================== EMPLOYEE DETAILS MODAL ==================== --}}
<div class="modal fade" id="employeeProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-0">
                <div class="row g-0 h-100">
                    {{-- Left Side: Theme Color Background --}}
                    <div class="col-md-5 text-white d-flex flex-column align-items-center justify-content-center p-5" style="background-color: #123456;">
                        
                        {{-- Loaders & Content --}}
                        <div id="modal-loader-left" class="spinner-border text-light" role="status"></div>
                        <div id="modal-content-left" class="text-center d-none">
                            <div class="mb-4">
                                <div class="rounded-circle bg-white bg-opacity-25 d-inline-flex align-items-center justify-content-center shadow" style="width: 120px; height: 120px; border: 4px solid rgba(255,255,255,0.3);">
                                    <i class="bi bi-person-fill display-3 text-white"></i>
                                </div>
                            </div>
                            <h4 id="modal-user-name" class="fw-bold mb-2"></h4>
                            <span id="modal-user-role-badge" class="badge border border-white border-opacity-50 mb-3 px-3 py-2 rounded-pill"></span>
                            <p id="modal-user-position" class="mb-0 fs-5 opacity-75"></p>
                            <hr class="border-white opacity-50 w-50 mx-auto my-4">
                            <div class="opacity-75"><i class="bi bi-calendar-check me-2"></i> Joined <span id="modal-user-joined"></span></div>
                        </div>
                    </div>

                    {{-- Right Side: Details --}}
                    <div class="col-md-7 bg-white p-5 position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal"></button>
                        <h5 class="fw-bold mb-4" style="color: #123456;">Contact & Work Details</h5>

                        <div id="modal-loader-right" class="spinner-border text-primary" role="status" style="color: #123456 !important;"></div>
                        <div id="modal-content-right" class="d-none">
                            @foreach(['Email' => 'email', 'Phone' => 'phone', 'Department' => 'department', 'Location' => 'location'] as $label => $id)
                                <div class="mb-4">
                                    <label class="small text-muted fw-bold text-uppercase mb-1">{{ $label }}</label>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-circle-fill me-3" style="color: #123456; font-size: 8px;"></i>
                                        <span id="modal-user-{{ $id }}" class="fs-6"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('employeeProfileModal'));
    
    document.querySelectorAll('.view-profile-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            
            // Show Loaders
            ['left', 'right'].forEach(side => {
                document.getElementById(`modal-loader-${side}`).classList.remove('d-none');
                document.getElementById(`modal-content-${side}`).classList.add('d-none');
            });
            
            modal.show();

            // Fetch Data
            fetch(`{{ route('admin.directory') }}/${userId}/details`)
                .then(res => res.json())
                .then(data => {
                    // Update Text
                    document.getElementById('modal-user-name').textContent = data.name;
                    document.getElementById('modal-user-position').textContent = data.position;
                    document.getElementById('modal-user-joined').textContent = data.joined_date;
                    document.getElementById('modal-user-email').textContent = data.email;
                    document.getElementById('modal-user-phone').textContent = data.phone_number;
                    document.getElementById('modal-user-department').textContent = data.department;
                    document.getElementById('modal-user-location').textContent = data.location;
                    
                    // Update Badge
                    const badge = document.getElementById('modal-user-role-badge');
                    badge.className = `badge border border-white border-opacity-50 mb-3 px-3 py-2 rounded-pill ${data.role_class}`;
                    badge.textContent = data.role;

                    // Show Content
                    ['left', 'right'].forEach(side => {
                        document.getElementById(`modal-loader-${side}`).classList.add('d-none');
                        document.getElementById(`modal-content-${side}`).classList.remove('d-none');
                    });
                });
        });
    });
});
</script>
@endpush
@endsection