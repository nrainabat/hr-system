<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    // 1. Show the form
    public function create()
    {
        // Fetch types from DB so the dropdown works
        $leaveTypes = LeaveType::all(); 
        
        return view('employee.leave', compact('leaveTypes'));
    }

    // 2. Store the data
    public function store(Request $request)
    {
        // A. Validate the Input
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', 
        ]);

        // B. Handle File Upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        // C. Save to Database
        LeaveApplication::create([
            'user_id'    => Auth::id(), 
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'reason'     => $request->reason,
            'attachment' => $attachmentPath,
            'status'     => 'pending',
        ]);

        // D. Redirect
        return redirect()->route('dashboard')->with('success', 'Leave application submitted successfully!');
    }

    // 3. Show History
    public function history()
    {
        $leaves = LeaveApplication::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('employee.leaveHistory', compact('leaves'));
    }

    
}