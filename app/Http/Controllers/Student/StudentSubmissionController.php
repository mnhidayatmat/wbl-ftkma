<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\StudentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class StudentSubmissionController extends Controller
{
    /**
     * Show the submission form for an assessment.
     */
    public function create(Assessment $assessment)
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('students.profile.create')
                ->with('error', 'Please create your student profile first.');
        }

        // Check if assessment requires submission
        if (!$assessment->requiresSubmission()) {
            return back()->with('error', 'This assessment does not require a submission.');
        }

        // Get existing submissions
        $submissions = StudentSubmission::where('assessment_id', $assessment->id)
            ->where('student_id', $student->id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        $attemptCount = $submissions->count();

        // Check if student can still submit
        if ($attemptCount >= $assessment->max_attempts) {
            return back()->with('error', 'You have reached the maximum number of attempts for this assessment.');
        }

        // Check if submission window is open
        if (!$assessment->isSubmissionOpen() && !$assessment->acceptsLateSubmission()) {
            return back()->with('error', 'The submission window for this assessment has closed.');
        }

        $isLate = $assessment->isLateSubmission();
        $latePenalty = $assessment->calculateLatePenalty();

        return view('student.submissions.create', compact(
            'assessment',
            'student',
            'submissions',
            'attemptCount',
            'isLate',
            'latePenalty'
        ));
    }

    /**
     * Store a new submission.
     */
    public function store(Request $request, Assessment $assessment)
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('students.profile.create')
                ->with('error', 'Please create your student profile first.');
        }

        // Check if assessment requires submission
        if (!$assessment->requiresSubmission()) {
            return back()->with('error', 'This assessment does not require a submission.');
        }

        // Get current attempt count
        $attemptCount = StudentSubmission::where('assessment_id', $assessment->id)
            ->where('student_id', $student->id)
            ->count();

        // Check max attempts
        if ($attemptCount >= $assessment->max_attempts) {
            return back()->with('error', 'You have reached the maximum number of attempts.');
        }

        // Check submission window
        if (!$assessment->isSubmissionOpen() && !$assessment->acceptsLateSubmission()) {
            return back()->with('error', 'The submission window has closed.');
        }

        // Build validation rules
        $allowedExtensions = $assessment->getAllowedExtensions();
        $maxSize = $assessment->max_file_size_mb * 1024; // Convert to KB for validation

        $rules = [
            'file' => [
                'required',
                File::types($allowedExtensions)->max($maxSize),
            ],
            'student_remarks' => ['nullable', 'string', 'max:1000'],
        ];

        if ($assessment->require_declaration) {
            $rules['declaration'] = ['required', 'accepted'];
        }

        $validated = $request->validate($rules, [
            'file.required' => 'Please select a file to upload.',
            'file.max' => 'The file size must not exceed ' . $assessment->max_file_size_mb . 'MB.',
            'file.mimetypes' => 'The file type is not allowed. Allowed types: ' . implode(', ', $allowedExtensions),
            'declaration.required' => 'You must accept the academic integrity declaration.',
            'declaration.accepted' => 'You must accept the academic integrity declaration.',
        ]);

        // Store the file
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = $student->id . '_' . $assessment->id . '_' . ($attemptCount + 1) . '_' . time() . '.' . $extension;

        $path = $file->storeAs(
            'submissions/' . $assessment->course_code . '/' . $assessment->id,
            $fileName,
            'local'
        );

        // Check if late
        $isLate = $assessment->isLateSubmission();
        $latePenalty = $isLate ? $assessment->calculateLatePenalty() : null;

        // Create submission record
        StudentSubmission::create([
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'file_path' => $path,
            'file_name' => $fileName,
            'original_name' => $originalName,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'attempt_number' => $attemptCount + 1,
            'is_late' => $isLate,
            'late_penalty_applied' => $latePenalty,
            'declaration_accepted' => $assessment->require_declaration ? true : false,
            'student_remarks' => $validated['student_remarks'] ?? null,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Redirect based on course code
        $redirectRoute = match($assessment->course_code) {
            'FYP' => 'student.fyp.overview',
            'PPE' => 'student.ppe.overview',
            'IP' => 'student.ip.overview',
            'OSH' => 'student.osh.overview',
            'LI' => 'student.li.overview',
            default => 'dashboard',
        };

        return redirect()->route($redirectRoute)
            ->with('success', 'Your submission has been uploaded successfully.' .
                ($isLate ? ' Note: This is a late submission with ' . number_format($latePenalty, 1) . '% penalty.' : ''));
    }

    /**
     * Download a submission file.
     */
    public function download(StudentSubmission $submission)
    {
        $student = auth()->user()->student;

        // Check if this is the student's submission
        if (!$student || $submission->student_id !== $student->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!Storage::disk('local')->exists($submission->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('local')->download(
            $submission->file_path,
            $submission->original_name
        );
    }
}
