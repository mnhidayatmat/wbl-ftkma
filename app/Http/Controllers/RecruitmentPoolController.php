<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Company;
use App\Models\RecruitmentHandover;
use App\Exports\RecruitmentPoolExport;
use App\Mail\RecruitmentPackageMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class RecruitmentPoolController extends Controller
{
    /**
     * Display the recruitment pool with filtering options.
     */
    public function index(Request $request)
    {
        // Start with students who have approved resumes and are in active groups
        $query = Student::with(['group', 'company', 'resumeInspection', 'placementTracking', 'academicTutor', 'industryCoach'])
            ->inActiveGroups();

        // Filter by programme (multi-select)
        if ($request->filled('programmes') && is_array($request->programmes)) {
            $query->whereIn('programme', $request->programmes);
        }

        // Filter by CGPA range
        if ($request->filled('cgpa_min')) {
            $query->where('cgpa', '>=', $request->cgpa_min);
        }
        if ($request->filled('cgpa_max')) {
            $query->where('cgpa', '<=', $request->cgpa_max);
        }

        // Filter by skills (match any - OR logic)
        if ($request->filled('skills') && is_array($request->skills)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->skills as $skill) {
                    if (!empty($skill)) {
                        $q->orWhereJsonContains('skills', $skill);
                    }
                }
            });
        }

        // Filter by resume status
        if ($request->filled('resume_status')) {
            if ($request->resume_status === 'approved') {
                $query->whereHas('resumeInspection', function ($q) {
                    $q->where('status', 'RECOMMENDED');
                })->whereNotNull('resume_pdf_path');
            } elseif ($request->resume_status === 'with_resume') {
                $query->whereNotNull('resume_pdf_path');
            }
        }

        // Filter by placement status
        if ($request->filled('placement_status')) {
            if ($request->placement_status === 'ready') {
                // SAL released + Resume approved
                $query->whereHas('placementTracking', function ($q) {
                    $q->whereNotNull('sal_released_at');
                })->whereHas('resumeInspection', function ($q) {
                    $q->where('status', 'RECOMMENDED');
                });
            } elseif ($request->placement_status === 'not_applied') {
                $query->whereHas('placementTracking', function ($q) {
                    $q->where('status', 'SAL_RELEASED');
                });
            } elseif ($request->placement_status === 'applied') {
                $query->whereHas('placementTracking', function ($q) {
                    $q->whereIn('status', ['APPLIED', 'INTERVIEWED', 'OFFER_RECEIVED', 'ACCEPTED', 'SCL_RELEASED']);
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

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_dir', 'asc');
        $allowedSortColumns = ['name', 'matric_no', 'programme', 'cgpa', 'group'];

        if ($sortBy === 'group') {
            // Join with groups table for sorting by group name
            $query->leftJoin('groups', 'students.group_id', '=', 'groups.id')
                  ->orderBy('groups.name', $sortDirection)
                  ->select('students.*'); // Ensure we only select student columns
        } elseif (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $students = $query->paginate(20)->withQueryString();

        // Get unique programmes for filter dropdown
        $programmes = Student::inActiveGroups()
            ->distinct()
            ->pluck('programme')
            ->filter()
            ->sort()
            ->values();

        // Get all unique skills from students for autocomplete
        $allSkills = Student::inActiveGroups()
            ->whereNotNull('skills')
            ->get()
            ->pluck('skills')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return view('recruitment.pool.index', compact('students', 'programmes', 'allSkills'));
    }

    /**
     * Export selected students to Excel.
     */
    public function exportExcel(Request $request)
    {
        $studentIds = $request->input('student_ids', []);

        if (empty($studentIds)) {
            return back()->with('error', 'No students selected for export.');
        }

        $filename = 'recruitment_pool_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new RecruitmentPoolExport($studentIds), $filename);
    }

    /**
     * Generate PDF catalog of selected students.
     */
    public function exportPdf(Request $request)
    {
        $studentIds = $request->input('student_ids', []);

        if (empty($studentIds)) {
            return back()->with('error', 'No students selected for PDF export.');
        }

        $students = Student::with(['group', 'company', 'placementTracking', 'resumeInspection'])
            ->whereIn('id', $studentIds)
            ->orderBy('programme')
            ->orderBy('name')
            ->get();

        $filters = $this->getAppliedFilters($request);

        $pdf = PDF::loadView('recruitment.exports.catalog', compact('students', 'filters'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'recruitment_catalog_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download resumes as ZIP file.
     */
    public function downloadResumes(Request $request)
    {
        $studentIds = $request->input('student_ids', []);

        if (empty($studentIds)) {
            return back()->with('error', 'No students selected for resume download.');
        }

        $students = Student::whereIn('id', $studentIds)
            ->whereNotNull('resume_pdf_path')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No resumes found for selected students.');
        }

        $zipFilename = 'student_resumes_' . now()->format('Y-m-d_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFilename);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($students as $student) {
                $resumePath = storage_path('app/' . $student->resume_pdf_path);

                if (file_exists($resumePath)) {
                    $filename = $student->matric_no . '_' . str_replace(' ', '_', $student->name) . '_Resume.pdf';
                    $zip->addFile($resumePath, $filename);
                }
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Send recruitment package via email.
     */
    public function emailToRecruiter(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'company_id' => 'required|exists:companies,id',
            'recruiter_emails' => 'required|string',
            'message' => 'nullable|string',
            'include_excel' => 'boolean',
            'include_pdf' => 'boolean',
            'include_resumes' => 'boolean',
        ]);

        $studentIds = $request->student_ids;
        $company = Company::findOrFail($request->company_id);
        $recruiterEmails = array_filter(array_map('trim', explode(',', $request->recruiter_emails)));
        $message = $request->message;

        $students = Student::with(['group', 'company', 'placementTracking', 'resumeInspection'])
            ->whereIn('id', $studentIds)
            ->orderBy('programme')
            ->orderBy('name')
            ->get();

        // Track handover
        $handover = RecruitmentHandover::create([
            'company_id' => $company->id,
            'recruiter_emails' => json_encode($recruiterEmails),
            'student_ids' => json_encode($studentIds),
            'student_count' => count($studentIds),
            'message' => $message,
            'handed_over_by' => auth()->id(),
            'filters_applied' => json_encode($this->getAppliedFilters($request)),
        ]);

        // Send email
        Mail::to($recruiterEmails)->send(
            new RecruitmentPackageMail($students, $company, $message, $handover, $request)
        );

        return redirect()->route('recruitment.pool.index')
            ->with('success', 'Recruitment package sent successfully to ' . count($recruiterEmails) . ' recipient(s).');
    }

    /**
     * Get applied filters for display/logging.
     */
    private function getAppliedFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('programmes')) {
            $filters['Programmes'] = implode(', ', $request->programmes);
        }
        if ($request->filled('cgpa_min') || $request->filled('cgpa_max')) {
            $min = $request->cgpa_min ?? '0';
            $max = $request->cgpa_max ?? '4.0';
            $filters['CGPA Range'] = "$min - $max";
        }
        if ($request->filled('skills')) {
            $filters['Skills'] = implode(', ', array_filter($request->skills));
        }
        if ($request->filled('resume_status')) {
            $filters['Resume Status'] = ucfirst(str_replace('_', ' ', $request->resume_status));
        }
        if ($request->filled('placement_status')) {
            $filters['Placement Status'] = ucfirst(str_replace('_', ' ', $request->placement_status));
        }

        return $filters;
    }

    /**
     * Show handover history.
     */
    public function handovers(Request $request)
    {
        $handovers = RecruitmentHandover::with(['company', 'handedOverBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('recruitment.handovers.index', compact('handovers'));
    }
}
