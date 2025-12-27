<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // CLOCK IN
    public function clockIn()
    {
        $user_id = Auth::id();
        $today = Carbon::today();

        // Check if there is currently an ACTIVE session (Clocked In but NOT Clocked Out)
        $activeSession = Attendance::where('user_id', $user_id)
                            ->where('date', $today)
                            ->whereNull('clock_out') // Only looks for open sessions
                            ->first();

        // If an active session exists, prevent another Clock In
        if ($activeSession) {
            return redirect()->back()->with('error', 'You are already clocked in!');
        }

        // Create a NEW record (This allows multiple shifts per day)
        Attendance::create([
            'user_id' => $user_id,
            'date' => $today,
            'clock_in' => Carbon::now()->format('H:i:s'),
            'status' => 'Present',
        ]);

        return redirect()->back()->with('success', 'Clocked In Successfully!');
    }

    // CLOCK OUT
    public function clockOut()
    {
        $user_id = Auth::id();
        $today = Carbon::today();

        // Find the record that is currently "Working" (Null clock_out)
        $activeSession = Attendance::where('user_id', $user_id)
                            ->where('date', $today)
                            ->whereNull('clock_out')
                            ->first();

        if ($activeSession) {
            $activeSession->update([
                'clock_out' => Carbon::now()->format('H:i:s'),
                'status' => 'Completed',
            ]);
            return redirect()->back()->with('success', 'Clocked Out Successfully!');
        }

        return redirect()->back()->with('error', 'You are not clocked in!');
    }
    
    // VIEW HISTORY
    public function index()
    {
        $attendanceHistory = Attendance::where('user_id', Auth::id())
                                ->orderBy('date', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        return view('employee.attendance', compact('attendanceHistory'));
    }
}