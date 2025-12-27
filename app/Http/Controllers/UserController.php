<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <--- Import DB Facade

class UserController extends Controller
{
    // 1. Show the Create User Form
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // FETCH DEPARTMENTS FROM DATABASE
        $departments = DB::table('departments')->orderBy('name')->get();

        // Pass the variable to the view using 'compact'
        return view('users.create', compact('departments'));
    }

    // 2. Store the New User (No changes needed here if you save the name)
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:users,username',
            'email'      => 'required|email|max:255|unique:users,email',
            'password'   => 'required|string|min:6',
            'role'       => 'required|in:admin,supervisor,employee,intern',
            'department' => 'nullable|string|max:100', // Ensure this matches input name
            'position'   => 'nullable|string|max:100',
        ]);

        User::create([
            'name'       => $request->name,
            'username'   => $request->username,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'department' => $request->department,
            'position'   => $request->position,
            'status'     => 'active',
        ]);

        return redirect()->route('admin.users.create')
            ->with('success', 'User created successfully!');
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