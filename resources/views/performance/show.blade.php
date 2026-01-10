@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-5 border-bottom pb-4">
                        <h3 class="fw-bold text-uppercase" style="letter-spacing: 2px;">Performance Appraisal</h3>
                        <p class="text-muted">Confidential Report</p>
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted text-uppercase small fw-bold">Employee</p>
                            <h5 class="fw-bold">{{ $review->employee->name }}</h5>
                            <p class="mb-0 text-muted">{{ $review->employee->position ?? 'Staff' }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-1 text-muted text-uppercase small fw-bold">Date</p>
                            <h5 class="fw-bold">{{ \Carbon\Carbon::parse($review->review_date)->format('d F, Y') }}</h5>
                            <p class="mb-0 text-muted">Reviewer: {{ $review->reviewer->name }}</p>
                        </div>
                    </div>

                    <div class="row g-3 mb-5">
                        <div class="col-md-3"><div class="p-3 border rounded text-center bg-light"><small class="text-muted d-block mb-2">Quality</small><h2 class="fw-bold mb-0 text-primary">{{ $review->rating_quality }}</h2></div></div>
                        <div class="col-md-3"><div class="p-3 border rounded text-center bg-light"><small class="text-muted d-block mb-2">Efficiency</small><h2 class="fw-bold mb-0 text-primary">{{ $review->rating_efficiency }}</h2></div></div>
                        <div class="col-md-3"><div class="p-3 border rounded text-center bg-light"><small class="text-muted d-block mb-2">Teamwork</small><h2 class="fw-bold mb-0 text-primary">{{ $review->rating_teamwork }}</h2></div></div>
                        <div class="col-md-3"><div class="p-3 border rounded text-center bg-light"><small class="text-muted d-block mb-2">Punctuality</small><h2 class="fw-bold mb-0 text-primary">{{ $review->rating_punctuality }}</h2></div></div>
                    </div>

                    <div class="alert {{ $review->average_score >= 4 ? 'alert-success' : 'alert-secondary' }} d-flex justify-content-between align-items-center mb-5 p-4">
                        <div><h5 class="mb-0 fw-bold">Overall Score</h5></div>
                        <div class="display-4 fw-bold">{{ $review->average_score }} <span class="fs-6 text-muted">/ 5.0</span></div>
                    </div>

                    <div class="mb-5">
                        <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">Comments</h6>
                        <p class="text-muted fst-italic">"{{ $review->comments ?? 'No comments provided.' }}"</p>
                    </div>

                    <div class="text-center d-print-none">
                        <button onclick="window.print()" class="btn btn-dark me-2">Print</button>
                        <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection