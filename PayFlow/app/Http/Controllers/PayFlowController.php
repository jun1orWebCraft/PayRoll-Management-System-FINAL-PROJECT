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

    if ($user->name === 'Accountant') {
        // Logic for accountant dashboard
        // Example: show accountant-specific data or just a custom view
        return view('accountant.dashboard', compact('user'));
    } elseif ($user->name === 'HR') {
        // HR dashboard logic as in your original code
        $leaveRequests = LeaveRequest::with('employee')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            $recentActivities = ActivityLog::orderBy('created_at', 'desc')
            ->take(6) // limit to 6 entries
            ->get();

        $activeEmployees = Employee::where('status', 'Active')->count();
        $onLeave = Employee::where('status', 'On Leave')->count();
        $totalEmployees = Employee::count();
        $pendingPayrolls = Payroll::where('status', 'Pending')->count();
        return view('pages.dashboard', compact('totalEmployees', 'onLeave', 'activeEmployees', 'leaveRequests', 'pendingPayrolls', 'recentActivities'));
    } 
}


    public function employees()  
    {
        $positions = Position::all(); 
        return view('pages.employees', compact('positions'));
    }

    public function attendance(){
        return view('pages.attendance');
    }

    public function taxanddeductions()
    {
        return view('pages.taxanddeductions');
    }                                                                                                                               

    public function payrollprocessing()
    {
        return view('pages.payrollprocessing');
    }

    public function reports()
{
    // ✅ Employees by Position (Pie Chart)
    $positions = Position::withCount('employees')->get();
    $labels = $positions->pluck('position_name');
    $data = $positions->pluck('employees_count');

    // ✅ Weekly Attendance Report (Bar Chart)
    $startOfWeek = Carbon::now()->startOfWeek();
    $endOfWeek = Carbon::now()->endOfWeek();

    // Default days (Mon–Sun)
    $weekDays = collect();
    for ($i = 0; $i < 7; $i++) {
        $day = $startOfWeek->copy()->addDays($i);
        $weekDays[$day->format('D')] = 0;
    }

    $attendanceData = Attendance::selectRaw('DATE(date) as day, COUNT(*) as total')
        ->whereBetween('date', [$startOfWeek, $endOfWeek])
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

    // ✅ Compute Average Attendance Rate
    $totalEmployees = \App\Models\Employee::count(); // total employees
    $totalAttendance = Attendance::whereBetween('date', [$startOfWeek, $endOfWeek])
        ->where('status', 'Present')
        ->count();

    // Each employee is expected to attend 7 days (Mon–Sun)
    $expectedAttendance = $totalEmployees * 7;

    $averageAttendanceRate = $expectedAttendance > 0
        ? round(($totalAttendance / $expectedAttendance) * 100, 2)
        : 0;
    $averageSalary = Payroll::avg('basic_salary') ?? 0;
    $totalPayrollCost = Payroll::sum('net_pay') ?? 0;
    $totalDeduction = Deduction::sum('amount') ?? 0;
    // Pass all data to view
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

    $user = auth()->user(); // HR user

    if (! Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect.']);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return back()->with('status', 'Password updated successfully.');
}

}
