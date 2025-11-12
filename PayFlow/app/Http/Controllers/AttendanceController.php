<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee')->orderBy('date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('name')) {
            $query->where('status', $request->name);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $attendances = $query->get();

        return view('pages.attendance', compact('attendances'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('attendances.create', compact('employees'));
    }

    public function scanner()
    {
        return view('attendance.scanner');
    }

    public function store(Request $request)
    {
        return $this->processAttendance($request->input('qr_data'));
    }

    public function storeScanner(Request $request)
    {
        return $this->processAttendance($request->input('qr_data'));
    }

    private function processAttendance($qrData)
    {
        $employee = Employee::where('employee_no', $qrData)
                            ->orWhere('QR_code', $qrData)
                            ->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found.'
            ]);
        }

        $todayRecord = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', Carbon::today())
            ->first();

        $now = Carbon::now('Asia/Manila');
        $cutoff = Carbon::createFromTime(7, 30, 0, 'Asia/Manila');

        if (!$todayRecord) {
            Attendance::create([
                'employee_id' => $employee->employee_id,
                'date' => $now->toDateString(),
                'time_in' => $now,
                'status' => $now->lessThanOrEqualTo($cutoff) ? 'Present' : 'Late',
            ]);
            $message = "Time In recorded for {$employee->first_name} {$employee->last_name}";
        } elseif (!$todayRecord->time_out) {
            $timeIn = Carbon::parse($todayRecord->time_in);
            $totalHours = $timeIn->diffInMinutes($now) / 60;

            $todayRecord->update([
                'time_out' => $now,
                'total_hours' => round($totalHours, 2),
            ]);
            $message = "Time Out recorded for {$employee->first_name} {$employee->last_name}";
        } else {
            $message = "Already timed out today for {$employee->first_name} {$employee->last_name}";
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    public function show(Attendance $attendance)
    {
        return view('attendances.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $employees = Employee::all();
        return view('attendances.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'time_in' => 'nullable|date',
            'time_out' => 'nullable|date|after_or_equal:time_in',
        ]);

        $attendance->update([
            'employee_id' => $request->employee_id,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
        ]);

        return redirect()->route('attendances.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }
}
