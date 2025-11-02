<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class PayFlowController extends Controller
{
public function dashboard()   
{
    $user = auth()->user();

    if ($user->name === 'Accountant') {
        // Logic for accountant dashboard
        // Example: show accountant-specific data or just a custom view
        return view('accountant.dashboard', compact('user'));
    } elseif ($user->name === 'HR') {
        // HR dashboard logic as in your original code
        $leaveRequests = LeaveRequest::with('employee')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $activeEmployees = Employee::where('status', 'Active')->count();
        $onLeave = Employee::where('status', 'On Leave')->count();
        $totalEmployees = Employee::count();
        return view('pages.dashboard', compact('totalEmployees', 'onLeave', 'activeEmployees', 'leaveRequests'));
    } 
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
