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
        // Start Query Builder
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

        // Order by name and paginate results (e.g., 12 per page for a 3x4 grid)
        $users = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        return view('admin.directory', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
    // Prepare data for the modal
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'role' => ucfirst($user->role),
            'role_class' => match($user->role) {
                'admin' => 'bg-danger',
                'supervisor' => 'bg-warning text-dark',
                'intern' => 'bg-info text-dark',
                default => 'bg-primary'
            },
            'position' => $user->position ?? 'Not Assigned',
            'department' => $user->department ?? 'N/A',
            'phone_number' => $user->phone_number ?? 'N/A',
            'location' => $user->department ? $user->department . ' Wing' : 'Main Office',
            'joined_date' => $user->created_at ? $user->created_at->format('d M Y') : 'N/A',
    ]);
    }
}      