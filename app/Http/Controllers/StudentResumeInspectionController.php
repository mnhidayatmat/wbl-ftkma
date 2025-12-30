<?php

namespace App\Http\Controllers;

use App\Models\ReferenceSample;
use App\Models\ResumeInspectionHistory;
use App\Models\Student;
use App\Models\StudentResumeInspection;
use App\Models\WblGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentResumeInspectionController extends Controller
{
    /**
     * Display student's own resume inspection page.
     */
    public function studentIndex(): View
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            // Check if user is an admin who switched to student role but doesn't have a student profile
            if ($user->hasRole('admin') && $user->getActiveRole() === 'student') {
                abort(403, 'You need to have a student profile to access this page. Please switch back to admin role or contact the administrator.');
            }
            abort(404, 'Student profile not found.');
        }

        // Check if student is in a completed group
        $isInCompletedGroup = $student->isInCompletedGroup();

        // Get active reference samples grouped by category
        $referenceSamples = ReferenceSample::active()
            ->ordered()
            ->get()
            ->groupBy('category');

        $inspection = $student->resumeInspection;
        if (! $inspection) {
            // Students in completed groups cannot create new inspections
            if ($isInCompletedGroup) {
                return view('resume-inspection.student.index', [
                    'student' => $student,
                    'inspection' => null,
                    'isInCompletedGroup' => true,
                    'referenceSamples' => $referenceSamples,
                    'history' => collect(), // Empty collection
                ]);
            }
            $inspection = StudentResumeInspection::create([
                'student_id' => $student->id,
                'status' => 'PENDING',
            ]);
        }

        // Load history logs ordered by most recent first
        $history = ResumeInspectionHistory::where('inspection_id', $inspection->id)
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('resume-inspection.student.index', [
            'student' => $student,
            'inspection' => $inspection->load('reviewer'),
            'isInCompletedGroup' => $isInCompletedGroup,
            'referenceSamples' => $referenceSamples,
            'history' => $history,
        ]);
    }

    /**
     * Upload combined resume and portfolio (Student action).
     */
    public function studentUploadDocument(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            // Check if user is an admin who switched to student role but doesn't have a student profile
            if ($user->hasRole('admin') && $user->getActiveRole() === 'student') {
                abort(403, 'You need to have a student profile to access this page. Please switch back to admin role or contact the administrator.');
            }
            abort(404, 'Student profile not found.');
        }

        // Prevent uploads for students in completed groups
        if ($student->isInCompletedGroup()) {
            return redirect()->back()->with('error', 'Your WBL group has been completed. You can no longer upload or modify documents. Data remains available for viewing only.');
        }

        try {
            $validated = $request->validate([
                'document' => ['required', 'file', 'mimes:pdf', 'max:15360'], // 15MB max (resume + portfolio combined)
            ], [
                'document.required' => 'Please select a PDF file to upload.',
                'document.file' => 'The uploaded file is invalid.',
                'document.mimes' => 'Only PDF files are allowed.',
                'document.max' => 'The file size must not exceed 15MB.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }

        $inspection = $student->resumeInspection;
        if (! $inspection) {
            $inspection = StudentResumeInspection::create([
                'student_id' => $student->id,
                'status' => 'PENDING',
            ]);
        }

        // Check if checklist is complete
        if (! $inspection->isChecklistComplete()) {
            return redirect()->back()
                ->with('error', 'Please complete all compliance declaration checklist items before submitting your document.')
                ->withInput();
        }

        // Delete old files if exist
        if ($inspection->resume_file_path && Storage::exists($inspection->resume_file_path)) {
            Storage::delete($inspection->resume_file_path);
        }
        if ($inspection->portfolio_file_path && Storage::exists($inspection->portfolio_file_path)) {
            Storage::delete($inspection->portfolio_file_path);
        }

        try {
            // Generate custom filename: Resume_StudentName.pdf
            $studentName = preg_replace('/[^A-Za-z0-9\s]/', '', $student->name); // Remove special characters
            $studentName = str_replace(' ', '_', trim($studentName)); // Replace spaces with underscores
            $filename = 'Resume_' . $studentName . '.pdf';

            // Store new combined document with custom filename
            $path = $request->file('document')->storeAs('resumes', $filename, 'public');

            if (! $path) {
                throw new \Exception('Failed to store the file. Please try again.');
            }

            $inspection->update([
                'resume_file_path' => $path,
                'portfolio_file_path' => null, // No longer needed
                'status' => 'PENDING', // Reset to pending when new file uploaded
                'coordinator_comment' => null,
                'student_reply' => null,
                'student_replied_at' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'approved_at' => null,
            ]);

            // Get file size for feedback
            $fileSize = Storage::disk('public')->size($path);
            $fileSizeFormatted = $this->formatBytes($fileSize);

            Log::info('Student Combined Resume & Portfolio Uploaded', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'document_path' => $path,
                'file_size' => $fileSize,
                'checklist_confirmed_at' => $inspection->checklist_confirmed_at,
            ]);

            return redirect()->back()
                ->with('success', 'Submission received. You confirmed compliance with all submission guidelines. Waiting for coordinator review.')
                ->with('submission_feedback', [
                    'file_name' => basename($path),
                    'file_size' => $fileSizeFormatted,
                    'submitted_at' => now(),
                    'status' => 'PENDING',
                ]);
        } catch (\Exception $e) {
            Log::error('Error uploading student document', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to upload document: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Save compliance checklist (Student action).
     */
    public function saveChecklist(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        $validated = $request->validate([
            'checklist_merged_pdf' => ['required', 'accepted'],
            'checklist_document_order' => ['required', 'accepted'],
            'checklist_resume_concise' => ['required', 'accepted'],
            'checklist_achievements_highlighted' => ['required', 'accepted'],
            'checklist_poster_includes_required' => ['required', 'accepted'],
            'checklist_poster_pages_limit' => ['required', 'accepted'],
            'checklist_own_work_ready' => ['required', 'accepted'],
        ], [
            'checklist_merged_pdf.accepted' => 'You must confirm that you have merged your Resume and Poster Projects into ONE PDF file.',
            'checklist_document_order.accepted' => 'You must confirm the document order (Resume → PD3 → PD4 → PD5).',
            'checklist_resume_concise.accepted' => 'You must confirm that your Resume is concise (1–2 pages) and uses space efficiently.',
            'checklist_achievements_highlighted.accepted' => 'You must confirm that you have highlighted your ACHIEVEMENTS and CONTRIBUTIONS.',
            'checklist_poster_includes_required.accepted' => 'You must confirm that each Poster includes all required elements.',
            'checklist_poster_pages_limit.accepted' => 'You must confirm that the total poster pages do NOT exceed 6 pages.',
            'checklist_own_work_ready.accepted' => 'You must confirm that this document is your own work and ready for coordinator review.',
        ]);

        $inspection = $student->resumeInspection;
        if (! $inspection) {
            $inspection = StudentResumeInspection::create([
                'student_id' => $student->id,
                'status' => 'PENDING',
            ]);
        }

        $inspection->update([
            'checklist_merged_pdf' => true,
            'checklist_document_order' => true,
            'checklist_resume_concise' => true,
            'checklist_achievements_highlighted' => true,
            'checklist_poster_includes_required' => true,
            'checklist_poster_pages_limit' => true,
            'checklist_own_work_ready' => true,
            'checklist_confirmed_at' => now(),
            'checklist_ip_address' => $request->ip(),
        ]);

        Log::info('Student Compliance Checklist Confirmed', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'inspection_id' => $inspection->id,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Compliance checklist saved successfully. You may now upload your document.');
    }

    /**
     * Student reply to coordinator comment.
     */
    public function studentReply(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            // Check if user is an admin who switched to student role but doesn't have a student profile
            if ($user->hasRole('admin') && $user->getActiveRole() === 'student') {
                abort(403, 'You need to have a student profile to access this page. Please switch back to admin role or contact the administrator.');
            }
            abort(404, 'Student profile not found.');
        }

        $validated = $request->validate([
            'reply' => ['required', 'string', 'max:2000'],
        ]);

        $inspection = $student->resumeInspection;
        if (! $inspection) {
            abort(404, 'Resume inspection not found.');
        }

        if (! $inspection->coordinator_comment) {
            return redirect()->back()->with('error', 'No coordinator comment to reply to.');
        }

        $previousReply = $inspection->student_reply;

        $inspection->update([
            'student_reply' => $validated['reply'],
            'student_replied_at' => now(),
        ]);

        // Log student reply to history
        ResumeInspectionHistory::log(
            $inspection->id,
            $previousReply ? 'STUDENT_REPLY_UPDATED' : 'STUDENT_REPLY',
            $inspection->status,
            $validated['reply'],
            $previousReply,
            [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'is_student_action' => true,
            ]
        );

        Log::info('Student Replied to Coordinator Comment', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'inspection_id' => $inspection->id,
        ]);

        return redirect()->back()->with('success', 'Your reply has been submitted successfully.');
    }

    /**
     * Display coordinator/admin review page.
     */
    public function coordinatorIndex(Request $request): View
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get all students with their inspections (including those without inspections)
        $query = Student::with(['resumeInspection.reviewer', 'group']);

        // Filter by group
        if ($request->filled('group')) {
            $query->where('group_id', $request->group);
        }

        // Search by name or matric number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->get();

        // Filter by status (if provided)
        if ($request->filled('status')) {
            $status = $request->status;
            $students = $students->filter(function ($student) use ($status) {
                if (! $student->resumeInspection) {
                    return $status === 'NOT_SUBMITTED';
                }

                return $student->resumeInspection->status === $status;
            });
        }

        // Get all groups for filter dropdown
        $groups = WblGroup::orderBy('name')->get();

        // Calculate statistics
        $allInspections = StudentResumeInspection::with('student')->get();
        $stats = [
            'total' => Student::count(),
            'not_submitted' => Student::whereDoesntHave('resumeInspection', function ($q) {
                $q->whereNotNull('resume_file_path');
            })->count(),
            'pending' => $allInspections->where('status', 'PENDING')->count(),
            'approved' => $allInspections->where('status', 'PASSED')->count(),
            'revision_required' => $allInspections->where('status', 'REVISION_REQUIRED')->count(),
        ];

        return view('resume-inspection.coordinator.index', [
            'students' => $students,
            'groups' => $groups,
            'stats' => $stats,
            'filters' => [
                'group' => $request->group,
                'status' => $request->status,
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Display coordinator review page for a specific inspection.
     */
    public function coordinatorReview(StudentResumeInspection $inspection): View
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $inspection->load(['student.group', 'reviewer']);

        // Load history logs ordered by most recent first
        $history = ResumeInspectionHistory::where('inspection_id', $inspection->id)
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('resume-inspection.coordinator.review', [
            'inspection' => $inspection,
            'student' => $inspection->student,
            'history' => $history,
        ]);
    }

    /**
     * Review resume (Coordinator/Admin action).
     */
    public function review(Request $request, StudentResumeInspection $inspection): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:PASSED,REVISION_REQUIRED'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        // Store previous values for history
        $previousStatus = $inspection->status;
        $previousComment = $inspection->coordinator_comment;
        $isNewComment = empty($previousComment) && ! empty($validated['comment']);
        $isUpdatedComment = ! empty($previousComment) && ! empty($validated['comment']) && $previousComment !== $validated['comment'];

        $updateData = [
            'status' => $validated['status'],
            'coordinator_comment' => $validated['comment'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ];

        // Set approved_at timestamp when status is PASSED
        if ($validated['status'] === 'PASSED') {
            $updateData['approved_at'] = now();
        } else {
            // Clear approved_at if revision is required
            $updateData['approved_at'] = null;
        }

        $inspection->update($updateData);

        // Determine action type for history
        $action = 'REVIEWED';
        if ($validated['status'] === 'PASSED') {
            $action = 'APPROVED';
        } elseif ($validated['status'] === 'REVISION_REQUIRED') {
            $action = 'REVISION_REQUESTED';
        }

        // Log comment changes separately if applicable
        if ($isNewComment) {
            $action = 'COMMENT_ADDED';
        } elseif ($isUpdatedComment) {
            $action = 'COMMENT_UPDATED';
        }

        // Create history log entry
        ResumeInspectionHistory::log(
            $inspection->id,
            $action,
            $validated['status'],
            $validated['comment'] ?? null,
            $previousComment,
            [
                'previous_status' => $previousStatus,
                'status_changed' => $previousStatus !== $validated['status'],
                'comment_changed' => $previousComment !== ($validated['comment'] ?? null),
            ]
        );

        $actionLabel = $validated['status'] === 'PASSED' ? 'Approved' : 'Requested Revision';

        Log::info('Resume Inspection Reviewed', [
            'inspection_id' => $inspection->id,
            'student_id' => $inspection->student_id,
            'student_name' => $inspection->student->name,
            'status' => $validated['status'],
            'action' => $actionLabel,
            'reviewed_by' => auth()->id(),
            'reviewed_by_name' => auth()->user()->name,
        ]);

        $message = $validated['status'] === 'PASSED'
            ? 'Resume approved successfully. Student can now apply for placements.'
            : 'Revision requested. Student will be notified to resubmit.';

        return redirect()->route('coordinator.resume.index')
            ->with('success', $message);
    }

    /**
     * Reset resume inspection for a student (Coordinator/Admin action).
     */
    public function reset(StudentResumeInspection $inspection): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Delete file from storage if exists
        if ($inspection->resume_file_path && Storage::disk('public')->exists($inspection->resume_file_path)) {
            Storage::disk('public')->delete($inspection->resume_file_path);
        }
        if ($inspection->portfolio_file_path && Storage::disk('public')->exists($inspection->portfolio_file_path)) {
            Storage::disk('public')->delete($inspection->portfolio_file_path);
        }

        // Store previous values for history
        $previousStatus = $inspection->status;
        $previousComment = $inspection->coordinator_comment;

        // Reset all fields
        $inspection->update([
            'resume_file_path' => null,
            'portfolio_file_path' => null,
            'status' => 'PENDING',
            'coordinator_comment' => null,
            'student_reply' => null,
            'student_replied_at' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'approved_at' => null,
            'checklist_merged_pdf' => false,
            'checklist_document_order' => false,
            'checklist_resume_concise' => false,
            'checklist_achievements_highlighted' => false,
            'checklist_poster_includes_required' => false,
            'checklist_poster_pages_limit' => false,
            'checklist_own_work_ready' => false,
            'checklist_confirmed_at' => null,
            'checklist_ip_address' => null,
        ]);

        // Create history log entry
        ResumeInspectionHistory::log(
            $inspection->id,
            'RESET',
            'PENDING',
            'Resume inspection reset by coordinator/admin',
            $previousComment,
            [
                'previous_status' => $previousStatus,
                'reset_by' => auth()->id(),
                'reset_by_name' => auth()->user()->name,
            ]
        );

        Log::info('Resume Inspection Reset', [
            'inspection_id' => $inspection->id,
            'student_id' => $inspection->student_id,
            'student_name' => $inspection->student->name,
            'previous_status' => $previousStatus,
            'reset_by' => auth()->id(),
            'reset_by_name' => auth()->user()->name,
        ]);

        return redirect()->route('coordinator.resume.index')
            ->with('success', 'Resume inspection has been reset successfully. Student can now resubmit their document.');
    }

    /**
     * View document in iframe (combined resume & portfolio).
     */
    public function viewDocument(StudentResumeInspection $inspection)
    {
        $user = auth()->user();

        // Student can view their own, Coordinator/Admin can view any
        if ($user->isStudent()) {
            // Check if user has a student profile
            if (! $user->student) {
                // Check if user is an admin who switched to student role but doesn't have a student profile
                if ($user->hasRole('admin') && $user->getActiveRole() === 'student') {
                    abort(403, 'You need to have a student profile to access this page. Please switch back to admin role or contact the administrator.');
                }
                abort(404, 'Student profile not found.');
            }
            if ($user->student->id !== $inspection->student_id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif (! $user->isAdmin() && ! $user->isCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        if (! $inspection->resume_file_path) {
            abort(404, 'Document file not found.');
        }

        // Check if file exists in public disk
        if (! Storage::disk('public')->exists($inspection->resume_file_path)) {
            Log::error('Resume file not found', [
                'inspection_id' => $inspection->id,
                'file_path' => $inspection->resume_file_path,
                'storage_path' => storage_path('app/public/'.$inspection->resume_file_path),
            ]);
            abort(404, 'Document file not found at: '.$inspection->resume_file_path);
        }

        $filePath = Storage::disk('public')->path($inspection->resume_file_path);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($inspection->resume_file_path).'"',
        ]);
    }

    /**
     * Download document (combined resume & portfolio).
     */
    public function downloadDocument(StudentResumeInspection $inspection)
    {
        $user = auth()->user();

        // Student can download their own, Coordinator/Admin can download any
        if ($user->isStudent()) {
            // Check if user has a student profile
            if (! $user->student) {
                // Check if user is an admin who switched to student role but doesn't have a student profile
                if ($user->hasRole('admin') && $user->getActiveRole() === 'student') {
                    abort(403, 'You need to have a student profile to access this page. Please switch back to admin role or contact the administrator.');
                }
                abort(404, 'Student profile not found.');
            }
            if ($user->student->id !== $inspection->student_id) {
                abort(403, 'Unauthorized access.');
            }
        }

        if (! $inspection->resume_file_path) {
            abort(404, 'Document file not found.');
        }

        // Check if file exists in public disk
        if (! Storage::disk('public')->exists($inspection->resume_file_path)) {
            Log::error('Resume file not found', [
                'inspection_id' => $inspection->id,
                'file_path' => $inspection->resume_file_path,
                'storage_path' => storage_path('app/public/'.$inspection->resume_file_path),
            ]);
            abort(404, 'Document file not found at: '.$inspection->resume_file_path);
        }

        // Get the original filename or generate one
        $filename = basename($inspection->resume_file_path);
        if (empty($filename) || $filename === 'resumes') {
            $filename = 'resume_portfolio_'.$inspection->student_id.'_'.now()->format('Y-m-d').'.pdf';
        }

        return Storage::disk('public')->download($inspection->resume_file_path, $filename);
    }

    /**
     * Delete uploaded document (Student action).
     */
    public function studentDeleteDocument(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            // Check if user is an admin who switched to student role but doesn't have a student profile
            if ($user->hasRole('admin') && $user->getActiveRole() === 'student') {
                abort(403, 'You need to have a student profile to access this page. Please switch back to admin role or contact the administrator.');
            }
            abort(404, 'Student profile not found.');
        }

        $inspection = $student->resumeInspection;
        if (! $inspection) {
            return redirect()->back()->with('error', 'No document found to delete.');
        }

        if (! $inspection->resume_file_path) {
            return redirect()->back()->with('error', 'No document uploaded.');
        }

        // Delete file from storage
        if (Storage::exists($inspection->resume_file_path)) {
            Storage::delete($inspection->resume_file_path);
        }

        // Update inspection record
        $inspection->update([
            'resume_file_path' => null,
            'status' => 'PENDING',
            'coordinator_comment' => null,
            'student_reply' => null,
            'student_replied_at' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        Log::info('Student Document Deleted', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'inspection_id' => $inspection->id,
        ]);

        return redirect()->back()->with('success', 'Document deleted successfully. You can upload a new document.');
    }

    /**
     * Format file size in human-readable format.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }

    /**
     * Download sample PDF for reference.
     */
    public function downloadSample(int $sample)
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        // Define sample file paths
        $samples = [
            1 => 'samples/sample-1-resume-structure.pdf',
            2 => 'samples/sample-2-poster-layout.pdf',
            3 => 'samples/sample-3-achievement-contribution.pdf',
        ];

        if (! isset($samples[$sample])) {
            abort(404, 'Sample not found.');
        }

        $filePath = public_path($samples[$sample]);

        // If file doesn't exist, return a placeholder message
        if (! file_exists($filePath)) {
            // Create samples directory if it doesn't exist
            $samplesDir = public_path('samples');
            if (! is_dir($samplesDir)) {
                mkdir($samplesDir, 0755, true);
            }

            // Return a message that sample files need to be added
            return redirect()->back()->with('info', 'Sample files are being prepared. Please check back later or contact the coordinator for reference samples.');
        }

        return response()->download($filePath, basename($samples[$sample]));
    }
}
