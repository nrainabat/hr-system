@extends('layouts.app')
@section('title', 'Leave Types')
@section('content')
<div class="container py-4">
    <div class="row">
        {{-- Add Form --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white fw-bold" style="background-color: #123456;">
                    Add Leave Type
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.leave.types.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>Type Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Annual Leave" required>
                        </div>
                        {{-- Removed Days Allowed Input --}}
                        <button class="btn w-100 text-white" style="background-color: #123456;">Add</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- List --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Current Leave Types</div>
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($types as $type)
                        <tr>
                            <td>{{ $type->name }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.leave.types.delete', $type->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection