@extends('layouts.app')

@section('title', 'Reports and Analytics')

@section('content')
<div class="container py-3">

    {{-- ===== Header Section ===== --}}
    <div class="mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h4 class="fw-bold mb-1">Good Day, <span class="">{{ Auth::user()->name ?? 'HR' }}</span></h4>
                <p class="text-muted mb-0">
                    We’re glad to see you again — here’s this month’s reports from <strong>PayFlow</strong>.
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
        <div class="col-md-3">
    <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-muted mb-0">Average Salary</h6>
            <div class="rounded-circle  bg-opacity-10 p-2">
                <i class="bi bi-cash-coin fs-5 "></i>
            </div>
        </div>
        <h2 class="fw-bold mb-1">₱{{ number_format($averageSalary, 2) }}</h2>
    </div>
</div>

<div class="col-md-3">
    <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-muted mb-0">Total Payroll Cost</h6>
            <div class="rounded-circle  bg-opacity-10 p-2">
                <i class="bi bi-wallet2 fs-5 "></i>
            </div>
        </div>
        <h2 class="fw-bold mb-1">₱{{ number_format($totalPayrollCost, 2) }}</h2>
    </div>
</div>

       <div class="col-md-3">
            <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Total Deduction</h6>
                    <div class="rounded-circle  bg-opacity-10 p-2">
                        <i class="bi bi-dash-circle fs-5 "></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">₱{{ number_format($totalDeduction, 2) }}</h2>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Average Attendance Rate</h6>
                    <div class="rounded-circle  bg-opacity-10 p-2">
                        <i class="bi bi-graph-up-arrow fs-5 "></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">{{ $averageAttendanceRate }}%</h2>
            </div>
        </div>

    </div>

    {{-- ===== Lower Charts Section ===== --}}
    <div class="row g-4 mt-3">

        {{-- Weekly Attendance Bar Chart --}}
        <div class="col-md-6">
            <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4 h-110 fixed-card">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-bar-chart-line me-2 "></i> Weekly Attendance Report
                </h5>
                <div class="scrollable-content d-flex justify-content-center align-items-center">
                    <canvas id="weeklyAttendanceChart" style="max-width: 400px; max-height: 250px;"></canvas>
                </div>
            </div>
        </div>


        {{-- Employee by Position (Pie Chart) --}}
        <div class="col-md-6">
            <div class="card bg-white text-dark border-0 shadow-sm rounded-4 p-4 h-100 fixed-card">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-pie-chart me-2 "></i> Employees by Position
                </h5>
                <div class="scrollable-content d-flex justify-content-center align-items-center">
                    <canvas id="employeePositionChart" style="max-width: 300px; max-height: 300px;"></canvas>
                </div>
            </div>
        </div>


    </div>
</div>

{{-- ===== Styles ===== --}}
<style>
    body {
        background-color: #f8f9fa;
    }
    .text-purple { color: #a855f7 !important; }
    .bg-purple { background-color: #a855f7 !important; }
    .card { transition: all 0.2s ease-in-out; }
    .card:hover { transform: translateY(-4px); box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1); }
    .fixed-card { height: 400px; display: flex; flex-direction: column; }
    .scrollable-content { flex: 1; overflow-y: auto; padding-right: 5px; width: 100%; }
    .scrollable-content::-webkit-scrollbar { width: 6px; }
    .scrollable-content::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 10px; }
</style>

{{-- ===== Chart.js Scripts ===== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const attendanceLabels = @json($attendanceLabels);
    const attendanceCounts = @json($attendanceCounts);

    const ctx = document.getElementById('weeklyAttendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: attendanceLabels,
            datasets: [{
                label: 'Days Present',
                data: attendanceCounts,
                backgroundColor: '#4e73df',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Days' }
                }
            },
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Weekly Attendance Overview' }
            }
        }
    });

    // Employees by Position (Pie Chart)
    const positionCtx = document.getElementById('employeePositionChart').getContext('2d');

new Chart(positionCtx, {
    type: 'pie',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Employees by Position',
            data: @json($data),
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#f6c23e',
                '#e74a3b',
                '#36b9cc',
                '#858796',
                '#fd7e14'
            ],
            borderWidth: 1,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                align: 'center',
                labels: {
                    boxWidth: 14,
                    padding: 15,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.label}: ${context.raw} employees`;
                    }
                }
            }
        },
        layout: {
            padding: 5
        }
    }
});
const weeklyCtx = document.getElementById('weeklyAttendanceChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: @json($attendanceLabels),
            datasets: [{
                label: 'Attendance Count',
                data: @json($attendanceCounts),
                backgroundColor: '#4e73df',
                borderRadius: 6,
                maxBarThickness: 40,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    title: {
                        display: true,
                        text: 'Number of Employees'
                    }
                }
            },
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Weekly Attendance Overview'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.raw} employees present`;
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
