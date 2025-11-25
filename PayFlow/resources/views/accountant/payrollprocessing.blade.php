@extends('layouts.accountant.app')

@section('title', 'Payroll Processing')

@section('content')
<div class="container py-3">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="text-truncate" style="max-width: 80%;">
            <h2 class="fw-bold text-dark mb-0" style="white-space: nowrap;">Payroll Processing</h2>
            <p class="text-muted mb-0" style="white-space: nowrap;">Manage and process employee payroll records.</p>
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
            <table class="table table-hover align-middle text-center mb-0" style="min-width: 1000px;">
                <thead class="table-primary">
                    <tr>
                        <th>Employee No</th>
                        <th>Employee Name</th>
                        <th>Position</th>
                        <th>Pay Period</th>
                        <th>Basic Salary</th>
                        <th>Overtime</th>
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
                            $overtimePay = $payroll->overtime_pay ?? 0;
                            $grossPay = $basicSalary + $overtimePay;
                            $deductions = $payroll->deductions ?? 0;
                            $netPay = $grossPay - $deductions;
                        @endphp
                        <tr>
                            <td>{{ $employee->employee_no ?? 'N/A' }}</td>
                            <td class="fw-semibold text-truncate" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $employee->first_name ?? '' }} {{ $employee->last_name ?? '' }}
                            </td>
                            <td>{{ $position->position_name ?? 'N/A' }}</td>
                            <td>{{ $payroll->pay_period_start }} - {{ $payroll->pay_period_end }}</td>
                            <td>₱{{ number_format($basicSalary, 2) }}</td>
                            <td>₱{{ number_format($overtimePay, 2) }}</td>
                            <td>₱{{ number_format($grossPay, 2) }}</td>
                            <td>₱{{ number_format($deductions, 2) }}</td>
                            <td class="fw-bold text-success">₱{{ number_format($netPay, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $payroll->status == 'Processed' ? 'info' : ($payroll->status == 'Paid' ? 'success' : 'secondary') }}">
                                    {{ $payroll->status }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-link text-warning p-0 me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPayrollModal{{ $payroll->payroll_id }}">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>

                                <button class="btn btn-link text-primary p-0 me-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewPayrollModal{{ $payroll->payroll_id }}">
                                    <i class="bi bi-eye fs-5"></i>
                                </button>

                                <form action="{{ route('accountant.payrollprocessing.destroy', $payroll->payroll_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Delete payroll record?')">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">No payroll records found.</td>
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
</div>
@endsection
