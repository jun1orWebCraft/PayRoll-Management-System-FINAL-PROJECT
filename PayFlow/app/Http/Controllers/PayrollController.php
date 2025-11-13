<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Attendance;
use App\Models\Deduction; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PayrollController extends Controller
{
    // Display Payroll List
    public function index(Request $request)
    {
        $positions = Position::all();
        $employees = Employee::with('position')->get();

        $query = Payroll::with('employee.position');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%");
            });
        }

        $payrolls = $query->orderByDesc('created_at')
                          ->paginate(10)
                          ->withQueryString();

        return view('accountant.payrollprocessing', compact('employees', 'payrolls', 'positions'));
    }

    // Store Payroll Record (Auto Compute based on Attendance)
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after_or_equal:pay_period_start',
        ]);

        $employee = Employee::with('position')->findOrFail($request->employee_id);

        // Fetch all attendance records within the pay period
        $attendances = Attendance::where('employee_id', $employee->employee_id)
            ->whereBetween('date', [$request->pay_period_start, $request->pay_period_end])
            ->where('status', 'Present')
            ->get();

        $totalHours = $attendances->sum('total_hours');
        $daysPresent = $attendances->count();

        // Base salary logic
        $baseSalary = $employee->position->salary_rate ?? $employee->basic_salary;

        if ($employee->employment_type === 'Full-Time') {
            // Assume 22 working days/month
            $dailyRate = $baseSalary / 22;
            $grossPay = $daysPresent * $dailyRate;
            $overtimePay = max(0, $totalHours - ($daysPresent * 8)) * ($dailyRate / 8 * 1.25);
        } else {
            // Part-time: hourly basis
            $hourlyRate = ($employee->position->salary_rate ?? 0) / 8;
            $grossPay = $totalHours * $hourlyRate;
            $overtimePay = 0;
        }

        // Get deduction for the employee in this pay period
        $deductionRecord = Deduction::where('employee_id', $employee->id)
            ->whereMonth('deduction_date', Carbon::parse($request->pay_period_end)->month)
            ->whereYear('deduction_date', Carbon::parse($request->pay_period_end)->year)
            ->first();

        $deductions = $deductionRecord->total_deduction ?? 0;

        // Net Pay
        $netPay = $grossPay + $overtimePay - $deductions;

        Payroll::create([
            'employee_id' => $employee->employee_id,
            'pay_period_start' => $request->pay_period_start,
            'pay_period_end' => $request->pay_period_end,
            'basic_salary' => $grossPay,
            'overtime_pay' => $overtimePay,
            'deductions' => $deductions,
            'net_pay' => $netPay,
            'payment_date' => now(),
            'status' => 'Processed',
        ]);

        return redirect()->route('accountant.payrollprocessing')
            ->with('success', 'Payroll generated and saved successfully!');
    }

    // Edit Payroll
    public function edit($id)
    {
        $payroll = Payroll::with('employee.position')->findOrFail($id);
        $employees = Employee::with('position')->get();
        $positions = Position::all();

        return view('accountant.editpayroll', compact('payroll', 'employees', 'positions'));
    }

    // Update Payroll
    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after_or_equal:pay_period_start',
        ]);

        $payroll = Payroll::findOrFail($id);
        $employee = Employee::with('position')->findOrFail($request->employee_id);

        // Recalculate based on updated attendance
        $attendances = Attendance::where('employee_id', $employee->employee_id)
            ->whereBetween('date', [$request->pay_period_start, $request->pay_period_end])
            ->where('status', 'Present')
            ->get();

        $totalHours = $attendances->sum('total_hours');
        $daysPresent = $attendances->count();

        $baseSalary = $employee->position->salary_rate ?? $employee->basic_salary;

        if ($employee->employment_type === 'Full-Time') {
            $dailyRate = $baseSalary / 22;
            $grossPay = $daysPresent * $dailyRate;
            $overtimePay = max(0, $totalHours - ($daysPresent * 8)) * ($dailyRate / 8 * 1.25);
        } else {
            $hourlyRate = ($employee->position->salary_rate ?? 0) / 8;
            $grossPay = $totalHours * $hourlyRate;
            $overtimePay = 0;
        }

        // Only deduct if deduction records exist
        $deductionRecords = Deduction::where('employee_id', $employee->employee_id)
            ->whereBetween('created_at', [$request->pay_period_start, $request->pay_period_end])
            ->get();

        $deductions = $deductionRecords->count() > 0
            ? $deductionRecords->sum('amount')
            : 0;

        $netPay = $grossPay + $overtimePay - $deductions;

        $payroll->update([
            'employee_id' => $employee->employee_id,
            'pay_period_start' => $request->pay_period_start,
            'pay_period_end' => $request->pay_period_end,
            'basic_salary' => $grossPay,
            'overtime_pay' => $overtimePay,
            'deductions' => $deductions,
            'net_pay' => $netPay,
            'status' => 'Processed',
        ]);

        return redirect()->route('accountant.payrollprocessing')
            ->with('success', 'Payroll updated successfully!');
    }

    // Delete Payroll Record
    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();

        return redirect()->route('accountant.payrollprocessing')
            ->with('success', 'Payroll record deleted successfully!');
    }

    // Password Update (for Accountant/User)
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
