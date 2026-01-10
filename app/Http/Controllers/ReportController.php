<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\LeaveType;

class ReportController extends Controller
{
    // =========================================================================
    // ADMIN: EXECUTIVE SUMMARY & ORGANIZATIONAL HEALTH
    // =========================================================================
    public function adminIndex()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $today = Carbon::today()->format('Y-m-d');

        // 1. WORKFORCE DEMOGRAPHICS
        $totalEmployees = User::where('role', '!=', 'admin')->where('status', 'active')->count();
        $genderDist = User::where('role', '!=', 'admin')
            ->select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->pluck('total', 'gender');
        
        $deptDist = User::where('role', '!=', 'admin')
            ->select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->pluck('total', 'department');

        // 2. ATTENDANCE ANALYTICS (TODAY)
        $attendanceToday = Attendance::where('date', $today)->get();
        $presentCount = $attendanceToday->where('status', 'Present')->count();
        // Assuming 'Late' is calculated if clock_in > 09:00:00 (Adjust time as needed)
        $lateCount = $attendanceToday->filter(function($record) {
            return $record->clock_in > '09:00:00'; 
        })->count();
        $absentCount = $totalEmployees - $presentCount;
        $attendanceRate = $totalEmployees > 0 ? round(($presentCount / $totalEmployees) * 100, 1) : 0;

        // 3. OVERTIME ANALYTICS (THIS MONTH)
        $totalOvertimeHours = Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('overtime_hours');

        // 4. LEAVE UTILIZATION
        $leaveStats = LeaveApplication::whereYear('start_date', $currentYear)
            ->select('leave_type', DB::raw('count(*) as total'))
            ->where('status', 'Approved')
            ->groupBy('leave_type')
            ->pluck('total', 'leave_type');

        $pendingLeaves = LeaveApplication::where('status', 'Pending')->count();

        // 5. MONTHLY TREND (Attendance count per day for the last 7 days)
        $dates = collect();
        $attendanceTrend = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates->push(Carbon::now()->subDays($i)->format('d M'));
            $attendanceTrend->push(Attendance::where('date', $date)->count());
        }

        return view('admin.reports', compact(
            'totalEmployees', 'genderDist', 'deptDist', 
            'presentCount', 'lateCount', 'absentCount', 'attendanceRate',
            'totalOvertimeHours', 'leaveStats', 'pendingLeaves',
            'dates', 'attendanceTrend'
        ));
    }

    // =========================================================================
    // SUPERVISOR: TEAM PERFORMANCE & OPERATIONAL READINESS
    // =========================================================================
    public function supervisorIndex()
    {
        $supervisorId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $today = Carbon::today()->format('Y-m-d');

        // Get Subordinates
        $myTeam = User::where('supervisor_id', $supervisorId)->get();
        $teamIds = $myTeam->pluck('id');

        // 1. DAILY TEAM STATUS
        $teamAttendanceToday = Attendance::whereIn('user_id', $teamIds)->where('date', $today)->get();
        $present = $teamAttendanceToday->count();
        $absent = $myTeam->count() - $present;
        $late = $teamAttendanceToday->filter(function($att) { return $att->clock_in > '09:00:00'; })->count();

        // 2. UPCOMING ABSENCES (Next 7 Days)
        $upcomingLeaves = LeaveApplication::whereIn('user_id', $teamIds)
            ->where('status', 'Approved')
            ->where('start_date', '>=', $today)
            ->where('start_date', '<=', Carbon::now()->addDays(7))
            ->with('user')
            ->get();

        // 3. PERFORMANCE METRICS (This Month)
        $teamStats = $myTeam->map(function($user) use ($currentMonth) {
            $attendance = Attendance::where('user_id', $user->id)->whereMonth('date', $currentMonth)->get();
            return [
                'name' => $user->name,
                'position' => $user->position,
                'days_worked' => $attendance->count(),
                'overtime' => $attendance->sum('overtime_hours'),
                'punctuality_score' => $attendance->count() > 0 
                    ? round(($attendance->where('clock_in', '<=', '09:00:00')->count() / $attendance->count()) * 100) . '%' 
                    : 'N/A'
            ];
        });

        return view('supervisor.reports', compact('myTeam', 'present', 'absent', 'late', 'upcomingLeaves', 'teamStats'));
    }

    // =========================================================================
    // EMPLOYEE: PERSONAL PRODUCTIVITY & RECORDS
    // =========================================================================
    public function employeeIndex()
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. WORK SUMMARY
        $myAttendance = Attendance::where('user_id', $userId)
            ->whereMonth('date', $currentMonth)
            ->get();

        $daysWorked = $myAttendance->count();
        $totalOvertime = $myAttendance->sum('overtime_hours');
        
        // Calculate Average Work Hours (Estimate)
        $totalHours = 0;
        foreach($myAttendance as $att) {
            if($att->clock_in && $att->clock_out) {
                $start = Carbon::parse($att->clock_in);
                $end = Carbon::parse($att->clock_out);
                $totalHours += $end->diffInHours($start);
            }
        }
        $avgHours = $daysWorked > 0 ? round($totalHours / $daysWorked, 1) : 0;

        // 2. LEAVE HISTORY BY TYPE
        $leavesTaken = LeaveApplication::where('user_id', $userId)
            ->where('status', 'Approved')
            ->whereYear('start_date', $currentYear)
            ->select('leave_type', DB::raw('count(*) as count'))
            ->groupBy('leave_type')
            ->get();

        // 3. RECENT ACTIVITY (Last 5 records)
        $recentAttendance = Attendance::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return view('employee.reports', compact('daysWorked', 'totalOvertime', 'avgHours', 'leavesTaken', 'recentAttendance'));
    }
}