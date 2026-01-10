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
use App\Models\LeaveCount;
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

        // 1. Common Data
        $todayAttendance = Attendance::where('user_id', $userId)->where('date', $today)->latest()->first();
        $totalLeaves = LeaveApplication::where('user_id', $userId)->count();
        $approvedCount = LeaveApplication::where('user_id', $userId)->where('status', 'approved')->count();
        $rejectedCount = LeaveApplication::where('user_id', $userId)->where('status', 'rejected')->count();
        $cancelledCount = LeaveApplication::where('user_id', $userId)->where('status', 'cancelled')->count();
        
        $announcements = Announcement::latest()->take(3)->get();

        // 2. NEW: Leave Balance Calculation
        $balances = LeaveCount::where('user_id', $userId)
                        ->where('year', date('Y'))
                        ->get();

        foreach ($balances as $balance) {
            // Calculate days used for this specific leave type
            $approvedLeaves = LeaveApplication::where('user_id', $userId)
                ->where('leave_type', $balance->leave_type)
                ->where('status', 'approved')
                ->whereYear('start_date', $balance->year)
                ->get();

            $daysUsed = 0;
            foreach ($approvedLeaves as $leave) {
                $start = Carbon::parse($leave->start_date);
                $end = Carbon::parse($leave->end_date);
                $daysUsed += $start->diffInDays($end) + 1;
            }

            $balance->days_used = $daysUsed;
            $balance->remaining = $balance->balance - $daysUsed;
        }

        // 3. Role Specific Data (Keep existing logic)
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
        $myTeam = collect(); 

        if ($isIntern) {
            $recentDocuments = InternDocument::where('user_id', $userId)->latest()->take(5)->get();
        } 
        elseif ($isSupervisor) {
            $myTeam = User::where('supervisor_id', $userId)->get();
            $totalTeam = $myTeam->count();
            $myInternsCount = $myTeam->where('role', 'intern')->count();

            $teamIds = $myTeam->pluck('id');
            $teamAttendanceRecords = Attendance::whereIn('user_id', $teamIds)
                                        ->where('date', $today)
                                        ->get()
                                        ->keyBy('user_id');

            $myTeam->transform(function($member) use ($teamAttendanceRecords, &$teamPresent, &$teamLate) {
                $att = $teamAttendanceRecords->get($member->id);
                if ($att) {
                    $member->attendance_status = $att->status;
                    $member->clock_in_time = $att->clock_in;
                    if (in_array($att->status, ['Present', 'Overtime'])) {
                        $teamPresent++;
                    } elseif (in_array($att->status, ['Late', 'Half Day'])) {
                        $teamLate++;
                    }
                } else {
                    $member->attendance_status = 'Absent';
                    $member->clock_in_time = null;
                }
                return $member;
            });
            
            $recordedCount = $teamPresent + $teamLate; 
            $teamAbsent = $totalTeam - $recordedCount;
            if($teamAbsent < 0) $teamAbsent = 0;

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
            'teamPresent', 'teamLate', 'teamAbsent', 'totalTeam', 'myTeam',
            'balances' 
        ));
    }
}