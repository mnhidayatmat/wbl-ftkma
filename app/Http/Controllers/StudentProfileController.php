<?php

namespace App\Http\Controllers;

use App\Models\LecturerCourseAssignment;
use App\Models\Student;
use App\Models\StudentCourseAssignment;
use App\Models\WblGroup;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentProfileController extends Controller
{
    /**
     * Display the student profile.
     */
    public function show(): View|RedirectResponse
    {
        $user = auth()->user();
        
        // Ensure user is a student
        if (!$user->isStudent()) {
            abort(403, 'Only students can access this page.');
        }
        
        $student = $user->student;
        
        // If no student profile exists, redirect to create form
        if (!$student) {
            return redirect()->route('students.profile.create');
        }

        // Check authorization
        $this->authorize('view', $student);

        // Load relationships
        $student->load(['group', 'company', 'industryCoach', 'academicTutor', 'academicAdvisor', 'user']);

        // Get course assignments - ensure it's always a collection
        $courseAssignments = $student->courseAssignments()->with('lecturer')->get()->keyBy('course_type');

        // Get available options for each assignment type
        $atOptions = User::where('role', 'at')->orWhere('role', 'lecturer')->orderBy('name')->get();
        $icOptions = User::where('role', 'industry')->orderBy('name')->get();
        $supervisorLiOptions = User::where('role', 'supervisor_li')->orderBy('name')->get();
        
        // Get lecturers assigned to each course
        $ipLecturers = LecturerCourseAssignment::where('course_type', 'IP')->with('lecturer')->get()->pluck('lecturer')->filter();
        $oshLecturers = LecturerCourseAssignment::where('course_type', 'OSH')->with('lecturer')->get()->pluck('lecturer')->filter();
        $ppeLecturers = LecturerCourseAssignment::where('course_type', 'PPE')->with('lecturer')->get()->pluck('lecturer')->filter();
        
        // If no lecturers assigned to course, show all lecturers
        $allLecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        if ($ipLecturers->isEmpty()) {
            $ipLecturers = $allLecturers;
        }
        if ($oshLecturers->isEmpty()) {
            $oshLecturers = $allLecturers;
        }
        if ($ppeLecturers->isEmpty()) {
            $ppeLecturers = $allLecturers;
        }

        return view('students.profile.show', compact(
            'student', 
            'courseAssignments',
            'atOptions',
            'icOptions',
            'supervisorLiOptions',
            'ipLecturers',
            'oshLecturers',
            'ppeLecturers'
        ));
    }

    /**
     * Show the form for creating a new student profile.
     */
    public function create(): View
    {
        $groups = WblGroup::orderBy('name')->get();
        $industryCoaches = User::where('role', 'industry')->orderBy('name')->get();

        return view('students.profile.create', compact('groups', 'industryCoaches'));
    }

    /**
     * Store a newly created student profile.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Ensure optional fields are always present, even if null
        $request->merge([
            'ic_id' => $request->input('ic_id', null),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'matric_no' => ['required', 'string', 'max:255', 'unique:students,matric_no'],
            'programme' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'exists:wbl_groups,id'],
            'ic_id' => ['nullable', 'exists:users,id'],
            'background' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('student-images', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Safely extract ic_id with null fallback
        $icId = !empty($validated['ic_id']) ? $validated['ic_id'] : null;

        Student::create([
            'name' => $validated['name'],
            'matric_no' => $validated['matric_no'],
            'programme' => $validated['programme'],
            'group_id' => $validated['group_id'],
            'user_id' => $user->id,
            'ic_id' => $icId,
            'background' => $validated['background'] ?? null,
            'image_path' => $validated['image_path'] ?? null,
        ]);

        return redirect()->route('students.profile.show')
            ->with('success', 'Profile created successfully.');
    }

    /**
     * Show the form for editing the student profile.
     */
    public function edit(Student $student): View
    {
        // Check authorization using policy
        $this->authorize('update', $student);

        $groups = WblGroup::orderBy('name')->get();
        $industryCoaches = User::where('role', 'industry')->orderBy('name')->get();
        
        // Get course assignments - ensure it's always a collection
        $courseAssignments = $student->courseAssignments()->with('lecturer')->get()->keyBy('course_type');
        
        // Get available options for each assignment type
        $atOptions = User::where('role', 'at')->orWhere('role', 'lecturer')->orderBy('name')->get();
        
        // Get all lecturers (used for Academic Advisor and as fallback for course assignments)
        $allLecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        
        // Get lecturers assigned to each course
        $ipLecturers = LecturerCourseAssignment::where('course_type', 'IP')->with('lecturer')->get()->pluck('lecturer')->filter();
        $oshLecturers = LecturerCourseAssignment::where('course_type', 'OSH')->with('lecturer')->get()->pluck('lecturer')->filter();
        $ppeLecturers = LecturerCourseAssignment::where('course_type', 'PPE')->with('lecturer')->get()->pluck('lecturer')->filter();
        $supervisorLiLecturers = LecturerCourseAssignment::where('course_type', 'Industrial Training')->with('lecturer')->get()->pluck('lecturer')->filter();
        
        // If no lecturers assigned to course, show all lecturers
        if ($ipLecturers->isEmpty()) {
            $ipLecturers = $allLecturers;
        }
        if ($oshLecturers->isEmpty()) {
            $oshLecturers = $allLecturers;
        }
        if ($ppeLecturers->isEmpty()) {
            $ppeLecturers = $allLecturers;
        }
        // Supervisor LI should come from lecturers only
        if ($supervisorLiLecturers->isEmpty()) {
            $supervisorLiOptions = $allLecturers;
        } else {
            $supervisorLiOptions = $supervisorLiLecturers;
        }
        
        // Academic Advisor should come from lecturers only
        $academicAdvisorOptions = $allLecturers;

        // Load resume inspection relationship
        $student->load('resumeInspection');

        return view('students.profile.edit', compact(
            'student', 
            'groups', 
            'industryCoaches',
            'courseAssignments',
            'atOptions',
            'supervisorLiOptions',
            'academicAdvisorOptions',
            'ipLecturers',
            'oshLecturers',
            'ppeLecturers'
        ));
    }

    /**
     * Update the student profile.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        // Check authorization
        $this->authorize('update', $student);

        // Ensure optional fields are always present, even if null
        $request->merge([
            'ic_id' => $request->input('ic_id', $student->ic_id),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'matric_no' => ['required', 'string', 'max:255', 'unique:students,matric_no,' . $student->id],
            'programme' => ['required', 'string', 'max:255'],
            'cgpa' => ['nullable', 'numeric', 'min:0', 'max:4.00'],
            'group_id' => ['required', 'exists:wbl_groups,id'],
            'mobile_phone' => ['nullable', 'string', 'max:20'],
            'ic_id' => ['nullable', 'exists:users,id'],
            'at_id' => ['nullable', 'exists:users,id'],
            'supervisor_li_id' => ['nullable', 'exists:users,id'],
            'academic_advisor_id' => ['nullable', 'exists:users,id'],
            'ip_lecturer_id' => ['nullable', 'exists:users,id'],
            'osh_lecturer_id' => ['nullable', 'exists:users,id'],
            'ppe_lecturer_id' => ['nullable', 'exists:users,id'],
            'background' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'resume' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // 5MB max
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($student->image_path) {
                Storage::disk('public')->delete($student->image_path);
            }
            $imagePath = $request->file('image')->store('student-images', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Handle resume PDF upload
        if ($request->hasFile('resume')) {
            // Delete old resume if exists
            if ($student->resume_pdf_path) {
                Storage::disk('public')->delete($student->resume_pdf_path);
            }
            $resumePath = $request->file('resume')->store('student-resumes', 'public');
            $validated['resume_pdf_path'] = $resumePath;
        }

        // Safely extract ids with null fallback
        $icId = !empty($validated['ic_id']) ? $validated['ic_id'] : null;
        $atId = !empty($validated['at_id']) ? $validated['at_id'] : null;
        $academicAdvisorId = !empty($validated['academic_advisor_id']) ? $validated['academic_advisor_id'] : null;

        // Update with null-safe values
        $updateData = [
            'name' => $validated['name'],
            'matric_no' => $validated['matric_no'],
            'programme' => $validated['programme'],
            'cgpa' => $validated['cgpa'] ?? null,
            'group_id' => $validated['group_id'],
            'mobile_phone' => $validated['mobile_phone'] ?? null,
            'ic_id' => $icId,
            'at_id' => $atId,
            'academic_advisor_id' => $academicAdvisorId,
            'background' => $validated['background'] ?? null,
        ];

        if (isset($validated['image_path'])) {
            $updateData['image_path'] = $validated['image_path'];
        }
        
        if (isset($validated['resume_pdf_path'])) {
            $updateData['resume_pdf_path'] = $validated['resume_pdf_path'];
        }

        $student->update($updateData);

        // Update course assignments
        // Supervisor LI for Industrial Training
        if (!empty($validated['supervisor_li_id'])) {
            StudentCourseAssignment::updateOrCreate(
                ['student_id' => $student->id, 'course_type' => 'Industrial Training'],
                ['lecturer_id' => $validated['supervisor_li_id']]
            );
        } else {
            StudentCourseAssignment::where('student_id', $student->id)
                ->where('course_type', 'Industrial Training')
                ->delete();
        }

        // IP Lecturer
        if (!empty($validated['ip_lecturer_id'])) {
            StudentCourseAssignment::updateOrCreate(
                ['student_id' => $student->id, 'course_type' => 'IP'],
                ['lecturer_id' => $validated['ip_lecturer_id']]
            );
        } else {
            StudentCourseAssignment::where('student_id', $student->id)
                ->where('course_type', 'IP')
                ->delete();
        }

        // OSH Lecturer
        if (!empty($validated['osh_lecturer_id'])) {
            StudentCourseAssignment::updateOrCreate(
                ['student_id' => $student->id, 'course_type' => 'OSH'],
                ['lecturer_id' => $validated['osh_lecturer_id']]
            );
        } else {
            StudentCourseAssignment::where('student_id', $student->id)
                ->where('course_type', 'OSH')
                ->delete();
        }

        // PPE Lecturer
        if (!empty($validated['ppe_lecturer_id'])) {
            StudentCourseAssignment::updateOrCreate(
                ['student_id' => $student->id, 'course_type' => 'PPE'],
                ['lecturer_id' => $validated['ppe_lecturer_id']]
            );
        } else {
            StudentCourseAssignment::where('student_id', $student->id)
                ->where('course_type', 'PPE')
                ->delete();
        }

        // FYP AT Assignment
        if (!empty($atId)) {
            StudentCourseAssignment::updateOrCreate(
                ['student_id' => $student->id, 'course_type' => 'FYP'],
                ['lecturer_id' => $atId]
            );
        } else {
            StudentCourseAssignment::where('student_id', $student->id)
                ->where('course_type', 'FYP')
                ->delete();
        }

        return redirect()->route('students.profile.show')
            ->with('success', 'Profile and assignments updated successfully.');
    }
}
