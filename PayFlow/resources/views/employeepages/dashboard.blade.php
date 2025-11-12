@extends('layouts.employeeapp')

@section('content')
<div class="container  mb-5">
    <!-- TOP BAR: Date/Time + Notification -->
    <div class="d-flex align-items-center justify-content-start mb-4 gap-3">
    <!-- Dashboard Title -->
        <h3 class="fw-bold mb-0">Dashboard</h3>

        <!-- Current Date & Time -->
        <div class="d-flex align-items-center justify-content-end gap-2 px-3 py-1 rounded-3 bg-light shadow-sm">
            <i class="bi bi-clock fs-5 text-secondary"></i>
            <span class="fw-medium" id="currentDateTime"></span>
        </div>

        <!-- Notification Icon -->
        <div class="position-relative" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#notificationModal">
            <i class="bi bi-bell fs-4 text-secondary"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $notifications->where('is_read', 0)->count() }}
            </span>
        </div>
        <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($notifications->count() > 0)
                        <ul class="list-group">
                            @foreach($notifications as $notification)
                                <li class="list-group-item d-flex justify-content-between align-items-center {{ $notification->is_read ? '' : 'fw-bold' }}">
                                    <span>{{ $notification->message }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center mb-0">No notifications</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Mark All as Read</button>
                    </form>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

    </div>
    <!-- TOP CARDS -->
    <div class="row g-3 mb-4">
        @php
            use Carbon\Carbon;

            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();

            $currentMonthPayroll = \App\Models\Payroll::whereBetween('payment_date', [$currentMonthStart, $currentMonthEnd])
                ->where('status', 'Paid')
                ->sum('net_pay');

            $currentMonthPaymentDate = \App\Models\Payroll::whereBetween('payment_date', [$currentMonthStart, $currentMonthEnd])
                ->where('status', 'Paid')
                ->latest('payment_date')
                ->value('payment_date');

            $ytdPayroll = \App\Models\Payroll::whereYear('payment_date', Carbon::now()->year)
                ->where('status', 'Paid')
                ->sum('net_pay');
        @endphp

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <h6 class="text-muted"><i class="bi bi-currency-dollar me-2"></i>Current Month Net</h6>
                <h4 class="fw-bold text-primary">₱{{ number_format($currentMonthPayroll, 2) }}</h4>
                @if($currentMonthPaymentDate)
                    <small>Pay Date: {{ Carbon::parse($currentMonthPaymentDate)->format('m/d/Y') }}</small>
                @else
                    <small>No payments yet</small>
                @endif
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <h6 class="text-muted"><i class="bi bi-graph-up-arrow me-2"></i>YTD Earnings</h6>
                <h4 class="fw-bold text-success">₱{{ number_format($ytdPayroll, 2) }}</h4>
                <small>Year to date</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <h6 class="text-muted"><i class="bi bi-calendar3 me-2"></i>Leave Balance</h6>
                <h4 class="fw-bold text-purple"> {{ $latestLeaveRemaining }} days</h4>
                <small>{{ $latestLeaveType }} remaining</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <h6 class="text-muted"><i class="bi bi-check2-circle me-2"></i>Attendance Rate</h6>
                <h4 class="fw-bold text-info">{{ $attendanceRate }}%</h4>
                <small>This month</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT SIDE -->
        <div class="col-lg-8">
            <!-- PAY PERIOD -->
            @php

            use App\Models\Payroll;

            // Get the logged-in employee
            $employeeId = auth()->user()->employee_id;

            // Current pay period (latest payroll)
            $currentPayroll = Payroll::where('employee_id', $employeeId)
                ->latest('pay_period_end')
                ->first();

            // Recent payslips (last 5)
            $recentPayslips = Payroll::where('employee_id', $employeeId)
                ->orderBy('pay_period_end', 'desc')
                ->take(5)
                ->get();
            @endphp

            <!-- PAY PERIOD -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 p-4">
                <h5 class="fw-bold mb-3">Current Pay Period</h5>
                @if($currentPayroll)
                    <p class="text-muted mb-4">
                        {{ Carbon::parse($currentPayroll->pay_period_start)->format('m/d/Y') }} - 
                        {{ Carbon::parse($currentPayroll->pay_period_end)->format('m/d/Y') }}
                    </p>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h6 class="text-muted">Gross Salary</h6>
                            <h4 class="fw-bold text-dark">₱{{ number_format($currentPayroll->basic_salary, 2) }}</h4>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Deductions</h6>
                            <h4 class="fw-bold text-danger">₱{{ number_format($currentPayroll->deductions, 2) }}</h4>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Net Pay</h6>
                            <h4 class="fw-bold text-success">₱{{ number_format($currentPayroll->net_pay, 2) }}</h4>
                        </div>
                    </div>
                @else
                    <p class="text-muted">No payroll records available.</p>
                @endif
            </div>

            <!-- RECENT PAYSLIPS -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 p-4">
                <h5 class="fw-bold mb-3">Recent Payslips</h5>
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted small">
                            <th>Period</th>
                            <th>Net Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayslips as $payroll)
                            <tr>
                                <td>{{ Carbon::parse($payroll->pay_period_start)->format('M Y') }}</td>
                                <td>₱{{ number_format($payroll->net_pay, 2) }}</td>
                                <td>
                                    @if($payroll->status === 'Paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="javascript:void(0)" 
                                    class="text-decoration-none me-2 viewPayslipBtn" 
                                    data-payroll-id="{{ $payroll->payroll_id }}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#payslipModal">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('employee.payslip.download', $payroll->payroll_id) }}" class="text-decoration-none">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No recent payslips available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Payslip Modal -->
                <div class="modal fade" id="payslipModal" tabindex="-1" aria-labelledby="payslipModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="payslipModalLabel">Payslip Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="payslipContent">
                                <p class="text-center text-muted">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>

            <!-- ATTENDANCE SUMMARY -->
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-bold mb-3">Attendance Summary - This Month</h5>
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="fw-bold text-primary">{{ $presentDays }}</h4>
                        <p class="text-muted small mb-0">Days Present</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="fw-bold text-danger">{{ $absentDays }}</h4>
                        <p class="text-muted small mb-0">Days Absent</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="fw-bold text-success">{{ round($totalHoursWorked) }}</h4>
                        <p class="text-muted small mb-0">Hours Worked</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="fw-bold text-info">{{ round($overtimeHours) }}</h4>
                        <p class="text-muted small mb-0">Overtime Hours</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-lg-4">
            <!-- LEAVE BALANCE -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 p-4" style="height: 360px; overflow-y: auto;">
                <h6 class="fw-bold mb-3">Leave Balance</h6>

                @foreach($leaveProgress as $type => $progress)
                    <div class="mb-3">
                        <small>{{ $type }}</small>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar"
                                style="width: {{ $progress['percentage'] }}%;"
                                aria-valuenow="{{ $progress['used'] }}"
                                aria-valuemin="0"
                                aria-valuemax="{{ $progress['total'] }}">
                            </div>
                        </div>
                        <small class="text-muted">{{ $progress['remaining'] }}/{{ $progress['total'] }}</small>
                    </div>
                @endforeach
            </div>



            <!-- LEAVE REQUESTS -->
            <div class="card shadow-sm border-0 rounded-4 p-4" style="height: 360px;">
                <h6 class="fw-bold mb-3">Leave Requests</h6>

                <div class="overflow-auto" style="max-height: 340px;">
                    @forelse($leaveRequests as $leave)
                        <div class="mb-3 p-3 rounded bg-light d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-semibold mb-1">{{ $leave->leave_type }}</h6>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}
                                    @if($leave->start_date != $leave->end_date)
                                        - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                    @endif
                                    • 
                                    {{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }} day(s)
                                </small>
                            </div>
                            <span class="badge 
                                @if($leave->status === 'Pending') bg-warning text-dark
                                @elseif($leave->status === 'Approved') bg-success
                                @else bg-danger @endif
                                mt-1">
                                {{ $leave->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No leave requests found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.viewPayslipBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const payrollId = this.dataset.payrollId;
            const payslipContent = document.getElementById('payslipContent');

            // Show loading text
            payslipContent.innerHTML = '<p class="text-center text-muted">Loading...</p>';

            // Fetch modal content via AJAX
            fetch(`/employeepages/payslip/${payrollId}`)
                .then(res => res.text())
                .then(html => payslipContent.innerHTML = html)
                .catch(() => payslipContent.innerHTML = '<p class="text-danger text-center">Failed to load payslip.</p>');

            // Optional: you can show modal programmatically, but Bootstrap handles it automatically with data-bs-toggle
        });
    });
});
</script>

@endsection