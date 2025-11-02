<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function payrollprocessing()
{
    $employees = Employee::all();
    $payrolls = Payroll::with('employee')->get();
    $positions = Position::all();

    return view('accountant.payrollprocessing', compact('employees', 'payrolls', 'positions'));
}

public function store(Request $request)
{
    // Fetch employee with their position
    $employee = Employee::with('position')->findOrFail($request->employee_id);

    //  Get base salary from position
    $baseSalary = $employee->position->salary_rate;

    //  Optional: compute overtime
    $overtimeRate = 100; // Example: ₱100 per hour
    $overtimePay = $request->overtime_hours ? $request->overtime_hours * $overtimeRate : 0;

    // ⚖️ Compute deductions based on employment type (example)
    $deductions = 0;
    if ($employee->employment_type === 'Full-Time') {
        // Example: 10% of salary for all deductions combined (SSS, PhilHealth, etc.)
        $deductions = $baseSalary * 0.10;
    }

    // ✅ Compute net pay
    $netPay = $baseSalary + $overtimePay - $deductions;

    // 💾 Save payroll record
    Payroll::create([
        'employee_id' => $employee->employee_id,
        'pay_period_start' => $request->pay_period_start,
        'pay_period_end' => $request->pay_period_end,
        'basic_salary' => $baseSalary,
        'overtime_pay' => $overtimePay,
        'deductions' => $deductions,
        'net_pay' => $netPay,
        'status' => 'Processed',
    ]);

    return redirect()->route('accountant.payrollprocessing')
                     ->with('success', 'Payroll record saved successfully!');
}

}