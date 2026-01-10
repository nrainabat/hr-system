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

        // RULE: Clock in before or at 9:00 AM is Present, otherwise Late
        $status = $now->format('H:i') > '09:00' ? 'Late' : 'Present';

        Attendance::create([
            'user_id' => $user_id,
            'date' => $today,
            'clock_in' => $now->format('H:i:s'),
            'status' => $status, // Set initial status based on punctuality
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
            
            // Calculate Total Duration in Minutes
            $totalMinutes = $clockInTime->diffInMinutes($clockOutTime);
            
            // Define Standard Work Hours (8 Hours = 480 Minutes)
            $standardWorkMinutes = 8 * 60;

            $status = $attendance->status; // Start with the existing status (Late/Present)
            $overtimeHours = null;
            $message = 'Clocked Out Successfully!';

            // === LOGIC RULES ===

            // CASE A: Worked LESS than 8 hours
            if ($totalMinutes < $standardWorkMinutes) {
                $status = 'Incomplete';
                $message = 'Clocked Out. Warning: Shift less than 8 hours.';
            }
            // CASE B: Worked 8 hours or MORE
            else {
                // Check for Overtime (Duration > 8 hours)
                if ($totalMinutes > $standardWorkMinutes) {
                    $otMinutes = $totalMinutes - $standardWorkMinutes;
                    
                    // Format Overtime: "1h 30m"
                    $hours = floor($otMinutes / 60);
                    $minutes = $otMinutes % 60;
                    $overtimeHours = ($hours > 0 ? $hours . 'h ' : '') . ($minutes > 0 ? $minutes . 'm' : '');

                    // Update Status logic:
                    // 1. If they were LATE, keep status as 'Late' (but record the OT).
                    // 2. If they were PRESENT (On Time), change status to 'Overtime'.
                    if ($status == 'Present') {
                        $status = 'Overtime';
                    }
                }
                // If exactly 8 hours, status remains 'Present' (or 'Late' if they were late)
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