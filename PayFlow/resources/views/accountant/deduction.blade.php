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
            <i class="bi bi-plus-lg"></i> Compute Deduction
        </button>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Deductions Table --}}
    <div class="table-responsive bg-white rounded-4 p-3 shadow-sm border">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Employee Name</th>
                    <th>SSS</th>
                    <th>PhilHealth</th>
                    <th>Pag-IBIG</th>
                    <th>Withholding Tax</th>
                    <th>Total Deduction</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deductions as $deduction)
                    @php
                        $employee = $deduction->employee;
                        $total = $deduction->sss + $deduction->philhealth + $deduction->pagibig + $deduction->withholding_tax;
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        <td>₱{{ number_format($deduction->sss, 2) }}</td>
                        <td>₱{{ number_format($deduction->philhealth, 2) }}</td>
                        <td>₱{{ number_format($deduction->pagibig, 2) }}</td>
                        <td>₱{{ number_format($deduction->withholding_tax, 2) }}</td>
                        <td><strong>₱{{ number_format($total, 2) }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($deduction->deduction_date)->format('M d, Y') }}</td>
                        <td>
                            <form action="{{ route('deductions.destroy', $deduction->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Delete this deduction?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No deductions found.</td>
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
                    <h5 class="modal-title fw-semibold">Compute Deduction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('accountant.deductions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Deductions to Apply</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="sss" id="deductionSSS" checked>
                                <label class="form-check-label" for="deductionSSS">SSS (5%)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="philhealth" id="deductionPhilHealth" checked>
                                <label class="form-check-label" for="deductionPhilHealth">PhilHealth (2.5%)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="pagibig" id="deductionPagIbig" checked>
                                <label class="form-check-label" for="deductionPagIbig">Pag-IBIG (₱100)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="withholding_tax" id="deductionTax" checked>
                                <label class="form-check-label" for="deductionTax">Withholding Tax</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary px-4">Compute & Save</button>
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
