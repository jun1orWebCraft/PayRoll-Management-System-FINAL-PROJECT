<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Show all attendance records
        $attendances = Attendance::with('employee')->orderBy('created_at', 'desc')->get();
        return view('pages.attendance', compact('attendances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Show form to manually create attendance (optional)
        $employees = Employee::all();
        return view('attendances.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage (time-in / time-out).
     */
     public function scanner()
    {
        return view('attendance.scanner'); // <-- points to resources/views/attendance/scanner.blade.php
    }
        public function store(Request $request)
        {
            $qrData = $request->input('qr_data');

            // Find employee by QR content (could be employee_no or QR_code column)
            $employee = Employee::where('employee_no', $qrData)
                                ->orWhere('QR_code', $qrData)
                                ->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => '❌ Employee not found for scanned QR code.'
                ]);
            }

            // Check today's attendance
            $todayRecord = Attendance::where('employee_id', $employee->employee_id)
                ->whereDate('date', Carbon::today())
                ->first();

            // Set timezone explicitly (Philippines)
            $now = Carbon::now('Asia/Manila');
            $cutoff = Carbon::createFromTime(7, 30, 0, 'Asia/Manila'); // 7:30 AM

            if (!$todayRecord) {
                Attendance::create([
                    'employee_id' => $employee->employee_id,
                    'date' => $now->toDateString(),
                    'time_in' => $now,
                    // ✅ Determine status based on cutoff
                    'status' => $now->lessThanOrEqualTo($cutoff) ? 'Present' : 'Late',
                ]);
                $message = "✅ Time In recorded for {$employee->first_name} {$employee->last_name}";
            } elseif (!$todayRecord->time_out) {
                $todayRecord->update([
                    'time_out' => $now,
                ]);
                $message = "👋 Time Out recorded for {$employee->first_name} {$employee->last_name}";
            } else {
                $message = "⚠️ Already timed out today for {$employee->first_name} {$employee->last_name}";
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
            ]);
        }



    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        return view('attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        $employees = Employee::all();
        return view('attendances.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }
}
