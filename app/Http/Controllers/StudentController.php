<?php

namespace App\Http\Controllers;

use App\Exports\StudentsErrorsExport;
use App\Exports\StudentsTemplateExport;
use App\Imports\StudentsImport;
use App\Imports\StudentsPreviewImport;
use App\Models\Company;
use App\Models\Student;
use App\Models\WblGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    /**
     * Check if WBL coordinator can access a specific student.
     */
    private function authorizeWblCoordinatorAccess(Student $student): void
    {
        $user = auth()->user();

        // Admin can access all students
        if ($user->isAdmin()) {
            return;
        }

        // WBL Coordinators can only access students from their programme
        if ($user->isBtaWblCoordinator()) {
            if ($student->programme !== 'Bachelor of Mechanical Engineering Technology (Automotive) with Honours') {
                abort(403, 'You can only access BTA students.');
            }
        } elseif ($user->isBtdWblCoordinator()) {
            if ($student->programme !== 'Bachelor of Mechanical Engineering Technology (Design and Analysis) with Honours') {
                abort(403, 'You can only access BTD students.');
            }
        } elseif ($user->isBtgWblCoordinator()) {
            if ($student->programme !== 'Bachelor of Mechanical Engineering Technology (Oil and Gas) with Honours') {
                abort(403, 'You can only access BTG students.');
            }
        }
    }

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

        // Filter by programme for WBL Coordinators
        if ($user->isBtaWblCoordinator()) {
            $query->where('programme', 'Bachelor of Mechanical Engineering Technology (Automotive) with Honours');
        } elseif ($user->isBtdWblCoordinator()) {
            $query->where('programme', 'Bachelor of Mechanical Engineering Technology (Design and Analysis) with Honours');
        } elseif ($user->isBtgWblCoordinator()) {
            $query->where('programme', 'Bachelor of Mechanical Engineering Technology (Oil and Gas) with Honours');
        }

        // Handle per_page parameter (supports 'all' for showing all records)
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $allStudents = $query->get();
            $students = new \Illuminate\Pagination\LengthAwarePaginator(
                $allStudents,
                $allStudents->count(),
                $allStudents->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $students = $query->paginate((int) $perPage)->withQueryString();
        }

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

        $isWblCoordinator = $user->isWblCoordinator();

        return view('students.index', compact('students', 'tabs', 'activeTab', 'sortBy', 'sortDirection', 'baseUrl', 'currentParams', 'isWblCoordinator'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Only show active groups for assignment
        $groups = WblGroup::where('status', 'ACTIVE')->orderBy('name')->get();
        $companies = Company::orderBy('company_name')->get();

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
            'programme' => ['nullable', 'string', 'max:255'],
            'group_id' => ['nullable', 'exists:wbl_groups,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'cgpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'ic_number' => ['nullable', 'string', 'max:20'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_phone_number' => ['nullable', 'string', 'max:20'],
            'next_of_kin' => ['nullable', 'string', 'max:255'],
            'next_of_kin_phone_number' => ['nullable', 'string', 'max:20'],
            'home_address' => ['nullable', 'string'],
            'skills' => ['nullable', 'json'],
            'interests' => ['nullable', 'string'],
            'preferred_industry' => ['nullable', 'string', 'max:255'],
            'preferred_location' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'], // 2MB max
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('students', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Decode skills JSON if present
        if (isset($validated['skills'])) {
            $validated['skills'] = json_decode($validated['skills'], true);
        }

        $student = Student::create($validated);

        // Redirect to the same group filter if student was assigned to a group
        $redirectParams = [];
        if ($student->group_id) {
            $redirectParams['group'] = $student->group_id;
        }

        return redirect()->route('admin.students.index', $redirectParams)
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): View
    {
        $this->authorizeWblCoordinatorAccess($student);

        $student->load(['group', 'company', 'academicTutor', 'industryCoach', 'academicAdvisor', 'courseAssignments.lecturer']);

        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student): View
    {
        $this->authorizeWblCoordinatorAccess($student);

        // Only show active groups for assignment (or current group if completed)
        $groups = WblGroup::where(function ($q) use ($student) {
            $q->where('status', 'ACTIVE')
                ->orWhere('id', $student->group_id); // Include current group even if completed
        })->orderBy('name')->get();
        $companies = Company::orderBy('company_name')->get();
        $lecturers = \App\Models\User::where('role', 'lecturer')->orderBy('name')->get();
        $industryCoaches = \App\Models\User::where('role', 'industry')->orderBy('name')->get();

        return view('students.edit', compact('student', 'groups', 'companies', 'lecturers', 'industryCoaches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $this->authorizeWblCoordinatorAccess($student);

        // Ensure optional fields are always present, even if null
        $request->merge([
            'at_id' => $request->input('at_id', $student->at_id),
            'ic_id' => $request->input('ic_id', $student->ic_id),
            'company_id' => $request->input('company_id', $student->company_id),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'matric_no' => ['required', 'string', 'max:50', 'unique:students,matric_no,'.$student->id],
            'programme' => ['nullable', 'string', 'max:255'],
            'group_id' => ['nullable', 'exists:wbl_groups,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'at_id' => ['nullable', 'exists:users,id'],
            'ic_id' => ['nullable', 'exists:users,id'],
            'cgpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'ic_number' => ['nullable', 'string', 'max:20'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_phone_number' => ['nullable', 'string', 'max:20'],
            'next_of_kin' => ['nullable', 'string', 'max:255'],
            'next_of_kin_phone_number' => ['nullable', 'string', 'max:20'],
            'home_address' => ['nullable', 'string'],
            'skills' => ['nullable', 'json'],
            'interests' => ['nullable', 'string'],
            'preferred_industry' => ['nullable', 'string', 'max:255'],
            'preferred_location' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'], // 2MB max
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($student->image_path) {
                \Storage::disk('public')->delete($student->image_path);
            }

            $imagePath = $request->file('image')->store('students', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Safely extract values with null fallback
        $atId = ! empty($validated['at_id']) ? $validated['at_id'] : null;
        $icId = ! empty($validated['ic_id']) ? $validated['ic_id'] : null;

        // Decode skills JSON if present
        if (isset($validated['skills'])) {
            $validated['skills'] = json_decode($validated['skills'], true);
        }

        // Update with null-safe values
        $student->update([
            'name' => $validated['name'],
            'matric_no' => $validated['matric_no'],
            'programme' => $validated['programme'],
            'group_id' => $validated['group_id'],
            'company_id' => ! empty($validated['company_id']) ? $validated['company_id'] : null,
            'at_id' => $atId,
            'ic_id' => $icId,
            'cgpa' => $validated['cgpa'] ?? null,
            'ic_number' => $validated['ic_number'] ?? null,
            'parent_name' => $validated['parent_name'] ?? null,
            'parent_phone_number' => $validated['parent_phone_number'] ?? null,
            'next_of_kin' => $validated['next_of_kin'] ?? null,
            'next_of_kin_phone_number' => $validated['next_of_kin_phone_number'] ?? null,
            'home_address' => $validated['home_address'] ?? null,
            'skills' => $validated['skills'] ?? null,
            'interests' => $validated['interests'] ?? null,
            'preferred_industry' => $validated['preferred_industry'] ?? null,
            'preferred_location' => $validated['preferred_location'] ?? null,
            'image_path' => $validated['image_path'] ?? $student->image_path,
        ]);

        // Redirect back to the same group and page if available
        $redirectParams = [];

        // Use return_group if provided (original group filter), otherwise use student's current group
        if ($request->filled('return_group')) {
            $redirectParams['group'] = $request->return_group;
        } elseif ($student->group_id) {
            $redirectParams['group'] = $student->group_id;
        }

        if ($request->filled('page') && (int) $request->page > 1) {
            $redirectParams['page'] = (int) $request->page;
        }

        if ($request->filled('per_page')) {
            $redirectParams['per_page'] = $request->per_page;
        }

        return redirect()->route('admin.students.index', $redirectParams)
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Student $student): RedirectResponse
    {
        $this->authorizeWblCoordinatorAccess($student);

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

        // Store group_id before deleting
        $groupId = $student->group_id;

        $student->delete();

        // Redirect back to the same group filter if student had a group
        $redirectParams = [];
        if ($request->filled('group')) {
            $redirectParams['group'] = $request->group;
        } elseif ($groupId) {
            $redirectParams['group'] = $groupId;
        }

        if ($request->filled('per_page')) {
            $redirectParams['per_page'] = $request->per_page;
        }

        return redirect()->route('admin.students.index', $redirectParams)
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Import students from Excel file.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $import = new StudentsImport;
            Excel::import($import, $request->file('file'));

            $errors = $import->getErrors();
            $failures = $import->getFailures();

            // Check if there are any errors or failures
            if (count($errors) > 0 || count($failures) > 0) {
                $errorMessages = [];

                // Add general errors
                foreach ($errors as $error) {
                    $errorMessages[] = $error;
                }

                // Add validation failures with row numbers
                foreach ($failures as $failure) {
                    $row = $failure->row();
                    $attribute = $failure->attribute();
                    $errorMessages[] = "Row {$row}: {$attribute} - ".implode(', ', $failure->errors());
                }

                // Return with errors but also success for rows that were imported
                return redirect()->route('admin.students.index')
                    ->with('warning', 'Import completed with some errors. '.count($errorMessages).' row(s) failed.')
                    ->with('import_errors', $errorMessages);
            }

            return redirect()->route('admin.students.index')
                ->with('success', 'Students imported successfully!');

        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Download Excel template for student import.
     */
    public function downloadTemplate()
    {
        return Excel::download(new StudentsTemplateExport, 'students_import_template.xlsx');
    }

    /**
     * Preview students import before committing to database.
     */
    public function previewImport(Request $request): RedirectResponse|View
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            // Parse and validate the file
            $import = new StudentsPreviewImport;
            Excel::import($import, $request->file('file'));
            $previewData = $import->getPreviewData();

            // Calculate statistics
            $stats = [
                'total' => count($previewData),
                'valid' => count(array_filter($previewData, fn ($row) => $row['valid'])),
                'invalid' => count(array_filter($previewData, fn ($row) => ! $row['valid'])),
            ];

            // Store in session
            session([
                'student_import_preview' => [
                    'data' => $previewData,
                    'stats' => $stats,
                    'uploaded_at' => now()->timestamp,
                ],
            ]);

            // Redirect to preview page
            return view('students.import_preview', [
                'previewData' => $previewData,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Failed to process file: '.$e->getMessage());
        }
    }

    /**
     * Confirm and execute the import after preview.
     */
    public function confirmImport(Request $request): RedirectResponse
    {
        // Check if preview data exists in session
        $previewSession = session('student_import_preview');

        if (! $previewSession) {
            return redirect()->route('admin.students.index')
                ->with('error', 'No import data found. Please upload a file first.');
        }

        $previewData = $previewSession['data'];
        $stats = $previewSession['stats'];

        try {
            $imported = 0;
            $skipped = 0;

            // Import only valid rows
            foreach ($previewData as $row) {
                if ($row['valid']) {
                    $data = $row['data'];

                    // Parse skills if needed
                    $skills = null;
                    if (! empty($data['skills'])) {
                        if (is_string($data['skills'])) {
                            $skills = array_map('trim', explode(',', $data['skills']));
                        } else {
                            $skills = $data['skills'];
                        }
                    }

                    Student::create([
                        'name' => $data['name'],
                        'matric_no' => $data['matric_no'],
                        'programme' => $data['programme'] ?: null,
                        'group_id' => $data['group_id'],
                        'company_id' => $data['company_id'],
                        'cgpa' => $data['cgpa'] ?: null,
                        'ic_number' => $data['ic_number'] ?: null,
                        'mobile_phone' => $data['mobile_phone'] ?: null,
                        'parent_name' => $data['parent_name'] ?: null,
                        'parent_phone_number' => $data['parent_phone_number'] ?: null,
                        'next_of_kin' => $data['next_of_kin'] ?: null,
                        'next_of_kin_phone_number' => $data['next_of_kin_phone_number'] ?: null,
                        'home_address' => $data['home_address'] ?: null,
                        'background' => $data['background'] ?: null,
                        'skills' => $skills,
                        'interests' => $data['interests'] ?: null,
                        'preferred_industry' => $data['preferred_industry'] ?: null,
                        'preferred_location' => $data['preferred_location'] ?: null,
                    ]);

                    $imported++;
                } else {
                    $skipped++;
                }
            }

            // Clear session data
            session()->forget('student_import_preview');

            // Build success message
            $message = "Successfully imported {$imported} student(s).";
            if ($skipped > 0) {
                $message .= " {$skipped} row(s) were skipped due to validation errors.";
            }

            return redirect()->route('admin.students.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Download error rows as Excel file.
     */
    public function downloadErrors()
    {
        $previewSession = session('student_import_preview');

        if (! $previewSession) {
            return redirect()->route('admin.students.index')
                ->with('error', 'No import data found.');
        }

        // Get only invalid rows
        $invalidRows = array_filter($previewSession['data'], fn ($row) => ! $row['valid']);

        if (empty($invalidRows)) {
            return redirect()->back()
                ->with('info', 'No errors to download.');
        }

        $timestamp = date('Y-m-d_His');

        return Excel::download(
            new StudentsErrorsExport(array_values($invalidRows)),
            "students_import_errors_{$timestamp}.xlsx"
        );
    }

    /**
     * Cancel import and clear session data.
     */
    public function cancelImport(): RedirectResponse
    {
        session()->forget('student_import_preview');

        return redirect()->route('admin.students.index')
            ->with('info', 'Import cancelled.');
    }
}
