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
        $user_id = Auth::user()->id; // Fixed: Get ID, not username
        $today = Carbon::today();

        // Prevent double clock-in
        $attendance = Attendance::where('user_id', $user_id)->where('date', $today)->first();
        if ($attendance) {
            return redirect()->back()->with('error', 'Already clocked in.');
        }

        Attendance::create([
            'user_id' => $user_id,
            'date' => $today,
            'clock_in' => Carbon::now()->format('H:i:s'),
            'status' => 'Present', // Set initial status
        ]);

        return redirect()->back()->with('success', 'Clocked In Successfully!');
    }

    // CLOCK OUT
    public function clockOut()
    {
        $user_id = Auth::user()->id;
        $today = Carbon::today();

        // Find today's record
        $attendance = Attendance::where('user_id', $user_id)->where('date', $today)->first();

        // Update if clock_in exists but clock_out doesn't
        if ($attendance && is_null($attendance->clock_out)) {
            $attendance->update([
                'clock_out' => Carbon::now()->format('H:i:s'),
                'status' => 'Completed', // Optional: Update status to Completed
            ]);
            return redirect()->back()->with('success', 'Clocked Out Successfully!');
        }

        return redirect()->back()->with('error', 'Cannot clock out yet.');
    }
}