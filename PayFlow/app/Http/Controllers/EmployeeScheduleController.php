<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSchedule;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeScheduleController extends Controller
{
    public function index()
    {
        $schedules = EmployeeSchedule::with('employee')->get();
        $employees = Employee::where('employment_type', 'Part-Time')
        ->whereDoesntHave('schedule')
        ->get();
        return view('pages.employeeschedule', compact('schedules', 'employees'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'monday' => 'nullable|string',
            'tuesday' => 'nullable|string',
            'wednesday' => 'nullable|string',
            'thursday' => 'nullable|string',
            'friday' => 'nullable|string',
            'saturday' => 'nullable|string',
            'sunday' => 'nullable|string',
        ]);

        EmployeeSchedule::create($validatedData);

        return redirect()->route('employeeschedule.index')->with('success', 'Employee schedule created successfully.');
    }

    public function update(Request $request, EmployeeSchedule $employeeschedule)
    {
        $validatedData = $request->validate([
            'monday' => 'nullable|string',
            'tuesday' => 'nullable|string',
            'wednesday' => 'nullable|string',
            'thursday' => 'nullable|string',
            'friday' => 'nullable|string',
            'saturday' => 'nullable|string',
            'sunday' => 'nullable|string',
        ]);

        $employeeschedule->update($validatedData);

        return redirect()->route('employeeschedule.index')->with('success', 'Employee schedule updated successfully.');
    }

    public function destroy(EmployeeSchedule $employeeschedule)
    {
        $employeeschedule->delete();
        return redirect()->route('employeeschedule.index')->with('success', 'Employee schedule deleted successfully.');
    }
}
