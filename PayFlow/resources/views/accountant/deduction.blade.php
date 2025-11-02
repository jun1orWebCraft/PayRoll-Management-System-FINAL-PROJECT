@extends('layouts.accountant.app')

@section('title', 'Employee Deductions')

@section('content')
<div class="container py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Employee Deductions</h2>
            <p class="text-muted mb-0">Manage and monitor all employee deduction records</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addDeductionModal">
            <i class="bi bi-plus-lg"></i> Add Deduction
        </button>
    </div>

    {{-- Search / Filters --}}
    <form method="GET" action="{{ route('accountant.deductions') }}">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control shadow-sm" placeholder="Search employees..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach($deductionTypes as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="month" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option value="">All Months</option>
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0,0,0,$m,1)) }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>
    </form>

    {{-- Deductions Table --}}
    <div class="table-responsive bg-white rounded-4 p-3 shadow-sm border">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Employee Name</th>
                    <th>Deduction Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deductions as $deduction)
                    <tr>
                        <td class="fw-semibold">{{ $deduction->employee->first_name }} {{ $deduction->employee->last_name }}</td>
                        <td>{{ $deduction->deduction_name }}</td>
                        <td>₱{{ number_format($deduction->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($deduction->deduction_date)->format('M d, Y') }}</td>
                        <td>{{ $deduction->remarks ?? '-' }}</td>
                        <td>
                            <button class="btn btn-link text-primary p-0 me-2" data-bs-toggle="modal"
                                data-bs-target="#viewDeductionModal{{ $deduction->id }}">
                                <i class="bi bi-eye fs-5"></i>
                            </button>
                            <form action="{{ route('deductions.destroy', $deduction->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0" 
                                        onclick="return confirm('Delete this deduction?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- View Deduction Modal --}}
                    <div class="modal fade" id="viewDeductionModal{{ $deduction->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-md modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow-lg">
                                <div class="modal-header bg-success text-white border-0">
                                    <h5 class="modal-title fw-semibold">Deduction Details</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Employee:</strong> {{ $deduction->employee->first_name }} {{ $deduction->employee->last_name }}</p>
                                    <p><strong>Deduction Type:</strong> {{ $deduction->deduction_name }}</p>
                                    <p><strong>Amount:</strong> ₱{{ number_format($deduction->amount, 2) }}</p>
                                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($deduction->deduction_date)->format('M d, Y') }}</p>
                                    <p><strong>Remarks:</strong> {{ $deduction->remarks ?? '—' }}</p>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No deductions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Add Deduction Modal --}}
    <div class="modal fade" id="addDeductionModal" tabindex="-1" aria-labelledby="addDeductionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-semibold">Add Deduction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('deductions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->employee_id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deduction Type</label>
                            <input type="text" name="deduction_name" class="form-control" placeholder="e.g. SSS, PhilHealth" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="deduction_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Optional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary px-4">Save Deduction</button>
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
