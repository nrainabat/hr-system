<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\JobPosition;
use App\Models\User;

class OrganizationController extends Controller
{
    // === DEPARTMENTS ===
    public function indexDepartments()
    {
        // Variable defined as '$departments'
        $departments = Department::orderBy('name')->get();

        foreach($departments as $dept) {
            $dept->user_count = User::where('department', $dept->name)->count();
        }

        // CORRECT: Matches '$departments'
        return view('admin.organization.departments', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate(['name' => 'required|unique:departments,name']);
        Department::create($request->all());
        return back()->with('success', 'Department added successfully!');
    }

    public function destroyDepartment($id)
    {
        Department::findOrFail($id)->delete();
        return back()->with('success', 'Department removed.');
    }

    // === JOB POSITIONS ===
    public function indexJobs()
    {
        // Variable defined as '$jobs'
        $jobs = JobPosition::orderBy('title')->get();

        // CORRECT: Matches '$jobs' (Fix for your error)
        return view('admin.organization.jobs', compact('jobs'));
    }

    public function storeJob(Request $request)
    {
        $request->validate(['title' => 'required|unique:job_positions,title']);
        JobPosition::create($request->all());
        return back()->with('success', 'Job Position added successfully!');
    }

    public function destroyJob($id)
    {
        JobPosition::findOrFail($id)->delete();
        return back()->with('success', 'Job Position removed.');
    }

    // === STRUCTURE ===
    public function structure()
    {
        $departments = Department::orderBy('name')->get();
        
        $structure = [];
        
        foreach($departments as $dept) {
            $users = User::where('department', $dept->name)->get();
            
            $structure[] = [
                'department' => $dept->name,
                'supervisors' => $users->where('role', 'supervisor'),
                'employees' => $users->whereIn('role', ['employee', 'intern']),
            ];
        }

        return view('admin.organization.structure', compact('structure'));
    }
}