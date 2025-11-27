<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountantController extends Controller
{
    public function dashboard()
    {
        // Recent payrolls with employee and status
        $recentPayrolls = Payroll::with('employee')
            ->latest()
            ->take(10)
            ->get();

        // Payroll by position (safe for employees without positions)
        $payrollByPosition = DB::table('payrolls')
            ->join('employees', 'payrolls.employee_id', '=', 'employees.employee_id')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.position_id')
            ->select(
                 DB::raw('COALESCE(positions.position_name, "Unassigned") as position'),
                 DB::raw('SUM(payrolls.net_pay) as total')
        )
            ->groupBy('positions.position_name')
            ->get() ?? collect();


        // Top 5 earners with employee status
        $topEarners = Payroll::with('employee')
            ->orderByDesc('net_pay')
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'name' => $p->employee->first_name . ' ' . $p->employee->last_name,
                'position' => $p->employee->position->position_name ?? 'N/A',
                'status' => $p->employee->status ?? 'N/A',
                'net_pay' => $p->net_pay
            ]);

        // Employee payroll status for single column view
        $employeePayrollStatus = Payroll::with('employee')
            ->latest()
            ->get()
            ->map(fn($p) => [
                'name' => $p->employee->first_name . ' ' . $p->employee->last_name,
                'status' => $p->status,
            ]);

        return view('accountant.dashboard', compact(
            'recentPayrolls',
            'payrollByPosition',
            'topEarners',
            'employeePayrollStatus'
        ));
    }
    public function settings()
    {
        return view('accountant.settings');
    }
}
