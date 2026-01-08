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
    // ... (Admin, Supervisor, Employee, Intern methods remain unchanged)
    public function admin()
    {
        // ... (Keep existing Admin logic)
        $totalUsers = User::count();
        $totalEmployees = User::where('role', 'employee')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();
        $totalInterns = User::where('role', 'intern')->count();

        $today = Carbon::today();
        $presentCount = Attendance::whereDate('date', $today)->whereIn('status', ['Present', 'Overtime'])->count();
        $lateCount = Attendance::whereDate('date', $today)->whereIn('status', ['Late', 'Half Day'])->count();
        $totalRecordsToday = Attendance::whereDate('date', $today)->count();
        $absentCount = $totalUsers - $totalRecordsToday;
        $attendancePercentage = $totalUsers > 0 ? round(($totalRecordsToday / $totalUsers) * 100) : 0;

        $pendingLeaveRequests = LeaveApplication::with('user')->where('status', 'pending')->orderBy('created_at', 'desc')->get();

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

    public function supervisor()
    {
        if(Auth::user()->role !== 'supervisor') abort(403);
        return $this->getDashboardData('employee.dashboard', false, true);
    }

    public function employee()
    {
        return $this->getDashboardData('employee.dashboard', false, false);
    }

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

        // 1. Fetch Assigned Supervisor
        $assignedSupervisor = $user->supervisor; 

        // 2. Common Data
        $todayAttendance = Attendance::where('user_id', $userId)->where('date', $today)->latest()->first();
        $totalLeaves = LeaveApplication::where('user_id', $userId)->count();
        $approvedCount = LeaveApplication::where('user_id', $userId)->where('status', 'approved')->count();
        $rejectedCount = LeaveApplication::where('user_id', $userId)->where('status', 'rejected')->count();
        $cancelledCount = LeaveApplication::where('user_id', $userId)->where('status', 'cancelled')->count();
        
        $announcements = Announcement::latest()->take(3)->get();

        // 3. Role Specific Data
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
        $myTeam = collect(); // Default empty collection

        if ($isIntern) {
            $recentDocuments = InternDocument::where('user_id', $userId)->latest()->take(5)->get();
        } 
        elseif ($isSupervisor) {
            // A. Identify "My Team" (Strictly by supervisor_id assignment)
            $myTeam = User::where('supervisor_id', $userId)->get();
            $totalTeam = $myTeam->count();
            $myInternsCount = $myTeam->where('role', 'intern')->count();

            // B. Fetch Team Attendance for Today to map statuses
            $teamIds = $myTeam->pluck('id');
            $teamAttendanceRecords = Attendance::whereIn('user_id', $teamIds)
                                        ->where('date', $today)
                                        ->get()
                                        ->keyBy('user_id');

            // C. Iterate members to attach status and calculate counts
            $teamPresent = 0;
            $teamLate = 0;

            $myTeam->transform(function($member) use ($teamAttendanceRecords, &$teamPresent, &$teamLate) {
                $att = $teamAttendanceRecords->get($member->id);
                
                if ($att) {
                    // Member has a record
                    $member->attendance_status = $att->status; // 'Present', 'Late', etc.
                    $member->clock_in_time = $att->clock_in;

                    if (in_array($att->status, ['Present', 'Overtime'])) {
                        $teamPresent++;
                    } elseif (in_array($att->status, ['Late', 'Half Day'])) {
                        $teamLate++;
                    }
                } else {
                    // Member has NO record
                    $member->attendance_status = 'Absent';
                    $member->clock_in_time = null;
                }
                return $member;
            });
            
            // Calculate Absent
            $recordedCount = $teamPresent + $teamLate; 
            $teamAbsent = $totalTeam - $recordedCount;
            if($teamAbsent < 0) $teamAbsent = 0;

            // D. Fetch Pending Documents
            $pendingInternDocuments = InternDocument::with('user')
                                        ->whereHas('user', function($query) use ($userId) {
                                            $query->where('supervisor_id', $userId);
                                        })
                                        ->where('status', 'pending')
                                        ->orderBy('created_at', 'desc')
                                        ->get();

            $pendingReviewCount = $pendingInternDocuments->count();
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
            'myTeam', 'assignedSupervisor'
        ));
    }
}