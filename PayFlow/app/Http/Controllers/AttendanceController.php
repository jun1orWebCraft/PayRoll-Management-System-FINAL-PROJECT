<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\EmployeeSchedule;

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

        if (! $employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found.']);
        }

        $now = Carbon::now('Asia/Manila');
        $today = $now->toDateString();
        $dayName = strtolower($now->format('l'));

        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', $today)
            ->first();

        $parseSchedule = function (?string $schedStr) use ($today) {
            $result = [
                'morning' => null,
                'afternoon' => null,
            ];

            if (! $schedStr) return $result;

            $parts = array_map('trim', explode('/', $schedStr));
            if (count($parts) === 1) $parts[] = '00:00-00:00';

            list($morningStr, $afternoonStr) = $parts;

            $toBlock = function ($blockStr) use ($today) {
                $blockStr = trim($blockStr);
                if ($blockStr === '' || $blockStr === '00:00-00:00') return null;

                if (strpos($blockStr, '-') === false) return null;
                [$s, $e] = array_map('trim', explode('-', $blockStr));
                try {
                    $start = Carbon::createFromFormat('Y-m-d H:i', "$today $s", 'Asia/Manila');
                    $end   = Carbon::createFromFormat('Y-m-d H:i', "$today $e", 'Asia/Manila');
                } catch (\Exception $ex) {
                    return null;
                }
                return ['start' => $start, 'end' => $end];
            };

            $result['morning'] = $toBlock($morningStr);
            $result['afternoon'] = $toBlock($afternoonStr);

            return $result;
        };

        $overlapMinutes = function (Carbon $aStart, Carbon $aEnd, Carbon $bStart, Carbon $bEnd) {
            $start = $aStart->greaterThan($bStart) ? $aStart : $bStart;
            $end   = $aEnd->lessThan($bEnd) ? $aEnd : $bEnd;
            if ($start->gte($end)) return 0;
            return $start->diffInMinutes($end);
        };

        if (!$attendance) {
            if ($employee->employment_type === 'Full-Time') {
                $shiftStart = Carbon::createFromFormat('Y-m-d H:i', "$today 07:00", 'Asia/Manila');
                $status = $now->lte($shiftStart) ? 'Working' : 'Late';

                Attendance::create([
                    'employee_id' => $employee->employee_id,
                    'date' => $today,
                    'time_in' => $now,
                    'status' => $status,
                    'total_hours' => null,
                    'over_time' => 0,
                ]);

                return response()->json(['status' => 'success', 'message' => "Time In recorded for {$employee->first_name} {$employee->last_name}. Status: {$status}"]);
            }

            if ($employee->employment_type === 'Part-Time') {
                $sched = EmployeeSchedule::where('employee_id', $employee->employee_id)->first();
                $blocks = $parseSchedule($sched ? $sched->{$dayName} : null);

                $morning = $blocks['morning'];
                $afternoon = $blocks['afternoon'];

                $applicableStart = null;

                if ($morning) {
                    if ($now->lte($morning['end'])) {
                        $applicableStart = $morning['start'];
                    } else {
                        if ($afternoon) {
                            $applicableStart = $afternoon['start'];
                        } else {
                            $applicableStart = $morning['start'];
                        }
                    }
                } else {
                    if ($afternoon) {
                        $applicableStart = $afternoon['start'];
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'No schedule assigned for today.']);
                    }
                }

                $status = $now->lte($applicableStart) ? 'Working' : 'Late';

                Attendance::create([
                    'employee_id' => $employee->employee_id,
                    'date' => $today,
                    'time_in' => $now,
                    'status' => $status,
                    'total_hours' => null,
                    'over_time' => 0,
                ]);

                return response()->json(['status' => 'success', 'message' => "Time In recorded for {$employee->first_name} {$employee->last_name}. Status: {$status}"]);
            }
        }

        if ($attendance && !$attendance->time_out) {
            $timeIn = Carbon::parse($attendance->time_in)->setTimezone('Asia/Manila');

            if ($employee->employment_type === 'Full-Time') {
                $regularEnd = Carbon::createFromFormat('Y-m-d H:i', "$today 17:00", 'Asia/Manila');
                $totalHours = $timeIn->diffInMinutes($now) / 60;
                $overHours = $now->greaterThan($regularEnd) ? $regularEnd->diffInMinutes($now) / 60 : 0;

                $attendance->update([
                    'time_out' => $now,
                    'total_hours' => round($totalHours, 2),
                    'over_time' => round($overHours, 2),
                    'status' => 'Present',
                ]);

                return response()->json(['status' => 'success', 'message' => "Time Out recorded for {$employee->first_name} {$employee->last_name}. Total: ".round($totalHours,2)." hrs, OT: ".round($overHours,2)." hrs"]);
            }

            if ($employee->employment_type === 'Part-Time') {
                $sched = EmployeeSchedule::where('employee_id', $employee->employee_id)->first();
                $blocks = $parseSchedule($sched ? $sched->{$dayName} : null);

                $morning = $blocks['morning'];
                $afternoon = $blocks['afternoon'];

                if (! $morning && ! $afternoon) {
                    return response()->json(['status' => 'error', 'message' => 'No schedule assigned for today.']);
                }

                $minutes = 0;

                if ($morning) {
                    $minutes += $overlapMinutes($timeIn, $now, $morning['start'], $morning['end']);
                }

                if ($afternoon) {
                    $minutes += $overlapMinutes($timeIn, $now, $afternoon['start'], $afternoon['end']);
                }

                $lastBlockEnd = $afternoon ?? $morning ? $afternoon['end'] ?? $morning['end'] : null;
                $recordedTimeOut = $now->greaterThan($lastBlockEnd) ? $lastBlockEnd : $now;

                $attendance->update([
                    'time_out' => $recordedTimeOut,
                    'total_hours' => round($minutes / 60, 2),
                    'over_time' => 0,
                    'status' => 'Present',
                ]);

                return response()->json(['status'=>'success','message'=>"Time Out recorded for {$employee->first_name} {$employee->last_name}. Hours: ".round($minutes/60,2)]);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Already timed out today.']);
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
