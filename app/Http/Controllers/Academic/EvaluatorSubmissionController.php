<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\StudentSubmission;
use Illuminate\Support\Facades\Storage;

class EvaluatorSubmissionController extends Controller
{
    /**
     * Check if user is authorized to view a submission.
     */
    private function isAuthorized(StudentSubmission $submission): bool
    {
        $user = auth()->user();
        $student = $submission->student;

        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // AT can view their assigned students
        if ($user->isAt() && $student->at_id === $user->id) {
            return true;
        }

        // IC can view their assigned students
        if ($user->isIndustry() && $student->ic_id === $user->id) {
            return true;
        }

        // Lecturer can view students in their courses
        if ($user->isLecturer()) {
            return true; // Simplified - could add course-specific check
        }

        // Coordinator can view all in their programme
        if ($user->hasAnyRole(['coordinator', 'fyp_coordinator', 'ppe_coordinator', 'osh_coordinator', 'ip_coordinator', 'li_coordinator'])) {
            return true;
        }

        // Supervisor LI can view their assigned students
        if ($user->hasRole('supervisor_li') && $student->supervisor_li_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Download a student submission file (for evaluators).
     */
    public function download(StudentSubmission $submission)
    {
        if (! $this->isAuthorized($submission)) {
            abort(403, 'You are not authorized to view this submission.');
        }

        if (! Storage::disk('local')->exists($submission->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('local')->download(
            $submission->file_path,
            $submission->original_name
        );
    }

    /**
     * Preview a student submission file inline (for evaluators).
     */
    public function preview(StudentSubmission $submission)
    {
        if (! $this->isAuthorized($submission)) {
            abort(403, 'You are not authorized to view this submission.');
        }

        if (! Storage::disk('local')->exists($submission->file_path)) {
            abort(404, 'File not found.');
        }

        $mimeType = $submission->mime_type ?? 'application/octet-stream';

        return response()->file(
            Storage::disk('local')->path($submission->file_path),
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.$submission->original_name.'"',
            ]
        );
    }
}
