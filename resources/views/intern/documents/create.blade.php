@extends('layouts.app')

@section('title', 'Upload Document')

@section('content')
<div class="container">
    {{-- Centered Layout --}}
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white fw-bold py-3" style="background-color: #873260;">
                    <i class="bi bi-cloud-upload me-2"></i> UPLOAD NEW DOCUMENT
                </div>
                <div class="card-body p-4">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('intern.documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- 1. Document Name --}}
                        <div class="mb-4">
                            <label for="filename" class="form-label fw-semibold">Document Name</label>
                            <input type="text" class="form-control" id="filename" name="filename" placeholder="e.g. October Logbook" required>
                        </div>

                        {{-- 2. File Selection (UPDATED: Smaller Font) --}}
                        <div class="mb-4">
                            <label for="document" class="form-label fw-semibold" style="font-size: 0.9rem;">Select File</label>
                            {{-- 'form-control-sm' makes the input box and text smaller --}}
                            <input class="form-control form-control-sm" type="file" id="document" name="document" required>
                            {{-- Explicitly setting smaller font size for helper text --}}
                            <div class="form-text" style="font-size: 0.8rem;">Allowed formats: PDF, DOCX, JPG, PNG (Max 5MB)</div>
                        </div>

                        {{-- 3. Description --}}
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description / Note</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Optional short note">
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('intern.documents.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                            
                            {{-- UPDATED: Changed btn-primary to btn-success (Green) --}}
                            <button type="submit" class="btn btn-success px-4 fw-bold">
                                <i class="bi bi-send-fill me-1"></i> Upload & Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection