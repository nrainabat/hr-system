@extends('layouts.app')

@section('title', 'My Documents')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">My Documents</h3>
        <a href="{{ route('intern.documents.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Upload New
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Date Uploaded</th>
                            <th>Filename</th>
                            <th>Description</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr>
                                <td class="ps-4 text-muted">{{ $doc->created_at->format('d M Y, h:i A') }}</td>
                                <td class="fw-semibold">{{ $doc->filename }}</td>
                                <td>{{ $doc->description ?? '-' }}</td>
                                <td class="text-center">
                                    @if($doc->status == 'signed')
                                        <span class="badge bg-success rounded-pill px-3">Signed</span>
                                    @elseif($doc->status == 'pending')
                                        <span class="badge bg-warning text-dark rounded-pill px-3">Pending</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x display-4 d-block mb-3"></i>
                                    No documents uploaded yet.
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