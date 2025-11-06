<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class MarkAbsent extends Command
{
    protected $signature = 'attendance:mark-absent';
    protected $description = 'Automatically mark absent for employees who did not check in today';

    public function handle()
    {
        $today = Carbon::today('Asia/Manila');

        $employees = Employee::all();

        foreach ($employees as $employee) {
            // Skip if already has attendance today
            $attendance = Attendance::where('employee_id', $employee->employee_id)
                            ->whereDate('date', $today)
                            ->first();
            if ($attendance) continue;

            // Skip if employee has approved leave today
            $onLeave = LeaveRequest::where('employee_id', $employee->employee_id)
                        ->where('status', 'Approved')
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->exists();
            if ($onLeave) continue;

            // Create absent record
            Attendance::create([
                'employee_id' => $employee->employee_id,
                'date' => $today,
                'status' => 'Absent',
                'time_in' => null,
                'time_out' => null,
                'total_hours' => 0,
            ]);
        }

        $this->info('Absent marking completed successfully.');
    }
}
