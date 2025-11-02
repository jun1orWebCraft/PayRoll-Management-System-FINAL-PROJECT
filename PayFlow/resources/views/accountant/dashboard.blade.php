@extends('layouts.accountant.app')

@section('content')
<div class="container py-3">

    {{-- Welcome Section --}}
    <div class="mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h4 class="fw-bold mb-1">
                    Welcome Back, <span class="text-primary">{{ Auth::user()->name ?? 'Accountant' }}</span>
                </h4>
                <p class="text-muted mb-0">Here’s your payroll overview at <strong>PayFlow</strong> today.</p>
            </div>
            <div class="text-end mt-3 mt-md-0">
                <h6 class="mb-0 text-secondary"><i class="bi bi-building me-1"></i> <strong>PayFlow Payroll System</strong></h6>
                <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
            </div>
        </div>
    </div>

    {{-- Dashboard Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 bg-gradient bg-light">
                <i class="bi bi-graph-up-arrow text-primary fs-2 mb-2"></i>
                <h6 class="text-muted">Total Payroll</h6>
                <h4 class="fw-bold text-dark">₱{{ number_format($totalPayroll ?? 0, 2) }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 bg-gradient bg-light">
                <i class="bi bi-dash-circle text-danger fs-2 mb-2"></i>
                <h6 class="text-muted">Total Deduction</h6>
                <h4 class="fw-bold text-dark">₱{{ number_format($totalDeduction ?? 0, 2) }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 bg-gradient bg-light">
                <i class="bi bi-check-circle text-success fs-2 mb-2"></i>
                <h6 class="text-muted">Processed Payrolls</h6>
                <h4 class="fw-bold text-dark">{{ $processedPayrolls ?? 0 }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 bg-gradient bg-light">
                <i class="bi bi-hourglass-split text-warning fs-2 mb-2"></i>
                <h6 class="text-muted">Pending Payrolls</h6>
                <h4 class="fw-bold text-dark">{{ $pendingPayrolls ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Recent Payrolls --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white fw-bold d-flex align-items-center">
                    <i class="bi bi-cash-stack me-2"></i> Recent Payrolls
                </div>
                <div class="card-body">
                   @forelse($recentPayrolls ?? [] as $payroll)
                     <div class="d-flex align-items-center justify-content-between border-bottom py-3">
                      <div class="d-flex align-items-center">
                       <div class="rounded-circle bg-light p-2 me-3">
                         <i class="bi bi-person-circle fs-4 text-primary"></i>
                     </div>
                 <div>
                <h6 class="mb-0 fw-semibold">{{ $payroll->employee->name ?? 'N/A' }}</h6>
                <small class="text-muted">
                    {{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d') }} - {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}
                </small>
            </div>
         </div>
        <div class="text-end">
            <h6 class="fw-bold text-success mb-0">₱{{ number_format($payroll->net_salary, 2) }}</h6>
            <small class="text-muted">{{ $payroll->status }}</small>
        </div>
    </div>
@empty
    <p class="text-muted text-center py-4 mb-0">No recent payroll records found.</p>
@endforelse

                </div>
            </div>
        </div>

        {{-- Attendance Overview --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-secondary text-white fw-bold d-flex align-items-center">
                    <i class="bi bi-calendar-check me-2"></i> Attendance Overview
                </div>
                <div class="card-body">
                    @forelse($attendanceSummary ?? [] as $att)
                        <div class="d-flex align-items-center justify-content-between border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light p-2 me-3">
                                    <i class="bi bi-person-badge fs-4 text-secondary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $att->employee->name ?? 'N/A' }}</h6>
                                    <small class="text-muted">This Month</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="d-block text-danger fw-semibold">Absent: {{ $att->total_absent }}</small>
                                <small class="d-block text-warning fw-semibold">Late: {{ $att->total_late }}</small>
                                <small class="d-block text-success fw-semibold">Present: {{ $att->total_present }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4 mb-0">No attendance records found.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
