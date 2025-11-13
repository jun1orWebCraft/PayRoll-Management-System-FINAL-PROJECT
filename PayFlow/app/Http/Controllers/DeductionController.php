<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeductionController extends Controller
{
    public function deduction()
    {
        // Eager load employee relation
    $deductions = Deduction::with('employee')
                    ->orderBy('deduction_date', 'desc')
                    ->get();

    $employees = Employee::where('employment_type', 'Full-Time')->get();

    return view('accountant.deduction', compact('deductions', 'employees'));
    }

    public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'deduction_date' => 'nullable|date',
    ]);

    $employee = Employee::findOrFail($request->employee_id);
    $monthlySalary = $employee->monthly_salary;

    $sss = $employee->is_availing_sss ? $monthlySalary * 0.05 : 0;
    $philhealth = $employee->is_availing_philhealth ? max(500, min($monthlySalary * 0.025, 2500)) : 0;
    $pagibig = $employee->is_availing_pagibig ? 100 : 0;
    $withholdingTax = $employee->is_subject_to_tax ? $this->computeWithholdingTax($monthlySalary) : 0;

    $total = $sss + $philhealth + $pagibig + $withholdingTax;

    Deduction::create([
        'employee_id' => $employee->id,
        'sss' => $sss,
        'philhealth' => $philhealth,
        'pagibig' => $pagibig,
        'withholding_tax' => $withholdingTax,
        'total_deduction' => $total,
        'deduction_date' => $request->deduction_date ?? Carbon::now(),
    ]);

    return redirect()->route('accountant.deduction')->with('success', 'Deduction computed successfully!');
}

    private function computeWithholdingTax($monthlySalary)
    {
        $annual = $monthlySalary * 12;
        $tax = 0;

        if ($annual <= 250000) {
            $tax = 0;
        } elseif ($annual <= 400000) {
            $tax = ($annual - 250000) * 0.20;
        } elseif ($annual <= 800000) {
            $tax = 30000 + ($annual - 400000) * 0.25;
        } elseif ($annual <= 2000000) {
            $tax = 130000 + ($annual - 800000) * 0.30;
        } elseif ($annual <= 8000000) {
            $tax = 490000 + ($annual - 2000000) * 0.32;
        } else {
            $tax = 2410000 + ($annual - 8000000) * 0.35;
        }

        return $tax / 12;
    }
}
