<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Admin Dashboard
    public function admin()
    {
        abort_if(Auth::user()->role !== 'admin', 403);
        return view('admin.dashboard');
    }

    // Supervisor Dashboard
    public function supervisor()
    {
        abort_if(Auth::user()->role !== 'supervisor', 403);
        return view('supervisor.dashboard');
    }

    // Employee Dashboard (Main Logic)
    public function employee()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        // 1. Attendance - UPDATED LOGIC
        // We use 'latest()' to ensure we fetch the MOST RECENT attendance record for today.
        // This allows the system to see if the last action was 'Clock Out', 
        // so it can show the 'Clock In' button again for a new shift.
        $todayAttendance = Attendance::where('user_id', $userId)
                            ->where('date', $today)
                            ->latest() // <--- CRITICAL CHANGE: Gets the latest entry
                            ->first();

        // 2. Leave Statistics
        $totalLeaves = LeaveApplication::where('user_id', $userId)->count();
        $approvedCount = LeaveApplication::where('user_id', $userId)->where('status', 'approved')->count();
        $rejectedCount = LeaveApplication::where('user_id', $userId)->where('status', 'rejected')->count();
        $cancelledCount = LeaveApplication::where('user_id', $userId)->where('status', 'cancelled')->count();

        // 3. Recent Leave Applications (Last 5)
        $recentLeaves = LeaveApplication::where('user_id', $userId)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        return view('employee.dashboard', compact(
            'todayAttendance', 
            'totalLeaves', 
            'approvedCount', 
            'rejectedCount', 
            'cancelledCount', 
            'recentLeaves'
        ));
    }
}