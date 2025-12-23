<?php

namespace App\Http\Controllers\Academic\PPE;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\PPE\PpeActivityLog;
use App\Models\PPE\PpeAssessmentWindow;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PpeAssessmentSettingController extends Controller
{
    /**
     * Display the Assessment Control & Monitoring Panel.
     */
    public function index(): View
    {
        // Only Admin can access
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Get assessment windows
        $lecturerWindow = PpeAssessmentWindow::firstOrCreate(
            ['evaluator_role' => 'lecturer'],
            [
                'is_enabled' => false,
                'created_by' => auth()->id(),
            ]
        );

        $icWindow = PpeAssessmentWindow::firstOrCreate(
            ['evaluator_role' => 'ic'],
            [
                'is_enabled' => false,
                'created_by' => auth()->id(),
            ]
        );

        // Calculate progress statistics
        $totalStudents = Student::count();
        
        // Lecturer progress
        $lecturerAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('lecturer')
            ->active()
            ->get();
        
        $lecturerTotalWeight = $lecturerAssessments->sum('weight_percentage');
        $lecturerCompleted = StudentAssessmentMark::whereIn('assessment_id', $lecturerAssessments->pluck('id'))
            ->whereNotNull('mark')
            ->distinct('student_id')
            ->count('student_id');
        $lecturerProgress = $totalStudents > 0 ? ($lecturerCompleted / $totalStudents) * 100 : 0;

        // IC progress
        $icAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();
        
        $totalRubricQuestions = $icAssessments->sum(function($assessment) {
            return $assessment->rubrics->count();
        });
        
        $icCompleted = StudentAssessmentRubricMark::whereHas('rubric.assessment', function($query) {
            $query->where('course_code', 'PPE')
                  ->where('evaluator_role', 'ic');
        })
        ->distinct('student_id')
        ->count('student_id');
        
        $icProgress = $totalStudents > 0 ? ($icCompleted / $totalStudents) * 100 : 0;

        // Pending evaluations (students with incomplete evaluations)
        $pendingLecturer = $totalStudents - $lecturerCompleted;
        $pendingIc = $totalStudents - $icCompleted;

        // Get recent activity logs
        $activityLogs = PpeActivityLog::with('adminUser')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('academic.ppe.settings.index', compact(
            'lecturerWindow',
            'icWindow',
            'totalStudents',
            'lecturerCompleted',
            'lecturerProgress',
            'icCompleted',
            'icProgress',
            'pendingLecturer',
            'pendingIc',
            'activityLogs'
        ));
    }

    /**
     * Update assessment window settings.
     */
    public function updateWindow(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'evaluator_role' => ['required', 'in:lecturer,ic'],
            'is_enabled' => ['nullable', 'in:1'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Handle checkbox: if not present in request, it's false
        $isEnabled = $request->has('is_enabled') && $request->is_enabled == '1';

        $window = PpeAssessmentWindow::updateOrCreate(
            ['evaluator_role' => $validated['evaluator_role']],
            [
                'is_enabled' => $isEnabled,
                'start_at' => !empty($validated['start_at']) ? $validated['start_at'] : null,
                'end_at' => !empty($validated['end_at']) ? $validated['end_at'] : null,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => auth()->id(),
            ]
        );

        // Log activity
        $action = $isEnabled ? 'window_enabled' : 'window_disabled';
        if ($window->wasRecentlyCreated) {
            $action = 'window_opened';
        } else {
            $action = 'window_updated';
        }

        PpeActivityLog::create([
            'action' => $action,
            'evaluator_role' => $validated['evaluator_role'],
            'description' => "Assessment window {$window->status_label} for {$validated['evaluator_role']}",
            'admin_user_id' => auth()->id(),
            'metadata' => [
                'is_enabled' => $isEnabled,
                'start_at' => $validated['start_at'] ?? null,
                'end_at' => $validated['end_at'] ?? null,
            ],
        ]);

        return redirect()->route('academic.ppe.settings.index')
            ->with('success', 'Assessment window updated successfully.');
    }

    /**
     * Send reminder emails to evaluators.
     */
    public function sendReminder(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'evaluator_role' => ['required', 'in:lecturer,ic'],
        ]);

        $role = $validated['evaluator_role'];
        $window = PpeAssessmentWindow::where('evaluator_role', $role)->first();

        if (!$window || !$window->is_enabled) {
            return redirect()->route('academic.ppe.settings.index')
                ->with('error', "Assessment window for {$role} is not enabled.");
        }

        // Get pending students
        if ($role === 'lecturer') {
            $assessments = Assessment::forCourse('PPE')
                ->forEvaluator('lecturer')
                ->active()
                ->get();
            
            $studentsWithMarks = StudentAssessmentMark::whereIn('assessment_id', $assessments->pluck('id'))
                ->whereNotNull('mark')
                ->distinct('student_id')
                ->pluck('student_id');
            
            $pendingStudents = Student::whereNotIn('id', $studentsWithMarks)->get();
        } else {
            $icAssessments = Assessment::forCourse('PPE')
                ->forEvaluator('ic')
                ->active()
                ->whereIn('assessment_type', ['Oral', 'Rubric'])
                ->with('rubrics')
                ->get();
            
            $totalRubricQuestions = $icAssessments->sum(function($assessment) {
                return $assessment->rubrics->count();
            });
            
            $studentsWithAllRubrics = StudentAssessmentRubricMark::whereHas('rubric.assessment', function($query) {
                $query->where('course_code', 'PPE')
                      ->where('evaluator_role', 'ic');
            })
            ->select('student_id')
            ->groupBy('student_id')
            ->havingRaw('COUNT(DISTINCT assessment_rubric_id) = ?', [$totalRubricQuestions])
            ->pluck('student_id');
            
            $pendingStudents = Student::whereNotIn('id', $studentsWithAllRubrics)->get();
        }

        // TODO: Implement actual email sending
        // For now, just log the action
        PpeActivityLog::create([
            'action' => $role === 'lecturer' ? 'reminder_sent_lecturer' : 'reminder_sent_ic',
            'evaluator_role' => $role,
            'description' => "Reminder sent to {$role} for {$pendingStudents->count()} pending students",
            'admin_user_id' => auth()->id(),
            'metadata' => [
                'pending_count' => $pendingStudents->count(),
                'student_ids' => $pendingStudents->pluck('id')->toArray(),
            ],
        ]);

        return redirect()->route('academic.ppe.settings.index')
            ->with('success', "Reminder sent to {$role} for {$pendingStudents->count()} pending students.");
    }
}
