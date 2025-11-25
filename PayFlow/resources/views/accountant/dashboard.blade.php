@extends('layouts.accountant.app')

@section('content')
<div class="container py-3">

    {{-- Header --}}
    <div class="mb-4 border-bottom pb-3 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Welcome Back, <span class="text-primary">{{ Auth::user()->name ?? 'Accountant' }}</span></h4>
            <p class="text-muted mb-0">Here’s your payroll overview at <strong>PayFlow</strong> today.</p>
        </div>
        <div class="text-end">
            <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
        </div>
    </div>

    {{-- Employee Payroll Status --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="bi bi-person-lines-fill me-2"></i> Employee Payroll Status
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employeePayrollStatus ?? [] as $emp)
                                <tr>
                                    <td>{{ $emp['name'] }}</td>
                                    <td>
                                        <span class="badge {{ $emp['status'] == 'Processed' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $emp['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No payroll records found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Payroll by Position --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-bar-chart me-2"></i> Payroll by Position
                </div>
                <div class="card-body">
                    <canvas id="payrollByPositionChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Top 5 Earners --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-star me-2"></i> Top 5 Earners
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th class="text-end">Net Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                           @forelse($topEarners ?? [] as $earner)
                             <tr>
                                <td>{{ $earner['name'] }}</td>
                                <td>{{ $earner['position'] }}</td>
                                <td>
                                    <span class="badge {{ $earner['status'] == 'Processed' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $earner['status'] }}
                                    </span>
                                </td>
                                <td class="text-end">₱{{ number_format($earner['net_pay'],2) }}</td>
                             </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No data found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Payrolls --}}
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="bi bi-clock-history me-2"></i> Recent Payrolls
                </div>
                <div class="card-body">
                    @forelse($recentPayrolls ?? [] as $payroll)
                        @php $employee = $payroll->employee ?? null; @endphp
                        <div class="d-flex align-items-center justify-content-between border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light p-2 me-3">
                                    <img src="{{ $employee && $employee->profile_picture && file_exists(storage_path('app/public/' . $employee->profile_picture)) 
                                            ? asset('storage/' . $employee->profile_picture) 
                                            : asset('images/default-profile.png') }}" 
                                        alt="Profile photo" class="rounded-circle" width="50" height="50">
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $employee->first_name ?? '' }} {{ $employee->last_name ?? '' }}</h6>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d') ?? 'N/A' }} - 
                                        {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') ?? 'N/A' }}
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="d-block text-secondary fw-semibold">Status: {{ $payroll->status ?? 'N/A' }}</small>
                                <h6 class="fw-bold text-success mb-0">₱{{ number_format($payroll->net_pay ?? 0, 2) }}</h6>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4 mb-0">No recent payroll records found.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@php
$payrollByPosition = $payrollByPosition ?? collect();
@endphp
<script>
    const posCtx = document.getElementById('payrollByPositionChart').getContext('2d');

    const payrollByPositionChart = new Chart(posCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($payrollByPosition->pluck('position')?? []) !!},
            datasets: [{
                label: 'Net Pay',
                data: {!! json_encode($payrollByPosition->pluck('total') ?? []) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

@endpush

@endsection
