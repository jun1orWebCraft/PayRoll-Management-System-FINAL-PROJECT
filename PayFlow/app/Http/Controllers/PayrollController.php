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

    // Store Payroll Record
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after_or_equal:pay_period_start',
        ]);

        $employee = Employee::with('position')->findOrFail($request->employee_id);

        // Calculate working days in the pay period (Mon-Sat)
        $periodStart = Carbon::parse($request->pay_period_start);
        $periodEnd = Carbon::parse($request->pay_period_end);
        $workingDays = 0;
        for ($date = $periodStart; $date->lte($periodEnd); $date->addDay()) {
            if ($date->isWeekday()) {
                $workingDays++;
            }
        }

        // Fetch attendance records
        $attendances = Attendance::where('employee_id', $employee->employee_id)
            ->whereBetween('date', [$request->pay_period_start, $request->pay_period_end])
            ->where('status', 'Present')
            ->get();

        $daysPresent = $attendances->count();
        $totalHours = $attendances->sum('total_hours');

        // Base salary
        $baseSalary = $employee->position->salary_rate ?? $employee->basic_salary;

        if ($employee->employment_type === 'Full-Time') {
            $dailyRate = $baseSalary / $workingDays;
            $basicPay = $daysPresent * $dailyRate;
            $overtimePay = max(0, $totalHours - ($daysPresent * 8)) * ($dailyRate / 8 * 1.25);
        } else {
            $hourlyRate = ($employee->position->salary_rate ?? $baseSalary) / 8;
            $basicPay = $totalHours * $hourlyRate;
            $overtimePay = 0;
        }

        $grossPay = $basicPay + $overtimePay;

        // Fetch deductions
        $deductions = Deduction::where('employee_id', $employee->employee_id)
                        ->whereBetween('deduction_date', [$request->pay_period_start, $request->pay_period_end])
                        ->sum('total_deduction');

        // Net pay
        $netPay = $grossPay - $deductions;

        // Save payroll
        Payroll::create([
            'employee_id' => $employee->employee_id,
            'pay_period_start' => $request->pay_period_start,
            'pay_period_end' => $request->pay_period_end,
            'basic_salary' => $basicPay,
            'overtime_pay' => $overtimePay,
            'gross_pay' => $grossPay, // optional column if you have it
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

        $periodStart = Carbon::parse($request->pay_period_start);
        $periodEnd = Carbon::parse($request->pay_period_end);
        $workingDays = 0;
        for ($date = $periodStart; $date->lte($periodEnd); $date->addDay()) {
            if ($date->isWeekday()) $workingDays++;
        }

        $attendances = Attendance::where('employee_id', $employee->employee_id)
            ->whereBetween('date', [$request->pay_period_start, $request->pay_period_end])
            ->where('status', 'Present')
            ->get();

        $daysPresent = $attendances->count();
        $totalHours = $attendances->sum('total_hours');

        $baseSalary = $employee->position->salary_rate ?? $employee->basic_salary;

        if ($employee->employment_type === 'Full-Time') {
            $dailyRate = $baseSalary / $workingDays;
            $basicPay = $daysPresent * $dailyRate;
            $overtimePay = max(0, $totalHours - ($daysPresent * 8)) * ($dailyRate / 8 * 1.25);
        } else {
            $hourlyRate = ($employee->position->salary_rate ?? $baseSalary) / 8;
            $basicPay = $totalHours * $hourlyRate;
            $overtimePay = 0;
        }

        $grossPay = $basicPay + $overtimePay;

        $deductions = Deduction::where('employee_id', $employee->employee_id)
                        ->whereBetween('deduction_date', [$request->pay_period_start, $request->pay_period_end])
                        ->sum('total_deduction');

        $netPay = $grossPay - $deductions;

        $payroll->update([
            'employee_id' => $employee->employee_id,
            'pay_period_start' => $request->pay_period_start,
            'pay_period_end' => $request->pay_period_end,
            'basic_salary' => $basicPay,
            'overtime_pay' => $overtimePay,
            'gross_pay' => $grossPay,
            'deductions' => $deductions,
            'net_pay' => $netPay,
            'status' => 'Processed',
        ]);

        return redirect()->route('accountant.payrollprocessing')
            ->with('success', 'Payroll updated successfully!');
    }

    // Delete Payroll
    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();

        return redirect()->route('accountant.payrollprocessing')
            ->with('success', 'Payroll record deleted successfully!');
    }

    // Update Password
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
