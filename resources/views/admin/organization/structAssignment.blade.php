@extends('layouts.app')

@section('title', 'Supervisor Assignments')

@section('content')
<div class="container py-4">
    
    @include('admin.organization.nav')

    <div class="row">
        {{-- ASSIGNMENT FORM --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header fw-bold text-white" style="background-color: #123456;">
                    <i class="bi bi-person-plus-fill me-2"></i> Assign Supervisor
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.org.structure.assign') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Select Staff (Employee/Intern)</label>
                            <select name="user_id" class="form-select" required>
                                <option value="" disabled selected>Choose Staff...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ ucfirst($emp->role) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Select Supervisor</label>
                            <select name="supervisor_id" class="form-select" required>
                                <option value="" disabled selected>Choose Supervisor...</option>
                                @foreach($supervisors as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn text-white w-100" style="background-color: #123456;">
                            Assign
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ASSIGNMENT LIST --}}
        <div class="col-md-8">
            <h5 class="fw-bold mb-3" style="color: #123456;">
                <i class="bi bi-list-check me-2"></i> Current Assignments
            </h5>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Role</th>
                                <th>Current Supervisor</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffList as $user)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $user->name }}</td>
                                <td class="small">{{ $user->department ?? '-' }}</td>
                                <td class="small">{{ $user->position ?? '-' }}</td>
                                <td>
                                    @if($user->role == 'intern') <span class="badge bg-warning text-dark">Intern</span>
                                    @else <span class="badge bg-secondary">Employee</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->supervisor)
                                        <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i> {{ $user->supervisor->name }}</span>
                                    @else
                                        <span class="text-muted fst-italic small">Unassigned</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($user->supervisor)
                                        <form action="{{ route('admin.org.structure.unassign', $user->id) }}" method="POST" onsubmit="return confirm('Remove supervisor assignment?');">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" title="Unassign">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No staff found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection