<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    // 1. LIST: Show evaluations
    public function index()
    {
        $user = Auth::user();

        // SCENARIO A: Supervisor (Can only see/evaluate their own team)
        if ($user->role === 'supervisor') {
            // Get strictly assigned subordinates
            $subordinates = User::where('supervisor_id', $user->id)->get();
            $subordinateIds = $subordinates->pluck('id');

            // Show reviews ONLY for these subordinates
            $reviews = PerformanceReview::with('employee')
                ->whereIn('user_id', $subordinateIds)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // For the "New Evaluation" dropdown, ONLY pass subordinates
            return view('performance.index', compact('reviews', 'subordinates'));
        } 
        // SCENARIO B: Admin (Can see all)
        elseif ($user->role === 'admin') {
            $reviews = PerformanceReview::with(['employee', 'reviewer'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            // Admin can evaluate anyone (or restrict if needed)
            $subordinates = User::where('role', '!=', 'admin')->get(); 
            return view('performance.index', compact('reviews', 'subordinates'));
        }
        // SCENARIO C: Employee/Intern (Can only see their own)
        else {
            $reviews = PerformanceReview::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('performance.index', compact('reviews'));
        }
    }

    // 2. CREATE: Show form with STRICT CHECK
    public function create(Request $request)
    {
        // 1. Basic Role Check
        if (!in_array(Auth::user()->role, ['supervisor', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $employeeId = $request->query('employee_id');
        $employee = User::findOrFail($employeeId);

        // 2. STRICT RELATIONSHIP CHECK
        // If I am a Supervisor, this employee MUST be assigned to me.
        if (Auth::user()->role === 'supervisor') {
            if ($employee->supervisor_id !== Auth::id()) {
                abort(403, 'Access Denied: You can only evaluate employees strictly assigned to you.');
            }
        }

        return view('performance.create', compact('employee'));
    }

    // 3. STORE: Save with STRICT CHECK
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

        // REPEAT STRICT CHECK (Prevent tampering with POST data)
        if (Auth::user()->role === 'supervisor') {
            if ($employee->supervisor_id !== Auth::id()) {
                abort(403, 'Access Denied: You cannot evaluate this employee.');
            }
        }

        // Calculate Average
        $total = $request->rating_quality + $request->rating_efficiency + $request->rating_teamwork + $request->rating_punctuality;
        $average = $total / 4;

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

        return redirect()->route('performance.index')->with('success', 'Performance review submitted successfully.');
    }

    public function show($id)
    {
        $review = PerformanceReview::with(['employee', 'reviewer'])->findOrFail($id);
        
        // Authorization: Admin, The Reviewer, or The Employee
        if (Auth::user()->id !== $review->user_id && 
            Auth::user()->id !== $review->reviewer_id && 
            Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('performance.show', compact('review'));
    }
}