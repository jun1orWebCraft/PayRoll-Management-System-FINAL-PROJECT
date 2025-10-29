<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class PayFlowController extends Controller
{
    public function dashboard()  
    {
        $leaveRequests = LeaveRequest::with('employee')
        ->orderBy('created_at', 'desc')
        ->take(5) // show only recent 5
        ->get();
        $activeEmployees = Employee::where('status', 'Active')->count();
    $onLeave = Employee::where('status', 'On Leave')->count();
        $totalEmployees = Employee::count();
        return view('pages.dashboard', compact('totalEmployees', 'onLeave', 'activeEmployees', 'leaveRequests'));                                   
    }

    public function employees()  
    {
        return view('pages.employees');
    }

    public function attendance(){
        return view('pages.attendance');
    }

    public function taxanddeductions()
    {
        return view('pages.taxanddeductions');
    }                                                                                                                               

    public function payrollprocessing()
    {
        return view('pages.payrollprocessing');
    }

    public function reports()
    {
        return view('pages.reports');
    }     
    public function settings()
    {
        return view('pages.settings');
    }
}
