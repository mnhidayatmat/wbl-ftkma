<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentRubric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    /**
     * Get the route name for assessments index based on course code.
     */
    private function getAssessmentsIndexRoute(string $courseCode): string
    {
        $courseLower = strtolower($courseCode);

        return "academic.{$courseLower}.assessments.index";
    }

    /**
     * Display a listing of assessments for a specific course.
     */
    public function index(Request $request, string $course = 'PPE'): View
    {
        // Only Admin can manage assessments
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Validate course code
        $validCourses = ['PPE', 'IP', 'OSH', 'FYP', 'LI'];
        if (! in_array(strtoupper($course), $validCourses)) {
            abort(404, 'Course not found.');
        }

        $courseCode = strtoupper($course);

        // For FYP, IP, PPE, LI, and OSH, group by assessment name to handle multiple CLOs per assessment
        if (in_array($courseCode, ['FYP', 'IP', 'PPE', 'LI', 'OSH'])) {
            $assessments = Assessment::where('course_code', $courseCode)
                ->with(['creator', 'clos'])
                ->orderBy('assessment_name')
                ->get()
                ->groupBy('assessment_name');

            // Calculate total percentage from all CLOs
            $totalPercentage = Assessment::where('course_code', $courseCode)
                ->get()
                ->sum(function ($assessment) {
                    return $assessment->clos->sum('weight_percentage') ?: $assessment->weight_percentage;
                });
        } else {
            // For any other future courses, return flat collection
            $assessments = Assessment::where('course_code', $courseCode)
                ->with('creator')
                ->orderBy('clo_code')
                ->orderBy('assessment_name')
                ->get();

            // Calculate total percentage (use weight_percentage from assessment directly)
            $totalPercentage = $assessments->sum('weight_percentage');
        }

        $courseName = Assessment::getCourseCodes()[$courseCode] ?? $courseCode;
        $courseLower = strtolower($courseCode);

        return view("academic.{$courseLower}.assessments.index", compact(
            'assessments',
            'courseCode',
            'courseName',
            'totalPercentage'
        ));
    }

    /**
     * Show the form for creating a new assessment.
     */
    public function create(Request $request, string $course = 'PPE'): View
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validCourses = ['PPE', 'IP', 'OSH', 'FYP', 'LI'];
        if (! in_array(strtoupper($course), $validCourses)) {
            abort(404, 'Course not found.');
        }

        $courseCode = strtoupper($course);
        $courseName = Assessment::getCourseCodes()[$courseCode] ?? $courseCode;
        $courseLower = strtolower($courseCode);

        $assessmentTypes = Assessment::getAssessmentTypes();
        $evaluatorRoles = Assessment::getEvaluatorRoles();

        // Get approved CLO codes from CLO-PLO mappings (only those allowed for assessment)
        $approvedCloCodes = \App\Models\CloPloMapping::forCourse($courseCode)
            ->allowedForAssessment()
            ->pluck('clo_code')
            ->toArray();

        // Fallback to all CLO codes if no mappings exist yet
        $allCloCodes = Assessment::getCloCodes($courseCode);
        $cloCodes = ! empty($approvedCloCodes) ? $approvedCloCodes : $allCloCodes;

        return view("academic.{$courseLower}.assessments.create", compact(
            'courseCode',
            'courseName',
            'assessmentTypes',
            'evaluatorRoles',
            'cloCodes'
        ));
    }

    /**
     * Store a newly created assessment.
     */
    public function store(Request $request, string $course = 'PPE'): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validCourses = ['PPE', 'IP', 'OSH', 'FYP', 'LI'];
        if (! in_array(strtoupper($course), $validCourses)) {
            abort(404, 'Course not found.');
        }

        $courseCode = strtoupper($course);
        $totalWeight = 0; // Initialize for component/rubric validation

        // For all courses, handle multiple CLOs (FYP-style)

        $validated = $request->validate([
            'assessment_name' => ['required', 'string', 'max:255'],
            'assessment_type' => ['required', 'string'],
            'evaluator_role' => ['nullable', 'string', 'in:lecturer,at,ic,supervisor_li'], // Keep for backward compatibility
            'evaluator_total_score' => ['nullable', 'numeric', 'min:0', 'max:100'], // Keep for backward compatibility
            'evaluators' => ['required', 'array', 'min:1'],
            'evaluators.*.role' => ['required', 'string', 'in:lecturer,at,ic,supervisor_li'],
            'evaluators.*.total_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'clos' => ['required', 'array', 'min:1'],
            'clos.*.clo_code' => ['required', 'string'],
            'clos.*.weight_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        // Validate CLO codes
        $approvedCloCodes = \App\Models\CloPloMapping::forCourse($courseCode)
            ->allowedForAssessment()
            ->pluck('clo_code')
            ->toArray();

        $allCloCodes = Assessment::getCloCodes($courseCode);
        $validCloCodes = ! empty($approvedCloCodes) ? $approvedCloCodes : $allCloCodes;

        $totalWeight = 0;
        foreach ($validated['clos'] as $clo) {
            if (! in_array($clo['clo_code'], $validCloCodes)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Invalid CLO code: {$clo['clo_code']}. Only approved CLOs can be used in assessments.");
            }
            $totalWeight += floatval($clo['weight_percentage']);
        }

        // Note: CLO weightages no longer need to sum to 100%
        // The total score is multiplied by each CLO's weightage to get the actual contribution

        // Validate evaluators total score matches total CLO weight
        $totalEvaluatorScore = 0;
        foreach ($validated['evaluators'] as $evaluator) {
            $totalEvaluatorScore += floatval($evaluator['total_score']);
        }

        if (abs($totalEvaluatorScore - $totalWeight) > 0.05) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Total evaluator scores ({$totalEvaluatorScore}%) must equal total CLO weight ({$totalWeight}%).");
        }

        // Check total percentage for the course (sum of all CLO weights from all assessments)
        $existingTotal = \App\Models\AssessmentClo::whereHas('assessment', function ($q) use ($courseCode) {
            $q->where('course_code', $courseCode)->where('is_active', true);
        })->sum('weight_percentage');

        if (($existingTotal + $totalWeight) > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Total weight percentage would exceed 100%. Current total: {$existingTotal}%");
        }

        // Create assessment (without clo_code and weight_percentage for FYP - they're in assessment_clos)
        // Use first evaluator for backward compatibility
        $firstEvaluator = $validated['evaluators'][0];
        $assessment = Assessment::create([
            'course_code' => $courseCode,
            'assessment_name' => $validated['assessment_name'],
            'assessment_type' => $validated['assessment_type'],
            'clo_code' => $validated['clos'][0]['clo_code'], // Keep first CLO for backward compatibility
            'weight_percentage' => $totalWeight, // Total weight for backward compatibility
            'evaluator_role' => $firstEvaluator['role'], // Use first evaluator for backward compatibility
            'is_active' => $request->has('is_active') ? true : false,
            'created_by' => auth()->id(),
        ]);

        // Save multiple CLOs
        $this->saveClos($assessment, $validated['clos']);

        // Save multiple evaluators
        $this->saveEvaluators($assessment, $validated['evaluators']);

        // Determine total weight for components/rubrics validation
        $totalAssessmentWeight = $totalWeight ?? 0;

        // Handle rubric questions for Oral/Rubric types
        if (in_array($validated['assessment_type'], ['Oral', 'Rubric']) && $request->has('rubrics')) {
            try {
                $this->saveRubrics($assessment, $request->input('rubrics', []), $totalAssessmentWeight);
            } catch (\Exception $e) {
                // Delete the assessment if rubric validation fails
                $assessment->delete();

                return redirect()->back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        }

        // Handle assessment components (sub-questions) vs Logbook components
        if ($validated['assessment_type'] === 'Logbook') {
            try {
                $logbookComponents = $request->input('logbook_components', []);
                $this->saveLogbookComponents($assessment, $logbookComponents, $totalAssessmentWeight);
            } catch (\Exception $e) {
                $assessment->delete();

                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        } else {
            // Regular assessment components
            try {
                $components = $request->input('components', []);
                $this->saveComponents($assessment, $components, $totalAssessmentWeight);
            } catch (\Exception $e) {
                $assessment->delete();

                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }

        return redirect()->route($this->getAssessmentsIndexRoute($courseCode))
            ->with('success', 'Assessment created successfully.');
    }

    /**
     * Show the form for editing the specified assessment.
     */
    public function edit(Request $request, Assessment $assessment, ?string $course = null): View
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Get course from assessment
        $course = $course ?? $assessment->course_code;

        $validCourses = ['PPE', 'IP', 'OSH', 'FYP', 'LI'];
        if (! in_array(strtoupper($course), $validCourses) || $assessment->course_code !== strtoupper($course)) {
            abort(404, 'Assessment not found.');
        }

        $courseCode = strtoupper($course);
        $courseName = Assessment::getCourseCodes()[$courseCode] ?? $courseCode;
        $courseLower = strtolower($courseCode);

        $assessmentTypes = Assessment::getAssessmentTypes();
        $evaluatorRoles = Assessment::getEvaluatorRoles();

        // Get approved CLO codes from CLO-PLO mappings (only those allowed for assessment)
        $approvedCloCodes = \App\Models\CloPloMapping::forCourse($courseCode)
            ->allowedForAssessment()
            ->pluck('clo_code')
            ->toArray();

        // Fallback to all CLO codes if no mappings exist yet
        $allCloCodes = Assessment::getCloCodes($courseCode);
        $cloCodes = ! empty($approvedCloCodes) ? $approvedCloCodes : $allCloCodes;

        $rubrics = $assessment->rubrics()->ordered()->get();
        $allComponents = $assessment->components()->ordered()->get();

        // Separate logbook components (have duration_label) from regular components
        $components = $allComponents->filter(function ($component) {
            return empty($component->duration_label);
        })->values();

        $logbookComponents = $allComponents->filter(function ($component) {
            return ! empty($component->duration_label);
        })->values();

        // Load CLOs and evaluators for all courses (FYP-style)
        $assessment->load('clos', 'evaluators');

        return view("academic.{$courseLower}.assessments.edit", compact(
            'assessment',
            'courseCode',
            'courseName',
            'assessmentTypes',
            'evaluatorRoles',
            'cloCodes',
            'rubrics',
            'components',
            'logbookComponents'
        ));
    }

    /**
     * Update the specified assessment.
     */
    public function update(Request $request, Assessment $assessment, ?string $course = null): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Get course from assessment
        $course = $course ?? $assessment->course_code;

        $validCourses = ['PPE', 'IP', 'OSH', 'FYP', 'LI'];
        if (! in_array(strtoupper($course), $validCourses) || $assessment->course_code !== strtoupper($course)) {
            abort(404, 'Assessment not found.');
        }

        $courseCode = strtoupper($course);

        // For all courses, handle multiple CLOs (FYP-style)

        $validated = $request->validate([
            'assessment_name' => ['required', 'string', 'max:255'],
            'assessment_type' => ['required', 'string'],
            'evaluator_role' => ['nullable', 'string', 'in:lecturer,at,ic,supervisor_li'], // Keep for backward compatibility
            'evaluator_total_score' => ['nullable', 'numeric', 'min:0', 'max:100'], // Keep for backward compatibility
            'evaluators' => ['required', 'array', 'min:1'],
            'evaluators.*.role' => ['required', 'string', 'in:lecturer,at,ic,supervisor_li'],
            'evaluators.*.total_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'clos' => ['required', 'array', 'min:1'],
            'clos.*.clo_code' => ['required', 'string'],
            'clos.*.weight_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        // Validate CLO codes
        $approvedCloCodes = \App\Models\CloPloMapping::forCourse($courseCode)
            ->allowedForAssessment()
            ->pluck('clo_code')
            ->toArray();

        $allCloCodes = Assessment::getCloCodes($courseCode);
        $validCloCodes = ! empty($approvedCloCodes) ? $approvedCloCodes : $allCloCodes;

        $totalWeight = 0;
        foreach ($validated['clos'] as $clo) {
            if (! in_array($clo['clo_code'], $validCloCodes)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Invalid CLO code: {$clo['clo_code']}. Only approved CLOs can be used in assessments.");
            }
            $totalWeight += floatval($clo['weight_percentage']);
        }

        // Note: CLO weightages no longer need to sum to 100%
        // The total score is multiplied by each CLO's weightage to get the actual contribution

        // Validate evaluators total score matches total CLO weight
        $totalEvaluatorScore = 0;
        foreach ($validated['evaluators'] as $evaluator) {
            $totalEvaluatorScore += floatval($evaluator['total_score']);
        }

        if (abs($totalEvaluatorScore - $totalWeight) > 0.05) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Total evaluator scores ({$totalEvaluatorScore}%) must equal total CLO weight ({$totalWeight}%).");
        }

        // Update assessment (without clo_code and weight_percentage for FYP - they're in assessment_clos)
        // Use first evaluator and first CLO for backward compatibility
        $firstEvaluator = $validated['evaluators'][0];
        $assessment->update([
            'assessment_name' => $validated['assessment_name'],
            'assessment_type' => $validated['assessment_type'],
            'clo_code' => $validated['clos'][0]['clo_code'], // Keep first CLO for backward compatibility
            'weight_percentage' => $totalWeight, // Total weight for backward compatibility
            'evaluator_role' => $firstEvaluator['role'], // Use first evaluator for backward compatibility
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        // Save multiple CLOs
        $this->saveClos($assessment, $validated['clos']);

        // Save multiple evaluators
        $this->saveEvaluators($assessment, $validated['evaluators']);

        // Handle rubric questions for Oral/Rubric types
        if (in_array($validated['assessment_type'], ['Oral', 'Rubric'])) {
            try {
                $rubrics = $request->input('rubrics', []);
                // Determine weight from CLOs (FYP-style for all courses)
                $rubricWeight = $assessment->clos()->sum('weight_percentage') ?: ($totalWeight ?? $assessment->weight_percentage);
                $this->saveRubrics($assessment, $rubrics, $rubricWeight);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        } else {
            // Delete rubrics if assessment type changed
            $assessment->rubrics()->delete();
        }

        // Handle components based on assessment type
        if ($validated['assessment_type'] === 'Logbook') {
            // Delete regular components if switching to Logbook
            $assessment->components()->whereNull('duration_label')->delete();

            try {
                $logbookComponents = $request->input('logbook_components', []);
                $logbookWeight = $assessment->clos()->sum('weight_percentage') ?: ($totalWeight ?? $assessment->weight_percentage);
                $this->saveLogbookComponents($assessment, $logbookComponents, $logbookWeight);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        } else {
            // Regular assessment components
            try {
                // Delete logbook components if switching from Logbook
                $assessment->components()->whereNotNull('duration_label')->delete();

                $components = $request->input('components', []);
                $componentWeight = $assessment->clos()->sum('weight_percentage') ?: ($totalWeight ?? $assessment->weight_percentage);
                $this->saveComponents($assessment, $components, $componentWeight);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }

        return redirect()->route($this->getAssessmentsIndexRoute($courseCode))
            ->with('success', 'Assessment updated successfully.');
    }

    /**
     * Remove the specified assessment.
     */
    public function destroy(Assessment $assessment, ?string $course = null): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Get course from assessment
        $course = $course ?? $assessment->course_code;

        $validCourses = ['PPE', 'IP', 'OSH', 'FYP', 'LI'];
        if (! in_array(strtoupper($course), $validCourses) || $assessment->course_code !== strtoupper($course)) {
            abort(404, 'Assessment not found.');
        }

        $courseCode = $assessment->course_code;
        $assessment->delete();

        return redirect()->route($this->getAssessmentsIndexRoute($courseCode))
            ->with('success', 'Assessment deleted successfully.');
    }

    /**
     * Toggle active status of an assessment.
     */
    public function toggleActive(Assessment $assessment, ?string $course = null): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Get course from assessment
        $course = $course ?? $assessment->course_code;

        $validCourses = ['PPE', 'IP', 'OSH', 'FYP', 'LI'];
        if (! in_array(strtoupper($course), $validCourses) || $assessment->course_code !== strtoupper($course)) {
            abort(404, 'Assessment not found.');
        }

        $assessment->update(['is_active' => ! $assessment->is_active]);

        return redirect()->route($this->getAssessmentsIndexRoute($assessment->course_code))
            ->with('success', 'Assessment status updated successfully.');
    }

    /**
     * Save rubric questions for an assessment.
     */
    private function saveRubrics(Assessment $assessment, array $rubrics, float $assessmentWeight): void
    {
        $rubricIds = [];
        $totalRubricWeight = 0;

        foreach ($rubrics as $index => $rubricData) {
            if (empty($rubricData['question_title'])) {
                continue;
            }

            // Handle both new and existing rubrics
            if (! empty($rubricData['id'])) {
                // Update existing rubric
                $rubric = AssessmentRubric::where('id', $rubricData['id'])
                    ->where('assessment_id', $assessment->id)
                    ->first();

                if ($rubric) {
                    $rubric->update([
                        'question_code' => $rubricData['question_code'] ?? 'Q'.($index + 1),
                        'question_title' => $rubricData['question_title'],
                        'question_description' => $rubricData['question_description'] ?? null,
                        'clo_code' => $rubricData['clo_code'],
                        'weight_percentage' => $rubricData['weight_percentage'],
                        'rubric_min' => $rubricData['rubric_min'] ?? 1,
                        'rubric_max' => $rubricData['rubric_max'] ?? 5,
                        'order' => $index,
                    ]);
                } else {
                    // Rubric ID doesn't exist, create new one
                    $rubric = AssessmentRubric::create([
                        'assessment_id' => $assessment->id,
                        'question_code' => $rubricData['question_code'] ?? 'Q'.($index + 1),
                        'question_title' => $rubricData['question_title'],
                        'question_description' => $rubricData['question_description'] ?? null,
                        'clo_code' => $rubricData['clo_code'],
                        'weight_percentage' => $rubricData['weight_percentage'],
                        'rubric_min' => $rubricData['rubric_min'] ?? 1,
                        'rubric_max' => $rubricData['rubric_max'] ?? 5,
                        'order' => $index,
                    ]);
                }
            } else {
                // Create new rubric
                $rubric = AssessmentRubric::create([
                    'assessment_id' => $assessment->id,
                    'question_code' => $rubricData['question_code'] ?? 'Q'.($index + 1),
                    'question_title' => $rubricData['question_title'],
                    'question_description' => $rubricData['question_description'] ?? null,
                    'clo_code' => $rubricData['clo_code'],
                    'weight_percentage' => $rubricData['weight_percentage'],
                    'rubric_min' => $rubricData['rubric_min'] ?? 1,
                    'rubric_max' => $rubricData['rubric_max'] ?? 5,
                    'order' => $index,
                ]);
            }

            $rubricIds[] = $rubric->id;
            $totalRubricWeight += floatval($rubric->weight_percentage);
        }

        // Delete rubrics that were removed
        if (! empty($rubricIds)) {
            $assessment->rubrics()->whereNotIn('id', $rubricIds)->delete();
        } else {
            // If no rubrics provided, delete all existing ones
            $assessment->rubrics()->delete();
        }

        // Validate total rubric weight equals assessment weight (only if rubrics exist)
        if (! empty($rubricIds) && abs($totalRubricWeight - $assessmentWeight) > 0.05) {
            $difference = abs($totalRubricWeight - $assessmentWeight);
            $message = sprintf(
                'Total rubric weight (%.2f%%) must equal assessment weight (%.2f%%). Current difference: %.2f%%. Please adjust the rubric weights so they sum to %.2f%%.',
                $totalRubricWeight,
                $assessmentWeight,
                $difference,
                $assessmentWeight
            );
            throw new \Exception($message);
        }
    }

    /**
     * Save assessment components (sub-questions/elements).
     */
    private function saveComponents(Assessment $assessment, array $components, float $assessmentWeight): void
    {
        $componentIds = [];
        $totalComponentWeight = 0;

        foreach ($components as $index => $componentData) {
            // Skip if component name is empty or missing
            if (empty($componentData['component_name'])) {
                continue;
            }

            // Validate required fields
            if (empty($componentData['clo_code'])) {
                throw new \Exception('CLO code is required for component: '.($componentData['component_name'] ?? 'Component '.($index + 1)));
            }

            if (! isset($componentData['weight_percentage']) || $componentData['weight_percentage'] === '' || $componentData['weight_percentage'] === null) {
                throw new \Exception('Weight percentage is required for component: '.($componentData['component_name'] ?? 'Component '.($index + 1)));
            }

            $weightPercentage = floatval($componentData['weight_percentage']);
            if ($weightPercentage < 0 || $weightPercentage > $assessmentWeight) {
                throw new \Exception("Weight percentage for component '{$componentData['component_name']}' must be between 0 and {$assessmentWeight}%");
            }

            // Handle both new and existing components
            if (! empty($componentData['id'])) {
                // Update existing component
                $component = \App\Models\AssessmentComponent::where('id', $componentData['id'])
                    ->where('assessment_id', $assessment->id)
                    ->first();

                if ($component) {
                    $component->update([
                        'component_name' => $componentData['component_name'],
                        'criteria_keywords' => $componentData['criteria_keywords'] ?? null,
                        'clo_code' => $componentData['clo_code'],
                        'weight_percentage' => $weightPercentage,
                        'min_score' => ! empty($componentData['min_score']) ? floatval($componentData['min_score']) : null,
                        'max_score' => ! empty($componentData['max_score']) ? floatval($componentData['max_score']) : null,
                        'example_answer' => $componentData['example_answer'] ?? null,
                        'order' => $componentData['order'] ?? $index,
                    ]);
                } else {
                    // Component ID doesn't exist, create new one
                    $component = \App\Models\AssessmentComponent::create([
                        'assessment_id' => $assessment->id,
                        'component_name' => $componentData['component_name'],
                        'criteria_keywords' => $componentData['criteria_keywords'] ?? null,
                        'clo_code' => $componentData['clo_code'],
                        'weight_percentage' => $weightPercentage,
                        'min_score' => ! empty($componentData['min_score']) ? floatval($componentData['min_score']) : null,
                        'max_score' => ! empty($componentData['max_score']) ? floatval($componentData['max_score']) : null,
                        'example_answer' => $componentData['example_answer'] ?? null,
                        'order' => $componentData['order'] ?? $index,
                    ]);
                }
            } else {
                // Create new component
                $component = \App\Models\AssessmentComponent::create([
                    'assessment_id' => $assessment->id,
                    'component_name' => $componentData['component_name'],
                    'criteria_keywords' => $componentData['criteria_keywords'] ?? null,
                    'clo_code' => $componentData['clo_code'],
                    'weight_percentage' => $weightPercentage,
                    'min_score' => ! empty($componentData['min_score']) ? floatval($componentData['min_score']) : null,
                    'max_score' => ! empty($componentData['max_score']) ? floatval($componentData['max_score']) : null,
                    'example_answer' => $componentData['example_answer'] ?? null,
                    'order' => $componentData['order'] ?? $index,
                ]);
            }

            $componentIds[] = $component->id;
            $totalComponentWeight += $weightPercentage;
        }

        // Delete components that were removed
        if (! empty($componentIds)) {
            $assessment->components()->whereNotIn('id', $componentIds)->delete();
        } else {
            // If no components provided, delete all existing ones
            $assessment->components()->delete();
        }

        // Validate total component weight equals assessment weight (only if components exist)
        if (! empty($componentIds) && abs($totalComponentWeight - $assessmentWeight) > 0.05) {
            $difference = abs($totalComponentWeight - $assessmentWeight);
            $message = sprintf(
                'Total component weight (%.2f%%) must equal assessment weight (%.2f%%). Current difference: %.2f%%. Please adjust the component weights so they sum to %.2f%%.',
                $totalComponentWeight,
                $assessmentWeight,
                $difference,
                $assessmentWeight
            );
            throw new \Exception($message);
        }
    }

    /**
     * Save Logbook assessment components with 1-10 rubric scale.
     */
    private function saveLogbookComponents(Assessment $assessment, array $components, float $assessmentWeight): void
    {
        $componentIds = [];
        $totalComponentWeight = 0;

        foreach ($components as $index => $componentData) {
            // Skip if duration label is empty or missing
            if (empty($componentData['duration_label'])) {
                continue;
            }

            // Validate required fields
            if (empty($componentData['clo_code'])) {
                throw new \Exception('CLO code is required for logbook component: '.($componentData['duration_label'] ?? 'Month '.($index + 1)));
            }

            if (! isset($componentData['weight_percentage']) || $componentData['weight_percentage'] === '' || $componentData['weight_percentage'] === null) {
                throw new \Exception('Weight percentage is required for logbook component: '.($componentData['duration_label'] ?? 'Month '.($index + 1)));
            }

            $weightPercentage = floatval($componentData['weight_percentage']);
            if ($weightPercentage < 0 || $weightPercentage > $assessmentWeight) {
                throw new \Exception("Weight percentage for logbook component '{$componentData['duration_label']}' must be between 0 and {$assessmentWeight}%");
            }

            // Handle both new and existing components
            if (! empty($componentData['id'])) {
                // Update existing component
                $component = \App\Models\AssessmentComponent::where('id', $componentData['id'])
                    ->where('assessment_id', $assessment->id)
                    ->first();

                if ($component) {
                    $component->update([
                        'component_name' => $componentData['duration_label'], // Use duration_label as component_name
                        'criteria_keywords' => $componentData['criteria_keywords'] ?? null,
                        'clo_code' => $componentData['clo_code'],
                        'weight_percentage' => $weightPercentage,
                        'rubric_scale_min' => $componentData['rubric_scale_min'] ?? 1,
                        'rubric_scale_max' => $componentData['rubric_scale_max'] ?? 10,
                        'duration_label' => $componentData['duration_label'],
                        'order' => $componentData['order'] ?? $index,
                    ]);
                } else {
                    // Component ID doesn't exist, create new one
                    $component = \App\Models\AssessmentComponent::create([
                        'assessment_id' => $assessment->id,
                        'component_name' => $componentData['duration_label'],
                        'criteria_keywords' => $componentData['criteria_keywords'] ?? null,
                        'clo_code' => $componentData['clo_code'],
                        'weight_percentage' => $weightPercentage,
                        'rubric_scale_min' => $componentData['rubric_scale_min'] ?? 1,
                        'rubric_scale_max' => $componentData['rubric_scale_max'] ?? 10,
                        'duration_label' => $componentData['duration_label'],
                        'order' => $componentData['order'] ?? $index,
                    ]);
                }
            } else {
                // Create new component
                $component = \App\Models\AssessmentComponent::create([
                    'assessment_id' => $assessment->id,
                    'component_name' => $componentData['duration_label'],
                    'criteria_keywords' => $componentData['criteria_keywords'] ?? null,
                    'clo_code' => $componentData['clo_code'],
                    'weight_percentage' => $weightPercentage,
                    'rubric_scale_min' => $componentData['rubric_scale_min'] ?? 1,
                    'rubric_scale_max' => $componentData['rubric_scale_max'] ?? 10,
                    'duration_label' => $componentData['duration_label'],
                    'order' => $componentData['order'] ?? $index,
                ]);
            }

            $componentIds[] = $component->id;
            $totalComponentWeight += $weightPercentage;
        }

        // Delete logbook components that were removed (only those with duration_label)
        if (! empty($componentIds)) {
            $assessment->components()
                ->whereNotNull('duration_label')
                ->whereNotIn('id', $componentIds)
                ->delete();
        } else {
            // If no components provided, delete all existing logbook components
            $assessment->components()->whereNotNull('duration_label')->delete();
        }

        // Validate total component weight equals assessment weight (only if components exist)
        if (! empty($componentIds) && abs($totalComponentWeight - $assessmentWeight) > 0.05) {
            $difference = abs($totalComponentWeight - $assessmentWeight);
            $message = sprintf(
                'Total logbook component weight (%.2f%%) must equal assessment weight (%.2f%%). Current difference: %.2f%%. Please adjust the component weights so they sum to %.2f%%.',
                $totalComponentWeight,
                $assessmentWeight,
                $difference,
                $assessmentWeight
            );
            throw new \Exception($message);
        }
    }

    /**
     * Reorder assessment components (Admin only).
     */
    public function reorderComponents(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'components' => ['required', 'array'],
            'components.*.id' => ['required', 'exists:assessment_components,id'],
            'components.*.order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['components'] as $componentData) {
            \App\Models\AssessmentComponent::where('id', $componentData['id'])
                ->update(['order' => $componentData['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Component order updated successfully']);
    }

    /**
     * Save multiple CLOs for an assessment (FYP).
     */
    private function saveClos(Assessment $assessment, array $clos): void
    {
        $cloIds = [];

        foreach ($clos as $index => $cloData) {
            if (empty($cloData['clo_code'])) {
                continue;
            }

            // Handle both new and existing CLOs
            if (! empty($cloData['id'])) {
                // Update existing CLO
                $clo = \App\Models\AssessmentClo::where('id', $cloData['id'])
                    ->where('assessment_id', $assessment->id)
                    ->first();

                if ($clo) {
                    $clo->update([
                        'clo_code' => $cloData['clo_code'],
                        'weight_percentage' => floatval($cloData['weight_percentage']),
                        'order' => $cloData['order'] ?? $index,
                    ]);
                } else {
                    // CLO ID doesn't exist, create new one
                    $clo = \App\Models\AssessmentClo::create([
                        'assessment_id' => $assessment->id,
                        'clo_code' => $cloData['clo_code'],
                        'weight_percentage' => floatval($cloData['weight_percentage']),
                        'order' => $cloData['order'] ?? $index,
                    ]);
                }
            } else {
                // Check if CLO already exists for this assessment (by assessment_id and clo_code)
                $existingClo = \App\Models\AssessmentClo::where('assessment_id', $assessment->id)
                    ->where('clo_code', $cloData['clo_code'])
                    ->first();

                if ($existingClo) {
                    // Update existing CLO
                    $existingClo->update([
                        'weight_percentage' => floatval($cloData['weight_percentage']),
                        'order' => $cloData['order'] ?? $index,
                    ]);
                    $clo = $existingClo;
                } else {
                    // Create new CLO
                    $clo = \App\Models\AssessmentClo::create([
                        'assessment_id' => $assessment->id,
                        'clo_code' => $cloData['clo_code'],
                        'weight_percentage' => floatval($cloData['weight_percentage']),
                        'order' => $cloData['order'] ?? $index,
                    ]);
                }
            }

            $cloIds[] = $clo->id;
        }

        // Delete CLOs that were removed
        if (! empty($cloIds)) {
            $assessment->clos()->whereNotIn('id', $cloIds)->delete();
        } else {
            // If no CLOs provided, delete all existing ones
            $assessment->clos()->delete();
        }
    }

    /**
     * Save multiple evaluators for an assessment (FYP).
     */
    private function saveEvaluators(Assessment $assessment, array $evaluators): void
    {
        $evaluatorIds = [];

        foreach ($evaluators as $index => $evaluatorData) {
            if (empty($evaluatorData['role']) || ! isset($evaluatorData['total_score'])) {
                continue;
            }

            // Handle both new and existing evaluators
            if (! empty($evaluatorData['id'])) {
                // Update existing evaluator
                $evaluator = \App\Models\AssessmentEvaluator::where('id', $evaluatorData['id'])
                    ->where('assessment_id', $assessment->id)
                    ->first();

                if ($evaluator) {
                    $evaluator->update([
                        'evaluator_role' => $evaluatorData['role'],
                        'total_score' => floatval($evaluatorData['total_score']),
                        'order' => $evaluatorData['order'] ?? $index,
                    ]);
                    $evaluatorIds[] = $evaluator->id;
                }
            } else {
                // Create new evaluator
                $evaluator = \App\Models\AssessmentEvaluator::create([
                    'assessment_id' => $assessment->id,
                    'evaluator_role' => $evaluatorData['role'],
                    'total_score' => floatval($evaluatorData['total_score']),
                    'order' => $evaluatorData['order'] ?? $index,
                ]);
                $evaluatorIds[] = $evaluator->id;
            }
        }

        // Delete evaluators that were removed
        if (! empty($evaluatorIds)) {
            $assessment->evaluators()->whereNotIn('id', $evaluatorIds)->delete();
        } else {
            // If no evaluators provided, delete all existing ones
            $assessment->evaluators()->delete();
        }
    }
}
