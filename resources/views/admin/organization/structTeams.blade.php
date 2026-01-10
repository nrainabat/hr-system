@extends('layouts.app')

@section('title', 'Team Department')

@section('content')
<div class="container-fluid py-4">
    
    @include('admin.organization.nav')

    <div class="row">
        
        {{-- LEFT PANEL: DEPARTMENTS LIST --}}
        <div class="col-md-3">
            <h5 class="fw-bold mb-3" style="color: #123456;">
                <i class="bi bi-building me-2"></i>Departments
            </h5>
            <div class="list-group shadow-sm">
                @foreach($departments as $dept)
                    <a href="{{ route('admin.org.structure.teams', ['department_id' => $dept->id]) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ (isset($selectedDept) && $selectedDept->id == $dept->id) ? 'active' : '' }}"
                       style="{{ (isset($selectedDept) && $selectedDept->id == $dept->id) ? 'background-color: #123456; border-color: #123456;' : '' }}">
                        
                        <div class="w-100 pe-2">
                            <div class="fw-bold mb-1">{{ $dept->name }}</div>
                            
                            {{-- UPDATED: Supervisor Name & Phone --}}
                            <small class="{{ (isset($selectedDept) && $selectedDept->id == $dept->id) ? 'text-white-50' : 'text-muted' }} d-block" style="font-size: 0.75rem; line-height: 1.3;">
                                @if($dept->supervisor)
                                    <div class="mb-1">
                                        <i class="bi bi-person-badge me-1"></i> {{ $dept->supervisor->name }}
                                    </div>
                                    <div>
                                        <i class="bi bi-telephone me-1"></i> {{ $dept->supervisor->phone_number ?? '-' }}
                                    </div>
                                @else
                                    <div><i class="bi bi-exclamation-circle me-1"></i> No Supervisor</div>
                                @endif
                            </small>
                        </div>

                        <span class="badge {{ (isset($selectedDept) && $selectedDept->id == $dept->id) ? 'bg-light text-dark' : 'bg-secondary' }} rounded-pill">
                            {{ $dept->user_count ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- RIGHT PANEL: EMPLOYEES LIST --}}
        <div class="col-md-9">
            @if($selectedDept)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold mb-0" style="color: #123456;">
                            {{ $selectedDept->name }} <span class="text-muted fw-normal fs-6">Team Members</span>
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Employee Name</th>
                                    <th>Position</th>
                                    <th>Role</th>
                                    <th>Assigned Supervisor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-2" style="width: 40px; height: 40px;">
                                                @if($user->profile_image)
                                                    <img src="{{ asset('storage/' . $user->profile_image) }}" class="w-100 h-100 rounded-circle object-fit-cover" alt="Profile">
                                                @else
                                                    <i class="bi bi-person text-muted"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $user->name }}</div>
                                                <div class="small text-muted">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->position ?? '-' }}</td>
                                    <td>
                                        @if($user->role == 'supervisor') <span class="badge bg-success">Supervisor</span>
                                        @elseif($user->role == 'intern') <span class="badge bg-warning text-dark">Intern</span>
                                        @else <span class="badge bg-secondary">Employee</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->supervisor)
                                            <span class="text-primary fw-bold"><i class="bi bi-person-badge me-1"></i> {{ $user->supervisor->name }}</span>
                                        @else
                                            <span class="text-muted small">Not Assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-people display-4 d-block mb-3 opacity-25"></i>
                                        No employees found in {{ $selectedDept->name }}.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center py-5 shadow-sm border-0">
                    <i class="bi bi-arrow-left-circle display-4 mb-3 d-block text-info"></i>
                    <h5 class="fw-bold">Select a Department</h5>
                    <p class="mb-0">Click on a department from the left panel to view its team members.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection