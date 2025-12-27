<?php

use Illuminate\Support\Facades\Route;
// Home
Route::get('/', function () {
    return view('home');
});

use App\Http\Controllers\AuthController;
// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

use App\Http\Controllers\DashboardController;
//dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin']);
    Route::get('/supervisor/dashboard', [DashboardController::class, 'supervisor']);
    Route::get('/employee/dashboard', [DashboardController::class, 'employee']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
});

use App\Http\Controllers\UserController;
//key in new employee/user
Route::middleware(['auth'])->group(function () {

    Route::get('/admin/users/create', [UserController::class, 'create'])
        ->name('admin.users.create');

    Route::post('/admin/users/store', [UserController::class, 'store'])
        ->name('admin.users.store');
});

use App\Http\Controllers\ProfileController;
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard'); // or whatever you named this file
});

//Apply for a leave
use App\Http\Controllers\LeaveController;
Route::middleware(['auth'])->group(function () {
    // Show the form
    Route::get('/employee/leave', [LeaveController::class, 'create'])->name('leave.create');
    
    // Process the form submission
    Route::post('/employee/leave/store', [LeaveController::class, 'store'])->name('leave.store');
});

//clock in & clock out
use App\Http\Controllers\AttendanceController;
Route::middleware(['auth'])->group(function () {
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');
    Route::get('/employee/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
});