<div class="p-4">
    <h5 class="fw-bold mb-3">
        Payslip: {{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d, Y') }} - 
        {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}
    </h5>

    <table class="table table-borderless">
        <tr>
            <th>Employee Name:</th>
            <td>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
        </tr>
        <tr>
            <th>Employee ID:</th>
            <td>{{ $payroll->employee->employee_no }}</td>
        </tr>
        <tr>
            <th>Pay Period:</th>
            <td>{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('m/d/Y') }} - 
                {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('m/d/Y') }}</td>
        </tr>
    </table>

    <div class="row text-center mt-4">
        <div class="col-md-4">
            <h6 class="text-muted">Gross Salary</h6>
            <h4 class="fw-bold text-dark">₱{{ number_format($payroll->basic_salary ?? 0, 2) }}</h4>
        </div>
        <div class="col-md-4">
            <h6 class="text-muted">Deductions</h6>
            <h4 class="fw-bold text-danger">₱{{ number_format($payroll->deductions ?? 0, 2) }}</h4>
        </div>
        <div class="col-md-4">
            <h6 class="text-muted">Net Pay</h6>
            <h4 class="fw-bold text-success">₱{{ number_format($payroll->net_pay ?? 0, 2) }}</h4>
        </div>
    </div>
</div>
