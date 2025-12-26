<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\LeaveApplication; // Make sure you have this Model created
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function admin()
    {
        abort_if(Auth::user()->role !== 'admin', 403);
        return view('admin.dashboard');
    }

    public function supervisor()
    {
        abort_if(Auth::user()->role !== 'supervisor', 403);
        return view('supervisor.dashboard');
    }

    public function employee()
{
    $userId = Auth::id();
    $today = Carbon::today();

    // 1. Attendance
    $todayAttendance = Attendance::where('user_id', $userId)
                        ->where('date', $today)
                        ->first();

    // 2. Leave Stats (Add Total Count here)
    $totalLeaves = LeaveApplication::where('user_id', $userId)->count(); // <--- NEW
    $approvedCount = LeaveApplication::where('user_id', $userId)->where('status', 'approved')->count();
    $rejectedCount = LeaveApplication::where('user_id', $userId)->where('status', 'rejected')->count();
    $cancelledCount = LeaveApplication::where('user_id', $userId)->where('status', 'cancelled')->count();

    // 3. Recent Leaves
    $recentLeaves = LeaveApplication::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();

    return view('employee.dashboard', compact(
        'todayAttendance', 
        'totalLeaves', // <--- Pass this variable
        'approvedCount', 
        'rejectedCount', 
        'cancelledCount', 
        'recentLeaves'
    ));
}

    public function dashboard()
{
    // Get today's attendance record
    $todayAttendance = \App\Models\Attendance::where('user_id', \Illuminate\Support\Facades\Auth::id())
                        ->where('date', \Carbon\Carbon::today())
                        ->first();

    return view('employee.dashboard', compact('todayAttendance'));
}
}
