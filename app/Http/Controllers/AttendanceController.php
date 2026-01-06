<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // === 1. CLOCK IN ===
    public function clockIn()
    {
        $user_id = Auth::id();
        $today = Carbon::today();
        $now = Carbon::now();

        // Check for existing session
        $activeSession = Attendance::where('user_id', $user_id)
                            ->where('date', $today)
                            ->first();

        if ($activeSession) {
            return redirect()->back()->with('error', 'You have already clocked in for today!');
        }

        // Determine Status based on 9:00 AM rule
        // If clock in is after 9:00 AM, status is 'Late'
        $status = $now->format('H:i') > '09:00' ? 'Late' : 'Present';

        Attendance::create([
            'user_id' => $user_id,
            'date' => $today,
            'clock_in' => $now->format('H:i:s'),
            'status' => $status, // Initial status
        ]);

        return redirect()->back()->with('success', 'Clocked In Successfully! Status: ' . $status);
    }

    // === 2. CLOCK OUT ===
    public function clockOut()
    {
        $user_id = Auth::id();
        $today = Carbon::today();
        $now = Carbon::now();

        // Find active session
        $attendance = Attendance::where('user_id', $user_id)
                            ->where('date', $today)
                            ->whereNull('clock_out')
                            ->first();

        if ($attendance) {
            $clockInTime = Carbon::parse($attendance->clock_in);
            $clockOutTime = $now;
            
            // Calculate Duration in Hours
            $duration = $clockInTime->diffInHours($clockOutTime);
            
            $status = $attendance->status; // Keep existing status (e.g., Late) by default
            $overtimeHours = null;
            $message = 'Clocked Out Successfully!';

            // LOGIC RULES:
            
            // 1. OVERTIME: Clock Out AFTER 5:00 PM (17:00)
            if ($clockOutTime->format('H:i') > '17:00') {
                $status = 'Overtime';
                // Calculate Overtime (Time past 17:00)
                $endOfShift = Carbon::parse($today->format('Y-m-d') . ' 17:00:00');
                $otDuration = $endOfShift->diffInMinutes($clockOutTime);
                
                // Format: "1h 30m"
                $hours = floor($otDuration / 60);
                $minutes = $otDuration % 60;
                $overtimeHours = ($hours > 0 ? $hours . 'h ' : '') . ($minutes > 0 ? $minutes . 'm' : '');
            } 
            // 2. HALF DAY / NOT COMPLETED: Clock Out BEFORE 5:00 PM (17:00)
            elseif ($clockOutTime->format('H:i') < '17:00') {
                // If working hours are roughly 4 hours or less (or significantly early leave)
                // The requirement says "half day will be if employee working for 4 hours"
                // And "not completed will display if user clock out before 5 pm"
                
                $status = 'Half Day';
                $message = 'Clocked Out. Status: Not Completed / Half Day';
            }
            
            // Update Record
            $attendance->update([
                'clock_out' => $now->format('H:i:s'),
                'status' => $status,
                'overtime_hours' => $overtimeHours
            ]);

            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'You are not clocked in!');
    }
    
    // === 3. EMPLOYEE HISTORY ===
    public function index()
    {
        $attendanceHistory = Attendance::where('user_id', Auth::id())
                                ->orderBy('date', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        return view('employee.attendance', compact('attendanceHistory'));
    }

    // === 4. ADMIN REPORT ===
    public function adminIndex()
    {
        $attendanceRecords = Attendance::with('user')
                                ->orderBy('date', 'desc')
                                ->orderBy('clock_in', 'desc')
                                ->get();

        return view('admin.attendance', compact('attendanceRecords'));
    }
}