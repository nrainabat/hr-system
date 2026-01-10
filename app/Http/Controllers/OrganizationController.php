<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\JobPosition;
use App\Models\User;

class OrganizationController extends Controller
{
    // ... (Departments and Job Positions methods remain unchanged) ...
    public function indexDepartments()
    {
        $departments = Department::orderBy('name')->get();
        foreach($departments as $dept) {
            $dept->user_count = User::where('department', $dept->name)->count();
        }
        return view('admin.organization.departments', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate(['name' => 'required|unique:departments,name']);
        Department::create($request->all());
        return back()->with('success', 'Department added successfully!');
    }

    public function updateDepartment(Request $request, $id)
    {
        $request->validate(['name' => 'required|unique:departments,name,'.$id]);
        Department::findOrFail($id)->update($request->all());
        return redirect()->route('admin.org.departments.index')->with('success', 'Department updated successfully!');
    }

    public function destroyDepartment($id)
    {
        Department::findOrFail($id)->delete();
        return back()->with('success', 'Department removed.');
    }

    // ... (Job Position methods remain unchanged) ...
    public function indexJobs()
    {
        $jobs = JobPosition::orderBy('title')->get();
        return view('admin.organization.jobs', compact('jobs'));
    }

    public function storeJob(Request $request)
    {
        $request->validate(['title' => 'required|unique:job_positions,title']);
        JobPosition::create($request->all());
        return back()->with('success', 'Job Position added successfully!');
    }

    public function updateJob(Request $request, $id)
    {
        $request->validate(['title' => 'required|unique:job_positions,title,'.$id]);
        JobPosition::findOrFail($id)->update($request->all());
        return redirect()->route('admin.org.jobs.index')->with('success', 'Job Position updated successfully!');
    }

    public function destroyJob($id)
    {
        JobPosition::findOrFail($id)->delete();
        return back()->with('success', 'Job Position removed.');
    }

    // ... (Structure Assignments methods remain unchanged) ...
    public function structureAssignments()
    {
        $staffList = User::whereIn('role', ['employee', 'intern'])
                         ->with('supervisor')
                         ->orderBy('name')
                         ->get();

        $supervisors = User::where('role', 'supervisor')->orderBy('name')->get();
        $employees = User::whereIn('role', ['employee', 'intern'])->orderBy('name')->get();

        return view('admin.organization.structAssignment', compact('staffList', 'supervisors', 'employees'));
    }

    // ==========================================
    // TEAM VIEW (UPDATED)
    // ==========================================
    public function structureTeams(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        
        foreach($departments as $dept) {
            // Count total users
            $dept->user_count = User::where('department', $dept->name)->count();

            // NEW: Fetch the Supervisor for this Department
            // We assume the first user with 'supervisor' role in this department is the lead
            $supervisor = User::where('department', $dept->name)
                              ->where('role', 'supervisor')
                              ->first();
            
            // Attach supervisor details to the department object
            $dept->supervisor = $supervisor; 
        }

        $selectedDeptId = $request->get('department_id', $departments->first()->id ?? 0);
        $selectedDept = Department::find($selectedDeptId);

        $employees = [];
        if ($selectedDept) {
            $employees = User::where('department', $selectedDept->name)
                             ->with('supervisor')
                             ->get();
        }

        return view('admin.organization.structTeams', compact('departments', 'selectedDept', 'employees'));
    }

    // ... (Action methods remain unchanged) ...
    public function assignSupervisor(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id|different:user_id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->supervisor_id = $request->supervisor_id;
        $user->save();
        return back()->with('success', 'Supervisor assigned successfully.');
    }

    public function unassignSupervisor($id)
    {
        $user = User::findOrFail($id);
        $user->supervisor_id = null;
        $user->save();
        return back()->with('success', 'Supervisor unassigned.');
    }
}