@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0 fw-bold">Evaluate: {{ $employee->name }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('performance.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $employee->id }}">

                        <div class="alert alert-light border small mb-4">
                            <strong>Instructions:</strong> Rate the employee on a scale of 1 (Poor) to 5 (Excellent).
                        </div>

                        <div class="row g-3 mb-4">
                            @foreach(['quality' => 'Quality of Work', 'efficiency' => 'Efficiency & Speed', 'teamwork' => 'Teamwork & Attitude', 'punctuality' => 'Punctuality & Reliability'] as $key => $label)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $label }}</label>
                                <select name="rating_{{ $key }}" class="form-select" required>
                                    <option value="" disabled selected>Select Rating...</option>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Good</option>
                                    <option value="3">3 - Average</option>
                                    <option value="2">2 - Below Average</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Feedback / Comments</label>
                            <textarea name="comments" class="form-control" rows="4" placeholder="Enter specific feedback here..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('performance.index') }}" class="btn btn-light border">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Submit Evaluation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection