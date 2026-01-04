@extends('layouts.app')

@section('title', 'Job Positions')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- ADD JOB FORM --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header text-white fw-bold" style="background-color: #123456;">
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
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-list-ul me-2"></i> Available Positions
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Job Position</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $job->title }}</td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.org.jobs.delete', $job->id) }}" method="POST" onsubmit="return confirm('Delete this job position?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-4 text-muted">No job positions found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection