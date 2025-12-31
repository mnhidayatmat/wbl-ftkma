<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DocumentTemplate;
use App\Models\PlacementApplicationEvidence;
use App\Models\PlacementCompanyApplication;
use App\Models\Student;
use App\Models\StudentPlacementTracking;
use App\Models\WblGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class StudentPlacementController extends Controller
{
    /**
     * Display student placement tracking with filtering and SAL release.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Check access: Admin, Coordinator (full), Lecturer/AT/IC/Supervisor LI (read-only)
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isCoordinator() && ! $user->isLecturer() &&
            ! $user->isAt() && ! $user->isIc() && ! $user->isSupervisorLi()) {
            abort(403, 'Unauthorized access.');
        }

        // Get groups for filter based on role
        $groupsQuery = WblGroup::orderBy('status')->orderBy('name');
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            // Lecturer/AT/IC/Supervisor LI can only see active groups
            $groupsQuery->where('status', 'ACTIVE');
        }
        $groups = $groupsQuery->get();

        // Default to latest created group if no group filter is specified
        if (! $request->filled('group') && $groups->count() > 0) {
            $latestGroup = WblGroup::latest('created_at')->first();

            // Verify this group is accessible to the current user
            if ($latestGroup && ($user->isAdmin() || $user->isCoordinator() || $latestGroup->status === 'ACTIVE')) {
                // Redirect with the latest group selected, preserving other query parameters
                $queryParams = array_merge($request->all(), ['group' => $latestGroup->id]);

                return redirect()->route('placement.index', $queryParams);
            }
        }

        // Build query for students
        $query = Student::with([
            'placementTracking.applicationEvidence',
            'placementTracking.companyApplications',
            'resumeInspection',
            'group',
            'company',
        ])
            ->orderBy('name');

        // Filter by group status based on role
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            // Lecturer/AT/IC/Supervisor LI can only see students in active groups
            $query->inActiveGroups();
        }

        // Optional: Filter by group status if requested (Admin/Coordinator only)
        if (($user->isAdmin() || $user->isCoordinator()) && $request->filled('group_status')) {
            if ($request->group_status === 'active') {
                $query->inActiveGroups();
            } elseif ($request->group_status === 'completed') {
                $query->inCompletedGroups();
            }
        }

        // Filter by group - validate that non-admin/coordinator users can only filter by active groups
        if ($request->filled('group')) {
            $requestedGroup = WblGroup::find($request->group);

            // For lecturers/AT/IC/Supervisor LI, only allow filtering by active groups
            if (! $user->isAdmin() && ! $user->isCoordinator()) {
                if (! $requestedGroup || $requestedGroup->status !== 'ACTIVE') {
                    // Invalid group selection - ignore it and redirect without the group parameter
                    // Preserve all other query parameters (search, placement_status, resume_status, etc.)
                    $queryParams = $request->except('group')->all();

                    return redirect()->route('placement.index', $queryParams);
                }
            }

            $query->where('group_id', $request->group);
        }

        // Filter by placement status
        if ($request->filled('placement_status')) {
            $query->whereHas('placementTracking', function ($q) use ($request) {
                $q->where('status', $request->placement_status);
            });
        }

        // Filter by resume status
        if ($request->filled('resume_status')) {
            if ($request->resume_status === 'NOT_STARTED') {
                $query->whereDoesntHave('resumeInspection')
                    ->orWhereHas('resumeInspection', function ($q) {
                        $q->whereNull('resume_file_path');
                    });
            } elseif ($request->resume_status === 'PENDING') {
                $query->whereHas('resumeInspection', function ($q) {
                    $q->where('status', 'PENDING');
                });
            } elseif ($request->resume_status === 'RECOMMENDED') {
                $query->whereHas('resumeInspection', function ($q) {
                    $q->where('status', 'PASSED');
                });
            } elseif ($request->resume_status === 'REVISION_REQUIRED') {
                $query->whereHas('resumeInspection', function ($q) {
                    $q->where('status', 'REVISION_REQUIRED');
                });
            }
        }

        // Search by name or matric
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        $students = $query->get();

        // Ensure tracking record exists for each student
        foreach ($students as $student) {
            if (! $student->placementTracking) {
                StudentPlacementTracking::create([
                    'student_id' => $student->id,
                    'group_id' => $student->group_id,
                    'status' => 'NOT_APPLIED',
                ]);
                $student->refresh();
                $student->load('placementTracking');
            }
        }

        // Calculate comprehensive statistics
        $stats = [
            'total' => Student::count(),
            'resume_recommended' => Student::whereHas('resumeInspection', function ($q) {
                $q->where('status', 'PASSED');
            })->count(),
            'sal_released' => StudentPlacementTracking::whereNotNull('sal_file_path')->count(),
            'applied' => StudentPlacementTracking::whereIn('status', ['APPLIED', 'INTERVIEWED', 'OFFER_RECEIVED', 'ACCEPTED', 'CONFIRMED', 'SCL_RELEASED'])->count(),
            'pending_sal' => Student::whereHas('resumeInspection', function ($q) {
                $q->where('status', 'PASSED');
            })->whereHas('placementTracking', function ($q) {
                $q->where('status', 'NOT_APPLIED');
            })->count(),
            'interviewed' => StudentPlacementTracking::whereIn('status', ['INTERVIEWED', 'OFFER_RECEIVED', 'ACCEPTED', 'CONFIRMED', 'SCL_RELEASED'])->count(),
            'offer_received' => StudentPlacementTracking::whereIn('status', ['OFFER_RECEIVED', 'ACCEPTED', 'CONFIRMED', 'SCL_RELEASED'])->count(),
            'accepted' => StudentPlacementTracking::whereIn('status', ['ACCEPTED', 'CONFIRMED', 'SCL_RELEASED'])->count(),
            'scl_released' => StudentPlacementTracking::whereNotNull('scl_file_path')->count(),
        ];

        // Placement funnel data (for chart)
        $funnelData = [
            'resume_recommended' => $stats['resume_recommended'],
            'sal_released' => $stats['sal_released'],
            'applied' => $stats['applied'],
            'interviewed' => $stats['interviewed'],
            'offer_received' => $stats['offer_received'],
            'accepted' => $stats['accepted'],
            'scl_released' => $stats['scl_released'],
        ];

        // Group-wise statistics (for comparison chart)
        $groupStats = WblGroup::withCount(['students' => function ($q) {
            // Only count students with placement tracking
        }])->get()->map(function ($group) {
            $studentsInGroup = Student::where('group_id', $group->id)->pluck('id');

            // Count students with resume recommended (PASSED status)
            $resumeOkCount = Student::where('group_id', $group->id)
                ->whereHas('resumeInspection', function ($q) {
                    $q->where('status', 'PASSED');
                })->count();

            return [
                'name' => $group->name,
                'total' => $studentsInGroup->count(),
                'resume_ok' => $resumeOkCount,
                'sal_released' => StudentPlacementTracking::whereIn('student_id', $studentsInGroup)->whereNotNull('sal_file_path')->count(),
                'applied' => StudentPlacementTracking::whereIn('student_id', $studentsInGroup)->whereIn('status', ['APPLIED', 'INTERVIEWED', 'OFFER_RECEIVED', 'ACCEPTED', 'CONFIRMED', 'SCL_RELEASED'])->count(),
                'accepted' => StudentPlacementTracking::whereIn('student_id', $studentsInGroup)->whereIn('status', ['ACCEPTED', 'CONFIRMED', 'SCL_RELEASED'])->count(),
            ];
        })->filter(function ($group) {
            return $group['total'] > 0; // Only include groups with students
        })->values();

        // Timeline data (last 30 days of SAL releases)
        $timelineData = StudentPlacementTracking::whereNotNull('sal_released_at')
            ->where('sal_released_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(sal_released_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => \Carbon\Carbon::parse($item->date)->format('M d'),
                    'count' => $item->count,
                ];
            });

        // Handle export requests
        if ($request->has('export')) {
            return $this->exportPlacementData($request->export, $students, $stats, $funnelData, $groupStats, $timelineData);
        }

        return view('placement.index', compact('students', 'groups', 'stats', 'funnelData', 'groupStats', 'timelineData'));
    }

    /**
     * Display students in a specific group.
     */
    public function showGroup(WblGroup $group): View
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isCoordinator() && ! $user->isLecturer() &&
            ! $user->isAt() && ! $user->isIc() && ! $user->isSupervisorLi()) {
            abort(403, 'Unauthorized access.');
        }

        // Get all students in this group with their placement tracking
        $students = Student::where('group_id', $group->id)
            ->with(['placementTracking.updatedByUser'])
            ->orderBy('name')
            ->get();

        // Ensure tracking record exists for each student
        foreach ($students as $student) {
            if (! $student->placementTracking) {
                StudentPlacementTracking::create([
                    'student_id' => $student->id,
                    'group_id' => $group->id,
                    'status' => 'NOT_APPLIED',
                ]);
                $student->refresh();
                $student->load('placementTracking');
            }
        }

        // Calculate statistics
        $stats = [
            'total' => $students->count(),
            'not_applied' => $students->filter(fn ($s) => $s->placementTracking->status === 'NOT_APPLIED')->count(),
            'sal_released' => $students->filter(fn ($s) => $s->placementTracking->status === 'SAL_RELEASED')->count(),
            'applied' => $students->filter(fn ($s) => $s->placementTracking->status === 'APPLIED')->count(),
            'interviewed' => $students->filter(fn ($s) => $s->placementTracking->status === 'INTERVIEWED')->count(),
            'offer_received' => $students->filter(fn ($s) => $s->placementTracking->status === 'OFFER_RECEIVED')->count(),
            'accepted' => $students->filter(fn ($s) => $s->placementTracking->status === 'ACCEPTED')->count(),
            // Count ACCEPTED with proof as "confirmed" (confirmation proof indicates completion)
            'confirmed' => $students->filter(fn ($s) => $s->placementTracking &&
                $s->placementTracking->status === 'ACCEPTED' &&
                $s->placementTracking->confirmation_proof_path
            )->count(),
            'scl_released' => $students->filter(fn ($s) => $s->placementTracking->status === 'SCL_RELEASED')->count(),
        ];

        return view('placement.group', compact('group', 'students', 'stats'));
    }

    /**
     * Update student placement status.
     */
    public function updateStatus(Request $request, Student $student): RedirectResponse
    {
        $user = auth()->user();

        // Students can update their own status (with restrictions)
        // Admin/Coordinator can update any status
        if ($user->isStudent() && $user->id !== $student->user_id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:NOT_APPLIED,SAL_RELEASED,APPLIED,INTERVIEWED,OFFER_RECEIVED,ACCEPTED,SCL_RELEASED'],
        ]);

        $tracking = $student->placementTracking;
        if (! $tracking) {
            $tracking = StudentPlacementTracking::create([
                'student_id' => $student->id,
                'group_id' => $student->group_id,
                'status' => $validated['status'],
                'updated_by' => auth()->id(),
            ]);
        } else {
            $tracking->update([
                'status' => $validated['status'],
                'updated_by' => auth()->id(),
            ]);
        }

        // Log status change
        Log::info('Student Placement Status Updated', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'old_status' => $tracking->getOriginal('status'),
            'new_status' => $validated['status'],
            'updated_by' => auth()->id(),
            'updated_by_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    /**
     * Release SAL for a single student.
     */
    public function releaseSal(Student $student): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            abort(403, 'Only Admin and Coordinator can release SAL.');
        }

        $tracking = $student->placementTracking;
        if (! $tracking) {
            $tracking = StudentPlacementTracking::create([
                'student_id' => $student->id,
                'group_id' => $student->group_id,
                'status' => 'SAL_RELEASED',
            ]);
        }

        // Generate SAL PDF (uses Word template if available, otherwise canvas template)
        $document = $this->generateSalPdf($student);

        // Always save as PDF
        $fileName = 'SAL_'.$student->matric_no.'_'.now()->format('Y-m-d_His').'.pdf';
        $filePath = 'placement/sal/'.$fileName;
        Storage::put($filePath, $document->output());

        // Update tracking
        $tracking->update([
            'status' => 'SAL_RELEASED',
            'sal_released_at' => now(),
            'sal_released_by' => auth()->id(),
            'sal_file_path' => $filePath,
            'updated_by' => auth()->id(),
        ]);

        // Log action
        Log::info('SAL Released', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'released_by' => auth()->id(),
            'released_by_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'SAL released successfully.');
    }

    /**
     * Bulk release SAL for students with Resume Recommended status.
     */
    public function bulkReleaseSal(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            abort(403, 'Only Admin and Coordinator can release SAL.');
        }

        // Get all students with Resume Recommended status who haven't received SAL
        $students = Student::whereHas('resumeInspection', function ($q) {
            $q->where('status', 'PASSED');
        })
            ->where(function ($q) {
                $q->whereHas('placementTracking', function ($subQ) {
                    $subQ->where('status', 'NOT_APPLIED');
                })->orWhereDoesntHave('placementTracking');
            })
            ->with(['resumeInspection', 'placementTracking'])
            ->get();

        $released = 0;
        foreach ($students as $student) {
            // Double-check eligibility
            $resumeInspection = $student->resumeInspection;
            if (! $resumeInspection || $resumeInspection->status !== 'PASSED') {
                continue;
            }

            $tracking = $student->placementTracking;
            if ($tracking && $tracking->status !== 'NOT_APPLIED') {
                continue; // Already has SAL or beyond
            }

            if (! $tracking) {
                $tracking = StudentPlacementTracking::create([
                    'student_id' => $student->id,
                    'group_id' => $student->group_id,
                    'status' => 'SAL_RELEASED',
                ]);
            }

            // Generate SAL PDF (uses Word template if available, otherwise canvas template)
            $document = $this->generateSalPdf($student);

            // Always save as PDF
            $fileName = 'SAL_'.$student->matric_no.'_'.now()->format('Y-m-d_His').'.pdf';
            $filePath = 'placement/sal/'.$fileName;
            Storage::put($filePath, $document->output());

            $tracking->update([
                'status' => 'SAL_RELEASED',
                'sal_released_at' => now(),
                'sal_released_by' => auth()->id(),
                'sal_file_path' => $filePath,
                'updated_by' => auth()->id(),
            ]);

            $released++;
        }

        Log::info('Bulk SAL Release', [
            'released_count' => $released,
            'released_by' => auth()->id(),
            'released_by_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', "SAL released for {$released} student(s).");
    }

    /**
     * Release SCL for a single student.
     */
    public function releaseScl(Student $student): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            abort(403, 'Only Admin and Coordinator can release SCL.');
        }

        $tracking = $student->placementTracking;
        // Check if student has accepted offer and uploaded proof (step 6 completed)
        $isStep6Complete = $tracking &&
            $tracking->status === 'ACCEPTED' &&
            $tracking->confirmation_proof_path;

        if (! $isStep6Complete) {
            return redirect()->back()->with('error', 'Student must have accepted the offer and uploaded confirmation proof before SCL can be released.');
        }

        // Generate SCL PDF
        $pdf = $this->generateSclPdf($student);
        $fileName = 'SCL_'.$student->matric_no.'_'.now()->format('Y-m-d_His').'.pdf';
        $filePath = 'placement/scl/'.$fileName;
        Storage::put($filePath, $pdf->output());

        // Update tracking
        $tracking->update([
            'status' => 'SCL_RELEASED',
            'scl_released_at' => now(),
            'scl_released_by' => auth()->id(),
            'scl_file_path' => $filePath,
            'updated_by' => auth()->id(),
        ]);

        // Log action
        Log::info('SCL Released', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'released_by' => auth()->id(),
            'released_by_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'SCL released successfully.');
    }

    /**
     * Upload confirmation proof.
     */
    public function uploadProof(Request $request, Student $student): RedirectResponse
    {
        $user = auth()->user();
        if ($user->isStudent() && $user->id !== $student->user_id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
        ]);

        $tracking = $student->placementTracking;
        if (! $tracking) {
            $tracking = StudentPlacementTracking::create([
                'student_id' => $student->id,
                'group_id' => $student->group_id,
                'status' => 'ACCEPTED',
            ]);
        }

        // Delete old proof if exists
        if ($tracking->confirmation_proof_path && Storage::exists($tracking->confirmation_proof_path)) {
            Storage::delete($tracking->confirmation_proof_path);
        }

        // Store new proof
        $filePath = $validated['proof']->store('placement/proofs');

        // Update tracking - keep status as ACCEPTED, confirmation proof is separate
        $updateData = [
            'confirmation_proof_path' => $filePath,
            'status' => 'ACCEPTED',
            'updated_by' => auth()->id(),
        ];

        // Set confirmed_at timestamp when proof is uploaded
        if (! $tracking->confirmed_at) {
            $updateData['confirmed_at'] = now()->startOfDay();
        }

        $tracking->update($updateData);

        Log::info('Confirmation Proof Uploaded', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'uploaded_by' => auth()->id(),
            'uploaded_by_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Confirmation proof uploaded successfully.');
    }

    /**
     * Download SAL PDF.
     */
    public function downloadSal(Student $student)
    {
        $tracking = $student->placementTracking;

        // Check if SAL has been released for this student
        if (! $tracking || ! in_array($tracking->status, ['SAL_RELEASED', 'APPLIED', 'INTERVIEWED', 'OFFER_RECEIVED', 'ACCEPTED', 'CONFIRMED', 'SCL_RELEASED'])) {
            abort(404, 'SAL not found. SAL has not been released for this student.');
        }

        // Load student relationships needed for SAL generation
        $student->load(['user', 'group', 'company']);

        // Generate SAL PDF on-the-fly using the Word template
        $document = $this->generateSalPdf($student);

        // Get PDF content
        $pdfContent = $document->output();

        // Return as PDF download
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="SAL_'.$student->matric_no.'.pdf"',
        ]);
    }

    /**
     * Download SCL PDF.
     */
    public function downloadScl(Student $student)
    {
        $tracking = $student->placementTracking;
        if (! $tracking || ! $tracking->scl_file_path || ! Storage::exists($tracking->scl_file_path)) {
            abort(404, 'SCL not found.');
        }

        return Storage::download($tracking->scl_file_path);
    }

    /**
     * Generate SAL PDF.
     */
    private function generateSalPdf(Student $student)
    {
        // Get template from database
        $template = DocumentTemplate::getSalTemplate();

        // Use Word template if uploaded, otherwise fall back to canvas template (PDF)
        if ($template->word_template_path && Storage::disk('public')->exists($template->word_template_path)) {
            return $this->generateSalFromWordTemplate($student, $template);
        }

        // Default: use canvas template (PDF)
        return $this->generateSalFromCanvasTemplate($student, $template);
    }

    /**
     * Generate SAL from Canvas template (PDF).
     */
    private function generateSalFromCanvasTemplate(Student $student, DocumentTemplate $template)
    {
        // Calculate WBL duration
        $wblDuration = '6 months';
        if ($student->group && $student->group->start_date && $student->group->end_date) {
            $start = \Carbon\Carbon::parse($student->group->start_date);
            $end = \Carbon\Carbon::parse($student->group->end_date);
            $months = $start->diffInMonths($end);
            $wblDuration = $months.' '.($months == 1 ? 'month' : 'months');
        }

        $marginTop = $template->settings['margin_top'] ?? 25;
        $marginBottom = $template->settings['margin_bottom'] ?? 25;
        $marginLeft = $template->settings['margin_left'] ?? 25;
        $marginRight = $template->settings['margin_right'] ?? 25;

        $pdf = Pdf::loadView('placement.pdf.sal-template', [
            'student' => $student,
            'wblDuration' => $wblDuration,
            'generatedAt' => now(),
            'template' => $template,
        ])->setPaper('a4', 'portrait')
            ->setOption('margin-top', $marginTop)
            ->setOption('margin-bottom', $marginBottom)
            ->setOption('margin-left', $marginLeft)
            ->setOption('margin-right', $marginRight)
            ->setOption('enable-local-file-access', true);

        return $pdf;
    }

    /**
     * Generate SAL from Word template.
     */
    private function generateSalFromWordTemplate(Student $student, DocumentTemplate $template)
    {
        $templatePath = Storage::disk('public')->path($template->word_template_path);
        $templateProcessor = new TemplateProcessor($templatePath);

        // Calculate WBL duration
        $wblDuration = '6 months';
        $groupStartDate = null;
        $groupEndDate = null;

        if ($student->group) {
            $groupStartDate = $student->group->start_date;
            $groupEndDate = $student->group->end_date;

            if ($groupStartDate && $groupEndDate) {
                $start = \Carbon\Carbon::parse($groupStartDate);
                $end = \Carbon\Carbon::parse($groupEndDate);
                $months = $start->diffInMonths($end);
                $wblDuration = $months.' '.($months == 1 ? 'month' : 'months');
            }
        }

        // Get SAL release date and reference number from settings
        $salReleaseDate = $template->settings['sal_release_date'] ?? now()->format('Y-m-d');
        $salReferenceNumber = $template->settings['sal_reference_number'] ?? '';

        // Get WBL Coordinator based on student's programme
        $wblCoordinator = Student::getWblCoordinator($student->programme);

        // Replace variables
        $variables = [
            'student_name' => $student->name ?? $student->user?->name ?? '',
            'student_matric' => $student->matric_no ?? '',
            'student_ic' => $student->ic_no ?? '',
            'student_faculty' => $student->faculty ?? 'Faculty of Technology and Management',
            'student_programme' => $student->programme ?? '',
            'student_programme_short' => Student::getProgrammeShortCode($student->programme),
            'student_email' => $student->user?->email ?? '',
            'student_phone' => $student->phone ?? '',
            'wbl_duration' => $wblDuration,
            'current_date' => now()->format('d F Y'),
            'group_name' => $student->group?->name ?? '',
            'group_start_date' => $groupStartDate ? \Carbon\Carbon::parse($groupStartDate)->format('d F Y') : '',
            'group_end_date' => $groupEndDate ? \Carbon\Carbon::parse($groupEndDate)->format('d F Y') : '',
            'sal_release_date' => $salReleaseDate ? \Carbon\Carbon::parse($salReleaseDate)->format('d F Y') : '',
            'sal_reference_number' => $salReferenceNumber,
            'wbl_coordinator_name' => $wblCoordinator?->name ?? '',
            'wbl_coordinator_email' => $wblCoordinator?->email ?? '',
            'wbl_coordinator_phone' => $wblCoordinator?->phone ?? '',
            'director_name' => $template->settings['director_name'] ?? '',
        ];

        // Set normal variables
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Set uppercase versions (with :upper suffix)
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key.':upper', strtoupper($value));
        }

        // Set director signature image if exists
        if (isset($template->settings['director_signature_path'])) {
            $signaturePath = Storage::disk('public')->path($template->settings['director_signature_path']);
            if (file_exists($signaturePath)) {
                try {
                    $templateProcessor->setImageValue('director_signature', [
                        'path' => $signaturePath,
                        'width' => 150,
                        'height' => 50,
                        'ratio' => true,
                    ]);
                } catch (\Exception $e) {
                    // If image replacement fails, just remove the placeholder
                    $templateProcessor->setValue('director_signature', '');
                }
            } else {
                $templateProcessor->setValue('director_signature', '');
            }
        } else {
            $templateProcessor->setValue('director_signature', '');
        }

        // Save Word document to temp file
        $tempDir = storage_path('app/temp');
        if (! file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $wordPath = $tempDir.'/SAL_'.$student->id.'_'.time().'.docx';
        $templateProcessor->saveAs($wordPath);

        // Convert Word to PDF using PhpWord with DomPDF renderer
        $pdfPath = $tempDir.'/SAL_'.$student->id.'_'.time().'.pdf';

        // Set PDF renderer to DomPDF
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');

        // Load the Word document and convert to PDF
        $phpWord = IOFactory::load($wordPath);
        $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->save($pdfPath);

        // Clean up the temp Word file
        if (file_exists($wordPath)) {
            unlink($wordPath);
        }

        // Return PDF wrapper object
        return new class($pdfPath)
        {
            private $pdfPath;

            public function __construct($pdfPath)
            {
                $this->pdfPath = $pdfPath;
            }

            public function output()
            {
                $content = file_get_contents($this->pdfPath);

                // Clean up temp PDF file after reading
                if (file_exists($this->pdfPath)) {
                    unlink($this->pdfPath);
                }

                return $content;
            }

            public function isWordDocument()
            {
                return false;
            }
        };
    }

    /**
     * Generate SCL PDF.
     */
    private function generateSclPdf(Student $student)
    {
        $tracking = $student->placementTracking;
        $group = $student->group;

        $pdf = Pdf::loadView('placement.pdf.scl', [
            'student' => $student,
            'tracking' => $tracking,
            'group' => $group,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait')
            ->setOption('margin-top', 25)
            ->setOption('margin-bottom', 25)
            ->setOption('margin-left', 25)
            ->setOption('margin-right', 25)
            ->setOption('enable-local-file-access', true);

        return $pdf;
    }

    /**
     * Generate SAL Word document (without PDF conversion).
     */
    private function generateSalWord(Student $student): string
    {
        // Get template from database
        $template = DocumentTemplate::getSalTemplate();

        // Check if Word template exists
        if (! $template->word_template_path || ! Storage::disk('public')->exists($template->word_template_path)) {
            abort(404, 'SAL Word template not found. Please contact administrator.');
        }

        $templatePath = Storage::disk('public')->path($template->word_template_path);
        $templateProcessor = new TemplateProcessor($templatePath);

        // Calculate WBL duration
        $wblDuration = '6 months';
        $groupStartDate = null;
        $groupEndDate = null;

        if ($student->group) {
            $groupStartDate = $student->group->start_date;
            $groupEndDate = $student->group->end_date;

            if ($groupStartDate && $groupEndDate) {
                $start = \Carbon\Carbon::parse($groupStartDate);
                $end = \Carbon\Carbon::parse($groupEndDate);
                $months = $start->diffInMonths($end);
                $wblDuration = $months.' '.($months == 1 ? 'month' : 'months');
            }
        }

        // Get SAL release date and reference number from settings
        $salReleaseDate = $template->settings['sal_release_date'] ?? now()->format('Y-m-d');
        $salReferenceNumber = $template->settings['sal_reference_number'] ?? '';

        // Get WBL Coordinator based on student's programme
        $wblCoordinator = Student::getWblCoordinator($student->programme);

        // Replace variables
        $variables = [
            'student_name' => $student->name ?? $student->user?->name ?? '',
            'student_matric' => $student->matric_no ?? '',
            'student_ic' => $student->ic_no ?? '',
            'student_faculty' => $student->faculty ?? 'Faculty of Technology and Management',
            'student_programme' => $student->programme ?? '',
            'student_programme_short' => Student::getProgrammeShortCode($student->programme),
            'student_email' => $student->user?->email ?? '',
            'student_phone' => $student->phone ?? '',
            'wbl_duration' => $wblDuration,
            'current_date' => now()->format('d F Y'),
            'group_name' => $student->group?->name ?? '',
            'group_start_date' => $groupStartDate ? \Carbon\Carbon::parse($groupStartDate)->format('d F Y') : '',
            'group_end_date' => $groupEndDate ? \Carbon\Carbon::parse($groupEndDate)->format('d F Y') : '',
            'sal_release_date' => $salReleaseDate ? \Carbon\Carbon::parse($salReleaseDate)->format('d F Y') : '',
            'sal_reference_number' => $salReferenceNumber,
            'wbl_coordinator_name' => $wblCoordinator?->name ?? '',
            'wbl_coordinator_email' => $wblCoordinator?->email ?? '',
            'wbl_coordinator_phone' => $wblCoordinator?->phone ?? '',
            'director_name' => $template->settings['director_name'] ?? '',
        ];

        // Set normal variables
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Set uppercase versions (with :upper suffix)
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key.':upper', strtoupper($value));
        }

        // Set director signature image if exists
        if (isset($template->settings['director_signature_path'])) {
            $signaturePath = Storage::disk('public')->path($template->settings['director_signature_path']);
            if (file_exists($signaturePath)) {
                try {
                    $templateProcessor->setImageValue('director_signature', [
                        'path' => $signaturePath,
                        'width' => 150,
                        'height' => 50,
                        'ratio' => true,
                    ]);
                } catch (\Exception $e) {
                    // If image replacement fails, just remove the placeholder
                    $templateProcessor->setValue('director_signature', '');
                }
            } else {
                $templateProcessor->setValue('director_signature', '');
            }
        } else {
            $templateProcessor->setValue('director_signature', '');
        }

        // Save Word document to temp file
        $tempDir = storage_path('app/temp');
        if (! file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $wordPath = $tempDir.'/SAL_'.$student->id.'_'.time().'.docx';
        $templateProcessor->saveAs($wordPath);

        return $wordPath;
    }

    /**
     * Convert Word document to PDF using LibreOffice.
     */
    private function convertWordToPdf(string $wordPath): string
    {
        // Generate PDF path
        $pdfPath = str_replace('.docx', '.pdf', $wordPath);
        $outputDir = dirname($wordPath);

        // Try to find LibreOffice executable
        $libreOfficePaths = [
            '/Applications/LibreOffice.app/Contents/MacOS/soffice', // macOS
            '/usr/bin/libreoffice', // Linux
            '/usr/bin/soffice', // Linux alternative
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe', // Windows
        ];

        $libreOfficePath = null;
        foreach ($libreOfficePaths as $path) {
            if (file_exists($path)) {
                $libreOfficePath = $path;
                break;
            }
        }

        if (! $libreOfficePath) {
            // Fallback to PhpWord PDF conversion (less accurate but works)
            return $this->convertWordToPdfFallback($wordPath);
        }

        // Use LibreOffice for accurate conversion
        $command = sprintf(
            '"%s" --headless --convert-to pdf --outdir "%s" "%s" 2>&1',
            $libreOfficePath,
            $outputDir,
            $wordPath
        );

        exec($command, $output, $returnCode);

        // Check if PDF was created
        if (file_exists($pdfPath)) {
            return $pdfPath;
        }

        // If LibreOffice failed, try fallback
        Log::warning('LibreOffice conversion failed', [
            'command' => $command,
            'output' => $output,
            'return_code' => $returnCode,
        ]);

        return $this->convertWordToPdfFallback($wordPath);
    }

    /**
     * Fallback Word to PDF conversion using PhpWord (less accurate).
     */
    private function convertWordToPdfFallback(string $wordPath): string
    {
        // Set PDF renderer to DomPDF
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');

        // Load the Word document
        $phpWord = IOFactory::load($wordPath);

        // Generate PDF path
        $pdfPath = str_replace('.docx', '.pdf', $wordPath);

        // Save as PDF
        $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->save($pdfPath);

        return $pdfPath;
    }

    /**
     * Display student's own placement tracking (Student role only).
     */
    public function studentView(): View
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access. This page is for students only.');
        }

        $student = $user->student;
        if (! $student) {
            return view('placement.student.no-profile');
        }

        // Check if student is in a completed group
        if ($student->isInCompletedGroup()) {
            // Students in completed groups have read-only access
            return $this->renderStudentPlacementView($student, true, true);
        }

        return $this->renderStudentPlacementView($student, true, false);
    }

    /**
     * View student placement tracking (Admin & Coordinator can view any student).
     */
    public function viewStudentPlacement(Student $student): View
    {
        $user = auth()->user();
        // Allow admin, coordinator, or the student themselves
        if (! $user->isAdmin() && ! $user->isCoordinator() && (! $user->isStudent() || $user->id !== $student->user_id)) {
            abort(403, 'Unauthorized access.');
        }

        return $this->renderStudentPlacementView($student, $user->isStudent());
    }

    /**
     * Render student placement view (shared logic for student and admin views).
     */
    private function renderStudentPlacementView(Student $student, bool $isStudentView, bool $readOnly = false): View
    {
        // Get or create placement tracking
        $tracking = $student->placementTracking;
        if (! $tracking) {
            $tracking = StudentPlacementTracking::create([
                'student_id' => $student->id,
                'group_id' => $student->group_id,
                'status' => 'NOT_APPLIED',
            ]);
        }

        // Load company applications
        $tracking->load('companyApplications');

        // Check resume inspection status
        $resumeInspection = $student->resumeInspection;
        $canApply = $resumeInspection && $resumeInspection->isPassed();

        // Determine Step 1 label based on resume inspection status
        $step1Label = $this->getStep1LabelFromResumeInspection($resumeInspection);

        // Get Step 1 date from resume inspection if approved
        $step1Date = null;
        if ($resumeInspection && $resumeInspection->isPassed() && $resumeInspection->approved_at) {
            $step1Date = $resumeInspection->approved_at;
        }

        // Load relationships
        $student->load([
            'company',
            'industryCoach',
            'academicTutor',
            'group',
            'company.mou',
            'company.moas',
        ]);

        // Get status progression with dates
        // Ensure all statuses have dates displayed
        $statuses = [
            'NOT_APPLIED' => ['label' => $step1Label, 'step' => 1, 'date' => $step1Date],
            'SAL_RELEASED' => ['label' => 'SAL Released', 'step' => 2, 'date' => $tracking->sal_released_at],
            'APPLIED' => ['label' => 'Applied', 'step' => 3, 'date' => $tracking->applied_at],
            'INTERVIEWED' => ['label' => 'Interviewed', 'step' => 4, 'date' => $tracking->interviewed_at],
            'OFFER_RECEIVED' => ['label' => 'Offer Received', 'step' => 5, 'date' => $tracking->offer_received_at],
            'ACCEPTED' => ['label' => 'Accepted', 'step' => 6, 'date' => $tracking->accepted_at],
            'SCL_RELEASED' => ['label' => 'SCL Released', 'step' => 7, 'date' => $tracking->scl_released_at],
        ];

        $currentStep = $statuses[$tracking->status]['step'] ?? 1;

        // Check if student is in completed group
        $isInCompletedGroup = $student->isInCompletedGroup();
        if ($readOnly || $isInCompletedGroup) {
            $readOnly = true;
        }

        // Get existing companies for selection
        $existingCompanies = Company::orderBy('company_name')->get(['id', 'company_name']);

        return view('placement.student.index', compact('student', 'tracking', 'statuses', 'currentStep', 'canApply', 'resumeInspection', 'step1Label', 'isStudentView', 'readOnly', 'isInCompletedGroup', 'existingCompanies'));
    }

    /**
     * Update student's own placement status.
     */
    public function studentUpdateStatus(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        // Prevent updates for students in completed groups
        if ($student->isInCompletedGroup()) {
            return redirect()->back()->with('error', 'Your WBL group has been completed. You can no longer update your placement status. Data remains available for viewing only.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:NOT_APPLIED,SAL_RELEASED,APPLIED,INTERVIEWED,OFFER_RECEIVED,ACCEPTED'],
            'notes' => ['nullable', 'string', 'max:1000'],
            // Application data (required when status is APPLIED)
            'companies_applied_count' => ['nullable', 'integer', 'min:0'],
            'first_application_date' => ['nullable', 'date'],
            'last_application_date' => ['nullable', 'date', 'after_or_equal:first_application_date'],
            'application_methods' => ['nullable', 'array'],
            'application_methods.*' => ['string', 'in:job_portal,company_website,email,career_fair,referral'],
            'application_notes' => ['nullable', 'string', 'max:1000'],
            'evidence_files' => ['nullable', 'array'],
            'evidence_files.*' => ['file', 'mimes:pdf,png,jpg,jpeg', 'max:5120'], // 5MB max
            // Offer data (required when status is OFFER_RECEIVED)
            'offer_company_ids' => ['required_if:status,OFFER_RECEIVED', 'nullable', 'array'],
            'offer_company_ids.*' => ['exists:placement_company_applications,id'],
        ], [
            'offer_company_ids.required_if' => 'Please select at least one company that gave you an offer.',
        ]);

        $tracking = $student->placementTracking;
        if (! $tracking) {
            // Create new tracking with NOT_APPLIED status (students cannot set initial status)
            $tracking = StudentPlacementTracking::create([
                'student_id' => $student->id,
                'group_id' => $student->group_id,
                'status' => 'NOT_APPLIED',
                'notes' => $validated['notes'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'Notes updated successfully. Status is determined by your resume inspection.');
        } else {
            $oldStatus = $tracking->status;

            // Students cannot change NOT_APPLIED status (it's controlled by resume inspection)
            if ($oldStatus === 'NOT_APPLIED' && $validated['status'] !== 'NOT_APPLIED') {
                return redirect()->back()->withErrors([
                    'status' => 'You cannot change this status. Step 1 status is determined by your resume inspection status. Please ensure your resume is approved first.',
                ]);
            }

            // Students cannot set SCL_RELEASED (admin only)
            if ($validated['status'] === 'SCL_RELEASED') {
                return redirect()->back()->with('error', 'You cannot set this status. Please contact administrator.');
            }

            // Define valid status progression
            $statusOrder = ['NOT_APPLIED', 'SAL_RELEASED', 'APPLIED', 'INTERVIEWED', 'OFFER_RECEIVED', 'ACCEPTED', 'SCL_RELEASED'];
            $oldIndex = array_search($oldStatus, $statusOrder);
            $newIndex = array_search($validated['status'], $statusOrder);

            // Students cannot go back to NOT_APPLIED (admin-controlled status)
            if ($validated['status'] === 'NOT_APPLIED') {
                return redirect()->back()->withErrors([
                    'status' => 'You cannot go back to this status as it is controlled by the administrator.',
                ]);
            }

            // Students can only go back to SAL_RELEASED if they have SAL file (meaning admin released it)
            if ($validated['status'] === 'SAL_RELEASED' && ! $tracking->sal_file_path) {
                return redirect()->back()->withErrors([
                    'status' => 'You cannot go back to SAL Released status. Please wait for administrator to release your SAL.',
                ]);
            }

            // Students cannot go back from SCL_RELEASED (final status)
            if ($oldStatus === 'SCL_RELEASED') {
                return redirect()->back()->withErrors([
                    'status' => 'Your placement journey is complete. You cannot change your status.',
                ]);
            }

            // Ensure student has SAL released before they can apply
            if ($validated['status'] === 'APPLIED' && $oldStatus !== 'SAL_RELEASED' && ! $tracking->sal_file_path) {
                return redirect()->back()->withErrors([
                    'status' => 'You must have SAL released before you can mark yourself as Applied. Please wait for coordinator to release your SAL.',
                ]);
            }

            // Prepare update data
            $updateData = [
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? $tracking->notes,
                'updated_by' => auth()->id(),
            ];

            // Set status date when moving to a new status (always update to today's date)
            if ($oldStatus !== $validated['status']) {
                $statusDateField = match ($validated['status']) {
                    'APPLIED' => 'applied_at',
                    'INTERVIEWED' => 'interviewed_at',
                    'OFFER_RECEIVED' => 'offer_received_at',
                    'ACCEPTED' => 'accepted_at',
                    default => null,
                };

                // Always set to today's date when status changes
                if ($statusDateField) {
                    $updateData[$statusDateField] = now()->startOfDay();
                }
            }

            // Handle application data
            if ($validated['status'] === 'APPLIED') {
                // Set applied_status_set_at if this is the first time setting to APPLIED
                if ($oldStatus !== 'APPLIED') {
                    $updateData['applied_status_set_at'] = now();
                    // Set default first_application_date to today if not already set
                    if (! $tracking->first_application_date) {
                        $updateData['first_application_date'] = now()->toDateString();
                    }
                }

                $updateData['application_methods'] = $validated['application_methods'] ?? $tracking->application_methods;
                $updateData['application_notes'] = $validated['application_notes'] ?? $tracking->application_notes;
            }

            // Handle offer received - mark multiple companies
            if ($validated['status'] === 'OFFER_RECEIVED' && ! empty($validated['offer_company_ids'])) {
                foreach ($validated['offer_company_ids'] as $companyId) {
                    $offerCompany = PlacementCompanyApplication::find($companyId);
                    if ($offerCompany && $offerCompany->placement_tracking_id === $tracking->id) {
                        $offerCompany->update([
                            'offer_received' => true,
                            'offer_received_date' => now()->toDateString(),
                        ]);
                    }
                }
            }

            // Handle accepted - create Company record and link to student
            if ($validated['status'] === 'ACCEPTED') {
                // Find the company application with offer_received = true
                $acceptedCompanyApplication = $tracking->companyApplications()
                    ->where('offer_received', true)
                    ->first();

                if ($acceptedCompanyApplication) {
                    $companyName = $acceptedCompanyApplication->company_name;

                    // Check if company already exists (case-insensitive)
                    $existingCompany = Company::whereRaw('LOWER(company_name) = ?', [strtolower($companyName)])->first();

                    if ($existingCompany) {
                        // Use existing company
                        $student->update(['company_id' => $existingCompany->id]);
                    } else {
                        // Create new company and link to student
                        $newCompany = Company::create([
                            'company_name' => $companyName,
                        ]);
                        $student->update(['company_id' => $newCompany->id]);
                    }
                }
            }

            // Update tracking
            $tracking->update($updateData);

            // Handle evidence file uploads
            if ($validated['status'] === 'APPLIED' && $request->hasFile('evidence_files')) {
                foreach ($request->file('evidence_files') as $file) {
                    $fileName = 'evidence_'.$student->matric_no.'_'.time().'_'.$file->getClientOriginalName();
                    $filePath = $file->storeAs('placement/evidence', $fileName, 'public');

                    PlacementApplicationEvidence::create([
                        'placement_tracking_id' => $tracking->id,
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            // If status changed, log it
            if ($oldStatus !== $validated['status']) {
                Log::info('Student Placement Status Updated (Student)', [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                    'updated_by' => auth()->id(),
                    'updated_by_name' => auth()->user()->name,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Status updated successfully. Admin has been notified.');
    }

    /**
     * Check if a company with similar name already exists (AJAX).
     */
    public function checkCompanyExists(Request $request)
    {
        $name = $request->query('name', '');

        if (strlen($name) < 3) {
            return response()->json(['exists' => false]);
        }

        // Check for exact or similar company name (case-insensitive)
        $company = Company::whereRaw('LOWER(company_name) = ?', [strtolower($name)])
            ->orWhereRaw('LOWER(company_name) LIKE ?', ['%'.strtolower($name).'%'])
            ->first();

        if ($company) {
            return response()->json([
                'exists' => true,
                'company_id' => $company->id,
                'company_name' => $company->company_name,
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Add a company application.
     */
    public function addCompany(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        $tracking = $student->placementTracking;
        if (! $tracking) {
            abort(404, 'Placement tracking not found.');
        }

        $validated = $request->validate([
            'placement_tracking_id' => ['required', 'exists:student_placement_tracking,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'application_deadline' => ['nullable', 'date'],
            'application_method' => ['required', 'in:through_coordinator,job_portal,company_website,email,career_fair,referral,other'],
            'application_method_other' => ['required_if:application_method,other', 'nullable', 'string', 'max:255'],
        ]);

        // Verify the tracking belongs to the student
        if ($validated['placement_tracking_id'] != $tracking->id) {
            abort(403, 'Unauthorized access.');
        }

        // Just store company name as text - Company record will be created when student accepts offer
        PlacementCompanyApplication::create([
            'placement_tracking_id' => $validated['placement_tracking_id'],
            'company_name' => $validated['company_name'],
            'application_deadline' => $validated['application_deadline'] ?? null,
            'application_method' => $validated['application_method'],
            'application_method_other' => $validated['application_method_other'] ?? null,
        ]);

        // Update companies_applied_count
        $tracking->update([
            'companies_applied_count' => $tracking->companyApplications()->count(),
        ]);

        return redirect()->back()->with('success', 'Company application added successfully.');
    }

    /**
     * Delete a company application.
     */
    public function deleteCompany(PlacementCompanyApplication $application): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        $tracking = $student->placementTracking;
        if (! $tracking || $application->placement_tracking_id != $tracking->id) {
            abort(403, 'Unauthorized access.');
        }

        $application->delete();

        // Update companies_applied_count
        $tracking->update([
            'companies_applied_count' => $tracking->companyApplications()->count(),
        ]);

        return redirect()->back()->with('success', 'Company removed successfully.');
    }

    /**
     * Mark student as interviewed from a specific company application.
     */
    public function markAsInterviewed(PlacementCompanyApplication $application): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        $tracking = $student->placementTracking;
        if (! $tracking) {
            abort(404, 'Placement tracking not found.');
        }

        // Verify the application belongs to the student's tracking
        if ($application->placement_tracking_id !== $tracking->id) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow if current status is APPLIED
        if ($tracking->status !== 'APPLIED') {
            return redirect()->back()->withErrors([
                'status' => 'You can only mark as interviewed when your status is "Applied".',
            ]);
        }

        // Mark this company application as interviewed
        $application->update([
            'interviewed' => true,
            'interviewed_at' => now(),
        ]);

        // Update status to INTERVIEWED
        $updateData = [
            'status' => 'INTERVIEWED',
            'updated_by' => auth()->id(),
            // Always set to today's date when marking as interviewed
            'interviewed_at' => now()->startOfDay(),
        ];

        $tracking->update($updateData);

        return redirect()->back()->with('success', 'Congratulations! You\'ve been marked as interviewed. Status updated to "Interviewed".');
    }

    /**
     * Update company interview status and date.
     */
    public function updateCompanyInterview(Request $request, PlacementCompanyApplication $application): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        $tracking = $student->placementTracking;
        if (! $tracking) {
            abort(404, 'Placement tracking not found.');
        }

        // Verify the application belongs to the student's tracking
        if ($application->placement_tracking_id !== $tracking->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'interviewed' => ['required', 'boolean'],
            'interview_date' => ['required_if:interviewed,1', 'nullable', 'date'],
        ], [
            'interview_date.required_if' => 'Please select the interview date.',
        ]);

        $updateData = [
            'interviewed' => $validated['interviewed'],
        ];

        if ($validated['interviewed']) {
            $updateData['interviewed_at'] = $application->interviewed_at ?? now();
            $updateData['interview_date'] = $validated['interview_date'];
        } else {
            $updateData['interviewed_at'] = null;
            $updateData['interview_date'] = null;
        }

        $application->update($updateData);

        return redirect()->back()->with('success', 'Interview status updated successfully.');
    }

    /**
     * Update company follow-up date and notes.
     */
    public function updateCompanyFollowUp(Request $request, PlacementCompanyApplication $application): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        $tracking = $student->placementTracking;
        if (! $tracking) {
            abort(404, 'Placement tracking not found.');
        }

        // Verify the application belongs to the student's tracking
        if ($application->placement_tracking_id !== $tracking->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'follow_up_date' => ['nullable', 'date'],
            'follow_up_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $application->update([
            'follow_up_date' => $validated['follow_up_date'],
            'follow_up_notes' => $validated['follow_up_notes'],
        ]);

        return redirect()->back()->with('success', 'Follow-up details updated successfully.');
    }

    /**
     * Student upload confirmation proof/acknowledgment.
     */
    public function studentUploadProof(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        $tracking = $student->placementTracking;
        if (! $tracking) {
            abort(404, 'Placement tracking not found.');
        }

        // Only allow if current status is ACCEPTED
        if ($tracking->status !== 'ACCEPTED') {
            return redirect()->back()->withErrors([
                'proof' => 'You can only upload confirmation proof when your status is "Accepted".',
            ]);
        }

        $validated = $request->validate([
            'proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
        ]);

        // Delete old proof if exists
        if ($tracking->confirmation_proof_path && Storage::exists($tracking->confirmation_proof_path)) {
            Storage::delete($tracking->confirmation_proof_path);
        }

        // Store new proof
        $fileName = 'confirmation_proof_'.$student->matric_no.'_'.time().'.'.$validated['proof']->getClientOriginalExtension();
        $filePath = $validated['proof']->storeAs('placement/proofs', $fileName);

        // Update tracking - keep status as ACCEPTED, confirmation proof is separate
        $tracking->update([
            'confirmation_proof_path' => $filePath,
            'status' => 'ACCEPTED',
            'confirmed_at' => now()->startOfDay(),
            'updated_by' => auth()->id(),
        ]);

        Log::info('Confirmation Proof Uploaded by Student', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Confirmation proof uploaded successfully! Your placement is now confirmed.');
    }

    /**
     * View confirmation proof file (Admin/Coordinator can view any student's proof).
     */
    public function viewProof(Student $student)
    {
        $user = auth()->user();

        // Check authorization: Admin, Coordinator, or the student themselves
        if (! $user->isAdmin() && ! $user->isCoordinator() && (! $user->isStudent() || $user->id !== $student->user_id)) {
            abort(403, 'Unauthorized access.');
        }

        $tracking = $student->placementTracking;
        if (! $tracking || ! $tracking->confirmation_proof_path) {
            abort(404, 'Confirmation proof not found.');
        }

        // Check if file exists
        if (! Storage::exists($tracking->confirmation_proof_path)) {
            abort(404, 'Confirmation proof file not found.');
        }

        // Get file mime type
        $mimeType = Storage::mimeType($tracking->confirmation_proof_path);
        $fileName = basename($tracking->confirmation_proof_path);

        // Return file response
        return Storage::response($tracking->confirmation_proof_path, $fileName, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Student download their own SAL PDF.
     */
    public function studentDownloadSal()
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access. This page is for students only.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        // Load student relationships needed for SAL generation
        $student->load(['user', 'group', 'company']);

        $tracking = $student->placementTracking;

        // Check if SAL has been released for this student
        if (! $tracking || ! in_array($tracking->status, ['SAL_RELEASED', 'APPLIED', 'INTERVIEWED', 'OFFER_RECEIVED', 'ACCEPTED', 'CONFIRMED', 'SCL_RELEASED'])) {
            abort(404, 'SAL not found. Please contact your coordinator to release your SAL.');
        }

        // Step 1: Generate SAL Word document with variables replaced
        $wordPath = $this->generateSalWord($student);

        // Step 2: Convert Word document to PDF
        $pdfPath = $this->convertWordToPdf($wordPath);

        // Clean up the Word file
        if (file_exists($wordPath)) {
            unlink($wordPath);
        }

        // Step 3: Return PDF for download
        return response()->download($pdfPath, 'SAL_'.$student->matric_no.'.pdf')->deleteFileAfterSend(true);
    }

    /**
     * Student download their own SCL PDF.
     */
    public function studentDownloadScl()
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access. This page is for students only.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        $tracking = $student->placementTracking;
        if (! $tracking || ! $tracking->scl_file_path || ! Storage::exists($tracking->scl_file_path)) {
            abort(404, 'SCL not found. Please contact your coordinator.');
        }

        return Storage::download($tracking->scl_file_path, 'SCL_'.$student->matric_no.'.pdf');
    }

    /**
     * View confirmation proof file (Student viewing their own proof).
     */
    public function studentViewProof()
    {
        $user = auth()->user();
        if (! $user->isStudent()) {
            abort(403, 'Unauthorized access. This page is for students only.');
        }

        $student = $user->student;
        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        $tracking = $student->placementTracking;
        if (! $tracking || ! $tracking->confirmation_proof_path) {
            abort(404, 'Confirmation proof not found.');
        }

        // Check if file exists
        if (! Storage::exists($tracking->confirmation_proof_path)) {
            abort(404, 'Confirmation proof file not found.');
        }

        // Get file mime type
        $mimeType = Storage::mimeType($tracking->confirmation_proof_path);
        $fileName = basename($tracking->confirmation_proof_path);

        // Return file response
        return Storage::response($tracking->confirmation_proof_path, $fileName, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Reset student placement tracking (Admin only).
     */
    public function reset(Student $student): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isAdmin()) {
            abort(403, 'Only Admin can reset placement tracking.');
        }

        $tracking = $student->placementTracking;

        if ($tracking) {
            // Delete associated files
            if ($tracking->sal_file_path && Storage::exists($tracking->sal_file_path)) {
                Storage::delete($tracking->sal_file_path);
            }
            if ($tracking->scl_file_path && Storage::exists($tracking->scl_file_path)) {
                Storage::delete($tracking->scl_file_path);
            }
            if ($tracking->confirmation_proof_path && Storage::exists($tracking->confirmation_proof_path)) {
                Storage::delete($tracking->confirmation_proof_path);
            }

            // Delete company applications and application evidence
            $tracking->companyApplications()->delete();
            $tracking->applicationEvidence()->delete();

            // Reset all tracking fields to initial state
            $tracking->update([
                'status' => 'NOT_APPLIED',
                'sal_released_at' => null,
                'sal_released_by' => null,
                'sal_file_path' => null,
                'scl_released_at' => null,
                'scl_released_by' => null,
                'scl_file_path' => null,
                'confirmation_proof_path' => null,
                'notes' => null,
                'companies_applied_count' => 0,
                'first_application_date' => null,
                'last_application_date' => null,
                'application_methods' => null,
                'application_notes' => null,
                'applied_status_set_at' => null,
                'applied_at' => null,
                'interviewed_at' => null,
                'offer_received_at' => null,
                'accepted_at' => null,
                'confirmed_at' => null,
                'updated_by' => auth()->id(),
            ]);
        }

        // Log action
        Log::info('Student Placement Tracking Reset', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'reset_by' => auth()->id(),
            'reset_by_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Placement tracking reset successfully for '.$student->name.'.');
    }

    /**
     * Export placement tracking data in various formats.
     */
    private function exportPlacementData($format, $students, $stats, $funnelData, $groupStats, $timelineData)
    {
        $timestamp = now()->format('Y-m-d_His');

        switch ($format) {
            case 'excel':
                return $this->exportToExcel($students, $stats, $timestamp);

            case 'csv':
                return $this->exportToCsv($students, $timestamp);

            case 'pdf':
                return $this->exportToPdf($students, $stats, $funnelData, $groupStats, $timestamp);

            default:
                return redirect()->route('placement.index')->with('error', 'Invalid export format');
        }
    }

    /**
     * Export to Excel format.
     */
    private function exportToExcel($students, $stats, $timestamp)
    {
        $data = $this->prepareExportData($students);

        $headers = [
            'Student Name',
            'Matric No',
            'Group',
            'Resume Status',
            'Placement Status',
            'SAL Released',
            'SAL Release Date',
            'Company',
            'Interviewed',
            'Applications Count',
        ];

        $rows = $data->map(function ($row) {
            return [
                $row['name'],
                $row['matric_no'],
                $row['group'],
                $row['resume_status'],
                $row['placement_status'],
                $row['sal_released'],
                $row['sal_date'],
                $row['company'],
                $row['interviewed'],
                $row['applications_count'],
            ];
        });

        $callback = function ($excel) use ($headers, $rows, $stats) {
            $excel->sheet('Placement Tracking', function ($sheet) use ($headers, $rows, $stats) {
                // Add summary statistics
                $sheet->row(1, ['PLACEMENT TRACKING SUMMARY']);
                $sheet->row(2, ['Total Students', $stats['total']]);
                $sheet->row(3, ['Resume Recommended', $stats['resume_recommended']]);
                $sheet->row(4, ['SAL Released', $stats['sal_released']]);
                $sheet->row(5, ['Applied', $stats['applied']]);
                $sheet->row(6, ['Accepted', $stats['accepted']]);
                $sheet->row(7, ['']);

                // Add headers
                $sheet->row(8, $headers);

                // Add data rows
                $startRow = 9;
                foreach ($rows as $index => $row) {
                    $sheet->row($startRow + $index, $row);
                }

                // Style the sheet
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A8:J8')->getFont()->setBold(true);
                $sheet->getStyle('A8:J8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('003A6C');
                $sheet->getStyle('A8:J8')->getFont()->getColor()->setRGB('FFFFFF');
            });
        };

        return response()->streamDownload(function () use ($callback) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(
                \Maatwebsite\Excel\Facades\Excel::raw(null, $callback)
            );
            $writer->save('php://output');
        }, "placement_tracking_{$timestamp}.xlsx");
    }

    /**
     * Export to CSV format.
     */
    private function exportToCsv($students, $timestamp)
    {
        $data = $this->prepareExportData($students);

        $headers = [
            'Student Name',
            'Matric No',
            'Group',
            'Resume Status',
            'Placement Status',
            'SAL Released',
            'SAL Release Date',
            'Company',
            'Interviewed',
            'Applications Count',
        ];

        $callback = function () use ($data, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row['name'],
                    $row['matric_no'],
                    $row['group'],
                    $row['resume_status'],
                    $row['placement_status'],
                    $row['sal_released'],
                    $row['sal_date'],
                    $row['company'],
                    $row['interviewed'],
                    $row['applications_count'],
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, "placement_tracking_{$timestamp}.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Export to PDF format.
     */
    private function exportToPdf($students, $stats, $funnelData, $groupStats, $timestamp)
    {
        $data = $this->prepareExportData($students);

        $pdf = Pdf::loadView('placement.pdf.analytics-report', compact('data', 'stats', 'funnelData', 'groupStats'));

        return $pdf->download("placement_analytics_{$timestamp}.pdf");
    }

    /**
     * Prepare data for export.
     */
    private function prepareExportData($students)
    {
        return $students->map(function ($student) {
            $resumeInspection = $student->resumeInspection;
            $tracking = $student->placementTracking;

            // Resume status
            $resumeStatus = 'Not Started';
            if ($resumeInspection) {
                if (empty($resumeInspection->resume_file_path)) {
                    $resumeStatus = 'Not Started';
                } elseif ($resumeInspection->status === 'PENDING') {
                    $resumeStatus = 'Submitted';
                } elseif ($resumeInspection->status === 'PASSED') {
                    $resumeStatus = 'Recommended';
                } elseif ($resumeInspection->status === 'REVISION_REQUIRED') {
                    $resumeStatus = 'Revision Required';
                } elseif ($resumeInspection->status === 'FAILED') {
                    $resumeStatus = 'Rejected';
                }
            }

            // Interview count
            $interviewCount = $tracking ? $tracking->companyApplications->filter(fn ($app) => $app->interviewed === true)->count() : 0;

            // Applications count
            $applicationsCount = $tracking ? $tracking->companyApplications->count() : 0;

            return [
                'name' => $student->name,
                'matric_no' => $student->matric_no,
                'group' => $student->group ? $student->group->name : '-',
                'resume_status' => $resumeStatus,
                'placement_status' => $tracking ? ucwords(str_replace('_', ' ', strtolower($tracking->status))) : 'Not Applied',
                'sal_released' => ($tracking && $tracking->sal_file_path) ? 'Yes' : 'No',
                'sal_date' => ($tracking && $tracking->sal_released_at) ? $tracking->sal_released_at->format('d M Y') : '-',
                'company' => $student->company ? $student->company->company_name : '-',
                'interviewed' => $interviewCount > 0 ? "Yes ({$interviewCount})" : 'No',
                'applications_count' => $applicationsCount,
            ];
        });
    }

    /**
     * Get Step 1 label based on resume inspection status.
     */
    private function getStep1LabelFromResumeInspection($resumeInspection): string
    {
        // If no resume inspection record exists or no resume file uploaded
        if (! $resumeInspection || empty($resumeInspection->resume_file_path)) {
            return 'Not started Resume Preparation';
        }

        // Map resume inspection status to Step 1 label
        return match ($resumeInspection->status) {
            'PENDING' => 'Pending Review',
            'PASSED' => 'Resume Recommended',
            'REVISION_REQUIRED' => 'Pending Review', // Needs revision, so still pending
            'FAILED' => 'Pending Review', // Failed, but can resubmit
            default => 'Not started Resume Preparation',
        };
    }
}
