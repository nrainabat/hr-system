<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\User; 
use App\Models\InternDocument;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Admin Dashboard
    public function admin()
    {
        abort_if(Auth::user()->role !== 'admin', 403);
        $totalUsers = User::count(); 
        $totalInterns = User::where('role', 'intern')->count();
        $totalEmployees = User::where('role', 'employee')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();

        return view('admin.dashboard', compact('totalUsers', 'totalInterns', 'totalEmployees', 'totalSupervisors'));
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

    // SHARED HELPER FUNCTION
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

        // 2. Role Specific Data
        $recentLeaves = [];
        $recentDocuments = [];
        $pendingInternDocuments = []; 
        
        // Supervisor Stats
        $myInternsCount = 0;
        $pendingReviewCount = 0;
        $signedCount = 0;

        if ($isIntern) {
            $recentDocuments = InternDocument::where('user_id', $userId)->latest()->take(5)->get();
        } 
        elseif ($isSupervisor) {
            // A. Fetch Pending Documents (All or Department specific)
            $pendingInternDocuments = InternDocument::with('user')
                                        ->where('status', 'pending')
                                        ->orderBy('created_at', 'desc')
                                        ->get();

            // B. Possibility 1: Count Interns in the same Department
            $myInternsCount = User::where('role', 'intern')
                                ->where('department', $user->department)
                                ->count();

            // C. Possibility 2: Count Pending Reviews
            $pendingReviewCount = InternDocument::where('status', 'pending')->count();

            // D. Possibility 3: Count Signed Documents (Productivity)
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
            // Pass Supervisor Stats
            'myInternsCount',
            'pendingReviewCount',
            'signedCount'
        ));
    }
}