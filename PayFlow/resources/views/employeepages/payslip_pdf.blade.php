<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .section-title {
            font-weight: bold;
            margin-top: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
        }
        th {
            text-align: left;
        }
        .summary {
            margin-top: 20px;
            text-align: center;
        }
        .summary h3 {
            margin: 6px 0;
        }
        .netpay {
            color: green;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Employee Payslip</h2>
        <p>Pay Period: {{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d, Y') }} - 
           {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}</p>
    </div>

    <div>
        <h4 class="section-title">Employee Information</h4>
        <table>
            <tr>
                <th>Employee Name:</th>
                <td>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
            </tr>
            <tr>
                <th>Employee No:</th>
                <td>{{ $payroll->employee->employee_no }}</td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>{{ $payroll->employee->email }}</td>
            </tr>
        </table>
    </div>

    <div>
        <h4 class="section-title">Earnings</h4>
        <table>
            <tr>
                <th>Basic Salary</th>
                <td>₱{{ number_format($payroll->basic_salary ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Allowances</th>
                <td>₱{{ number_format($payroll->allowances ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Overtime</th>
                <td>₱{{ number_format($payroll->overtime_pay ?? 0, 2) }}</td>
            </tr>
        </table>
    </div>

    <div>
        <h4 class="section-title">Deductions</h4>
        <table>
            <tr>
                <th>Tax</th>
                <td>₱{{ number_format($payroll->tax ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Other Deductions</th>
                <td>₱{{ number_format($payroll->deductions ?? 0, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <h3>Gross Salary: ₱{{ number_format($payroll->basic_salary ?? 0, 2) }}</h3>
        <h3 class="netpay">Net Pay: ₱{{ number_format($payroll->net_pay ?? 0, 2) }}</h3>
    </div>

    <div style="text-align: center; margin-top: 40px; font-size: 11px; color: #777;">
        Generated on {{ now()->format('M d, Y h:i A') }}
    </div>

</body>
</html>
