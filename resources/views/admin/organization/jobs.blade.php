@extends('layouts.app')

@section('title', 'Job Positions')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- ADD JOB FORM --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                {{-- Light Grey Header --}}
                <div class="card-header fw-bold" style="background-color: #e9ecef; color: #333;">
                    <i class="bi bi-briefcase-fill me-2"></i> Add Position
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.org.jobs.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Position Name</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Software Engineer" required>
                        </div>
                        <button type="submit" class="btn text-white w-100" style="background-color: #123456;">
                            <i class="bi bi-plus-lg"></i> Add Position
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- JOBS LIST --}}
        <div class="col-md-8">
            {{-- Separated Title with System Color --}}
            <h5 class="fw-bold mb-3" style="color: #123456;">
                <i class="bi bi-list-ul me-2"></i> Available Positions
            </h5>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Job Position</th>
                                <th>Created Date</th> {{-- NEW COLUMN --}}
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $job->title }}</td>
                                {{-- Date Format --}}
                                <td class="text-muted small">{{ $job->created_at ? $job->created_at->format('d M Y') : '-' }}</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.org.jobs.edit', $job->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.org.jobs.delete', $job->id) }}" method="POST" onsubmit="return confirm('Delete this job position?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">No job positions found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection