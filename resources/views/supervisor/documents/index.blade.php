@extends('layouts.app')

@section('title', 'Intern Documents')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold"><i class="bi bi-files me-2"></i> Review Intern Documents</h3>
            <p class="text-muted">Manage and sign documents uploaded by interns.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header text-white fw-bold py-2" style="background-color: #2D3748;">
            Document List
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Intern Name</th>
                            <th>Document Name</th>
                            <th>Description</th>
                            <th>Date Uploaded</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $doc->user->name }}</td>
                                <td>{{ $doc->filename }}</td>
                                <td>{{ Str::limit($doc->description, 30) }}</td>
                                <td class="text-muted">{{ $doc->created_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    @if($doc->status == 'signed')
                                        <span class="badge bg-success">Signed</span>
                                    @elseif($doc->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('supervisor.documents.review', $doc->id) }}" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="bi bi-pencil-square me-1"></i> Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    No documents found.
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