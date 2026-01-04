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

    {{-- 2. User Grid --}}
    <div class="row g-3">
        @forelse($users as $user)
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

{{-- ==================== EMPLOYEE DETAILS MODAL ==================== --}}
<div class="modal fade" id="employeeProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            
            <div class="modal-header border-0 position-absolute top-0 end-0 p-3" style="z-index: 10;">
                <button type="button" class="btn-close btn-close-white bg-white opacity-75" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <div class="row g-0 h-100">
                    {{-- Left Side: Profile Card --}}
                    <div class="col-md-5 text-white d-flex flex-column align-items-center justify-content-center p-5 text-center" style="background-color: #123456;">
                        
                        <div id="modal-loader-left" class="spinner-border text-light" role="status"></div>
                        
                        <div id="modal-content-left" class="d-none w-100">
                            {{-- Avatar Section --}}
                            <div class="mb-4 position-relative d-inline-block">
                                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center shadow overflow-hidden" style="width: 130px; height: 130px; border: 4px solid rgba(255,255,255,0.3);">
                                    {{-- Default Icon --}}
                                    <i id="modal-default-avatar" class="bi bi-person-fill display-2 text-white"></i>
                                    {{-- Actual Image (Hidden by default) --}}
                                    <img id="modal-user-image" src="" alt="Profile" class="w-100 h-100 object-fit-cover d-none">
                                </div>
                            </div>

                            <h4 id="modal-user-name" class="fw-bold mb-1"></h4>
                            <p id="modal-user-username" class="small opacity-50 mb-4"></p>

                            <div class="row w-100 mt-2 border-top border-white border-opacity-10 pt-3">
                                <div class="col-6 border-end border-white border-opacity-10">
                                    <small class="d-block opacity-50 text-uppercase" style="font-size: 0.7rem;">Department</small>
                                    <span id="modal-user-department-short" class="fw-bold"></span>
                                </div>
                                <div class="col-6">
                                    <small class="d-block opacity-50 text-uppercase" style="font-size: 0.7rem;">Joined</small>
                                    <span id="modal-user-joined"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Detailed Info --}}
                    <div class="col-md-7 bg-white p-4 p-md-5">
                        <div id="modal-loader-right" class="spinner-border text-primary" role="status" style="color: #123456 !important;"></div>

                        <div id="modal-content-right" class="d-none">
                            <h5 class="fw-bold mb-4 pb-2 border-bottom" style="color: #123456;">Employee Details</h5>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="small text-muted fw-bold text-uppercase">Email</label>
                                    <div class="d-flex align-items-center text-dark">
                                        <i class="bi bi-envelope me-2 text-muted"></i>
                                        <span id="modal-user-email"></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted fw-bold text-uppercase">Phone</label>
                                    <div class="d-flex align-items-center text-dark">
                                        <i class="bi bi-telephone me-2 text-muted"></i>
                                        <span id="modal-user-phone"></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted fw-bold text-uppercase">Gender</label>
                                    <div class="d-flex align-items-center text-dark">
                                        <i class="bi bi-person-standing me-2 text-muted"></i>
                                        <span id="modal-user-gender"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="small text-muted fw-bold text-uppercase">Position</label>
                                <div class="p-3 bg-light rounded border-start border-4 border-primary">
                                    <strong id="modal-user-position" class="d-block text-dark"></strong>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="small text-muted fw-bold text-uppercase">Address</label>
                                <p id="modal-user-address" class="text-dark small mb-0"></p>
                            </div>

                            <div class="mb-4">
                                <label class="small text-muted fw-bold text-uppercase">About</label>
                                <p id="modal-user-about" class="text-muted small fst-italic mb-0"></p>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-light text-muted" data-bs-dismiss="modal">Close</button>
                                
                                @if(Auth::user()->role === 'admin')
                                    <a href="#" id="modal-edit-btn" class="btn btn-warning text-dark fw-bold px-4">
                                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                                    </a>
                                @endif
                            </div>

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

            fetch(`{{ route('admin.directory') }}/${userId}/details`)
                .then(res => res.json())
                .then(data => {
                    // Populate Text
                    const fields = {
                        'name': data.name,
                        'username': '@' + data.username,
                        'position': data.position,
                        'joined': data.joined_date,
                        'department-short': data.department,
                        'email': data.email,
                        'phone': data.phone_number,
                        'gender': data.gender,
                        'address': data.address,
                        'about': data.about
                    };

                    for (const [key, value] of Object.entries(fields)) {
                        const el = document.getElementById(`modal-user-${key}`);
                        if(el) el.textContent = value;
                    }

                    // --- HANDLE PROFILE IMAGE ---
                    const userImg = document.getElementById('modal-user-image');
                    const defaultIcon = document.getElementById('modal-default-avatar');

                    if (data.profile_image) {
                        userImg.src = data.profile_image;
                        userImg.classList.remove('d-none');
                        defaultIcon.classList.add('d-none');
                    } else {
                        userImg.classList.add('d-none');
                        defaultIcon.classList.remove('d-none');
                    }
                    // ---------------------------

                    // Update Edit Button
                    const editBtn = document.getElementById('modal-edit-btn');
                    if(editBtn) {
                        editBtn.href = `{{ url('admin/users') }}/${data.id}/edit`;
                    }

                    // Hide Loaders
                    ['left', 'right'].forEach(side => {
                        document.getElementById(`modal-loader-${side}`).classList.add('d-none');
                        document.getElementById(`modal-content-${side}`).classList.remove('d-none');
                    });
                })
                .catch(err => {
                    console.error('Error fetching details:', err);
                    alert('Failed to load user details.');
                });
        });
    });
});
</script>
@endpush
@endsection