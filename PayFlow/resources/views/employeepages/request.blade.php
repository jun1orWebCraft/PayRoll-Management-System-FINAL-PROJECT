@extends('layouts.employeeapp')

@section('content')

<div class="container py-4">
    <div class="container  mb-5">
    <!-- TOP BAR: Date/Time + Notification -->
    <div class="d-flex align-items-center justify-content-start mb-4 gap-3">
    <!-- Dashboard Title -->
    <h3 class="fw-bold mb-0">Leave Request</h3>

    <!-- Current Date & Time -->
    <div class="d-flex align-items-center justify-content-end gap-2 px-3 py-1 rounded-3 bg-light shadow-sm">
        <i class="bi bi-clock fs-5 text-secondary"></i>
        <span class="fw-medium" id="currentDateTime">Nov 6, 2025 10:00 AM</span>
    </div>
</div>
    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: Leave Request Form -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Leave Request Details</h5>

                    <form action="{{ route('leave-requests.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Leave Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="leave_type" required>
                                    <option value="">Select leave type</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('leave_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason for Leave <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" name="reason" rows="4" maxlength="500"
                                      placeholder="Please provide a detailed reason for your leave request..." required>{{ old('reason') }}</textarea>
                            <div class="d-flex justify-content-between mt-1">
                                @error('reason')
                                    <small class="text-danger">{{ $message }}</small>
                                @else
                                    <small class="text-muted" id="charCount">0/500 characters</small>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Employee Info and Leave Balances -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="bi bi-person-circle me-2"></i>Employee Information</h6>
                    <hr>
                    @php
                        $employee = Auth::guard('employee')->user();
                    @endphp
                    <p class="mb-1"><strong>Name:</strong> {{ $employee->first_name }} {{ $employee->last_name }}</p>
                    <p class="mb-1"><strong>Employee No.:</strong> {{ $employee->employee_no ?? $employee->employee_id ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Position:</strong> {{ optional($employee->position)->position_name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="bi bi-calendar-check me-2"></i>Leave Balance</h6>
                    <hr>
                    @if(!empty($leaveBalances))
                        @foreach ($leaveBalances as $type => $days)
                            <p class="mb-1">{{ ucfirst($type) }}: <strong>{{ $days }} days</strong></p>
                        @endforeach
                    
                        
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold text-primary"><i class="bi bi-file-earmark-text text-primary me-2"></i>Leave Request Guidelines</h6>
                    <hr>
                    <ul class="ps-3 mb-0 small text-primary">
                        <li>Submit requests at least 2 weeks in advance</li>
                        <li>Emergency leave can be submitted retroactively</li>
                        <li>Provide detailed reason for your request</li>
                        <li>Check with your manager before submitting</li>
                        <li>You'll receive email notifications on status updates</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const start = document.getElementById('start_date');
    const end = document.getElementById('end_date');
    const output = document.getElementById('numDays');
    const reason = document.getElementById('reason');
    const charCount = document.getElementById('charCount');

    function calcDays() {
        if (start.value && end.value) {
            const s = new Date(start.value);
            const e = new Date(end.value);
            if (!isNaN(s) && !isNaN(e) && e >= s) {
                const diff = Math.floor((e - s) / (1000 * 60 * 60 * 24)) + 1;
                output.value = `${diff} day${diff > 1 ? 's' : ''}`;
            } else {
                output.value = '0 days';
            }
        } else {
            output.value = '0 days';
        }
    }

    function updateCharCount() {
        charCount.textContent = `${reason.value.length}/500 characters`;
    }

    start.addEventListener('change', calcDays);
    end.addEventListener('change', calcDays);
    reason.addEventListener('input', updateCharCount);

    calcDays();
    updateCharCount();
});

</script>
@endpush
@endsection
