<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForcePasswordChangeController;
use App\Http\Controllers\Shared\LeaveAttachmentController;
use App\Http\Controllers\Shared\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
});

// Authenticated General Routes & Role Redirector
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Mandatory First-Login Password Change
    Route::get('/password/change', [ForcePasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/change', [ForcePasswordChangeController::class, 'update'])->name('password.change.update');
});

Route::middleware(['auth', 'password.changed'])->group(function () {
    // Shared Profile Management (HR, Manager, Employee)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Secure Leave Attachment Viewer (Protected by LeaveRequestPolicy Anti-IDOR)
    Route::get('/leaves/{leave}/attachment', [LeaveAttachmentController::class, 'show'])->name('leaves.attachment');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('hr')) {
            return redirect()->route('hr.dashboard');
        } elseif ($user->hasRole('manager')) {
            return redirect()->route('manager.dashboard');
        } else {
            return redirect()->route('employee.dashboard');
        }
    })->name('dashboard');
});

// Modular Role-Based Routes
require __DIR__.'/hr.php';
require __DIR__.'/manager.php';
require __DIR__.'/employee.php';
