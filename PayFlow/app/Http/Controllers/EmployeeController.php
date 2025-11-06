<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\ActivityLog;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use App\Mail\EmployeeWelcomeMail;
use App\Models\LeaveRequest;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('position');
        $positions = Position::all();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        $employees = $query->paginate(10);
        return view('pages.employees', compact('employees', 'positions'));
    }

    public function create()
    {
        $positions = Position::all();
        return view('pages.employees', compact('positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'birthday' => 'required|date',
            'age' => 'required|integer',
            'hire_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive,On Leave',
            'employment_type' => 'required|string|max:50',
            'position_id' => 'required|integer',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $employee_no = Employee::generateEmployeeNo();
        $randomPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()'), 0, 8);
        $profilePicturePath = $request->hasFile('profile_picture') 
            ? $request->file('profile_picture')->store('profile_pictures', 'public') 
            : null;

        $qrCodeResult = Builder::create()
            ->writer(new PngWriter())
            ->data($employee_no)
            ->size(200)
            ->encoding(new Encoding('UTF-8'))
            ->build();

        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodeResult->getString());

        $employee = Employee::create([
            'employee_no' => $employee_no,
            'QR_code' => $qrCodeBase64,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($randomPassword),
            'phone' => $request->phone,
            'address' => $request->address,
            'birthday' => $request->birthday,
            'age' => $request->age,
            'hire_date' => $request->hire_date,
            'basic_salary' => $request->basic_salary,
            'status' => $request->status,
            'employment_type' => $request->employment_type,
            'position_id' => $request->position_id,
            'profile_picture' => $profilePicturePath,
        ]);

        try {
            Mail::to($employee->email)->send(new EmployeeWelcomeMail($employee, $randomPassword, $qrCodeBase64, null));
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
        }

        ActivityLog::create([
            'action' => "New employee {$employee->first_name} {$employee->last_name} added",
            'icon' => 'bi-person-plus',
            'color' => 'text-info',
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee added successfully and welcome email sent.');
    }

    public function show(Employee $employee)
    {
        return view('pages.employees', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $positions = Position::all();
        return view('pages.employees', compact('employee', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->employee_id . ',employee_id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'basic_salary' => 'required|numeric|min:0',
            'age' => 'required|integer',
            'status' => 'required|in:Active,Inactive,On Leave',
            'employment_type' => 'required|string|max:50',
            'position_id' => 'required|integer',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|min:6',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($employee->profile_picture) {
                Storage::disk('public')->delete($employee->profile_picture);
            }
            $employee->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $employee->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'age' => $request->age,
            'basic_salary' => $request->basic_salary,
            'status' => $request->status,
            'employment_type' => $request->employment_type,
            'position_id' => $request->position_id,
            'profile_picture' => $employee->profile_picture,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->profile_picture) {
            Storage::disk('public')->delete($employee->profile_picture);
        }

        $employeeName = $employee->first_name . ' ' . $employee->last_name;
        $employee->delete();

        ActivityLog::create([
            'action' => "Employee {$employeeName} deleted",
            'icon' => 'bi-person-dash',
            'color' => 'text-danger',
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function dashboard()
    {
        $employee = Auth::user(); 

        $baseBalances = [
            'Annual Leave'    => 10,
            'Vacation Leave'  => 5,
            'Sick Leave'      => 10,
            'Emergency Leave' => 3,
            'Maternity Leave' => 105,
            'Paternity Leave' => 7,
        ];

        $usedLeaves = array_fill_keys(array_keys($baseBalances), 0);

        $approvedLeaves = LeaveRequest::where('employee_id', $employee->employee_id)
                            ->where('status', 'Approved')
                            ->get();

        foreach ($approvedLeaves as $leave) {
            if (isset($usedLeaves[$leave->leave_type])) {
                $start = \Carbon\Carbon::parse($leave->start_date);
                $end = \Carbon\Carbon::parse($leave->end_date);
                $daysTaken = $start->diffInDays($end) + 1;
                $usedLeaves[$leave->leave_type] += $daysTaken;
            }
        }

        $leaveProgress = [];
        foreach ($baseBalances as $type => $total) {
            $used = $usedLeaves[$type];
            $remaining = max($total - $used, 0);
            $percentage = $total > 0 ? ($used / $total) * 100 : 0;
            $leaveProgress[$type] = [
                'used' => $used,
                'remaining' => $remaining,
                'total' => $total,
                'percentage' => $percentage,
            ];
        }

        $leaveRequests = LeaveRequest::where('employee_id', $employee->employee_id)
                            ->orderBy('start_date', 'desc')
                            ->get();

        $notifications = Notification::where('employee_id', $employee->employee_id)
                            ->orderBy('created_at', 'desc')
                            ->get();


        $today = \Carbon\Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $attendances = Attendance::where('employee_id', $employee->employee_id)
                        ->whereBetween('date', [$startOfMonth, $endOfMonth])
                        ->get();

        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'Present')->count(); 
        $absentDays = $attendances->where('status', 'Absent')->count();
        $onLeaveDays = $attendances->where('status', 'On Leave')->count(); 

        $totalHoursWorked = 0;
        $overtimeHours = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->time_in && $attendance->time_out && $attendance->status === 'Present') { 
                $end = \Carbon\Carbon::parse($attendance->time_out);

                $hoursWorked = $end->diffInMinutes($start) / 60; 
                $totalHoursWorked += $hoursWorked;

                if ($hoursWorked > 8) {
                    $overtimeHours += $hoursWorked - 8;
                }
            }
        }

        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;


        $latestLeaveRequest = LeaveRequest::where('employee_id', $employee->employee_id)
                            ->orderBy('created_at', 'desc')
                            ->first();

        $latestLeaveType = $latestLeaveRequest ? $latestLeaveRequest->leave_type : 'Annual Leave';
        $latestLeaveRemaining = $leaveProgress[$latestLeaveType]['remaining'] ?? 0;

        return view('employeepages.dashboard', compact(
            'leaveRequests',
            'leaveProgress',
            'notifications',
            'attendanceRate',
            'latestLeaveType',
            'latestLeaveRemaining',
            'presentDays',
            'absentDays',
            'totalHoursWorked',
            'overtimeHours'
        ));
    }


    public function markAllRead()
    {
        $employee = Auth::user();
        Notification::where('employee_id', $employee->employee_id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
        return back();
    }

    public function profile()
    {
        $employee = auth()->guard('employee')->user(); 
        $employee->load('position'); 
        return view('employeepages.profile', compact('employee'));
    }

    public function updateProfile(Request $request)
    {
        $employee = auth()->guard('employee')->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($employee->profile_picture && Storage::disk('public')->exists($employee->profile_picture)) {
                Storage::disk('public')->delete($employee->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $employee->profile_picture = $path;
        }

        $employee->first_name = $request->first_name;
        $employee->last_name = $request->last_name;
        $employee->phone = $request->phone;
        $employee->address = $request->address;

        $employee->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function settings()
    {
        return view('employeepages.settings');
    }

    public function request()
    {
        $leaveTypes = LeaveRequest::LEAVE_TYPES;

        $baseBalances = [
            'Annual Leave'    => 10,
            'Vacation Leave'  => 5,
            'Sick Leave'      => 10,
            'Emergency Leave' => 3,
            'Maternity Leave' => 105,
            'Paternity Leave' => 7,
        ];

        $employee = auth()->user(); 

        $leaveBalances = [];
        foreach ($leaveTypes as $type) {
            $leaveBalances[$type] = $baseBalances[$type] ?? 0;
        }

        $approvedLeaves = LeaveRequest::where('employee_id', $employee->employee_id)
                            ->where('status', 'Approved')
                            ->whereYear('start_date', now()->year)
                            ->get();

        foreach ($approvedLeaves as $leave) {
            if (isset($leaveBalances[$leave->leave_type])) {
                $start = \Carbon\Carbon::parse($leave->start_date);
                $end = \Carbon\Carbon::parse($leave->end_date);
                $daysTaken = $start->diffInDays($end) + 1;
                $leaveBalances[$leave->leave_type] = max(0, $leaveBalances[$leave->leave_type] - $daysTaken);
            }
        }

        return view('employeepages.request', compact('leaveTypes', 'leaveBalances'));
    }
}
