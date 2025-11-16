@extends('layouts.employeeapp')

@section('content')
<div class="container mt-4">
    <div class="container  mb-5">
    <div class="d-flex align-items-center justify-content-start mb-4 gap-3">
    <h3 class="fw-bold mb-0">Leave Request</h3>
    <div class="d-flex align-items-center justify-content-end gap-2 px-3 py-1 rounded-3 bg-light shadow-sm">
        <i class="bi bi-clock fs-5 text-secondary"></i>
        <span class="fw-medium" id="currentDateTime">Nov 6, 2025 10:00 AM</span>
    </div>
</div>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
    {{-- Notification Preferences --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold">Notification Preferences</h5>
            <p class="text-muted">Email Notifications</p>

            {{-- 🔒 Notification Preferences Form --}}
            <form method="POST" action="{{ route('notification.preferences.update') }}">
                @csrf
                @method('PUT')
                {{-- ⚡ Get the logged-in employee and preference --}}
                @php
                    $employee = auth()->guard('employee')->user();
                    $pref = optional($employee?->notificationPreference);
                @endphp

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="payslip_ready" id="payslip_ready"
                        {{ $pref?->payslip_ready ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="payslip_ready">
                        Payslip Ready
                    </label>
                    <p class="text-muted small mb-0">Get notified when your payslip is available</p>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="leave_updates" id="leave_updates"
                        {{ $pref?->leave_updates ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="leave_updates">
                        Leave Updates
                    </label>
                    <p class="text-muted small mb-0">Updates on leave request status</p>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="benefits_information" id="benefits_information"
                        {{ $pref?->benefits_information ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="benefits_information">
                        Benefits Information
                    </label>
                    <p class="text-muted small mb-0">Updates about your benefits and enrollment</p>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="important_reminders" id="important_reminders"
                        {{ $pref?->important_reminders ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="important_reminders">
                        Important Reminders
                    </label>
                    <p class="text-muted small mb-0">Deadline reminders and important updates</p>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    {{-- Change Password --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold">Change Password</h5>
            <form method="POST" action="{{ route('employee.changePassword') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
@endsection