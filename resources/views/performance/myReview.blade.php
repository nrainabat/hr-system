@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #0f172a;">My Performance Review</h2>
        {{-- No "New Evaluation" button here --}}
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Review Date</th>
                            <th>Reviewer</th>
                            <th>Comments</th>
                            <th class="text-center">Average Score</th>
                            <th class="text-end pe-4">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="ps-4">{{ \Carbon\Carbon::parse($review->review_date)->format('d M Y') }}</td>
                                <td class="fw-bold">{{ $review->reviewer->name ?? 'Admin' }}</td>
                                <td class="text-muted fst-italic text-truncate" style="max-width: 200px;">
                                    "{{ $review->comments }}"
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $review->average_score >= 4 ? 'bg-success' : ($review->average_score >= 3 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $review->average_score }} / 5.0
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('performance.show', $review->id) }}" class="btn btn-sm btn-primary">
                                        Read Full Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-clipboard-x display-6 d-block mb-3"></i>
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