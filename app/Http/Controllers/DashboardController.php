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
        abort_if(Auth::user()->role !== 'admin', 403);

        // 1. User Counters
        $totalUsers = User::count(); 
        $totalInterns = User::where('role', 'intern')->count();
        $totalEmployees = User::where('role', 'employee')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();

        // 2. Attendance Counters (Today)
        $today = Carbon::today();
        
        // Count 'Present' (Includes normal Present + Overtime)
        $presentCount = Attendance::whereDate('date', $today)
                            ->whereIn('status', ['Present', 'Overtime'])
                            ->count();
        
        // Count 'Late' (Includes Late + Half Day/Incomplete)
        $lateCount = Attendance::whereDate('date', $today)
                        ->whereIn('status', ['Late', 'Half Day'])
                        ->count();
        
        // Count 'Absent' (Total Users - Records Found)
        // Note: This logic assumes if there is no record, they are absent.
        $totalRecordsToday = Attendance::whereDate('date', $today)->count();
        $absentCount = $totalUsers - $totalRecordsToday;

        $attendancePercentage = $totalUsers > 0 ? round(($totalRecordsToday / $totalUsers) * 100) : 0;

        // 3. Department Data (For Chart)
        $departmentData = User::select('department', DB::raw('count(*) as total'))
                              ->whereNotNull('department')
                              ->groupBy('department')
                              ->get();
        
        $deptLabels = $departmentData->pluck('department');
        $deptCounts = $departmentData->pluck('total');

        // 4. Pending Leave Requests (NEW LIST)
        $pendingLeaveRequests = LeaveApplication::with('user')
                                    ->where('status', 'pending')
                                    ->latest()
                                    ->take(5)
                                    ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalInterns', 'totalEmployees', 'totalSupervisors',
            'presentCount', 'lateCount', 'absentCount', 'attendancePercentage',
            'deptLabels', 'deptCounts',
            'pendingLeaveRequests' // <--- Passed to view
        ));
    }
    
    // Supervisor Dashboard
    public function supervisor()
    {
        abort_if(Auth::user()->role !== 'supervisor', 403);
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
        abort_if(Auth::user()->role !== 'intern', 403);
        return $this->getDashboardData('employee.dashboard', true, false);
    }

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
        
        // Supervisor Stats
        $myInternsCount = 0;
        $pendingReviewCount = 0;
        $signedCount = 0;
        
        // Chart Data Variables
        $teamPresent = 0;
        $teamLate = 0;
        $teamAbsent = 0;
        $totalTeam = 0;

        if ($isIntern) {
            $recentDocuments = InternDocument::where('user_id', $userId)->latest()->take(5)->get();
        } 
        elseif ($isSupervisor) {
            // A. Fetch Pending Documents
            $pendingInternDocuments = InternDocument::with('user')
                                        ->where('status', 'pending')
                                        ->orderBy('created_at', 'desc')
                                        ->get();

            // B. Identify "My Team" (Interns & Employees in same Department)
            // You can also use 'supervisor_id' if you have set that up relationally.
            $myTeam = User::where('department', $user->department)
                          ->where('id', '!=', $user->id) // Exclude self
                          ->whereIn('role', ['employee', 'intern'])
                          ->get();

            $myInternsCount = $myTeam->where('role', 'intern')->count();
            $totalTeam = $myTeam->count();

            // C. Calculate Team Attendance for Chart
            // Get IDs of team members
            $teamIds = $myTeam->pluck('id');
            
            // Fetch today's attendance for these users
            $teamAttendance = Attendance::whereIn('user_id', $teamIds)
                                        ->where('date', $today)
                                        ->get();

            $teamPresent = $teamAttendance->where('status', 'Present')->count();
            // Group 'Late' and 'Half Day' as Late for the chart, or separate them
            $teamLate = $teamAttendance->whereIn('status', ['Late', 'Half Day'])->count();
            
            // Absent = Total Team - (Present + Late)
            // Note: This assumes anyone without a record is Absent
            $recordedCount = $teamPresent + $teamLate; 
            $teamAbsent = $totalTeam - $recordedCount;

            // D. Other Supervisor Counts
            $pendingReviewCount = InternDocument::where('status', 'pending')->count();
            $signedCount = InternDocument::where('status', 'signed')->count();
        } 
        else {
            $recentLeaves = LeaveApplication::where('user_id', $userId)->latest()->take(5)->get();
        }

        return view($viewName, compact(
            'todayAttendance', 
            'totalLeaves', 
            'approvedCount', 
            'rejectedCount', 
            'cancelledCount', 
            'recentLeaves',
            'recentDocuments',
            'pendingInternDocuments',
            'announcements',
            // Supervisor Stats
            'myInternsCount',
            'pendingReviewCount',
            'signedCount',
            // Chart Data
            'teamPresent',
            'teamLate',
            'teamAbsent',
            'totalTeam'
        ));
    }
}