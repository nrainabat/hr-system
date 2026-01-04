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
        $positions = DB::table('job_positions')->orderBy('title')->get();

        return view('users.create', compact('departments', 'positions'));
    }

    // 2. Store the New User (No changes needed here if you save the name)
    public function store(Request $request)
    {
    // ... (auth check)

    $request->validate([
        'name'         => 'required|string|max:255',
        'username'     => 'required|string|max:255|unique:users,username',
        'email'        => 'required|email|max:255|unique:users,email',
        'password'     => 'required|string|min:6',
        'role'         => 'required|in:admin,supervisor,employee,intern',
        'department'   => 'nullable|string|max:100',
        'position'     => 'nullable|string|max:100',
        'phone_number' => 'nullable|string|max:20',
        'gender'       => 'nullable|in:Male,Female',
        'about'        => 'nullable|string|max:500',
        'address'      => 'nullable|string|max:500', // <--- Validation
    ]);

    User::create([
        'name'         => $request->name,
        'username'     => $request->username,
        'email'        => $request->email,
        'password'     => Hash::make($request->password),
        'role'         => $request->role,
        'department'   => $request->department,
        'position'     => $request->position,
        'status'       => 'active',
        'phone_number' => $request->phone_number,
        'gender'       => $request->gender,
        'about'        => $request->about,
        'address'      => $request->address, // <--- Save
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

    // 3. Show the Edit Form
    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);
        $departments = DB::table('departments')->orderBy('name')->get();
        $positions = DB::table('job_positions')->orderBy('title')->get();

        // You can reuse the 'users.create' view if you modify it to support editing,
        // or create a new 'users.edit' view.
        // For simplicity, let's assume you create a view resources/views/users/edit.blade.php
        return view('users.edit', compact('user', 'departments', 'positions'));
    }

    // 4. Update the User
    public function update(Request $request, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            abort(403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $id, // Ignore current user
            'email'        => 'required|email|max:255|unique:users,email,' . $id,    // Ignore current user
            'role'         => 'required|in:admin,supervisor,employee,intern',
            'department'   => 'nullable|string',
            'position'     => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'gender'       => 'nullable|in:Male,Female',
            'about'        => 'nullable|string|max:500',
            'address'      => 'nullable|string|max:500',
            'password'     => 'nullable|string|min:6', // Optional password
        ]);

        $data = [
            'name'         => $request->name,
            'username'     => $request->username,
            'email'        => $request->email,
            'role'         => $request->role,
            'department'   => $request->department,
            'position'     => $request->position,
            'phone_number' => $request->phone_number,
            'gender'       => $request->gender,
            'about'        => $request->about,
            'address'      => $request->address,
        ];

        // Only update password if a new one is provided
        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.directory')->with('success', 'User updated successfully!');
    }

    public function updatePassword(Request $request, $id)
    {
        // 1. Authorization Check
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            abort(403);
        }

        // 2. Validation
        $request->validate([
            'password' => 'required|string|min:6|confirmed', // 'confirmed' checks for password_confirmation
        ]);

        // 3. Update Password
        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password reset successfully!');
    }
}   