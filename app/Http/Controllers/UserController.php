<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // 1. Show the Create User Form
    public function create()
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $departments = DB::table('departments')->orderBy('name')->get();
        $positions = DB::table('job_positions')->orderBy('title')->get();
        
        // Fetch Supervisors for the dropdown
        $supervisors = User::where('role', 'supervisor')->orderBy('name')->get();

        return view('users.create', compact('departments', 'positions', 'supervisors'));
    }

    // 2. Store the New User
    // 2. Store the New User
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'username'      => 'required|string|max:50|unique:users,username',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'role'          => 'required|string|in:admin,supervisor,employee,intern',
            'department'    => 'required|string',
            'supervisor_id' => 'nullable|exists:users,id',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after:start_date',
            
            // Validate Manual Leave Input (Required only for Interns)
            'annual_leave'  => 'nullable|required_if:role,intern|integer|min:0',
        ]);

        // 1. Create User
        $user = User::create([
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'department'    => $request->department,
            'position'      => $request->position,
            'status'        => 'active',
            'phone_number'  => $request->phone_number,
            'gender'        => $request->gender,
            'about'         => $request->about,
            'address'       => $request->address,
            'supervisor_id' => $request->supervisor_id,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
        ]);

        // 2. Initialize Annual Leave Balance
        $annualLeaveBalance = 14; 

        if ($request->role === 'intern') {
            $annualLeaveBalance = $request->input('annual_leave', 0);
        }

        LeaveCount::create([
            'user_id'    => $user->id,
            'leave_type' => 'Annual Leave',
            'balance'    => $annualLeaveBalance,
            'year'       => date('Y'),
        ]);
    }

    // 3. Show the Edit Form
    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);
        $departments = DB::table('departments')->orderBy('name')->get();
        $positions = DB::table('job_positions')->orderBy('title')->get();

        // Fetch Supervisors (Exclude the user themselves to prevent self-supervision)
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
            'name'          => 'required|string|max:255',
            'username'      => 'required|string|max:50|unique:users,username,'.$id,
            'email'         => 'required|email|unique:users,email,'.$id,
            'role'          => 'required|string|in:admin,supervisor,employee,intern',
            'supervisor_id' => 'nullable|exists:users,id',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after:start_date',
        ]);

        $data = [
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'role'          => $request->role,
            'department'    => $request->department,
            'position'      => $request->position,
            'phone_number'  => $request->phone_number,
            'gender'        => $request->gender,
            'about'         => $request->about,
            'address'       => $request->address,
            'supervisor_id' => $request->supervisor_id,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
        ];

        // Only update password if a new one is provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.directory')->with('success', 'User updated successfully!');
    }

    // 5. Delete User (Destroy)
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') abort(403, 'Unauthorized action.');

        $user = User::findOrFail($id);

        // Prevent Self-Deletion
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account while logged in.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Employee has been successfully removed.');
    }

    // 6. Force Password Reset (Admin Action)
    public function updatePassword(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password reset successfully!');
    }
}