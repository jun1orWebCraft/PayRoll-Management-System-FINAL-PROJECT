@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-3">

    
    <div class="mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h4 class="fw-bold mb-1">Welcome Back, <span class="">{{ Auth::user()->name ?? 'HR' }}</span> </h4>
                <p class="text-muted mb-0">
                    We’re glad to see you again — here’s what’s happening at <strong>PayFlow</strong> today.
                </p>
            </div>
            <div class="text-end mt-3 mt-md-0">
                <h6 class="mb-0 text-secondary">
                    <i class="bi bi-building me-1"></i> <strong>PayFlow Payroll System</strong>
                </h6>
                <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
            </div>
        </div>
    </div>

    
    {{-- ===== Top Summary Cards ===== --}}
    <div class="row g-4 mt-4">
        {{-- Total Employees --}}
        <div class="col-md-3">
            <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Total Employees</h6>
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-2">
                        <i class="bi bi-people fs-5 "></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">{{ $totalEmployees }}</h2>
            </div>
        </div>

        {{-- Active Employees --}}
        <div class="col-md-3">
            <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Active Employees</h6>
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-2">
                        <i class="bi bi-person-check fs-5 "></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">{{ $activeEmployees }}</h2>
            </div>
        </div>

        {{-- On Leave --}}
        <div class="col-md-3">
            <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">On Leave</h6>
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-2">
                        <i class="bi bi-calendar-x fs-5 "></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">{{ $onLeave }}</h2>
            </div>
        </div>

        {{-- Pending Payrolls --}}
        <div class="col-md-3">
            <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Pending Payrolls</h6>
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-2">
                        <i class="bi bi-graph-up-arrow fs-5 "></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">{{ $pendingPayrolls}}</h2>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-3">
   
        <div class="col-md-6">
    <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4 h-110 fixed-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Recent Activities</h5>
            <i class="bi bi-clock-history text-primary fs-5"></i>
        </div>

        <div class="scrollable-content">
            <ul class="list-unstyled mb-0">
                @forelse($recentActivities as $activity)
                    <li class="mb-2">
                        <i class="bi {{ $activity->icon }} {{ $activity->color }} me-2"></i>
                        {{ $activity->action }}
                        <small class="text-muted float-end">
                            {{ $activity->created_at->diffForHumans() }}
                        </small>
                    </li>
                @empty
                    <li class="text-muted">No recent activities yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>


        {{-- Employee Leave Requests --}}
<div class="col-md-6">
    <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4 h-100 fixed-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Employee Leave Requests</h5>
            <i class="bi bi-envelope-paper text-warning fs-5"></i>
        </div>

        <div class="scrollable-content">
            <table class="table table-borderless align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaveRequests as $leave)
                        <tr data-bs-toggle="modal" data-bs-target="#leaveModal{{ $leave->leave_request_id }}" style="cursor:pointer;">
                            <td>{{ $leave->employee->first_name ?? 'Unknown' }} {{ $leave->employee->last_name ?? '' }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                            <td>
                                @if ($leave->status === 'Pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif ($leave->status === 'Approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No leave requests found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
        @foreach ($leaveRequests as $leave)
<div class="modal fade" id="leaveModal{{ $leave->leave_request_id }}" tabindex="-1" aria-labelledby="leaveModalLabel{{ $leave->leave_request_id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="background: #fdfcf8;">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100 text-center" id="leaveModalLabel{{ $leave->leave_request_id }}" style="font-weight: bold; letter-spacing: 1px;">
                    Leave Request Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <div class="text-center mb-4">
                    <h6 class="text-muted" style="letter-spacing: 1px;">Employee Leave Request Form</h6>
                    <hr style="border-top: 2px solid #ccc; width: 50%;">
                </div>

                <div class="mb-3">
                    <p><strong>Employee No:</strong> {{ $leave->employee->employee_no }}</p>
                    <p><strong>Employee Name:</strong> {{ $leave->employee->first_name ?? 'Unknown' }} {{ $leave->employee->last_name ?? '' }}</p>
                    <p><strong>Leave Type:</strong> {{ $leave->leave_type }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge 
                            @if($leave->status === 'Approved') bg-success
                            @elseif($leave->status === 'Rejected') bg-danger
                            @else bg-warning text-dark
                            @endif">
                            {{ $leave->status }}
                        </span>
                    </p>
                </div>

                <div class="mb-3">
                    <p><strong>Reason:</strong></p>
                    <p class="border p-3" style="background: #fff; min-height: 60px;">{{ $leave->reason ?? 'N/A' }}</p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-center border-0">
                @if ($leave->status === 'Pending')
                    <form action="{{ route('leave.approve', $leave->leave_request_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success">Approve</button>
                    </form>
                    <form action="{{ route('leave.reject', $leave->leave_request_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </form>
                @endif
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

    </div>
</div>

{{-- Custom colors & scroll styling --}}
<style>
    body {
        background-color: #f8f9fa;
    }

    .text-purple {
        color: #a855f7 !important;
    }

    .bg-purple {
        background-color: #a855f7 !important;
    }

    .card {
        transition: all 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .fixed-card {
        height: 400px;
        display: flex;
        flex-direction: column;
    }

    .scrollable-content {
        flex: 1;
        overflow-y: auto;
        padding-right: 5px;
    }

    .scrollable-content::-webkit-scrollbar {
        width: 6px;
    }

    .scrollable-content::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 10px;
    }

    table tr td, table tr th {
        vertical-align: middle;
    }
</style>
@endsection
