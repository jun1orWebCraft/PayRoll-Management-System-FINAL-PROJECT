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

    <!-- Table -->
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

    <!-- Add Deduction Modal -->
    <div class="modal fade" id="addDeductionModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="deductionForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Compute Deduction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Employee</label>
                            <select name="employee_id" id="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->employee_id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Checkboxes -->
                        <div class="form-check d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <input type="checkbox" name="sss" id="sss" class="form-check-input">
                                <label for="sss">SSS (5%)</label>
                            </div>
                            <input type="text" id="sss_amount" readonly class="form-control form-control-sm w-25" value="₱0.00">
                        </div>

                        <div class="form-check d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <input type="checkbox" name="philhealth" id="philhealth" class="form-check-input">
                                <label for="philhealth">PhilHealth (2.5%)</label>
                            </div>
                            <input type="text" id="philhealth_amount" readonly class="form-control form-control-sm w-25" value="₱0.00">
                        </div>

                        <div class="form-check d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <input type="checkbox" name="pagibig" id="pagibig" class="form-check-input">
                                <label for="pagibig">Pag-IBIG (₱100)</label>
                            </div>
                            <input type="text" id="pagibig_amount" readonly class="form-control form-control-sm w-25" value="₱0.00">
                        </div>

                        <div class="form-check d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <input type="checkbox" name="withholding_tax" id="withholding_tax" class="form-check-input">
                                <label for="withholding_tax">Withholding Tax</label>
                            </div>
                            <input type="text" id="tax_amount" readonly class="form-control form-control-sm w-25" value="₱0.00">
                        </div>

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <label>Total Deduction:</label>
                            <input type="text" id="total_deduction" readonly class="form-control form-control-sm w-25" value="₱0.00">
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let basicSalary = 0;
    let currentTax = 0;

    const employeeSelect = document.getElementById('employee_id');
    const sssCheckbox = document.getElementById('sss');
    const philhealthCheckbox = document.getElementById('philhealth');
    const pagibigCheckbox = document.getElementById('pagibig');
    const taxCheckbox = document.getElementById('withholding_tax');

    const sssAmount = document.getElementById('sss_amount');
    const philhealthAmount = document.getElementById('philhealth_amount');
    const pagibigAmount = document.getElementById('pagibig_amount');
    const taxAmount = document.getElementById('tax_amount');
    const totalDeduction = document.getElementById('total_deduction');

    function resetAmounts(){
        [sssCheckbox, philhealthCheckbox, pagibigCheckbox, taxCheckbox].forEach(c => c.checked = false);
        [sssAmount, philhealthAmount, pagibigAmount, taxAmount, totalDeduction].forEach(a => a.value = "₱0.00");
    }

    function updateTotal(){
        let total = [sssAmount, philhealthAmount, pagibigAmount, taxAmount].reduce((sum,a) => sum + Number(a.value.replace('₱','')), 0);
        totalDeduction.value = "₱" + total.toFixed(2);
    }

    employeeSelect.addEventListener('change', function() {
        const empId = this.value;
        if(!empId){ resetAmounts(); basicSalary = 0; return; }

        fetch(`/accountant/deductions/compute/${empId}`)
            .then(res => res.json())
            .then(data => {
                basicSalary = data.basic_salary ?? 0;
                currentTax = data.withholding_tax ?? 0;

                sssAmount.value = sssCheckbox.checked ? "₱" + (basicSalary*0.05).toFixed(2) : "₱0.00";
                philhealthAmount.value = philhealthCheckbox.checked ? "₱" + (basicSalary*0.025).toFixed(2) : "₱0.00";
                pagibigAmount.value = pagibigCheckbox.checked ? "₱100.00" : "₱0.00";
                taxAmount.value = taxCheckbox.checked ? "₱" + currentTax.toFixed(2) : "₱0.00";

                updateTotal();
            });
    });

    sssCheckbox.addEventListener('change', () => { sssAmount.value = sssCheckbox.checked ? "₱" + (basicSalary*0.05).toFixed(2) : "₱0.00"; updateTotal(); });
    philhealthCheckbox.addEventListener('change', () => { philhealthAmount.value = philhealthCheckbox.checked ? "₱" + (basicSalary*0.025).toFixed(2) : "₱0.00"; updateTotal(); });
    pagibigCheckbox.addEventListener('change', () => { pagibigAmount.value = pagibigCheckbox.checked ? "₱100.00" : "₱0.00"; updateTotal(); });
    taxCheckbox.addEventListener('change', () => { taxAmount.value = taxCheckbox.checked ? "₱" + currentTax.toFixed(2) : "₱0.00"; updateTotal(); });

    // AJAX form submit
    const deductionForm = document.getElementById('deductionForm');
    deductionForm.addEventListener('submit', function(e){
        e.preventDefault();
        const formData = new FormData(this);
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        fetch("{{ route('accountant.deductions.store') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                const d = data.deduction;
                const tbody = document.getElementById('deductionTableBody');
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${d.employee.full_name}</td>
                    <td>₱${Number(d.sss).toFixed(2)}</td>
                    <td>₱${Number(d.philhealth).toFixed(2)}</td>
                    <td>₱${Number(d.pagibig).toFixed(2)}</td>
                    <td>₱${Number(d.withholding_tax).toFixed(2)}</td>
                    <td><strong>₱${Number(d.total_deduction).toFixed(2)}</strong></td>
                    <td>${new Date().toLocaleDateString('en-US',{month:'short',day:'2-digit',year:'numeric'})}</td>
                    <td>
                        <form action="/accountant/deductions/${d.id}" method="POST">
                            <input type="hidden" name="_token" value="${csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Delete this deduction?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                `;
                tbody.prepend(row);
                resetAmounts();
                deductionForm.reset();
                bootstrap.Modal.getInstance(document.getElementById('addDeductionModal')).hide();
                document.getElementById('successText').innerText = "Deduction added successfully!";
                document.getElementById('successMsg').classList.remove('d-none');
            }
        });
    });
});
</script>
@endsection
