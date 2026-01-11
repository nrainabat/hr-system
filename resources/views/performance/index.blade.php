@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #0f172a;">Evaluate Teams</h2>
        
        {{-- "New Evaluation" Dropdown --}}
        @if(isset($subordinates) && $subordinates->count() > 0)
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-plus-lg me-1"></i> New Evaluation
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    @foreach($subordinates as $member)
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('performance.create', ['employee_id' => $member->id]) }}">
                                {{ $member->name }} 
                                <span class="text-muted small ms-1">({{ $member->position ?? 'Staff' }})</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <button class="btn btn-secondary" disabled>No Team Members Assigned</button>
        @endif
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Employee Name</th>
                            <th>Review Date</th>
                            <th>Reviewer</th>
                            <th class="text-center">Score</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $review->employee->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($review->review_date)->format('d M Y') }}</td>
                                <td class="text-muted small">{{ $review->reviewer->name ?? 'Admin' }}</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $review->average_score >= 4 ? 'bg-success' : ($review->average_score >= 3 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $review->average_score }} / 5.0
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('performance.show', $review->id) }}" class="btn btn-sm btn-outline-secondary">
                                        View Report
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">You have not submitted any evaluations yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection