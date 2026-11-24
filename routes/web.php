<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::post('/demo-login', [AuthController::class, 'demoLogin'])->name('login.demo');
});

// Authenticated General Routes & Role Redirector
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Secure Leave Attachment Viewer (Protected by LeaveRequestPolicy Anti-IDOR)
    Route::get('/leaves/{leave}/attachment', [\App\Http\Controllers\Shared\LeaveAttachmentController::class, 'show'])->name('leaves.attachment');

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
