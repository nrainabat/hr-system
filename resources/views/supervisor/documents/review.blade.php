@extends('layouts.app')

@section('title', 'Review Document')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white fw-bold py-3" style="background-color: #4A5568;">
                    Review Document: {{ $document->filename }}
                </div>
                <div class="card-body p-4">
                    
                    {{-- Info Section --}}
                    <div class="mb-4 p-3 bg-light rounded">
                        <p class="mb-1"><strong>Intern:</strong> {{ $document->user->name }}</p>
                        <p class="mb-1"><strong>Date Uploaded:</strong> {{ $document->created_at->format('d M Y, h:i A') }}</p>
                        <p class="mb-0"><strong>Intern Note:</strong> {{ $document->description ?? 'None' }}</p>
                        {{-- Link to view original file (Assumes you have a route to view/download) --}}
                         <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-eye"></i> View Original File
                        </a>
                    </div>

                    <hr>

                    {{-- Review Form --}}
                    <form action="{{ route('supervisor.documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Action</label>
                            <select name="status" class="form-select" id="statusSelect" required>
                                <option value="signed" {{ $document->status == 'signed' ? 'selected' : '' }}>Sign & Approve</option>
                                <option value="rejected" {{ $document->status == 'rejected' ? 'selected' : '' }}>Reject</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload Signed Document (Optional)</label>
                            <input type="file" name="signed_file" class="form-control">
                            <div class="form-text">If you signed the document digitally, upload it here.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Comments</label>
                            <textarea name="supervisor_comment" class="form-control" rows="3" placeholder="Add feedback or reasons for rejection...">{{ $document->supervisor_comment }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle-fill me-1"></i> Submit Review
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection