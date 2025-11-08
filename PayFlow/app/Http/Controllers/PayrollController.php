<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class PayrollController extends Controller
{
    public function index()
    {
        $employees = Employee::with('position')->get();
        $payrolls = Payroll::with('employee.position')->latest()->get();
        $positions = Position::all();

        return view('accountant.payrollprocessing', compact('employees', 'payrolls', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after_or_equal:pay_period_start',
            'overtime_hours' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::with('position')->findOrFail($request->employee_id);

        $baseSalary = $employee->position->salary_rate;
        $overtimePay = ($request->overtime_hours ?? 0) * 100;
        $deductions = $employee->employment_type === 'Full-Time' ? $baseSalary * 0.10 : 0;
        $netPay = $baseSalary + $overtimePay - $deductions;

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

    public function edit($id)
    {
        $payroll = Payroll::with('employee.position')->findOrFail($id);
        $employees = Employee::with('position')->get();
        $positions = Position::all();

        return view('accountant.editpayroll', compact('payroll', 'employees', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after_or_equal:pay_period_start',
            'overtime_hours' => 'nullable|numeric|min:0',
        ]);

        $payroll = Payroll::findOrFail($id);
        $employee = Employee::with('position')->findOrFail($request->employee_id);

        $baseSalary = $employee->position->salary_rate;
        $overtimePay = ($request->overtime_hours ?? 0) * 100;
        $deductions = $employee->employment_type === 'Full-Time' ? $baseSalary * 0.10 : 0;
        $netPay = $baseSalary + $overtimePay - $deductions;

        $payroll->update([
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
                         ->with('success', 'Payroll record updated successfully!');
    }

    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();

        return redirect()->route('accountant.payrollprocessing')
                         ->with('success', 'Payroll record deleted successfully!');
    }
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'], 
            'new_password' => ['required', 'min:8', 'confirmed'], 
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }
}
