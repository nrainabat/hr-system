<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,user',
        ]);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully');
    }

    public function create()
    {
    return view('users.create');
    }

    public function dashboard()
{
    // Get today's attendance record
    $todayAttendance = \App\Models\Attendance::where('user_id', \Illuminate\Support\Facades\Auth::id())
                        ->where('date', \Carbon\Carbon::today())
                        ->first();

    return view('employee.dashboard', compact('todayAttendance'));
}
}


