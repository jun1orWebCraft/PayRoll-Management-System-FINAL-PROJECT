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

// ----------------------------
// 🔐 Authentication Routes
// ----------------------------
Route::middleware('web')->group(function () {

    // 🔸 Login / Logout
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // 🔸 Forgot / Reset Password
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('/verify-code', [PasswordResetLinkController::class, 'verifyCode'])->name('password.verify.code');

    // Laravel’s built-in reset route:
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
    
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
        
        Route::post('/settings/update-password', [PayFlowController::class, 'updatePassword'])->name('hr.update-password');
        Route::resource('employees', EmployeeController::class);
        Route::resource('attendance', AttendanceController::class);
    });

    Route::middleware(['auth', 'namecheck:Accountant'])->group(function () {
        Route::get('/accountant/dashboard', [AccountantController::class, 'dashboard'])
            ->name('accountant.dashboard');
        Route::get('/accountant/payrollprocessing', [PayrollController::class, 'payrollprocessing'])
            ->name('accountant.payrollprocessing');
        Route::post('/payroll/store', [PayrollController::class, 'store'])
            ->name('payroll.store');
        Route::get('/accountant/deductions', [DeductionController::class, 'deduction'])
            ->name('accountant.deductions');
        Route::get('/accountant/settings', [AccountantController::class, 'settings'])
            ->name('accountant.settings');
        Route::post('/deductions/store', [DeductionController::class, 'store'])
            ->name('deductions.store');
    });

    Route::middleware(['auth:employee'])->group(function () {
        Route::prefix('employeepages')->group(function () {

            Route::get('/dashboard', [EmployeeController::class, 'dashboard'])
                ->name('employee.dashboard');

      
            Route::get('/profile', [EmployeeController::class, 'profile'])
                ->name('employee.profile');
            
            Route::get('/request', [EmployeeController::class, 'request'])
            ->name('employee.request');
       
            Route::get('/settings', [EmployeeController::class, 'settings'])
                ->name('employee.settings');
            
            Route::resource('employee', EmployeeController::class);
            
        });
    });

    
});
