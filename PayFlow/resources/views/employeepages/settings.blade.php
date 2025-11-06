@extends('layouts.employeeapp')

@section('content')
<div class="container mt-4">
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

    {{-- Notification Preferences --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold">Notification Preferences</h5>
            <p class="text-muted">Email Notifications</p>

            <form method="POST" action="">
                @csrf
                @method('PUT')

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="payslip_ready" id="payslip_ready" checked>
                    <label class="form-check-label fw-semibold" for="payslip_ready">
                        Payslip Ready
                    </label>
                    <p class="text-muted small mb-0">Get notified when your payslip is available</p>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="leave_updates" id="leave_updates" checked>
                    <label class="form-check-label fw-semibold" for="leave_updates">
                        Leave Updates
                    </label>
                    <p class="text-muted small mb-0">Updates on leave request status</p>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="benefits_information" id="benefits_information">
                    <label class="form-check-label fw-semibold" for="benefits_information">
                        Benefits Information
                    </label>
                    <p class="text-muted small mb-0">Updates about your benefits and enrollment</p>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="important_reminders" id="important_reminders" checked>
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

            <form method="POST" action="">
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

    {{-- Two-Factor Authentication --}}
    <div class="card mb-5">
        <div class="card-body">
            <h5 class="card-title fw-bold">Two-Factor Authentication</h5>
            <p class="text-muted mb-3">
                SMS Authentication<br>
                <span class="small">Receive security codes via text message</span>
            </p>
            <button class="btn btn-outline-primary" type="button">
                <i class="bi bi-shield-lock me-1"></i> Enable
            </button>
        </div>
    </div>

</div>
@endsection