@extends('layouts.app')
@section('title', 'Edit Job Position')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white fw-bold" style="background-color: #123456;">
                    <i class="bi bi-pencil-square me-2"></i> Edit Job Position
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.org.jobs.update', $job->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Position Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $job->title) }}" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.org.jobs') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn text-white" style="background-color: #123456;">Update Position</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection