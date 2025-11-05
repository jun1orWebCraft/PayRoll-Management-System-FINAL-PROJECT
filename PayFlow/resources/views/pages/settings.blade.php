@extends('layouts.app')

@section('title', '')

@section('content')
<div class="container py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Settings</h2>
            <p class="text-muted mb-0">Manage your account, preferences, and system configurations</p>
        </div>
        <i class="bi bi-gear-wide-connected fs-1 text-primary"></i>
    </div>

    <div class="row g-4">
        {{-- Account Settings --}}
        <div class="col-md-6">
    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-person-circle fs-4 text-primary me-2"></i>
            <h5 class="fw-bold mb-0">Account Settings</h5>
        </div>
        <p class="text-muted small mb-3">Update your login credentials</p>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form action="{{ route('hr.update-password') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Current Password</label>
                <input type="password" name="current_password" class="form-control rounded-3 @error('current_password') is-invalid @enderror" required>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <input type="password" name="new_password" class="form-control rounded-3 @error('new_password') is-invalid @enderror" required>
                @error('new_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" class="form-control rounded-3" required>
            </div>

            <button type="submit" class="btn btn-primary rounded-3 w-100">Update Password</button>
        </form>
    </div>
</div>


        {{-- System Settings --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-sliders2 fs-4 text-success me-2"></i>
                    <h5 class="fw-bold mb-0">System Preferences</h5>
                </div>
                <p class="text-muted small mb-3">Customize dashboard appearance and notifications</p>

                <form>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                        <label class="form-check-label" for="darkModeSwitch">Enable Dark Mode</label>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifications" checked>
                        <label class="form-check-label" for="notifications">Receive Email Notifications</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Language</label>
                        <select class="form-select rounded-3">
                            <option selected>English</option>
                            <option>Filipino</option>
                            <option>Spanish</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success rounded-3 w-100">Save Preferences</button>
                </form>
            </div>
        </div>
    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleSwitch = document.getElementById('darkModeSwitch');
    const body = document.body;

    // ✅ Check saved mode in localStorage
    const savedMode = localStorage.getItem('theme');
    if (savedMode === 'dark') {
        body.classList.add('dark-mode');
        toggleSwitch.checked = true;
    }

    // ✅ Toggle dark mode
    toggleSwitch.addEventListener('change', function () {
        if (this.checked) {
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }
    });
});
</script>

@endsection
