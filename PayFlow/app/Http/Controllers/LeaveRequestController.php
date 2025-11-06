<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Notification;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $employeeId = Auth::user()->employee_id;
        $requests = LeaveRequest::where('employee_id', $employeeId)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('employeepages.request-index', compact('requests'));
    }

    public function create()
    {
        $leaveTypes = LeaveRequest::LEAVE_TYPES;
        $leaveBalances = [];
        return view('employeepages.request', compact('leaveTypes', 'leaveBalances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => ['required', 'string', Rule::in(LeaveRequest::LEAVE_TYPES)],
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:500',
        ]);

        $employee = Auth::guard('employee')->user();
        if (!$employee) {
            return back()->withErrors(['error' => 'Employee not authenticated.']);
        }

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $numDays = $start->diffInDays($end) + 1;

        LeaveRequest::create([
            'employee_id' => $employee->employee_id,
            'leave_type'  => trim($validated['leave_type']),
            'start_date'  => $validated['start_date'],
            'end_date'    => $validated['end_date'],
            'reason'      => $validated['reason'],
            'status'      => 'Pending',
            'approved_by' => null,
        ]);

        return redirect()
            ->route('employee.request')
            ->with('success', "Leave request submitted for {$numDays} day(s).");
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $this->authorizeOwner($leaveRequest);
        return view('employeepages.request-show', compact('leaveRequest'));
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        $this->authorizeOwner($leaveRequest);

        if ($leaveRequest->status !== 'Pending') {
            return redirect()->route('leave-requests.index')->with('info', 'Only pending requests can be edited.');
        }

        $leaveTypes = LeaveRequest::LEAVE_TYPES;
        return view('employeepages.request-edit', compact('leaveRequest', 'leaveTypes'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeOwner($leaveRequest);

        if ($leaveRequest->status !== 'Pending') {
            return redirect()->route('leave-requests.index')->with('info', 'Only pending requests can be updated.');
        }

        $validated = $request->validate([
            'leave_type' => ['required', 'string', Rule::in(LeaveRequest::LEAVE_TYPES)],
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:500',
        ]);

        $leaveRequest->update($validated);

        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request updated successfully.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $this->authorizeOwner($leaveRequest);

        if ($leaveRequest->status !== 'Pending') {
            return redirect()->route('leave-requests.index')->with('info', 'Only pending requests can be cancelled.');
        }

        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')->with('success', 'Leave request cancelled successfully.');
    }

    protected function authorizeOwner(LeaveRequest $leaveRequest)
    {
        $employeeId = Auth::user()->employee_id;
        if ($leaveRequest->employee_id !== $employeeId) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function approve($leave_request_id)
    {
        $leave = LeaveRequest::with('employee')->findOrFail($leave_request_id);
        $leave->status = 'Approved';
        $leave->approved_by = auth()->id();
        $leave->save();

        if ($leave->employee) {
            $employee = $leave->employee;
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);
            $today = Carbon::today();

            if ($today->gte($start) && $today->lte($end)) {
                $employee->status = 'On Leave';
                $employee->save();
            }

            while ($start->lte($end)) {
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $employee->employee_id,
                        'date' => $start->toDateString(),
                    ],
                    [
                        'status' => 'On Leave',
                        'time_in' => null,
                        'time_out' => null,
                        'total_hours' => 0,
                    ]
                );
                $start->addDay();
            }

            Notification::create([
                'employee_id' => $employee->employee_id,
                'type' => 'Leave Approved',
                'message' => "Your leave from {$leave->start_date} to {$leave->end_date} has been approved.",
                'link' => route('employee.request'),
                'is_read' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Leave approved. Attendance entries created and notification sent.');
    }

    public function reject($leave_request_id)
    {
        $leave = LeaveRequest::with('employee')->findOrFail($leave_request_id);
        $leave->status = 'Rejected';
        $leave->approved_by = auth()->id();
        $leave->save();

        if ($leave->employee) {
            $employee = $leave->employee;

            Notification::create([
                'employee_id' => $employee->employee_id,
                'type' => 'Leave Rejected',
                'message' => "Your leave from {$leave->start_date} to {$leave->end_date} has been rejected.",
                'link' => route('employee.request'),
                'is_read' => 0,
            ]);
        }

        return redirect()->back()->with('error', 'Leave request rejected.');
    }
}
