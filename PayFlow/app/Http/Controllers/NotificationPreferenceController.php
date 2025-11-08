<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationPreference;

class NotificationPreferenceController extends Controller
{
    public function update(Request $request)
    {
 
        $employee = Auth::guard('employee')->user();


        if (!$employee) {
            return redirect()->route('employee.login')
                ->withErrors(['error' => 'You must be logged in to update preferences.']);
        }

        $data = [
            'payslip_ready' => $request->has('payslip_ready'),
            'leave_updates' => $request->has('leave_updates'),
            'benefits_information' => $request->has('benefits_information'),
            'important_reminders' => $request->has('important_reminders'),
        ];

       
        NotificationPreference::updateOrCreate(
            ['employee_id' => $employee->employee_id], 
            $data
        );

        return back()->with('success', 'Notification preferences updated successfully.');
    }
}




