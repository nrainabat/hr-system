<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    // --- SUPERVISOR ACTIONS (Unchanged) ---
    public function evaluateTeams()
    {
        if (Auth::user()->role !== 'supervisor') {
            abort(403, 'Unauthorized');
        }
        $subordinates = User::where('supervisor_id', Auth::id())->get();
        $reviews = PerformanceReview::where('reviewer_id', Auth::id())
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('performance.index', compact('reviews', 'subordinates'));
    }

    public function myReviews()
    {
        $reviews = PerformanceReview::where('user_id', Auth::id())
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('performance.myReview', compact('reviews'));
    }

    // --- NEW: ADMIN ACTION (Consolidated) ---
    
    // This handles the single "Performance Review" page for Admin
    public function adminPerformance()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // 1. Dropdown: List ALL employees so Admin can evaluate anyone
        // (Excluding other admins if you prefer, currently filtering out self)
        $subordinates = User::where('id', '!=', Auth::id())->get();

        // 2. Table: List ALL performance records in the system
        $reviews = PerformanceReview::with(['employee', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Uses index.blade.php
        return view('performance.index', compact('reviews', 'subordinates'));
    }

    // --- SHARED ACTIONS ---

    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'supervisor') {
            return redirect()->route('performance.evaluateTeams');
        } elseif ($user->role === 'admin') {
            // Redirect to the new single Admin route
            return redirect()->route('admin.performance.index');
        } else {
            return redirect()->route('performance.myReview');
        }
    }

    public function create(Request $request)
    {
        if (!in_array(Auth::user()->role, ['supervisor', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $employeeId = $request->query('employee_id');
        if (!$employeeId) {
            return redirect()->back()->with('error', 'No employee selected.');
        }
        
        $employee = User::findOrFail($employeeId);

        // Strict Check for Supervisors only
        if (Auth::user()->role === 'supervisor') {
            if ($employee->supervisor_id !== Auth::id()) {
                abort(403, 'Access Denied: You can only evaluate employees strictly assigned to you.');
            }
        }

        return view('performance.create', compact('employee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rating_quality' => 'required|integer|min:1|max:5',
            'rating_efficiency' => 'required|integer|min:1|max:5',
            'rating_teamwork' => 'required|integer|min:1|max:5',
            'rating_punctuality' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);

        $employee = User::findOrFail($request->user_id);
        if (Auth::user()->role === 'supervisor' && $employee->supervisor_id !== Auth::id()) {
            abort(403, 'Access Denied.');
        }

        $average = ($request->rating_quality + $request->rating_efficiency + $request->rating_teamwork + $request->rating_punctuality) / 4;

        PerformanceReview::create([
            'user_id' => $request->user_id,
            'reviewer_id' => Auth::id(),
            'review_date' => Carbon::now(),
            'rating_quality' => $request->rating_quality,
            'rating_efficiency' => $request->rating_efficiency,
            'rating_teamwork' => $request->rating_teamwork,
            'rating_punctuality' => $request->rating_punctuality,
            'average_score' => $average,
            'comments' => $request->comments,
        ]);

        // Redirect Admin to the single index page
        if(Auth::user()->role === 'admin') {
            return redirect()->route('admin.performance.index')->with('success', 'Evaluation submitted.');
        }
        return redirect()->route('performance.evaluateTeams')->with('success', 'Evaluation submitted.');
    }

    public function show($id)
    {
        $review = PerformanceReview::with(['employee', 'reviewer'])->findOrFail($id);
        
        if (Auth::user()->id !== $review->user_id && 
            Auth::user()->id !== $review->reviewer_id && 
            Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('performance.show', compact('review'));
    }
}