<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\DeductionType;
use App\Models\Employee;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    public function deduction()
    {
        // Fetch all deductions
        $deductions = Deduction::orderBy('created_at', 'desc')->get();

        // Fetch all full-time employees only
        $employees = Employee::where('employment_type', 'Full-Time')->get();

        // Fetch all available deduction types (Tax, SSS, PhilHealth, Pag-IBIG, etc.)
        $deductionTypes = DeductionType::all();

        return view('accountant.deduction', compact('deductions', 'employees', 'deductionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $monthlySalary = $employee->monthly_salary; // Make sure your Employee model has this column

        // --- Deduction Computations (Employee Share Only) ---
        $sss = $monthlySalary * 0.05; // 5% employee share
        $philhealth = max(500, min($monthlySalary * 0.025, 2500)); // 2.5% capped between ₱500–₱2,500
        $pagibig = 100; // Fixed ₱100
        $withholdingTax = $this->computeWithholdingTax($monthlySalary);

        // --- Store or Update Deductions ---
        Deduction::create([
            'employee_id' => $employee->id,
            'sss' => $sss,
            'philhealth' => $philhealth,
            'pagibig' => $pagibig,
            'withholding_tax' => $withholdingTax,
            'total_deduction' => $sss + $philhealth + $pagibig + $withholdingTax,
        ]);

        return redirect()->route('accountant.deduction')->with('success', 'Deductions computed and saved successfully!');
    }

    /**
     * Compute Monthly Withholding Tax (simplified version of TRAIN Law)
     */
    private function computeWithholdingTax($monthlySalary)
    {
        $annualIncome = $monthlySalary * 12;
        $tax = 0;

        if ($annualIncome <= 250000) {
            $tax = 0;
        } elseif ($annualIncome <= 400000) {
            $tax = ($annualIncome - 250000) * 0.20;
        } elseif ($annualIncome <= 800000) {
            $tax = 30000 + ($annualIncome - 400000) * 0.25;
        } elseif ($annualIncome <= 2000000) {
            $tax = 130000 + ($annualIncome - 800000) * 0.30;
        } elseif ($annualIncome <= 8000000) {
            $tax = 490000 + ($annualIncome - 2000000) * 0.32;
        } else {
            $tax = 2410000 + ($annualIncome - 8000000) * 0.35;
        }

        return $tax / 12; // convert annual tax to monthly withholding
    }
}
