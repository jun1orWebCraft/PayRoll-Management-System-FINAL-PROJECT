<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayFlowController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;


Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')->name('password.request');
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')->name('password.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name('password.reset');
Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->name('password.update');
Route::get('/attendance/scanner', [AttendanceController::class, 'scanner'])
    ->name('attendance.scanner');

Route::middleware('auth')->group(function () {
    // Dashboard Pages
    Route::get('/', [PayFlowController::class, 'dashboard'])->name('dashboard');
    Route::get('/employees', [PayFlowController::class, 'employees'])->name('employees');
    Route::get('/attendance', [PayFlowController::class, 'attendance'])->name('attendance');
    Route::get('/taxanddeductions', [PayFlowController::class, 'taxanddeductions'])->name('taxanddeductions');
    Route::get('/payrollprocessing', [PayFlowController::class, 'payrollprocessing'])->name('payrollprocessing');
    Route::get('/reports', [PayFlowController::class, 'reports'])->name('reports');
    Route::get('/settings', [PayFlowController::class, 'settings'])->name('settings');

    // RESTful Resources
    Route::resource('employees', EmployeeController::class);
    Route::resource('attendance', AttendanceController::class);
});
