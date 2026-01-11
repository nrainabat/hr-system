<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    // --- NEW: SUPERVISOR PAGES ---

    // 1. Page to Evaluate Teams (List Subordinates)
    public function evaluateTeams()
    {
        if (Auth::user()->role !== 'supervisor') {
            abort(403, 'Unauthorized');
        }

        // Get strictly assigned subordinates
        $team = User::where('supervisor_id', Auth::id())->get();
        
        return view('performance.index', compact('team'));
    }

    // 2. Page to View Own Performance (As evaluated by Admin)
    public function myReviews()
    {
        // Get reviews where I am the SUBJECT (user_id)
        $reviews = PerformanceReview::where('user_id', Auth::id())
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('performance.my_reviews', compact('reviews'));
    }

    // --- NEW: ADMIN PAGES ---

    // 3. Page to Evaluate Supervisors (List Supervisors)
    public function adminEvaluateSupervisors()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Get all supervisors to evaluate
        $supervisors = User::where('role', 'supervisor')->get();

        return view('admin.performance.evaluate', compact('supervisors'));
    }

    // 4. Page to View ALL Records
    public function adminAllRecords()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $reviews = PerformanceReview::with(['employee', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.performance.records', compact('reviews'));
    }

    // --- EXISTING FUNCTIONS (Unchanged Logic) ---

    public function index()
    {
        // Redirect to specific pages based on role to avoid confusion
        $user = Auth::user();
        if ($user->role === 'supervisor') {
            return redirect()->route('performance.evaluate_teams');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.performance.records');
        } else {
            return redirect()->route('performance.my_reviews');
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

        if (Auth::user()->role === 'supervisor') {
            if ($employee->supervisor_id !== Auth::id()) {
                abort(403, 'Access Denied: You cannot evaluate this employee.');
            }
        }

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

        // Redirect back to the list they came from
        if(Auth::user()->role === 'admin') {
            return redirect()->route('admin.performance.records')->with('success', 'Evaluation submitted.');
        }
        return redirect()->route('performance.evaluate_teams')->with('success', 'Evaluation submitted.');
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