<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // <--- Make sure this is imported

class EmployeeDirectoryController extends Controller
{
    /**
     * Display the employee directory listing (Admin & All Users).
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search Logic
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('department', 'like', '%' . $searchTerm . '%')
                  ->orWhere('position', 'like', '%' . $searchTerm . '%');
            });
        }

        // Sort by name and paginate
        $users = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        return view('admin.directory', compact('users'));
    }

    /**
     * Return User Details as JSON for the Modal.
     */
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
            
            // === NEW: Employment Dates ===
            'joined_date' => $user->start_date 
                ? Carbon::parse($user->start_date)->format('d M Y') 
                : ($user->created_at ? $user->created_at->format('d M Y') : 'N/A'),

            'end_date' => $user->end_date 
                ? Carbon::parse($user->end_date)->format('d M Y') 
                : 'Permanent',
        ]);
    }

    /**
     * Supervisor "My Team" View.
     */
    public function myTeam(Request $request)
    {
        $query = User::where('supervisor_id', Auth::id());

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        return view('supervisor.team', compact('users'));
    }
}