<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\User; 
use App\Models\InternDocument;
use App\Models\Announcement;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Admin Dashboard
    public function admin()
    {
        $totalUsers = User::count();
        $totalEmployees = User::where('role', 'employee')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();
        $totalInterns = User::where('role', 'intern')->count();

        // Attendance Counters
        $today = Carbon::today();
        $presentCount = Attendance::whereDate('date', $today)->whereIn('status', ['Present', 'Overtime'])->count();
        $lateCount = Attendance::whereDate('date', $today)->whereIn('status', ['Late', 'Half Day'])->count();
        $totalRecordsToday = Attendance::whereDate('date', $today)->count();
        $absentCount = $totalUsers - $totalRecordsToday;
        $attendancePercentage = $totalUsers > 0 ? round(($totalRecordsToday / $totalUsers) * 100) : 0;

        // Leave Requests
        $pendingLeaveRequests = LeaveApplication::with('user')->where('status', 'pending')->orderBy('created_at', 'desc')->get();

        // Department Data for Chart
        $departments = User::select('department', DB::raw('count(*) as total'))
                        ->whereNotNull('department')
                        ->groupBy('department')
                        ->get();
        $deptLabels = $departments->pluck('department');
        $deptCounts = $departments->pluck('total');

        $announcements = Announcement::latest()->take(3)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalEmployees', 'totalSupervisors', 'totalInterns',
            'presentCount', 'lateCount', 'absentCount', 'attendancePercentage',
            'pendingLeaveRequests', 'deptLabels', 'deptCounts', 'announcements'
        ));
    }
    
    // Supervisor Dashboard
    public function supervisor()
    {
        if(Auth::user()->role !== 'supervisor') abort(403);
        return $this->getDashboardData('employee.dashboard', false, true);
    }

    // Employee Dashboard
    public function employee()
    {
        return $this->getDashboardData('employee.dashboard', false, false);
    }

    // Intern Dashboard
    public function intern()
    {
        if(Auth::user()->role !== 'intern') abort(403);
        return $this->getDashboardData('employee.dashboard', true, false);
    }

    // === HELPER FUNCTION ===
    private function getDashboardData($viewName, $isIntern = false, $isSupervisor = false)
    {
        $user = Auth::user();
        $userId = $user->id;
        $today = Carbon::today();

        // 1. Common Data
        $todayAttendance = Attendance::where('user_id', $userId)->where('date', $today)->latest()->first();
        $totalLeaves = LeaveApplication::where('user_id', $userId)->count();
        $approvedCount = LeaveApplication::where('user_id', $userId)->where('status', 'approved')->count();
        $rejectedCount = LeaveApplication::where('user_id', $userId)->where('status', 'rejected')->count();
        $cancelledCount = LeaveApplication::where('user_id', $userId)->where('status', 'cancelled')->count();
        
        $announcements = Announcement::latest()->take(3)->get();

        // 2. Role Specific Data
        $recentLeaves = [];
        $recentDocuments = [];
        $pendingInternDocuments = []; 
        
        $myInternsCount = 0;
        $pendingReviewCount = 0;
        $signedCount = 0;
        
        $teamPresent = 0;
        $teamLate = 0;
        $teamAbsent = 0;
        $totalTeam = 0;
        $myTeam = []; 

        if ($isIntern) {
            $recentDocuments = InternDocument::where('user_id', $userId)->latest()->take(5)->get();
        } 
        elseif ($isSupervisor) {
            // === MODIFIED SECTION START ===

            // B. Identify "My Team" (Strictly by supervisor_id assignment)
            // This fetches users who have THIS user as their supervisor
            $myTeam = User::where('supervisor_id', $userId)->get();

            $myInternsCount = $myTeam->where('role', 'intern')->count();
            $totalTeam = $myTeam->count();

            // A. Fetch Pending Documents (Only from assigned team members)
            $pendingInternDocuments = InternDocument::with('user')
                                        ->whereHas('user', function($query) use ($userId) {
                                            // Filter: User's supervisor must be Me
                                            $query->where('supervisor_id', $userId);
                                        })
                                        ->where('status', 'pending')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
            // === MODIFIED SECTION END ===

            // C. Calculate Team Attendance
            $teamIds = $myTeam->pluck('id');
            $teamAttendance = Attendance::whereIn('user_id', $teamIds)
                                        ->where('date', $today)
                                        ->get();

            $teamPresent = $teamAttendance->whereIn('status', ['Present', 'Overtime'])->count();
            $teamLate = $teamAttendance->whereIn('status', ['Late', 'Half Day'])->count();
            
            $recordedCount = $teamPresent + $teamLate; 
            $teamAbsent = $totalTeam - $recordedCount;
            if($teamAbsent < 0) $teamAbsent = 0;

            // D. Other Supervisor Counts
            $pendingReviewCount = $pendingInternDocuments->count(); // Updated to count the filtered collection
            $signedCount = InternDocument::whereHas('user', function($q) use ($userId){
                                $q->where('supervisor_id', $userId);
                           })->where('status', 'signed')->count();
        } 
        else {
            $recentLeaves = LeaveApplication::where('user_id', $userId)->latest()->take(5)->get();
        }

        return view($viewName, compact(
            'todayAttendance', 'totalLeaves', 'approvedCount', 'rejectedCount', 'cancelledCount', 
            'recentLeaves', 'recentDocuments', 'pendingInternDocuments', 'announcements',
            'myInternsCount', 'pendingReviewCount', 'signedCount',
            'teamPresent', 'teamLate', 'teamAbsent', 'totalTeam', 
            'myTeam'
        ));
    }
}