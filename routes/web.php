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
    Route::get('/employee/dashboard', [DashboardController::class, 'employee', 'intern'])->name('dashboard');
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

    // Admin Edit & Update User Details
    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');

    // === NEW: Route for Password Reset Modal ===
    Route::put('/admin/users/{id}/password', [UserController::class, 'updatePassword'])->name('admin.users.password');
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
// ... inside the existing LeaveController group ...

use App\Http\Controllers\LeaveController;
Route::middleware(['auth'])->group(function () {
    // 1. Show the Application Form (Existing)
    Route::get('/employee/leave', [LeaveController::class, 'create'])->name('leave.create');
    
    // 2. Process the Form (Existing)
    Route::post('/employee/leave/store', [LeaveController::class, 'store'])->name('leave.store');

    // 3. NEW: Show the List of Applications
    Route::get('/employee/leave/history', [LeaveController::class, 'history'])->name('leave.history');
});

//clock in & clock out
use App\Http\Controllers\AttendanceController;
Route::middleware(['auth'])->group(function () {
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');
    Route::get('/employee/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
});

// INTERN DOCUMENT UPLOAD ROUTE
use App\Http\Controllers\internDocumentController;
Route::middleware(['auth'])->group(function () {
    // 1. Show the list (History)
    Route::get('/intern/documents', [InternDocumentController::class, 'index'])->name('intern.documents.index');
    // 2. Show the form (Upload Page)
    Route::get('/intern/documents/create', [InternDocumentController::class, 'create'])->name('intern.documents.create');
    // 3. Process the upload
    Route::post('/intern/documents', [InternDocumentController::class, 'store'])->name('intern.documents.store');
    // SUPERVISOR DOCUMENTS
    Route::get('/supervisor/documents', [InternDocumentController::class, 'supervisorIndex'])->name('supervisor.documents.index');
    Route::get('/supervisor/documents/{id}/review', [InternDocumentController::class, 'edit'])->name('supervisor.documents.review');
    Route::put('/supervisor/documents/{id}', [InternDocumentController::class, 'update'])->name('supervisor.documents.update');
});

//ADMIN VIEW
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\EmployeeDirectoryController; 
Route::middleware(['auth'])->group(function () {
    Route::get('/directory', [EmployeeDirectoryController::class, 'index'])->name('admin.directory');
    Route::get('/directory/{id}/details', [App\Http\Controllers\EmployeeDirectoryController::class, 'show'])->name('directory.show');

    // === ORGANIZATION ROUTES (ADMIN) ===
    // This prefix 'admin.org.' matches the view calls like 'admin.org.departments.store'
    Route::prefix('admin/organization')->name('admin.org.')->group(function() {
        
        // Departments (Plural)
        Route::get('/departments', [OrganizationController::class, 'indexDepartments'])->name('departments');
        Route::post('/departments', [OrganizationController::class, 'storeDepartment'])->name('departments.store');
        Route::delete('/departments/{id}', [OrganizationController::class, 'destroyDepartment'])->name('departments.delete');

        // Jobs (Plural)
        Route::get('/jobs', [OrganizationController::class, 'indexJobs'])->name('jobs');
        Route::post('/jobs', [OrganizationController::class, 'storeJob'])->name('jobs.store');
        Route::delete('/jobs/{id}', [OrganizationController::class, 'destroyJob'])->name('jobs.delete');

        // Structure
        Route::get('/structure', [OrganizationController::class, 'structure'])->name('structure');
    });
});