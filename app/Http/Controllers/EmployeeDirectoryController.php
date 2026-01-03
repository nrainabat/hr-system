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
}