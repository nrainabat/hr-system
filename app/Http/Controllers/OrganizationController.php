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

    // NEW: Show Edit Form
    public function editDepartment($id)
    {
        $department = Department::findOrFail($id);
        return view('admin.organization.editDepartment', compact('department'));
    }

    // NEW: Update Data
    public function updateDepartment(Request $request, $id)
    {
        $request->validate(['name' => 'required|unique:departments,name,'.$id]);
        Department::findOrFail($id)->update($request->all());
        return redirect()->route('admin.org.departments')->with('success', 'Department updated successfully!');
    }

    public function destroyDepartment($id)
    {
        Department::findOrFail($id)->delete();
        return back()->with('success', 'Department removed.');
    }

    // === JOB POSITIONS ===
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

    // NEW: Show Edit Form
    public function editJob($id)
    {
        $job = JobPosition::findOrFail($id);
        return view('admin.organization.editJob', compact('job'));
    }

    // NEW: Update Data
    public function updateJob(Request $request, $id)
    {
        $request->validate(['title' => 'required|unique:job_positions,title,'.$id]);
        JobPosition::findOrFail($id)->update($request->all());
        return redirect()->route('admin.org.jobs')->with('success', 'Job Position updated successfully!');
    }

    public function destroyJob($id)
    {
        JobPosition::findOrFail($id)->delete();
        return back()->with('success', 'Job Position removed.');
    }

    public function structureAssignments()
    {
        // 1. Get all employees & interns to show in the list
        $staffList = User::whereIn('role', ['employee', 'intern'])
                         ->with('supervisor')
                         ->orderBy('name')
                         ->get();

        // 2. Get list of Supervisors for the dropdown
        $supervisors = User::where('role', 'supervisor')->orderBy('name')->get();

        // 3. Get list of Staff for the dropdown
        $employees = User::whereIn('role', ['employee', 'intern'])->orderBy('name')->get();

        return view('admin.organization.structure_assignments', compact('staffList', 'supervisors', 'employees'));
    }

    // ==========================================
    // NEW STRUCTURE: TEAM DEPARTMENT VIEW
    // ==========================================
    public function structureTeams(Request $request)
    {
        // 1. Get Departments with employee count
        $departments = Department::orderBy('name')->get();
        
        foreach($departments as $dept) {
            $dept->user_count = User::where('department', $dept->name)->count();
        }

        // 2. Determine Selected Department
        $selectedDeptId = $request->get('department_id', $departments->first()->id ?? 0);
        $selectedDept = Department::find($selectedDeptId);

        // 3. Get Employees for the selected department
        $employees = [];
        if ($selectedDept) {
            $employees = User::where('department', $selectedDept->name)
                             ->with('supervisor')
                             ->get();
        }

        return view('admin.organization.structure_teams', compact('departments', 'selectedDept', 'employees'));
    }

    // ==========================================
    // ACTIONS (Assign/Unassign)
    // ==========================================
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