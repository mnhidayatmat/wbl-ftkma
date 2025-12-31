<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\WorkplaceIssueAttachment;
use App\Models\WorkplaceIssueReport;
use App\Models\WorkplaceIssueReportHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WorkplaceIssueReportController extends Controller
{
    /**
     * Get the programme filter for WBL coordinators.
     */
    private function getWblCoordinatorProgrammeFilter(): ?string
    {
        $user = Auth::user();

        if ($user->isBtaWblCoordinator()) {
            return 'Bachelor of Mechanical Engineering Technology (Automotive) with Honours';
        } elseif ($user->isBtdWblCoordinator()) {
            return 'Bachelor of Mechanical Engineering Technology (Design and Analysis) with Honours';
        } elseif ($user->isBtgWblCoordinator()) {
            return 'Bachelor of Mechanical Engineering Technology (Oil and Gas) with Honours';
        }

        return null;
    }

    /**
     * Display a listing of workplace issue reports.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = WorkplaceIssueReport::with(['student.user', 'group', 'company', 'assignedTo']);

        // Role-based filtering
        if ($user->isStudent()) {
            // Students can only see their own reports
            $student = $user->student;
            if (!$student) {
                abort(403, 'Student record not found');
            }
            $query->where('student_id', $student->id);
        } elseif ($user->isIndustry()) {
            // Industry coaches can see reports from their students
            $studentIds = $user->assignedStudents()->pluck('id');
            $query->whereIn('student_id', $studentIds);
        } elseif ($user->isWblCoordinator()) {
            // WBL coordinators can only see reports from students in their programme
            $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
            if ($programmeFilter) {
                $studentIds = Student::where('programme', $programmeFilter)->pluck('id');
                $query->whereIn('student_id', $studentIds);
            }
        }
        // Admins and coordinators can see all reports (no filter)

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('matric_no', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'submitted_at');
        $sortDirection = $request->get('sort_dir', 'desc');

        $allowedSortColumns = ['submitted_at', 'status', 'severity', 'title'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'submitted_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $reports = $query->orderBy($sortBy, $sortDirection)->paginate(15);

        // Statistics for dashboard
        $statistics = [
            'total' => WorkplaceIssueReport::count(),
            'new' => WorkplaceIssueReport::where('status', 'new')->count(),
            'under_review' => WorkplaceIssueReport::where('status', 'under_review')->count(),
            'in_progress' => WorkplaceIssueReport::where('status', 'in_progress')->count(),
            'resolved' => WorkplaceIssueReport::where('status', 'resolved')->count(),
            'closed' => WorkplaceIssueReport::where('status', 'closed')->count(),
        ];

        return view('workplace-issues.index', compact('reports', 'statistics'));
    }

    /**
     * Show the form for creating a new workplace issue report.
     */
    public function create(): View
    {
        $user = Auth::user();

        if ($user->isStudent()) {
            $student = $user->student;
            if (!$student) {
                abort(403, 'Student record not found');
            }
        } else {
            abort(403, 'Only students can create workplace issue reports');
        }

        return view('workplace-issues.create', compact('student'));
    }

    /**
     * Store a newly created workplace issue report.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->isStudent()) {
            abort(403, 'Only students can submit workplace issue reports');
        }

        $student = $user->student;
        if (!$student) {
            abort(403, 'Student record not found');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:safety_health,harassment_discrimination,work_environment,supervision_guidance,custom',
            'custom_category' => 'nullable|required_if:category,custom|string|max:255',
            'severity' => 'required|in:low,medium,high,critical',
            'location' => 'nullable|string|max:255',
            'incident_date' => 'nullable|date',
            'incident_time' => 'nullable|date_format:H:i',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            // Create the issue report
            $report = WorkplaceIssueReport::create([
                'student_id' => $student->id,
                'group_id' => $student->group_id,
                'company_id' => $student->company_id, // Auto-link to student's current company
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'custom_category' => $validated['custom_category'] ?? null,
                'severity' => $validated['severity'],
                'location' => $validated['location'] ?? null,
                'incident_date' => $validated['incident_date'] ?? null,
                'incident_time' => $validated['incident_time'] ?? null,
                'status' => 'new',
                'submitted_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('workplace-issues/' . $report->id);

                    WorkplaceIssueAttachment::create([
                        'issue_report_id' => $report->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            // Log the creation in history
            WorkplaceIssueReportHistory::log(
                $report->id,
                'CREATED',
                'new',
                null,
                null,
                ['severity' => $validated['severity'], 'category' => $validated['category']]
            );

            DB::commit();

            return redirect()
                ->route('workplace-issues.show', $report)
                ->with('success', 'Your workplace issue report has been submitted successfully. The WBL coordinator will review it shortly.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to submit the report. Please try again.');
        }
    }

    /**
     * Display the specified workplace issue report.
     */
    public function show(WorkplaceIssueReport $workplaceIssue): View
    {
        $user = Auth::user();

        // Authorization check
        if ($user->isStudent()) {
            $student = $user->student;
            if (!$student || $workplaceIssue->student_id !== $student->id) {
                abort(403, 'Unauthorized access');
            }
        } elseif ($user->isIndustry()) {
            $studentIds = $user->assignedStudents()->pluck('id');
            if (!$studentIds->contains($workplaceIssue->student_id)) {
                abort(403, 'Unauthorized access');
            }
        } elseif ($user->isWblCoordinator()) {
            // WBL coordinators can only view reports from their programme
            $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
            if ($programmeFilter) {
                $student = $workplaceIssue->student;
                if (!$student || $student->programme !== $programmeFilter) {
                    abort(403, 'You can only view reports from students in your programme');
                }
            }
        }

        $workplaceIssue->load([
            'student.user',
            'student.company',
            'group',
            'attachments.uploadedBy',
            'history.user',
            'assignedTo',
            'reviewedBy',
            'resolvedBy',
            'closedBy'
        ]);

        return view('workplace-issues.show', compact('workplaceIssue'));
    }

    /**
     * Update the status and add coordinator comments.
     */
    public function update(Request $request, WorkplaceIssueReport $workplaceIssue): RedirectResponse
    {
        $user = Auth::user();

        // Only coordinators, admins, and WBL coordinators can update
        if (!$user->isAdmin() && !$user->hasRole('coordinator') && !$user->isWblCoordinator()) {
            abort(403, 'Unauthorized action');
        }

        // WBL coordinators can only update reports from their programme
        if ($user->isWblCoordinator()) {
            $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
            if ($programmeFilter) {
                $student = $workplaceIssue->student;
                if (!$student || $student->programme !== $programmeFilter) {
                    abort(403, 'You can only update reports from students in your programme');
                }
            }
        }

        $validated = $request->validate([
            'status' => 'required|in:new,under_review,in_progress,resolved,closed',
            'coordinator_comment' => 'nullable|string',
            'resolution_notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $workplaceIssue->status;
            $oldComment = $workplaceIssue->coordinator_comment;

            // Update the report
            $updateData = [
                'status' => $validated['status'],
            ];

            if ($request->filled('coordinator_comment')) {
                $updateData['coordinator_comment'] = $validated['coordinator_comment'];
            }

            if ($request->filled('resolution_notes')) {
                $updateData['resolution_notes'] = $validated['resolution_notes'];
            }

            if ($request->filled('assigned_to')) {
                $updateData['assigned_to'] = $validated['assigned_to'];
            }

            // Update status timestamps
            if ($validated['status'] === 'under_review' && $workplaceIssue->status !== 'under_review') {
                $updateData['reviewed_at'] = now();
                $updateData['reviewed_by'] = $user->id;
            }

            if ($validated['status'] === 'in_progress' && $workplaceIssue->status !== 'in_progress') {
                $updateData['in_progress_at'] = now();
            }

            if ($validated['status'] === 'resolved' && $workplaceIssue->status !== 'resolved') {
                $updateData['resolved_at'] = now();
                $updateData['resolved_by'] = $user->id;
            }

            if ($validated['status'] === 'closed' && $workplaceIssue->status !== 'closed') {
                $updateData['closed_at'] = now();
                $updateData['closed_by'] = $user->id;
            }

            $workplaceIssue->update($updateData);

            // Log status change
            if ($oldStatus !== $validated['status']) {
                WorkplaceIssueReportHistory::log(
                    $workplaceIssue->id,
                    'STATUS_CHANGED',
                    $validated['status'],
                    null,
                    null,
                    ['old_status' => $oldStatus, 'new_status' => $validated['status']]
                );
            }

            // Log comment update
            if ($request->filled('coordinator_comment') && $oldComment !== $validated['coordinator_comment']) {
                WorkplaceIssueReportHistory::log(
                    $workplaceIssue->id,
                    $oldComment ? 'COMMENT_UPDATED' : 'COMMENT_ADDED',
                    $workplaceIssue->status,
                    $validated['coordinator_comment'],
                    $oldComment
                );
            }

            // Log assignment
            if ($request->filled('assigned_to') && $workplaceIssue->assigned_to !== $validated['assigned_to']) {
                WorkplaceIssueReportHistory::log(
                    $workplaceIssue->id,
                    'ASSIGNED',
                    $workplaceIssue->status,
                    null,
                    null,
                    ['assigned_to_user_id' => $validated['assigned_to']]
                );
            }

            DB::commit();

            return back()->with('success', 'Report updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update the report. Please try again.');
        }
    }

    /**
     * Download an attachment.
     */
    public function downloadAttachment(WorkplaceIssueAttachment $attachment)
    {
        $user = Auth::user();
        $report = $attachment->issueReport;

        // Authorization check
        if ($user->isStudent()) {
            $student = $user->student;
            if (!$student || $report->student_id !== $student->id) {
                abort(403, 'Unauthorized access');
            }
        } elseif ($user->isIndustry()) {
            $studentIds = $user->assignedStudents()->pluck('id');
            if (!$studentIds->contains($report->student_id)) {
                abort(403, 'Unauthorized access');
            }
        } elseif ($user->isWblCoordinator()) {
            // WBL coordinators can only download attachments from their programme students
            $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
            if ($programmeFilter) {
                $student = $report->student;
                if (!$student || $student->programme !== $programmeFilter) {
                    abort(403, 'You can only access attachments from students in your programme');
                }
            }
        }

        if (!Storage::exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::download($attachment->file_path, $attachment->file_name);
    }

    /**
     * Delete an attachment (coordinators only).
     */
    public function deleteAttachment(WorkplaceIssueAttachment $attachment): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->hasRole('coordinator') && !$user->isWblCoordinator()) {
            abort(403, 'Unauthorized action');
        }

        // WBL coordinators can only delete attachments from their programme students
        if ($user->isWblCoordinator()) {
            $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
            if ($programmeFilter) {
                $report = $attachment->issueReport;
                $student = $report->student;
                if (!$student || $student->programme !== $programmeFilter) {
                    abort(403, 'You can only delete attachments from students in your programme');
                }
            }
        }

        DB::beginTransaction();
        try {
            // Delete the file from storage
            if (Storage::exists($attachment->file_path)) {
                Storage::delete($attachment->file_path);
            }

            // Delete the record
            $attachment->delete();

            DB::commit();

            return back()->with('success', 'Attachment deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete attachment');
        }
    }

    /**
     * Store student feedback on coordinator's response.
     */
    public function storeFeedback(Request $request, WorkplaceIssueReport $workplaceIssue): RedirectResponse
    {
        $user = Auth::user();

        // Only students can provide feedback
        if (!$user->isStudent()) {
            abort(403, 'Only students can provide feedback');
        }

        // Verify the student owns this report
        $student = $user->student;
        if (!$student || $workplaceIssue->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        // Don't allow feedback on closed reports
        if ($workplaceIssue->isClosed()) {
            return back()->with('error', 'Cannot provide feedback on closed reports');
        }

        // Only allow feedback if coordinator has responded
        if (!$workplaceIssue->coordinator_comment && !$workplaceIssue->resolution_notes) {
            return back()->with('error', 'You can only provide feedback after the coordinator has responded');
        }

        $validated = $request->validate([
            'student_feedback' => 'required|string|max:2000',
        ]);

        DB::beginTransaction();
        try {
            $oldFeedback = $workplaceIssue->student_feedback;

            // Update the feedback
            $workplaceIssue->update([
                'student_feedback' => $validated['student_feedback'],
                'student_feedback_at' => now(),
            ]);

            // Log the feedback in history
            WorkplaceIssueReportHistory::log(
                $workplaceIssue->id,
                $oldFeedback ? 'COMMENT_UPDATED' : 'COMMENT_ADDED',
                $workplaceIssue->status,
                'Student feedback: ' . $validated['student_feedback'],
                $oldFeedback ? 'Previous feedback: ' . $oldFeedback : null,
                ['type' => 'student_feedback']
            );

            DB::commit();

            return back()->with('success', 'Your feedback has been submitted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit feedback. Please try again.');
        }
    }
}
