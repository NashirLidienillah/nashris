<?php

use App\Http\Controllers\Manager\AttendanceController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\LeaveApprovalController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', ManagerDashboardController::class)->name('dashboard');

    // Team Attendance Monitoring
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

    // Team Leave Approvals (Step 1)
    Route::get('/leaves', [LeaveApprovalController::class, 'index'])->name('leaves.index');
    Route::post('/leaves/{leave}/approve', [LeaveApprovalController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveApprovalController::class, 'reject'])->name('leaves.reject');
});
