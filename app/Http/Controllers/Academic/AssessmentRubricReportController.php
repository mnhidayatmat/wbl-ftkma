<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentRubricReport;
use App\Models\AssessmentRubricReportDescriptor;
use App\Models\AssessmentRubricReportElement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssessmentRubricReportController extends Controller
{
    /**
     * Check if the current user is authorized to manage rubric reports for an assessment.
     */
    private function isAuthorizedForAssessment(Assessment $assessment): bool
    {
        $user = auth()->user();
        $courseCode = $assessment->course_code;

        return $user->isAdmin() ||
            ($courseCode === 'FYP' && $user->isFypCoordinator()) ||
            ($courseCode === 'IP' && $user->isIpCoordinator()) ||
            ($courseCode === 'OSH' && $user->isOshCoordinator()) ||
            ($courseCode === 'LI' && ($user->isLiCoordinator() || $user->isWblCoordinator())) ||
            ($courseCode === 'PPE' && $user->isPpeCoordinator());
    }

    /**
     * Get the route name prefix for an assessment's course.
     */
    private function getRoutePrefix(Assessment $assessment): string
    {
        return 'academic.'.strtolower($assessment->course_code);
    }

    /**
     * Display the rubric report for an assessment.
     */
    public function show(Assessment $assessment): View|RedirectResponse
    {
        if (! $this->isAuthorizedForAssessment($assessment)) {
            abort(403, 'Unauthorized access.');
        }

        $rubricReport = $assessment->rubricReport;

        if (! $rubricReport) {
            return redirect()->route($this->getRoutePrefix($assessment).'.assessments.rubric-report.create', $assessment)
                ->with('info', 'No rubric report exists for this assessment. Please create one.');
        }

        $rubricReport->load(['elements.descriptors', 'creator', 'uploader']);

        $courseName = Assessment::getCourseCodes()[$assessment->course_code] ?? $assessment->course_code;
        $ratingLevels = AssessmentRubricReport::RATING_LEVELS;

        return view('academic.assessments.rubric-report.show', compact(
            'assessment',
            'rubricReport',
            'courseName',
            'ratingLevels'
        ));
    }

    /**
     * Show the form for creating a new rubric report.
     */
    public function create(Assessment $assessment): View|RedirectResponse
    {
        if (! $this->isAuthorizedForAssessment($assessment)) {
            abort(403, 'Unauthorized access.');
        }

        // Check if rubric report already exists
        if ($assessment->rubricReport) {
            return redirect()->route($this->getRoutePrefix($assessment).'.assessments.rubric-report.show', $assessment)
                ->with('info', 'A rubric report already exists for this assessment.');
        }

        $courseName = Assessment::getCourseCodes()[$assessment->course_code] ?? $assessment->course_code;
        $ratingLevels = AssessmentRubricReport::RATING_LEVELS;

        return view('academic.assessments.rubric-report.create', compact(
            'assessment',
            'courseName',
            'ratingLevels'
        ));
    }

    /**
     * Store a newly created rubric report.
     */
    public function store(Request $request, Assessment $assessment): RedirectResponse
    {
        if (! $this->isAuthorizedForAssessment($assessment)) {
            abort(403, 'Unauthorized access.');
        }

        // Check if rubric report already exists
        if ($assessment->rubricReport) {
            return redirect()->route($this->getRoutePrefix($assessment).'.assessments.rubric-report.show', $assessment)
                ->with('error', 'A rubric report already exists for this assessment.');
        }

        $request->validate([
            'input_type' => 'required|in:manual,file',
        ]);

        if ($request->input_type === 'file') {
            return $this->storeFileUpload($request, $assessment);
        }

        return $this->storeManualInput($request, $assessment);
    }

    /**
     * Store a file upload rubric report.
     */
    private function storeFileUpload(Request $request, Assessment $assessment): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,xlsx,xls,doc,docx|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('rubric-reports/'.$assessment->course_code, 'public');

        AssessmentRubricReport::create([
            'assessment_id' => $assessment->id,
            'input_type' => 'file',
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route($this->getRoutePrefix($assessment).'.assessments.rubric-report.show', $assessment)
            ->with('success', 'Rubric report file uploaded successfully.');
    }

    /**
     * Store a manual input rubric report.
     */
    private function storeManualInput(Request $request, Assessment $assessment): RedirectResponse
    {
        $request->validate([
            'elements' => 'required|array|min:1',
            'elements.*.element_name' => 'required|string|max:255',
            'elements.*.criteria_keywords' => 'nullable|string',
            'elements.*.weight_percentage' => 'nullable|numeric|min:0|max:100',
            'elements.*.descriptors' => 'required|array|size:5',
            'elements.*.descriptors.*.descriptor' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $assessment) {
            $rubricReport = AssessmentRubricReport::create([
                'assessment_id' => $assessment->id,
                'input_type' => 'manual',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->elements as $order => $elementData) {
                $element = AssessmentRubricReportElement::create([
                    'rubric_report_id' => $rubricReport->id,
                    'element_name' => $elementData['element_name'],
                    'criteria_keywords' => $elementData['criteria_keywords'] ?? null,
                    'weight_percentage' => $elementData['weight_percentage'] ?? null,
                    'order' => $order,
                ]);

                foreach ($elementData['descriptors'] as $level => $descriptorData) {
                    $levelNum = (int) $level + 1; // Convert 0-indexed to 1-5
                    $ratingLevel = AssessmentRubricReport::RATING_LEVELS[$levelNum] ?? null;

                    AssessmentRubricReportDescriptor::create([
                        'element_id' => $element->id,
                        'level' => $levelNum,
                        'label' => $ratingLevel['label'] ?? 'Level '.$levelNum,
                        'descriptor' => $descriptorData['descriptor'],
                    ]);
                }
            }
        });

        return redirect()->route($this->getRoutePrefix($assessment).'.assessments.rubric-report.show', $assessment)
            ->with('success', 'Rubric report created successfully.');
    }

    /**
     * Show the form for editing the rubric report.
     */
    public function edit(Assessment $assessment): View|RedirectResponse
    {
        if (! $this->isAuthorizedForAssessment($assessment)) {
            abort(403, 'Unauthorized access.');
        }

        $rubricReport = $assessment->rubricReport;

        if (! $rubricReport) {
            return redirect()->route($this->getRoutePrefix($assessment).'.assessments.rubric-report.create', $assessment)
                ->with('info', 'No rubric report exists for this assessment. Please create one.');
        }

        $rubricReport->load(['elements.descriptors']);

        $courseName = Assessment::getCourseCodes()[$assessment->course_code] ?? $assessment->course_code;
        $ratingLevels = AssessmentRubricReport::RATING_LEVELS;

        return view('academic.assessments.rubric-report.edit', compact(
            'assessment',
            'rubricReport',
            'courseName',
            'ratingLevels'
        ));
    }

    /**
     * Update the rubric report.
     */
    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        if (! $this->isAuthorizedForAssessment($assessment)) {
            abort(403, 'Unauthorized access.');
        }

        $rubricReport = $assessment->rubricReport;

        if (! $rubricReport) {
            return redirect()->route($this->getRoutePrefix($assessment).'.assessments.rubric-report.create', $assessment)
                ->with('error', 'No rubric report exists for this assessment.');
        }

        // If it's a file upload type and a new file is provided
        if ($rubricReport->isFileUpload()) {
            if ($request->hasFile('file')) {
                $request->validate([
                    'file' => 'required|file|mimes:pdf,xlsx,xls,doc,docx|max:10240',
                ]);

                // Delete old file
                if ($rubricReport->file_path) {
                    Storage::disk('public')->delete($rubricReport->file_path);
                }

                $file = $request->file('file');
                $path = $file->store('rubric-reports/'.$assessment->course_code, 'public');

                $rubricReport->update([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => auth()->id(),
                ]);
            }

            return redirect()->route($this->getRoutePrefix($assessment).'.assessments.rubric-report.show', $assessment)
                ->with('success', 'Rubric report updated successfully.');
        }

        // Manual input update
        $request->validate([
            'elements' => 'required|array|min:1',
            'elements.*.element_name' => 'required|string|max:255',
            'elements.*.criteria_keywords' => 'nullable|string',
            'elements.*.weight_percentage' => 'nullable|numeric|min:0|max:100',
            'elements.*.descriptors' => 'required|array|size:5',
            'elements.*.descriptors.*.descriptor' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $rubricReport) {
            // Delete existing elements and descriptors
            $rubricReport->elements()->delete();

            // Create new elements and descriptors
            foreach ($request->elements as $order => $elementData) {
                $element = AssessmentRubricReportElement::create([
                    'rubric_report_id' => $rubricReport->id,
                    'element_name' => $elementData['element_name'],
                    'criteria_keywords' => $elementData['criteria_keywords'] ?? null,
                    'weight_percentage' => $elementData['weight_percentage'] ?? null,
                    'order' => $order,
                ]);

                foreach ($elementData['descriptors'] as $level => $descriptorData) {
                    $levelNum = (int) $level + 1;
                    $ratingLevel = AssessmentRubricReport::RATING_LEVELS[$levelNum] ?? null;

                    AssessmentRubricReportDescriptor::create([
                        'element_id' => $element->id,
                        'level' => $levelNum,
                        'label' => $ratingLevel['label'] ?? 'Level '.$levelNum,
                        'descriptor' => $descriptorData['descriptor'],
                    ]);
                }
            }
        });

        return redirect()->route($this->getRoutePrefix($assessment).'.assessments.rubric-report.show', $assessment)
            ->with('success', 'Rubric report updated successfully.');
    }

    /**
     * Delete the rubric report.
     */
    public function destroy(Assessment $assessment): RedirectResponse
    {
        if (! $this->isAuthorizedForAssessment($assessment)) {
            abort(403, 'Unauthorized access.');
        }

        $rubricReport = $assessment->rubricReport;

        if (! $rubricReport) {
            return redirect()->route($this->getRoutePrefix($assessment).'.assessments.index')
                ->with('error', 'No rubric report exists for this assessment.');
        }

        // Delete file if it's a file upload type
        if ($rubricReport->isFileUpload() && $rubricReport->file_path) {
            Storage::disk('public')->delete($rubricReport->file_path);
        }

        $rubricReport->delete();

        return redirect()->route($this->getRoutePrefix($assessment).'.assessments.index')
            ->with('success', 'Rubric report deleted successfully.');
    }

    /**
     * Download the uploaded rubric report file.
     */
    public function download(Assessment $assessment): StreamedResponse|RedirectResponse
    {
        if (! $this->isAuthorizedForAssessment($assessment)) {
            abort(403, 'Unauthorized access.');
        }

        $rubricReport = $assessment->rubricReport;

        if (! $rubricReport || ! $rubricReport->isFileUpload() || ! $rubricReport->file_path) {
            return redirect()->back()->with('error', 'No file available for download.');
        }

        if (! Storage::disk('public')->exists($rubricReport->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download(
            $rubricReport->file_path,
            $rubricReport->file_name
        );
    }

    /**
     * Export rubric report as PDF.
     */
    public function exportPdf(Assessment $assessment)
    {
        if (! $this->isAuthorizedForAssessment($assessment)) {
            abort(403, 'Unauthorized access.');
        }

        $rubricReport = $assessment->rubricReport;

        if (! $rubricReport) {
            return redirect()->back()->with('error', 'No rubric report exists for this assessment.');
        }

        // For file uploads, just download the original file
        if ($rubricReport->isFileUpload()) {
            return $this->download($assessment);
        }

        $rubricReport->load(['elements.descriptors']);

        $courseName = Assessment::getCourseCodes()[$assessment->course_code] ?? $assessment->course_code;
        $ratingLevels = AssessmentRubricReport::RATING_LEVELS;

        $pdf = Pdf::loadView('academic.assessments.rubric-report.pdf', compact(
            'assessment',
            'rubricReport',
            'courseName',
            'ratingLevels'
        ))->setPaper('a4', 'landscape');

        $filename = 'Rubric_Report_'.str_replace(' ', '_', $assessment->assessment_name).'_'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
