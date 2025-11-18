@extends('layouts.app')

@section('title', 'Payroll Data')

@section('content')
<div class="container py-4">

  {{-- Top Section --}}
  <div class="row g-4 mb-4">
    {{-- Total Outstanding --}}
    <div class="col-lg-8">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h6 class="text-muted mb-1">Total Outstanding</h6>
              <h3 class="fw-bold mb-0">₱{{ number_format($totalOutstanding, 2) }}</h3>
            </div>
            <div>
              <div class="btn-group">
                <button class="btn btn-sm btn-primary">1M</button>
                <button class="btn btn-sm btn-outline-secondary">3M</button>
                <button class="btn btn-sm btn-outline-secondary">6M</button>
                <button class="btn btn-sm btn-outline-secondary">1Y</button>
              </div>
            </div>
          </div>

          @php
            $isPositive = $percentageChange > 0;
            $isNegative = $percentageChange < 0;
          @endphp

          <p class="small fw-semibold mb-3 
            {{ $isPositive ? 'text-success' : ($isNegative ? 'text-danger' : 'text-muted') }}">
            {{ $isPositive ? '+' : '' }}{{ number_format($percentageChange, 2) }}% vs last month
          </p>
          {{-- Placeholder for Chart --}}
          <div class="bg-light rounded-4 d-flex justify-content-center align-items-center" style="height: 220px;">
            <span class="text-secondary">Chart Placeholder</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Payroll Summary --}}
    <div class="col-lg-4">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="fw-semibold mb-0">Payroll Summary</h6>
              <a href="#" class="text-primary small fw-medium" data-bs-toggle="modal" data-bs-target="#payrollSummaryModal">
                  View report
              </a>

            </div>

          <p class="text-muted small mb-3">
            From {{ $payrolls->min('pay_period_start') ?? '-' }} – {{ $payrolls->max('pay_period_end') ?? '-' }}
          </p>

          <div class="row text-center mb-3">
            <div class="col">
              <p class="text-muted small mb-1">Payment</p>
              <h6 class="fw-semibold mb-0">₱{{ number_format($totalPayment, 2) }}</h6>
            </div>
            <div class="col">
              <p class="text-muted small mb-1">Pending</p>
              <h6 class="fw-semibold mb-0">₱{{ number_format($totalPending, 2) }}</h6>
            </div>
            <div class="col">
              <p class="text-muted small mb-1">Paid</p>
              <h6 class="fw-semibold mb-0">₱{{ number_format($totalPayment, 2) }}</h6>
            </div>
          </div>

          {{-- Circle Progress Placeholder --}}
          <div class="rounded-circle mx-auto bg-primary bg-opacity-25 d-flex justify-content-center align-items-center" 
               style="height: 120px; width: 120px;">
            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center fw-bold" 
                 style="height: 80px; width: 80px;">
              {{ $totalOutstanding > 0 ? round(($totalPayment / $totalOutstanding) * 100) : 0 }}%
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Payroll Summary Modal -->
  <div class="modal fade" id="payrollSummaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content rounded-4 shadow">

        <div class="modal-header">
          <h5 class="modal-title fw-semibold">Payroll Summary Report</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <!-- Summary Section -->
          <div class="mb-4">
            <h6 class="fw-bold">Overall Summary</h6>

            <p class="mb-1"><strong>Total Payment:</strong> ₱{{ number_format($totalPayment, 2) }}</p>
            <p class="mb-1"><strong>Total Pending:</strong> ₱{{ number_format($totalPending, 2) }}</p>
            <p class="mb-1"><strong>Total Outstanding:</strong> ₱{{ number_format($totalOutstanding, 2) }}</p>

            <p class="mt-3 text-muted small">
              Period: {{ $payrolls->min('pay_period_start') ?? '-' }} — {{ $payrolls->max('pay_period_end') ?? '-' }}
            </p>
          </div>

          <hr>

          <!-- Payroll List -->
          <h6 class="fw-bold mb-3">Payroll Breakdown</h6>

          @forelse($payrolls as $payroll)
            @php $employee = $payroll->employee; @endphp

            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
              <div>
                <div class="fw-semibold">
                  {{ $employee->first_name ?? 'N/A' }} {{ $employee->last_name ?? '' }}
                </div>
                <small class="text-muted">
                  Pay Period: {{ $payroll->pay_period_start }} - {{ $payroll->pay_period_end }}
                </small>
              </div>

              <div class="text-end">
                <span class="fw-bold">₱{{ number_format($payroll->net_pay, 2) }}</span><br>
                <small class="
                  {{ $payroll->status == 'Paid' ? 'text-success' : ($payroll->status == 'Pending' ? 'text-warning' : 'text-muted') }}
                ">
                  {{ $payroll->status }}
                </small>
              </div>
            </div>

          @empty
            <p class="text-center text-muted py-3">No payroll records found.</p>
          @endforelse

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
  </div>


  {{-- Bottom Section --}}
  <div class="row g-4">

    {{-- Transaction History --}}
    <div class="col-lg-8">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">Transaction History</h6>
            <a href="#" class="text-primary small fw-medium" data-bs-toggle="modal" data-bs-target="#transactionModal">
              See All
            </a>
          </div>

          @forelse($payrolls as $payroll)
            @php $employee = $payroll->employee; @endphp
            <div class="d-flex justify-content-between align-items-center py-3 border-top">
              <div class="d-flex align-items-center">
                <img src="{{ $employee && $employee->profile_picture && file_exists(storage_path('app/public/' . $employee->profile_picture)) 
                  ? asset('storage/' . $employee->profile_picture) 
                  : asset('images/default-profile.png') }}" 
                  alt="Profile photo" class="rounded-circle me-3" width="50" height="50">
                <div>
                  <div class="fw-semibold">{{ $employee->first_name ?? 'N/A' }} {{ $employee->last_name ?? '' }}</div>
                  <small class="text-muted">Pay Period: {{ $payroll->pay_period_start }} - {{ $payroll->pay_period_end }}</small>
                </div>
              </div>
              <div class="text-end me-3">
                <small class="text-muted d-block">{{ $payroll->payment_date ?? '-' }}</small>
                <span class="fw-semibold">₱{{ number_format($payroll->net_pay, 2) }}</span>
              </div>
            </div>
          @empty
            <p class="text-center text-muted mb-0 py-3">No payroll records found.</p>
          @endforelse
        </div>
      </div>
    </div>

    <div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 shadow">

          <div class="modal-header">
            <h5 class="modal-title fw-semibold">All Transactions</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            @forelse($payrolls as $payroll)
              @php $employee = $payroll->employee; @endphp
              
              <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <div class="d-flex align-items-center">
                  <img src="{{ $employee && $employee->profile_picture && file_exists(storage_path('app/public/' . $employee->profile_picture)) 
                    ? asset('storage/' . $employee->profile_picture) 
                    : asset('images/default-profile.png') }}"
                    alt="Profile photo" class="rounded-circle me-3" width="45" height="45">

                  <div>
                    <div class="fw-semibold">
                      {{ $employee->first_name ?? 'N/A' }} {{ $employee->last_name ?? '' }}
                    </div>
                    <small class="text-muted">
                      Pay Period: {{ $payroll->pay_period_start }} - {{ $payroll->pay_period_end }}
                    </small>
                  </div>
                </div>

                <div class="text-end">
                  <small class="text-muted d-block">{{ $payroll->payment_date ?? '-' }}</small>
                  <span class="fw-semibold">₱{{ number_format($payroll->net_pay, 2) }}</span>
                </div>
              </div>

            @empty
              <p class="text-center text-muted py-3">No payroll records found.</p>
            @endforelse
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>


    {{-- Payroll Details (Previous + Upcoming) --}}
    <div class="col-lg-4">
      <div class="d-flex flex-column gap-4">

        {{-- Previous Payroll --}}
        @if($previousPayroll)
          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-semibold mb-0">Previous Payroll</h6>
                <span class="badge bg-success bg-opacity-10 text-success fw-semibold px-3 py-2">PAID</span>
              </div>
              <h4 class="fw-bold mb-0">₱{{ number_format($previousPayroll->net_pay, 2) }}</h4>
              <small class="text-muted">{{ $previousPayroll->payment_date }}</small>
            </div>
          </div>
        @endif

        {{-- Upcoming Payroll --}}
        @if($upcomingPayroll)
          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-semibold mb-0">Upcoming Payroll</h6>
                <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold px-3 py-2">PENDING</span>
              </div>
              <h4 class="fw-bold mb-0">₱{{ number_format($upcomingPayroll->net_pay, 2) }}</h4>
              <small class="text-muted">{{ $upcomingPayroll->payment_date }}</small>
              <hr>
              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <img src="https://ui-avatars.com/api/?name={{ urlencode($upcomingPayroll->employee->first_name ?? 'N/A') }}&background=random" 
                       class="rounded-circle me-3" width="40" height="40" alt="">
                  <div>
                    <div class="fw-semibold">{{ $upcomingPayroll->employee->first_name ?? 'N/A' }} {{ $upcomingPayroll->employee->last_name ?? '' }}</div>
                    <small class="text-muted">Employee ID: {{ $upcomingPayroll->employee_id }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endif

      </div>
    </div>
  </div>
</div>
@endsection
