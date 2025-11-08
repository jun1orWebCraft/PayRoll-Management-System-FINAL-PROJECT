<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AccountantController extends Controller
{
    public function dashboard()
    {
      
        $totalPayroll = Payroll::sum('net_pay');
        $totalDeductions = Payroll::sum('deductions');
        $processedPayrolls = Payroll::where('status', 'Processed')->count();
        $pendingPayrolls = Payroll::where('status', 'Pending')->count();
        
        // ✅ Fetch the 5 most recent payrolls
        $recentPayrolls = Payroll::with('employee')
            ->latest()
            ->take(5)
            ->get();

        $attendanceSummary = [];
        
        return view('accountant.dashboard', compact(
            'totalPayroll',
            'totalDeductions',
            'processedPayrolls',
            'pendingPayrolls',
            'recentPayrolls',
            'attendanceSummary'
        ));
    }

    public function settings()
    {
        return view('accountant.settings');
    }
}
