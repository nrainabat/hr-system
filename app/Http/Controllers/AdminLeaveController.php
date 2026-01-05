<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\LeaveCount;
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

    public function indexBalances()
    {
        $users = User::whereIn('role', ['employee', 'supervisor', 'intern'])->orderBy('name')->get();
        $leaveTypes = LeaveType::all();

        // Fetch balances with user details
        $balances = LeaveCount::with('user')->orderBy('user_id')->get();

        // Calculate Used & Remaining dynamically
        foreach ($balances as $balance) {
            // Get approved leaves for this user & type in the current year
            $approvedLeaves = LeaveApplication::where('user_id', $balance->user_id)
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

            // Attach temporary data to the object for the view
            $balance->days_used = $daysUsed;
            $balance->remaining = $balance->balance - $daysUsed;
        }

        return view('admin.leave.counts', compact('users', 'leaveTypes', 'balances'));
    }

    public function storeBalance(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type' => 'required|string',
            'balance' => 'required|integer|min:0',
        ]);

        LeaveCount::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'leave_type' => $request->leave_type,
                'year' => date('Y')
            ],
            [
                'balance' => $request->balance // This acts as the "Total Allocated"
            ]
        );

        return back()->with('success', 'Leave entitlement updated successfully!');
    }
}