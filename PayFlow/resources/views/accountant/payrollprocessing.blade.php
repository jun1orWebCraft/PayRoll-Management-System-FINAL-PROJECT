@extends('layouts.accountant.app')

@section('title', 'Payroll Processing')

@section('content')
<div class="container py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Payroll Processing</h2>
            <p class="text-muted mb-0">Manage and process employee payroll records</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addPayrollModal">
            <i class="bi bi-plus-lg"></i> Add Payroll
        </button>
    </div>

    {{-- Search / Filters --}}
    <form method="GET" action="{{ route('accountant.payrollprocessing') }}">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control shadow-sm" placeholder="Search employees..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="position" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option value="">All Positions</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->position_name }}" 
                            {{ request('position') == $pos->position_name ? 'selected' : '' }}>
                            {{ $pos->position_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="Processed" {{ request('status') == 'Processed' ? 'selected' : '' }}>Processed</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
        </div>
    </form>

    {{-- Payroll Table --}}
    <div class="table-responsive bg-white rounded-4 p-3 shadow-sm border">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Position</th>
                    <th>Pay Period</th>
                    <th>Gross Pay</th>
                    <th>Deductions</th>
                    <th>Net Pay</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $payroll)
                    @php
                        $employee = $payroll->employee ?? null;
                        $position = $employee->position ?? null;
                    @endphp
                    <tr>
                        <td>{{ $employee->employee_id ?? 'N/A' }}</td>
                        <td class="fw-semibold">{{ $employee->first_name ?? '' }} {{ $employee->last_name ?? '' }}</td>
                        <td>{{ $position->position_name ?? 'N/A' }}</td>
                        <td>{{ $payroll->pay_period_start }} - {{ $payroll->pay_period_end }}</td>
                        <td>₱{{ number_format(($payroll->basic_salary ?? 0) + ($payroll->overtime_pay ?? 0), 2) }}</td>
                        <td>₱{{ number_format($payroll->deductions ?? 0, 2) }}</td>
                        <td class="fw-bold text-success">₱{{ number_format($payroll->net_pay ?? 0, 2) }}</td>
                        <td>
                            <button class="btn btn-link text-primary p-0 border-0 shadow-none me-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewPayrollModal{{ $payroll->id }}">
                                <i class="bi bi-eye fs-5"></i>
                            </button>
                            <form action="{{ route('accountant.payrollprocessing.destroy', $payroll->payroll_id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Delete payroll record?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- View Payroll Modal --}}
                    <div class="modal fade" id="viewPayrollModal{{ $payroll->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow-lg">
                                <div class="modal-header bg-success text-white border-0">
                                    <h5 class="modal-title fw-semibold">Payroll Details</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="fw-semibold text-muted">Employee</label>
                                            <p>{{ $employee->first_name ?? '' }} {{ $employee->last_name ?? '' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-semibold text-muted">Position</label>
                                            <p>{{ $position->position_name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-semibold text-muted">Pay Period</label>
                                            <p>{{ $payroll->pay_period_start }} - {{ $payroll->pay_period_end }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-semibold text-muted">Basic Salary</label>
                                            <p>₱{{ number_format($payroll->basic_salary ?? 0, 2) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-semibold text-muted">Overtime Pay</label>
                                            <p>₱{{ number_format($payroll->overtime_pay ?? 0, 2) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-semibold text-muted">Deductions</label>
                                            <p>₱{{ number_format($payroll->deductions ?? 0, 2) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-semibold text-muted">Net Pay</label>
                                            <p class="fw-bold text-success">₱{{ number_format($payroll->net_pay ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No payroll records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Add Payroll Modal --}}
    <div class="modal fade" id="addPayrollModal" tabindex="-1" aria-labelledby="addPayrollModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-semibold">Add Payroll Record</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('accountant.payrollprocessing.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee</label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->employee_id }}">
                                            {{ $emp->first_name }} {{ $emp->last_name }} - {{ $emp->position->position_name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pay Period Start</label>
                                <input type="date" name="pay_period_start" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pay Period End</label>
                                <input type="date" name="pay_period_end" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Overtime Hours</label>
                                <input type="number" step="0.01" name="overtime_hours" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary px-4">Save Payroll</button>
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
