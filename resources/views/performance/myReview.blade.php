@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-trophy-fill me-2"></i>My Performance Reviews</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Below are the evaluations submitted by the Administrator regarding your performance.</p>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Review Date</th>
                            <th>Reviewer</th>
                            <th class="text-center">Average Score</th>
                            <th>Comments</th>
                            <th class="text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($review->review_date)->format('d M Y') }}</td>
                            <td>{{ $review->reviewer->name ?? 'Admin' }}</td>
                            <td class="text-center">
                                <span class="badge rounded-pill {{ $review->average_score >= 4 ? 'bg-success' : ($review->average_score >= 3 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $review->average_score }} / 5.0
                                </span>
                            </td>
                            <td class="text-muted fst-italic">
                                "{{ Str::limit($review->comments, 50) }}"
                            </td>
                            <td class="text-center">
                                <a href="{{ route('performance.show', $review->id) }}" class="btn btn-outline-secondary btn-sm">
                                    View Full Report
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No performance reviews found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection