@extends('layouts.app')

@section('title', 'Leave Balances')

@section('content')
<div class="container py-4">
    
    {{-- HEADER & BUTTON --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #123456;">
            <i class="bi bi-wallet2 me-2"></i> Leave Balances ({{ date('Y') }})
        </h4>
        {{-- Button triggers Modal --}}
        <button class="btn text-white" style="background-color: #123456;" type="button" data-bs-toggle="modal" data-bs-target="#balanceModal">
            <i class="bi bi-plus-lg me-1"></i> Add Leave Count
        </button>
    </div>

    {{-- BALANCES LIST TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Employee</th>
                        <th>Leave Type</th>
                        <th class="text-center">Total Allocated</th>
                        <th class="text-center">Used</th>
                        <th class="text-center">Balance</th>
                        <th class="text-end pe-4">Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($balances as $balance)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-2 border" style="width: 35px; height: 35px;">
                                    <span class="text-muted small fw-bold">{{ substr($balance->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    {{ $balance->user->name }}
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-secondary">{{ $balance->leave_type }}</span></td>
                        
                        {{-- Total Allocated --}}
                        <td class="text-center">
                            <span class="fw-bold">{{ $balance->balance }}</span> days
                        </td>

                        {{-- Used (Calculated) --}}
                        <td class="text-center text-danger">
                            -{{ $balance->days_used }}
                        </td>

                        {{-- Remaining Balance (Calculated) --}}
                        <td class="text-center">
                            <span class="badge {{ $balance->remaining < 0 ? 'bg-danger' : ($balance->remaining < 3 ? 'bg-warning text-dark' : 'bg-success') }} fs-6">
                                {{ $balance->remaining }}
                            </span>
                        </td>

                        <td class="text-end pe-4 small text-muted">
                            {{ $balance->updated_at->format('d M Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
                            No leave entitlements set yet. Click "Set New Balance" to add one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ==================== MODAL FORM ==================== --}}
<div class="modal fade" id="balanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            {{-- Modal Header --}}
            <div class="modal-header text-white" style="background-color: #123456;">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Set Leave</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- Modal Body (Form) --}}
            <div class="modal-body p-4">
                <form action="{{ route('admin.leave.balances.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Employee <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="" disabled selected>Select Employee...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Leave Type <span class="text-danger">*</span></label>
                        <select name="leave_type" class="form-select" required>
                            <option value="" disabled selected>Select Type...</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->name }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Total Entitlement (Days) <span class="text-danger">*</span></label>
                        <input type="number" name="balance" class="form-control" placeholder="e.g. 14" min="0" required>
                    </div>

                    {{-- BUTTON: Green, Small, Right Aligned --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            Save
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection