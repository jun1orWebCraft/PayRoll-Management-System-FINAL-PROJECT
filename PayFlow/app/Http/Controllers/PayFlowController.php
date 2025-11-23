<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\Position;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Deduction;
use App\Models\Payroll;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;

class PayFlowController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role === 'Accountant') {
            return view('accountant.dashboard', compact('user'));
        } elseif ($user->role === 'HR') {
            $today = Carbon::today();

            $endedLeaves = LeaveRequest::where('status', 'Approved')
                ->whereDate('end_date', '<', $today)
                ->with('employee')
                ->get();

            foreach ($endedLeaves as $leave) {
                if ($leave->employee && $leave->employee->status === 'On Leave') {
                    $leave->employee->status = 'Active';
                    $leave->employee->save();
                }
            }

            $leaveRequests = LeaveRequest::with('employee')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $recentActivities = ActivityLog::orderBy('created_at', 'desc')
                ->take(6)
                ->get();

            $activeEmployees = Employee::where('status', 'Active')->count();
            $onLeave = Employee::where('status', 'On Leave')->count();
            $totalEmployees = Employee::count();
            $pendingPayrolls = Payroll::where('status', 'Pending')->count();

            return view('pages.dashboard', compact(
                'totalEmployees',
                'onLeave',
                'activeEmployees',
                'leaveRequests',
                'pendingPayrolls',
                'recentActivities'
            ));
        }
    }

    public function approve($leave_request_id)
    {
        $leave = LeaveRequest::findOrFail($leave_request_id);
        $leave->status = 'Approved';
        $leave->approved_by = auth()->id();
        $leave->save();

        $employee = $leave->employee;
        if ($employee) {
            $employee->status = 'On Leave';
            $employee->save();
        }

        return redirect()->back()->with('success', 'Leave request approved and employee set to On Leave.');
    }

    public function reject($leave_request_id)
    {
        $leave = LeaveRequest::findOrFail($leave_request_id);
        $leave->status = 'Rejected';
        $leave->save();

        return redirect()->back()->with('success', 'Leave request rejected successfully.');
    }

    public function employees()
    {
        $positions = Position::all();
        return view('pages.employees', compact('positions'));
    }

    public function attendance()
    {
        return view('pages.attendance');
    }

    public function payrolldata()
    {
        $payrolls = Payroll::with('employee')->orderBy('payment_date', 'desc')->get();
        $previousPayroll = Payroll::where('status', 'Paid')->latest('payment_date')->first();
        $upcomingPayroll = Payroll::where('status', 'Pending')->latest('payment_date')->first();

        $totalOutstanding = $payrolls->sum('net_pay');
        $totalPayment = $payrolls->where('status', 'Paid')->sum('net_pay');
        $totalPending = $payrolls->where('status', 'Pending')->sum('net_pay');

        $thisMonthTotal = Payroll::whereYear('payment_date', now()->year)
        ->whereMonth('payment_date', now()->month)
        ->sum('net_pay');

        $lastMonth = now()->subMonth();
        $lastMonthTotal = Payroll::whereYear('payment_date', $lastMonth->year)
            ->whereMonth('payment_date', $lastMonth->month)
            ->sum('net_pay');

        if ($lastMonthTotal > 0) {
            $percentageChange = (($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100;
        } else {
            $percentageChange = 0;
        }
        return view('pages.payrolldata', compact(
            'payrolls',
            'previousPayroll',
            'upcomingPayroll',
            'totalOutstanding',
            'totalPayment',
            'totalPending',
            'percentageChange'
        ));
    }

    public function payrollprocessing()
    {
        return view('pages.payrollprocessing');
    }

    public function reports()
    {
        $positions = Position::withCount('employees')->get();
        $labels = $positions->pluck('position_name');    
        $data = $positions->pluck('employees_count');    

        $startOfWeek = Carbon::now()->startOfWeek();  
        $endOfWeek = Carbon::now()->endOfWeek();      

        $weekDays = collect();
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $weekDays[$day->format('D')] = 0;
        }

        $attendanceData = Attendance::selectRaw('DATE(date) as day, COUNT(*) as total')
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->where('status', 'Present')
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get();

        foreach ($attendanceData as $record) {
            $dayAbbrev = Carbon::parse($record->day)->format('D');
            $weekDays[$dayAbbrev] = $record->total;
        }

        $attendanceLabels = $weekDays->keys();    
        $attendanceCounts = $weekDays->values();  

        $totalEmployees = Employee::count();
        $totalAttendance = Attendance::whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->where('status', 'Present')
            ->count();

        $expectedAttendance = $totalEmployees * 7; 
        $averageAttendanceRate = $expectedAttendance > 0
            ? round(($totalAttendance / $expectedAttendance) * 100, 2)
            : 0;

        $averageSalary = Payroll::avg('basic_salary') ?? 0;
        $totalPayrollCost = Payroll::sum('net_pay') ?? 0;
        $totalDeduction = Payroll::sum('deductions') ?? 0;

        return view('pages.reports', compact(
            'labels',
            'data',
            'attendanceLabels',
            'attendanceCounts',
            'averageAttendanceRate',
            'totalDeduction',
            'totalPayrollCost',
            'averageSalary'
        ));
    }

    public function settings()
    {
        return view('pages.settings');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('status', 'Password updated successfully.');
    }
    public function employeeschedule()
    {
        return view('pages.employeeschedule');
    }
}
