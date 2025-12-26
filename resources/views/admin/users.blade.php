@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">User Management</h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        + Add User
    </a>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- User Table -->
<div class="card shadow-sm">
    <div class="card-body">

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr class="text-center">
                    <th>#</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th width="180">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $index => $user)
                    <tr class="text-center">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->username }}</td>
                        <td>
                            <span class="badge bg-info text-dark text-uppercase">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            @if($user->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                               class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <form action="{{ route('admin.users.destroy', $user->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>
</div>

@endsection
