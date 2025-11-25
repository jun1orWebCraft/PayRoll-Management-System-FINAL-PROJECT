@extends('layouts.accountant.app')

@section('title', 'Employee Deductions')

@section('content')
<div class="container py-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Employee Deductions</h2>
            <p class="text-muted mb-0">Manage all employee deduction records</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addDeductionModal">
            <i class="bi bi-plus-lg"></i> Compute Deduction
        </button>
    </div>

    <!-- Success message -->
    <div id="successMsg" class="alert alert-success alert-dismissible fade show d-none">
        <span id="successText"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Deductions Table -->
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
            <tbody id="deductionTableBody">
                @forelse($deductions as $deduction)
                    <tr>
                        <td>{{ $deduction->employee?->full_name ?? 'N/A' }}</td>
                        <td>₱{{ number_format($deduction->sss, 2) }}</td>
                        <td>₱{{ number_format($deduction->philhealth, 2) }}</td>
                        <td>₱{{ number_format($deduction->pagibig, 2) }}</td>
                        <td>₱{{ number_format($deduction->withholding_tax, 2) }}</td>
                        <td><strong>₱{{ number_format($deduction->total_deduction, 2) }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($deduction->deduction_date)->format('M d, Y') }}</td>
                        <td>
                            <form action="{{ route('accountant.deductions.destroy', $deduction->id) }}" method="POST">
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

    <!-- Add Deduction Modal -->
    <div class="modal fade" id="addDeductionModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('accountant.deductions.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Compute Deduction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->employee_id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Deduction checkboxes -->
                        <div class="form-check mb-2">
                            <input type="checkbox" name="sss" id="sss" class="form-check-input">
                            <label for="sss" class="form-check-label">SSS (5%)</label>
                        </div>

                        <div class="form-check mb-2">
                            <input type="checkbox" name="philhealth" id="philhealth" class="form-check-input">
                            <label for="philhealth" class="form-check-label">PhilHealth (2.5%)</label>
                        </div>

                        <div class="form-check mb-2">
                            <input type="checkbox" name="pagibig" id="pagibig" class="form-check-input">
                            <label for="pagibig" class="form-check-label">Pag-IBIG (₱100)</label>
                        </div>

                        <div class="form-check mb-2">
                            <input type="checkbox" name="withholding_tax" id="withholding_tax" class="form-check-input">
                            <label for="withholding_tax" class="form-check-label">Withholding Tax</label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Deduction</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
