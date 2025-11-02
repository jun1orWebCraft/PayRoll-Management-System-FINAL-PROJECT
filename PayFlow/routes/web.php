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

Route::middleware('web')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->middleware('guest')
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');


    Route::get('/attendance/scanner', [AttendanceController::class, 'scanner'])
        ->name('attendance.scanner');

    Route::middleware('auth')->group(function () {
        Route::get('/', [PayFlowController::class, 'dashboard'])->name('dashboard');

        Route::get('/employees', [PayFlowController::class, 'employees'])->name('employees');
        Route::get('/attendance', [PayFlowController::class, 'attendance'])->name('attendance');
        Route::get('/taxanddeductions', [PayFlowController::class, 'taxanddeductions'])->name('taxanddeductions');
        Route::get('/reports', [PayFlowController::class, 'reports'])->name('reports');
        Route::get('/settings', [PayFlowController::class, 'settings'])->name('settings');

        Route::resource('employees', EmployeeController::class);
        Route::resource('attendance', AttendanceController::class);
    });

    Route::middleware(['auth', 'namecheck:Accountant'])->group(function () {
        Route::get('/accountant/dashboard', [AccountantController::class, 'dashboard'])->name('accountant.dashboard');
        Route::get('/accountant/payrollprocessing', [PayrollController::class, 'payrollprocessing'])->name('accountant.payrollprocessing');
        Route::post('/payroll/store', [PayrollController::class, 'store'])->name('payroll.store');
        Route::get('/accountant/deductions', [DeductionController::class, 'deduction'])->name('accountant.deductions');
        Route::get('/accountant/settings', [AccountantController::class, 'settings'])
            ->name('accountant.settings');
        Route::post('/deductions/store', [DeductionController::class, 'store'])->name('deductions.store');

    });
});
