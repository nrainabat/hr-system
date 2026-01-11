@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f8f9fa;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg rounded-0"> 
                <div class="card-body p-5">
                    
                    <div class="d-flex justify-content-between align-items-end border-bottom pb-4 mb-5">
                        <div>
                            <h2 class="fw-bold text-uppercase text-dark mb-1" style="font-family: 'Times New Roman', Times, serif; letter-spacing: 1px;">Performance Appraisal</h2>
                            <p class="text-muted small mb-0 fw-bold tracking-wide">HUMAN RESOURCES DEPARTMENT • CONFIDENTIAL RECORD</p>
                        </div>
                        <div class="text-end">
                            <h5 class="fw-bold text-muted mb-0" style="opacity: 0.2; font-family: sans-serif;">OFFICIAL COPY</h5>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-6 border-end">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3 text-decoration-underline">Employee Information</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted fw-bold ps-0" style="width: 100px;">Name:</td>
                                    <td class="fs-5 text-dark fw-bold">{{ $review->employee->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold ps-0">Position:</td>
                                    <td class="text-dark">{{ $review->employee->position ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold ps-0">Department:</td>
                                    <td class="text-dark">{{ $review->employee->department ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 ps-md-5">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3 text-decoration-underline">Review Details</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted fw-bold ps-0" style="width: 100px;">Date:</td>
                                    <td class="text-dark">{{ \Carbon\Carbon::parse($review->review_date)->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold ps-0">Reviewer:</td>
                                    <td class="text-dark">{{ $review->reviewer->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h6 class="text-uppercase text-dark fw-bold mb-3 small" style="letter-spacing: 1px; background-color: #e9ecef; padding: 10px;">Performance Assessment</h6>
                        <table class="table table-bordered align-middle border-secondary">
                            <thead class="bg-light">
                                <tr class="text-center text-uppercase small fw-bold">
                                    <th class="text-start ps-4" style="width: 40%;">Criteria</th>
                                    <th style="width: 15%;">Rating (1-5)</th>
                                    <th class="text-start ps-4">Definition / Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">Quality of Work</td>
                                    <td class="text-center fs-5 fw-bold">{{ $review->rating_quality }}</td>
                                    <td class="ps-4 text-muted small">Accuracy, thoroughness and standard of work produced.</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">Efficiency</td>
                                    <td class="text-center fs-5 fw-bold">{{ $review->rating_efficiency }}</td>
                                    <td class="ps-4 text-muted small">Speed, organizational skills and volume of work completed.</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">Teamwork</td>
                                    <td class="text-center fs-5 fw-bold">{{ $review->rating_teamwork }}</td>
                                    <td class="ps-4 text-muted small">Ability to collaborate and contribute to team goals.</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">Punctuality</td>
                                    <td class="text-center fs-5 fw-bold">{{ $review->rating_punctuality }}</td>
                                    <td class="ps-4 text-muted small">Adherence to work hours and meeting schedules.</td>
                                </tr>
                            </tbody>
                            <tfoot style="border-top: 2px solid #000;">
                                <tr>
                                    <td class="text-end pe-4 text-uppercase fw-bold bg-light">Overall Score</td>
                                    <td class="text-center fw-bold text-white fs-5 {{ $review->average_score >= 4 ? 'bg-success' : ($review->average_score >= 3 ? 'bg-secondary' : 'bg-danger') }}" style="border-color: transparent;">
                                        {{ $review->average_score }}
                                    </td>
                                    <td class="ps-4 fst-italic text-muted bg-light">
                                        @if($review->average_score >= 4.5) Excellent Performance
                                        @elseif($review->average_score >= 3) Satisfactory Performance
                                        @else Needs Improvement
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mb-5">
                        <h6 class="text-uppercase text-dark fw-bold mb-2 small" style="letter-spacing: 1px;">Evaluator's Comments</h6>
                        <div class="p-4 border border-secondary bg-light" style="min-height: 100px;">
                            <p class="mb-0 text-dark fst-italic" style="font-family: serif; font-size: 1.1rem; line-height: 1.6;">
                                "{{ $review->comments ?? 'No additional comments provided.' }}"
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-5 text-center text-muted small border-top pt-3">
                        <p class="mb-0">This document contains confidential information and is intended for internal use only.</p>
                    </div>

                </div>
            </div>

            <div class="text-center mt-4 d-print-none pb-5">
                <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary px-4">Back to Evaluations</a>
            </div>
        </div>
    </div>
</div>
@endsection