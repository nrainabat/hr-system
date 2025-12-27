<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\InternDocument; // Import this
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Admin and Supervisor functions remain unchanged...
    public function admin() { /* ... */ return view('admin.dashboard'); }
    public function supervisor() { /* ... */ return view('supervisor.dashboard'); }

    // Unified Dashboard Logic
    public function employee()
    {
        $user = Auth::user();
        $userId = $user->id;
        $today = Carbon::today();

        // 1. Attendance (Common for both)
        $todayAttendance = Attendance::where('user_id', $userId)
                            ->where('date', $today)
                            ->latest()
                            ->first();

        // 2. Leave Statistics (Common for both)
        $totalLeaves = LeaveApplication::where('user_id', $userId)->count();
        $approvedCount = LeaveApplication::where('user_id', $userId)->where('status', 'approved')->count();
        $rejectedCount = LeaveApplication::where('user_id', $userId)->where('status', 'rejected')->count();
        $cancelledCount = LeaveApplication::where('user_id', $userId)->where('status', 'cancelled')->count();

        // 3. Conditional Data (Leaves vs Documents)
        $recentLeaves = [];
        $recentDocuments = [];

        if ($user->role === 'intern') {
            // Fetch Documents for Interns
            $recentDocuments = InternDocument::where('user_id', $userId)
                                ->latest()
                                ->take(5)
                                ->get();
        } else {
            // Fetch Leaves for Employees
            $recentLeaves = LeaveApplication::where('user_id', $userId)
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
        }

        // Return the SINGLE 'employee.dashboard' view
        return view('employee.dashboard', compact(
            'todayAttendance', 
            'totalLeaves', 
            'approvedCount', 
            'rejectedCount', 
            'cancelledCount', 
            'recentLeaves',
            'recentDocuments'
        ));
    }
}