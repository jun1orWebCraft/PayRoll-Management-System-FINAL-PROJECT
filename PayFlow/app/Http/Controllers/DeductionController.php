<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\DeductionType;
use App\Models\Employee;

class DeductionController extends Controller
{
    public function deduction()
    {
        // Fetch all deductions
        $deductions = Deduction::orderBy('created_at', 'desc')->get();

        // Fetch all full-time employees only
        $employees = Employee::where('employment_type', 'Full-Time')->get();

        // Fetch all available deduction types (Tax, SSS, PhilHealth, etc.)
        $deductionTypes = DeductionType::all();

        // Send to view
        return view('accountant.deduction', compact('deductions', 'employees', 'deductionTypes'));
    }

    public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'type_id' => 'required|exists:types,id',
        'amount' => 'required|numeric|min:0',
    ]);

    Deduction::create([
        'employee_id' => $request->employee_id,
        'type_id' => $request->type_id,
        'amount' => $request->amount,
    ]);

    return redirect()->route('accountant.deduction')->with('success', 'Deduction added successfully!');
}

}

