<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EmployeeDirectoryController extends Controller
{
    /**
     * Display the employee directory listing.
     */
    public function index(Request $request)
    {
        // 1. UPDATE: Start query by excluding 'admin' role
        $query = User::where('role', '!=', 'admin');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('department', 'like', '%' . $searchTerm . '%')
                  ->orWhere('position', 'like', '%' . $searchTerm . '%');
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        return view('admin.directory', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => ucfirst($user->role),
            'role_class' => match($user->role) {
                'admin' => 'bg-danger',
                'supervisor' => 'bg-warning text-dark',
                'intern' => 'bg-info text-dark',
                default => 'bg-primary'
            },
            'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
            
            'position' => $user->position ?? 'Not Assigned',
            'department' => $user->department ?? 'N/A',
            'phone_number' => $user->phone_number ?? 'N/A',
            'gender' => $user->gender ?? 'N/A',
            'about' => $user->about ?? 'No bio available.',
            'address' => $user->address ?? 'No address provided.',
            
            // === UPDATED: Employment Dates ===
            'joined_date' => $user->start_date 
                ? \Carbon\Carbon::parse($user->start_date)->format('d M Y') 
                : ($user->created_at ? $user->created_at->format('d M Y') : 'N/A'),

            // This was missing before:
            'end_date' => $user->end_date 
                ? \Carbon\Carbon::parse($user->end_date)->format('d M Y') 
                : 'Permanent',
        ]);
    }

    public function myTeam(Request $request)
    {
        // This naturally excludes other admins since it filters by supervisor_id
        $query = User::where('supervisor_id', Auth::id());

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('department', 'like', '%' . $searchTerm . '%')
                  ->orWhere('position', 'like', '%' . $searchTerm . '%');
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        return view('supervisor.team', compact('users'));
    }
}