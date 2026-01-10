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
        $query = User::query();

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
            // === NEW: Send Profile Image URL ===
            'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
            
            'position' => $user->position ?? 'Not Assigned',
            'department' => $user->department ?? 'N/A',
            'phone_number' => $user->phone_number ?? 'N/A',
            'gender' => $user->gender ?? 'N/A',
            'about' => $user->about ?? 'No bio available.',
            'address' => $user->address ?? 'No address provided.',
            'joined_date' => $user->created_at ? $user->created_at->format('d M Y') : 'N/A',
        ]);
    }

    public function myTeam(Request $request)
    {
        // 1. Fetch only users where supervisor_id matches the logged-in Supervisor
        $query = User::where('supervisor_id', Auth::id());

        // 2. Apply Search Filter (same as Admin)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('department', 'like', '%' . $searchTerm . '%')
                  ->orWhere('position', 'like', '%' . $searchTerm . '%');
            });
        }

        // 3. Paginate
        $users = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        // 4. Return the specific Supervisor view
        return view('supervisor.team', compact('users'));
    }
}