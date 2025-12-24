<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Student;
use App\Models\WblGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Student::with(['group', 'company']);

        // Filter by group
        if ($request->has('group') && $request->group !== null && $request->group !== '') {
            $query->where('group_id', $request->group);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_dir', 'asc');

        // Validate sort column
        $allowedSortColumns = ['name', 'matric_no', 'programme', 'company_id'];
        if (! in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'name';
        }

        // Validate sort direction
        if (! in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Handle company sorting (relationship)
        if ($sortBy === 'company_id') {
            $query->leftJoin('companies', 'students.company_id', '=', 'companies.id')
                ->select('students.*')
                ->orderBy('companies.company_name', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Filter by group status based on user role
        $user = auth()->user();
        if ($user->isAdmin() || $user->isCoordinator()) {
            // Admin and Coordinator can see all groups
            // Optional: Add filter for active/completed if needed
        } else {
            // Lecturer/AT/IC/Supervisor LI can only see students in active groups
            $query->inActiveGroups();
        }

        $students = $query->paginate(15)->withQueryString();

        // Get groups for tabs based on role
        $groupsQuery = WblGroup::orderBy('name');
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            $groupsQuery->where('status', 'ACTIVE');
        }
        $groups = $groupsQuery->get();

        // Build tabs array
        $tabs = [
            ['label' => 'All', 'value' => null],
        ];

        foreach ($groups as $group) {
            $tabs[] = [
                'label' => $group->name,
                'value' => $group->id,
            ];
        }

        // Current active tab
        $activeTab = $request->get('group', null);

        // Build sort URLs for columns
        $baseUrl = route('admin.students.index');
        $currentParams = $request->only(['group', 'search']);

        return view('students.index', compact('students', 'tabs', 'activeTab', 'sortBy', 'sortDirection', 'baseUrl', 'currentParams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Only show active groups for assignment
        $groups = WblGroup::where('status', 'ACTIVE')->orderBy('name')->get();
        $companies = Company::all();

        return view('students.create', compact('groups', 'companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'matric_no' => ['required', 'string', 'max:50', 'unique:students,matric_no'],
            'programme' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'exists:wbl_groups,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): View
    {
        $student->load(['group', 'company', 'academicTutor', 'industryCoach']);

        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student): View
    {
        // Only show active groups for assignment (or current group if completed)
        $groups = WblGroup::where(function ($q) use ($student) {
            $q->where('status', 'ACTIVE')
                ->orWhere('id', $student->group_id); // Include current group even if completed
        })->orderBy('name')->get();
        $companies = Company::all();
        $lecturers = \App\Models\User::where('role', 'lecturer')->orderBy('name')->get();
        $industryCoaches = \App\Models\User::where('role', 'industry')->orderBy('name')->get();

        return view('students.edit', compact('student', 'groups', 'companies', 'lecturers', 'industryCoaches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        // Ensure optional fields are always present, even if null
        $request->merge([
            'at_id' => $request->input('at_id', $student->at_id),
            'ic_id' => $request->input('ic_id', $student->ic_id),
            'company_id' => $request->input('company_id', $student->company_id),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'matric_no' => ['required', 'string', 'max:50', 'unique:students,matric_no,'.$student->id],
            'programme' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'exists:wbl_groups,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'at_id' => ['nullable', 'exists:users,id'],
            'ic_id' => ['nullable', 'exists:users,id'],
        ]);

        // Safely extract values with null fallback
        $atId = ! empty($validated['at_id']) ? $validated['at_id'] : null;
        $icId = ! empty($validated['ic_id']) ? $validated['ic_id'] : null;

        // Update with null-safe values
        $student->update([
            'name' => $validated['name'],
            'matric_no' => $validated['matric_no'],
            'programme' => $validated['programme'],
            'group_id' => $validated['group_id'],
            'company_id' => ! empty($validated['company_id']) ? $validated['company_id'] : null,
            'at_id' => $atId,
            'ic_id' => $icId,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): RedirectResponse
    {
        // Delete associated user account if exists and user only has student role
        if ($student->user) {
            $user = $student->user;

            // Only delete user if they ONLY have the student role (no admin, lecturer, etc.)
            $userRoles = $user->roles()->pluck('name')->toArray();
            $hasOnlyStudentRole = count($userRoles) === 1 && in_array('student', $userRoles);

            // Also check legacy role column
            $isOnlyStudent = $user->role === 'student' && $hasOnlyStudentRole;

            if ($isOnlyStudent) {
                $user->delete();
            }
        }

        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }
}
