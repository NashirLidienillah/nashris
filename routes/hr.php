<?php

use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\AuditLogController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\HR\DepartmentController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\LeaveApprovalController;
use App\Http\Controllers\HR\PayrollController;
use App\Http\Controllers\HR\PositionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', HRDashboardController::class)->name('dashboard');

    // Master Data: Departments
    Route::resource('departments', DepartmentController::class)->except(['create', 'show', 'edit']);

    // Master Data: Positions
    Route::resource('positions', PositionController::class)->except(['create', 'show', 'edit']);

    // Employee Directory & Document Management
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/documents', [EmployeeController::class, 'uploadDocument'])->name('employees.documents.upload');
    Route::delete('employees/{employee}/documents/{document}', [EmployeeController::class, 'destroyDocument'])->name('employees.documents.destroy');

    // Attendance Management & Correction
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');

    // Leave Management & Step 2 Final Approval
    Route::get('/leaves', [LeaveApprovalController::class, 'index'])->name('leaves.index');
    Route::post('/leaves/{leave}/approve', [LeaveApprovalController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveApprovalController::class, 'reject'])->name('leaves.reject');

    // Payroll Engine & Management
    Route::resource('payrolls', PayrollController::class)->only(['index', 'store', 'show']);
    Route::post('/payrolls/{payroll}/finalize', [PayrollController::class, 'finalize'])->name('payrolls.finalize');
    Route::get('/payrolls/items/{item}/pdf', [PayrollController::class, 'downloadPdf'])->name('payrolls.pdf');

    // Audit Log & Activity Trail
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

// Employee Documents View & Download (Accessible by HR or Owner Employee, Anti-IDOR enforced by EmployeeDocumentPolicy)
Route::middleware(['auth'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('employees/{employee}/documents/{document}/view', [EmployeeController::class, 'viewDocument'])->name('employees.documents.view');
    Route::get('employees/{employee}/documents/{document}/download', [EmployeeController::class, 'downloadDocument'])->name('employees.documents.download');
});
