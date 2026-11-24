<?php

use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\LeaveController;
use App\Http\Controllers\Employee\PayslipController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'password.changed', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', EmployeeDashboardController::class)->name('dashboard');

    // Attendance ESS (Clock-In & Clock-Out)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

    // Leave ESS (Requests & Balance)
    Route::resource('leaves', LeaveController::class)->only(['index', 'create', 'store']);

    // Payslips ESS
    Route::get('/payslips', [PayslipController::class, 'index'])->name('payslips.index');
    Route::get('/payslips/{item}', [PayslipController::class, 'show'])->name('payslips.show');
    Route::get('/payslips/{item}/download', [PayslipController::class, 'downloadPdf'])->name('payslips.download');
});
