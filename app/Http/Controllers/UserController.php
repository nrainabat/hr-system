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
   // 1. Show the Create User Form
    public function create()
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $departments = DB::table('departments')->orderBy('name')->get();
        $positions = DB::table('job_positions')->orderBy('title')->get();
        
        // FETCH SUPERVISORS to show in the dropdown
        $supervisors = User::where('role', 'supervisor')->orderBy('name')->get();

        return view('users.create', compact('departments', 'positions', 'supervisors'));
    }

    // 2. Store the New User
    public function store(Request $request)
    {
        // ... (existing validation)
        $request->validate([
            'supervisor_id' => 'nullable|exists:users,id',
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
            'address'      => $request->address,
            'supervisor_id'=> $request->supervisor_id, // <--- CRITICAL: Save the ID
        ]);

        return redirect()->route('admin.users.create')->with('success', 'User created successfully!');
    }

    // 3. Show the Edit Form
    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);
        $departments = DB::table('departments')->orderBy('name')->get();
        $positions = DB::table('job_positions')->orderBy('title')->get();

        // FETCH SUPERVISORS (Exclude self)
        $supervisors = User::where('role', 'supervisor')
                            ->where('id', '!=', $id)
                            ->orderBy('name')
                            ->get();

        return view('users.edit', compact('user', 'departments', 'positions', 'supervisors'));
    }

    // 4. Update the User
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);

        $request->validate([
            // ... (existing rules)
            'supervisor_id' => 'nullable|exists:users,id', // <--- Add this rule
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
            'supervisor_id'=> $request->supervisor_id, // <--- CRITICAL: Update the ID
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
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