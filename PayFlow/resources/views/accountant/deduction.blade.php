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
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Deductions Table --}}
    <div class="table-responsive bg-white rounded-4 p-3 shadow-sm border">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Employee Name</th>
                    <th>SSS (5%)</th>
                    <th>PhilHealth (2.5%)</th>
                    <th>Pag-IBIG (₱100)</th>
                    <th>Withholding Tax</th>
                    <th>Total Deduction</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deductions as $deduction)
                    <tr>
                        <td class="fw-semibold">{{ $deduction->employee->first_name }} {{ $deduction->employee->last_name }}</td>
                        <td>₱{{ number_format($deduction->sss, 2) }}</td>
                        <td>₱{{ number_format($deduction->philhealth, 2) }}</td>
                        <td>₱{{ number_format($deduction->pagibig, 2) }}</td>
                        <td>₱{{ number_format($deduction->withholding_tax, 2) }}</td>
                        <td><strong>₱{{ number_format($deduction->total_deduction, 2) }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($deduction->deduction_date)->format('M d, Y') }}</td>
                        <td>
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
                <form action="{{ route('deductions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary px-4">Compute Now</button>
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
