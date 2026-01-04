<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\User;

class AdminLeaveController extends Controller
{
    // === 1. MANAGE LEAVE TYPES ===
    public function indexTypes()
    {
        $types = LeaveType::all();
        return view('admin.leave.types', compact('types'));
    }

    public function storeType(Request $request)
    {
        $request->validate(['name' => 'required|unique:leave_types,name', 'days_allowed' => 'required|integer']);
        LeaveType::create($request->all());
        return back()->with('success', 'Leave Type added successfully.');
    }

    public function destroyType($id)
    {
        LeaveType::findOrFail($id)->delete();
        return back()->with('success', 'Leave Type removed.');
    }

    // === 2. MANAGE LEAVE REQUESTS ===
    public function indexRequests()
    {
        // Fetch pending requests with user details
        $requests = LeaveApplication::with('user')->where('status', 'pending')->latest()->get();
        return view('admin.leave.requests', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $leave = LeaveApplication::findOrFail($id);
        $leave->status = $request->status;
        $leave->save();

        return back()->with('success', 'Leave request ' . $request->status . '.');
    }

    // === 3. CALENDAR VIEW ===
    public function calendar()
    {
        return view('admin.leave.calendar');
    }

    // API Endpoint for Calendar Events
    public function getCalendarEvents()
    {
        $leaves = LeaveApplication::with('user')
                    ->where('status', 'approved') // Only show approved leaves
                    ->get();

        $events = $leaves->map(function ($leave) {
            return [
                'title' => $leave->user->name . ' (' . $leave->leave_type . ')',
                'start' => $leave->start_date,
                'end'   => date('Y-m-d', strtotime($leave->end_date . ' +1 day')), // FullCalendar needs +1 day for inclusive end
                'color' => '#123456', // Custom color
                'allDay' => true,
            ];
        });

        return response()->json($events);
    }
}