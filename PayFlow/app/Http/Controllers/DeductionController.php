<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeductionController extends Controller
{
    // Show deductions page
    public function deduction()
    {
        $deductions = Deduction::with('employee')->orderBy('deduction_date', 'desc')->get();
        $employees = Employee::where('employment_type', 'Full-Time')->get();

        return view('accountant.deduction', compact('deductions', 'employees'));
    }

    // Store a new deduction
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
        ]);

        // Fetch employee
        $employee = Employee::findOrFail($request->employee_id);
        $salary = $employee->basic_salary ?? 0;

        // Compute deductions based on checkboxes
        $sss = $request->has('sss') ? $salary * 0.05 : 0;
        $philhealth = $request->has('philhealth') ? $salary * 0.025 : 0;
        $pagibig = $request->has('pagibig') ? 100 : 0;
        $withholdingTax = $request->has('withholding_tax') ? $this->computeWithholdingTax($salary) : 0;

        // Total deduction
        $total = $sss + $philhealth + $pagibig + $withholdingTax;

        // Create deduction record
        $deduction = Deduction::create([
            'employee_id' => $employee->employee_id, // Make sure your table uses employee_id
            'sss' => $sss,
            'philhealth' => $philhealth,
            'pagibig' => $pagibig,
            'withholding_tax' => $withholdingTax,
            'total_deduction' => $total,
            'deduction_date' => Carbon::now(),
        ]);

        // Load employee relationship for AJAX response
        $deduction->load('employee');

        // AJAX response for frontend
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'deduction' => $deduction
            ]);
        }

        // Normal redirect
        return redirect()->route('accountant.deductions')
            ->with('success', 'Deduction added successfully!');
    }

    // Delete a deduction
    public function destroy(Deduction $deduction)
    {
        $deduction->delete();
        return redirect()->route('accountant.deductions')
            ->with('success', 'Deduction deleted successfully!');
    }

    // Compute withholding tax based on annualized salary
    private function computeWithholdingTax($basicSalary)
    {
        $annual = $basicSalary * 12;
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

        return $tax / 12; // Monthly tax
    }

    // Optional: Compute deduction amounts dynamically for frontend
    public function ajaxCompute($employee_id)
    {
        $employee = Employee::find($employee_id);

        if(!$employee){
            return response()->json([
                'basic_salary' => 0,
                'sss' => 0,
                'philhealth' => 0,
                'pagibig' => 0,
                'withholding_tax' => 0
            ]);
        }

        $salary = $employee->basic_salary ?? 0;

        return response()->json([
            'basic_salary' => $salary,
            'sss' => $salary * 0.05,
            'philhealth' => $salary * 0.025,
            'pagibig' => 100,
            'withholding_tax' => $this->computeWithholdingTax($salary)
        ]);
    }
}
