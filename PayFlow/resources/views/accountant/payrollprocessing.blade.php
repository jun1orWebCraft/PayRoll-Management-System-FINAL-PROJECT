@extends('layouts.accountant.app')

@section('title', 'Payroll Processing')

@section('content')
<div class="container py-3">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Payroll Processing</h2>
            <p class="text-muted">Manage and process employee payroll records.</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addPayrollModal">
            <i class="bi bi-plus-lg"></i> Add Payroll
        </button>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search / Filters --}}
    <form method="GET" action="{{ route('accountant.payrollprocessing') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control shadow-sm" placeholder="Search employees..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="position" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option value="">All Positions</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->position_name }}" {{ request('position') == $pos->position_name ? 'selected' : '' }}>
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
                    <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-outline-secondary shadow-sm">Filter</button>
            </div>
        </div>
    </form>

    {{-- Payroll Table --}}
    <div class="table-responsive bg-white rounded-4 shadow-sm border">
        <div style="overflow-x: auto; max-width: 100%;">
            <table class="table table-hover align-middle text-center mb-0" style="min-width: 900px;">
                <thead class="table-primary">
                    <tr>
                        <th>Employee No</th>
                        <th>Employee Name</th>
                        <th>Position</th>
                        <th>Pay Period</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                        @php
                            $employee = $payroll->employee ?? null;
                            $position = $employee->position ?? null;
                            $basicSalary = $employee->basic_salary ?? 0;
                            $deductions = $payroll->deductions ?? $employee->deductions ?? 0;
                            $overtimePay = $payroll->overtime_pay ?? 0;
                            $netPay = $basicSalary + $overtimePay - $deductions;
                        @endphp
                        <tr>
                            <td style="white-space: nowrap;">{{ $employee->employee_no ?? 'N/A' }}</td>
                            <td class="fw-semibold text-truncate" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $employee->first_name ?? '' }} {{ $employee->last_name ?? '' }}
                            </td>
                            <td style="white-space: nowrap;">{{ $position->position_name ?? 'N/A' }}</td>
                            <td style="white-space: nowrap;">{{ $payroll->pay_period_start }} - {{ $payroll->pay_period_end }}</td>
                            <td style="white-space: nowrap;">₱{{ number_format($basicSalary + $overtimePay, 2) }}</td>
                            <td style="white-space: nowrap;">₱{{ number_format($deductions, 2) }}</td>
                            <td class="fw-bold text-success" style="white-space: nowrap;">₱{{ number_format($netPay, 2) }}</td>
                            <td style="white-space: nowrap;">
                                <span class="badge bg-{{ $payroll->status == 'Processed' ? 'info' : ($payroll->status == 'Paid' ? 'success' : 'secondary') }}">
                                    {{ $payroll->status }}
                                </span>
                            </td>
                            <td style="white-space: nowrap;">
                                {{-- Edit Button --}}
                                <button class="btn btn-link text-warning p-0 me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPayrollModal{{ $payroll->payroll_id }}" 
                                        title="Edit Payroll">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>

                                {{-- View Button --}}
                                <button class="btn btn-link text-primary p-0 me-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewPayrollModal{{ $payroll->payroll_id }}"
                                        title="View Payroll Details">
                                    <i class="bi bi-eye fs-5"></i>
                                </button>

                                {{-- Delete Form --}}
                                <form action="{{ route('accountant.payrollprocessing.destroy', $payroll->payroll_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Delete payroll record?')" title="Delete Payroll">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Edit Payroll Modal --}}
                        <div class="modal fade" id="editPayrollModal{{ $payroll->payroll_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow-lg">
                                    <div class="modal-header bg-warning text-dark border-0">
                                        <h5 class="modal-title fw-semibold">Edit Payroll</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('accountant.payrollprocessing.update', $payroll->payroll_id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Employee</label>
                                                    <select name="employee_id" class="form-select" required>
                                                        @foreach($employees as $emp)
                                                            <option value="{{ $emp->employee_id }}" 
                                                                {{ $emp->employee_id == $payroll->employee_id ? 'selected' : '' }}>
                                                                {{ $emp->first_name }} {{ $emp->last_name }} - {{ $emp->position->position_name ?? 'N/A' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Pay Period Start</label>
                                                    <input type="date" name="pay_period_start" class="form-control"
                                                        value="{{ $payroll->pay_period_start }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Pay Period End</label>
                                                    <input type="date" name="pay_period_end" class="form-control"
                                                        value="{{ $payroll->pay_period_end }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Overtime Pay</label>
                                                    <input type="number" step="0.01" name="overtime_pay" class="form-control"
                                                    value="{{ $payroll->overtime_pay ?? 0 }}">
                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="submit" class="btn btn-warning px-4">Update Payroll</button>
                                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- View Payroll Modal --}}
                        <div class="modal fade" id="viewPayrollModal{{ $payroll->payroll_id }}" tabindex="-1" aria-hidden="true">
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
                                                <p>₱{{ number_format($employee->basic_salary ?? 0, 2) }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="fw-semibold text-muted">Overtime Pay</label>
                                                <p>₱{{ number_format($overtimePay, 2) }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="fw-semibold text-muted">Deductions</label>
                                                <p>₱{{ number_format($deductions, 2) }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="fw-semibold text-muted">Net Pay</label>
                                                <p class="fw-bold text-success">₱{{ number_format($netPay, 2) }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="fw-semibold text-muted">Payment Date</label>
                                                <p>{{ $payroll->payment_date ?? 'N/A' }}</p>
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
                            <td colspan="9" class="text-center text-muted py-4">No payroll records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $payrolls->links() }}
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
                                <label class="form-label fw-semibold">Employee</label>
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
                                <label class="form-label fw-semibold">Pay Period Start</label>
                                <input type="date" name="pay_period_start" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pay Period End</label>
                                <input type="date" name="pay_period_end" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Overtime Pay</label>
                               <input type="number" step="0.01" name="overtime_pay" class="form-control"
                                value="{{ $payroll->overtime_pay ?? 0 }}">
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

{{-- Optional inline styles for nicer horizontal scrollbar --}}
<style>
    div[style*="overflow-x: auto"]::-webkit-scrollbar { height: 8px; }
    div[style*="overflow-x: auto"]::-webkit-scrollbar-track { background: #f1f1f1; }
    div[style*="overflow-x: auto"]::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
    div[style*="overflow-x: auto"]::-webkit-scrollbar-thumb:hover { background: #555; }
</style>
@endsection
