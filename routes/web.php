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

    //DELETE FUNCTION
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
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

    // Admin Attendance Tracking
    Route::get('/admin/attendance', [AttendanceController::class, 'adminIndex'])
        ->name('admin.attendance');
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
    Route::get('/supervisor/team', [EmployeeDirectoryController::class, 'myTeam'])->name('supervisor.team');

    // === ORGANIZATION ROUTES (ADMIN) ===
    // This prefix 'admin.org.' matches the view calls like 'admin.org.departments.store'
    Route::prefix('admin/organization')->name('admin.org.')->group(function() {
        
       // 1. Departments Group (Prefix: admin.org.departments.)
        Route::prefix('departments')->name('departments.')->group(function() {
            Route::get('/', [OrganizationController::class, 'indexDepartments'])->name('index'); // -> admin.org.departments.index
            Route::post('/', [OrganizationController::class, 'storeDepartment'])->name('store');
            Route::get('/{id}/edit', [OrganizationController::class, 'editDepartment'])->name('edit');
            Route::put('/{id}', [OrganizationController::class, 'updateDepartment'])->name('update');
            Route::delete('/{id}', [OrganizationController::class, 'destroyDepartment'])->name('destroy'); // Fixes missing delete route
        });

        // 2. Job Positions Group (Prefix: admin.org.jobs.)
        Route::prefix('jobs')->name('jobs.')->group(function() {
            Route::get('/', [OrganizationController::class, 'indexJobs'])->name('index'); // -> admin.org.jobs.index
            Route::post('/', [OrganizationController::class, 'storeJob'])->name('store');
            Route::get('/{id}/edit', [OrganizationController::class, 'editJob'])->name('edit');
            Route::put('/{id}', [OrganizationController::class, 'updateJob'])->name('update');
            Route::delete('/{id}', [OrganizationController::class, 'destroyJob'])->name('destroy');
        });
        

        // 1. Supervisor Assignments
        Route::get('/structure/assignments', [OrganizationController::class, 'structureAssignments'])->name('structure.assignments');
        
        // 2. Team Teams
        Route::get('/structure/teams', [OrganizationController::class, 'structureTeams'])->name('structure.teams');

        // Actions
        Route::post('/structure/assign', [OrganizationController::class, 'assignSupervisor'])->name('structure.assign');
        Route::post('/structure/unassign/{id}', [OrganizationController::class, 'unassignSupervisor'])->name('structure.unassign');
    });

    
});

//LEAVE MANAGEMENT - ADMIN
use App\Http\Controllers\AdminLeaveController;
Route::middleware(['auth'])->prefix('admin/leave')->name('admin.leave.')->group(function() {
    // 1. Leave Types
    Route::get('/types', [AdminLeaveController::class, 'indexTypes'])->name('types');
    Route::post('/types', [AdminLeaveController::class, 'storeType'])->name('types.store');
    Route::delete('/types/{id}', [AdminLeaveController::class, 'destroyType'])->name('types.delete');
    Route::put('/types/{id}', [AdminLeaveController::class, 'updateType'])->name('types.update');

    // 2. Leave Requests
    Route::get('/requests', [AdminLeaveController::class, 'indexRequests'])->name('requests');
    Route::put('/requests/{id}', [AdminLeaveController::class, 'updateStatus'])->name('status');

    // 3. Leave Balances
    Route::get('/balances', [AdminLeaveController::class, 'indexBalances'])->name('balances');
    Route::post('/balances', [AdminLeaveController::class, 'storeBalance'])->name('balances.store');
});

use App\Http\Controllers\AnnouncementController;
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function() {
    // ... existing routes ...

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.delete');
});

use App\Http\Controllers\ReportController;
// Admin
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/reports', [ReportController::class, 'adminIndex'])->name('admin.reports');
});

// Supervisor
Route::middleware(['auth'])->prefix('supervisor')->group(function () {
    Route::get('/reports', [ReportController::class, 'supervisorIndex'])->name('supervisor.reports');
});

// Employee & Intern
Route::middleware(['auth'])->group(function () {
    Route::get('/my-reports', [ReportController::class, 'employeeIndex'])->name('employee.reports');
});


// PERFORMANCE REVIEWS
use App\Http\Controllers\PerformanceController;
Route::middleware(['auth'])->group(function () {
    // 1. List
    Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');
    
    // 2. Create (Restricted to Supervisor/Admin in Controller)
    Route::get('/performance/create', [PerformanceController::class, 'create'])->name('performance.create');
    Route::post('/performance', [PerformanceController::class, 'store'])->name('performance.store');
    
    // 3. Show
    Route::get('/performance/{id}', [PerformanceController::class, 'show'])->name('performance.show');
});