<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayFlowController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\NotificationPreferenceController;

Route::middleware('web')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('/verify-code', [PasswordResetLinkController::class, 'verifyCode'])->name('password.verify.code');

    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
    
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');

    Route::get('/attendance/scanner', [AttendanceController::class, 'scanner'])->name('attendance.scanner');

    Route::middleware('auth')->group(function () {
        Route::get('/', [PayFlowController::class, 'dashboard'])->name('dashboard');
        Route::get('/employees', [PayFlowController::class, 'employees'])->name('employees');
        Route::get('/attendance', [PayFlowController::class, 'attendance'])->name('attendance');
        Route::get('/payrolldata', [PayFlowController::class, 'payrolldata'])->name('payrolldata');
        Route::get('/reports', [PayFlowController::class, 'reports'])->name('reports');
        Route::get('/settings', [PayFlowController::class, 'settings'])->name('settings');

        Route::put('/leave-requests/{leave_request_id}/approve', [LeaveRequestController::class, 'approve'])->name('leave.approve');
        Route::put('/leave-requests/{leave_request_id}/reject', [LeaveRequestController::class, 'reject'])->name('leave.reject');

        Route::post('/settings/update-password', [PayFlowController::class, 'updatePassword'])->name('hr.update-password');
        Route::resource('employees', EmployeeController::class);
        Route::resource('attendance', AttendanceController::class);
    });

    Route::middleware(['auth', 'namecheck:Accountant'])->group(function () {
        Route::get('/accountant/dashboard', [AccountantController::class, 'dashboard'])->name('accountant.dashboard');
        Route::get('/accountant/payrollprocessing', [PayrollController::class, 'index'])->name('accountant.payrollprocessing');
        Route::post('/accountant/payrollprocessing/store', [PayrollController::class, 'store'])->name('accountant.payrollprocessing.store');
        Route::get('/accountant/payrollprocessing/{id}/edit', [PayrollController::class, 'edit'])->name('accountant.payrollprocessing.edit');
        Route::put('/accountant/payrollprocessing/{id}', [PayrollController::class, 'update'])->name('accountant.payrollprocessing.update');
        Route::delete('/accountant/payrollprocessing/{id}', [PayrollController::class, 'destroy'])->name('accountant.payrollprocessing.destroy');
        Route::get('/accountant/deductions', [DeductionController::class, 'deduction'])->name('accountant.deductions');
        Route::post('/deductions/store', [DeductionController::class, 'store'])->name('deductions.store');
        Route::get('/accountant/settings', [AccountantController::class, 'settings'])->name('accountant.settings');
        Route::post('/accountant/update-password', [PayrollController::class, 'updatePassword'])->name('account.update.password');
    });

    Route::middleware(['auth:employee'])->group(function () {
        Route::prefix('employeepages')->group(function () {
            Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
            Route::get('/profile', [EmployeeController::class, 'profile'])->name('employee.profile');
            Route::post('/profile/update', [EmployeeController::class, 'updateProfile'])->name('employee.profile.update');
            Route::get('/request', [EmployeeController::class, 'request'])->name('employee.request');
            Route::resource('leave-requests', LeaveRequestController::class);
            Route::get('/settings', [EmployeeController::class, 'settings'])->name('employee.settings');
            Route::resource('employee', EmployeeController::class);
            Route::put('/change-password', [EmployeeController::class, 'changePassword'])->name('employee.changePassword');
            Route::post('/notifications/mark-all-read', [EmployeeController::class, 'markAllRead'])->name('notifications.markAllRead');
            Route::put('/notification-preferences', [NotificationPreferenceController::class, 'update'])->name('notification.preferences.update');
        });
    });

});
