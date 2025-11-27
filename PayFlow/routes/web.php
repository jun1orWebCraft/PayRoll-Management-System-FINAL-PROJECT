<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PayFlowController,
    AccountantController,
    PayrollController,
    EmployeeController,
    AttendanceController,
    DeductionController,
    Auth\AuthenticatedSessionController,
    Auth\PasswordResetLinkController,
    Auth\NewPasswordController,
    LeaveRequestController,
    NotificationPreferenceController,
    EmployeeScheduleController
};

Route::middleware('web')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('/verify-code', [PasswordResetLinkController::class, 'verifyCode'])->name('password.verify.code');

    Route::get('/reset-password/{token}', fn($token) => view('auth.reset-password', ['token' => $token]))->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');


    Route::middleware(['auth'])->group(function () {

    Route::get('/', [PayFlowController::class, 'dashboard'])->name('dashboard');
    Route::get('/employees', [PayFlowController::class, 'employees'])->name('employees');
    Route::get('/attendance', [PayFlowController::class, 'attendance'])->name('attendance');
    Route::get('/payrolldata', [PayFlowController::class, 'payrolldata'])->name('payrolldata');
    Route::get('/reports', [PayFlowController::class, 'reports'])->name('reports');
    Route::get('/settings', [PayFlowController::class, 'settings'])->name('settings');
    Route::get('/employeeschedule', [PayFlowController::class, 'employeeschedule'])->name('employeeschedule');

    Route::put('/leave-requests/{leave_request_id}/approve', [LeaveRequestController::class, 'approve'])->name('leave.approve');
    Route::put('/leave-requests/{leave_request_id}/reject', [LeaveRequestController::class, 'reject'])->name('leave.reject');

    Route::post('/settings/update-password', [PayFlowController::class, 'updatePassword'])->name('hr.update-password');

    Route::resource('employees', EmployeeController::class);
    Route::resource('employeeschedule', EmployeeScheduleController::class);
    Route::resource('attendance', AttendanceController::class);
    Route::get('/attendance-scanner', [AttendanceController::class, 'scanner'])->name('attendance.scanner');
    Route::post('/attendance-scanner/store', [AttendanceController::class, 'storeScanner'])->name('attendance.scanner.store');
    });

    Route::middleware(['namecheck:Accountant'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/dashboard', [AccountantController::class, 'dashboard'])->name('dashboard');
    Route::get('/payrollprocessing', [PayrollController::class, 'index'])->name('payrollprocessing');
    Route::post('/payrollprocessing/store', [PayrollController::class, 'store'])->name('payrollprocessing.store');
    Route::get('/payrollprocessing/{id}/edit', [PayrollController::class, 'edit'])->name('payrollprocessing.edit');
    Route::put('/payrollprocessing/{id}', [PayrollController::class, 'update'])->name('payrollprocessing.update');
    Route::delete('/payrollprocessing/{id}', [PayrollController::class, 'destroy'])->name('payrollprocessing.destroy');
    Route::delete('/deductions/{deduction}', [DeductionController::class, 'destroy'])->name('deductions.destroy');
    Route::get('/deductions', [DeductionController::class, 'deduction'])->name('deductions');
    Route::post('/deductions/store', [DeductionController::class, 'store'])->name('deductions.store');
    Route::get('/accountant/deductions/compute/{employee}', [DeductionController::class, 'ajaxCompute']);
    Route::get('/accountant/deductions/compute/{employee_id}', [DeductionController::class, 'compute']);
    Route::get('/settings', [AccountantController::class, 'settings'])->name('settings');
    Route::post('/update-password', [PayrollController::class, 'updatePassword'])->name('update.password');
    });

    Route::middleware(['auth:employee'])->prefix('employeepages')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
        Route::get('/profile', [EmployeeController::class, 'profile'])->name('employee.profile');
        Route::post('/profile/update', [EmployeeController::class, 'updateProfile'])->name('employee.profile.update');
        Route::get('/request', [EmployeeController::class, 'request'])->name('employee.request');
        Route::resource('leave-requests', LeaveRequestController::class);
        Route::get('/settings', [EmployeeController::class, 'settings'])->name('employee.settings');
        Route::put('/change-password', [EmployeeController::class, 'changePassword'])->name('employee.changePassword');
        Route::post('/notifications/mark-all-read', [EmployeeController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::put('/notification-preferences', [NotificationPreferenceController::class, 'update'])->name('notification.preferences.update');
        Route::get('/payslip/{payroll}', [EmployeeController::class, 'viewPayslip'])->name('employee.payslip.view');
        Route::get('/payslip/{payroll}/download', [EmployeeController::class, 'downloadPayslip'])->name('employee.payslip.download');
    });
   
});
