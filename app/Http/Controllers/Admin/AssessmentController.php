<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentClo;
use App\Models\AssessmentRubric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    /**
     * Display a listing of assessments filtered by course.
     */
    public function index(Request $request): View
    {
        $courseCode = $request->query('course', 'PPE');
        
        $assessments = Assessment::where('course_code', $courseCode)
            ->with('creator')
            ->orderBy('clo_code')
            ->orderBy('assessment_name')
            ->get();

        // Calculate total percentage for the course
        $totalPercentage = $assessments->sum('weight_percentage');

        $courseCodes = Assessment::getCourseCodes();

        return view('admin.assessments.index', compact(
            'assessments',
            'courseCode',
            'courseCodes',
            'totalPercentage'
        ));
    }

    /**
     * Show the form for creating a new assessment.
     */
    public function create(Request $request): View
    {
        $courseCode = $request->query('course', 'PPE');
        
        $courseCodes = Assessment::getCourseCodes();
        $assessmentTypes = Assessment::getAssessmentTypes();
        $evaluatorRoles = Assessment::getEvaluatorRoles();
        
        // Get approved CLO codes from CLO-PLO mappings (only those allowed for assessment)
        // Use try-catch to handle cases where tables don't exist yet
        try {
            $approvedCloCodes = \App\Models\CloPloMapping::forCourse($courseCode)
                ->allowedForAssessment()
                ->pluck('clo_code')
                ->toArray();
        } catch (\Exception $e) {
            $approvedCloCodes = [];
        }
        
        // Fallback to all CLO codes if no mappings exist yet
        $allCloCodes = Assessment::getCloCodes($courseCode);
        $cloCodes = !empty($approvedCloCodes) ? $approvedCloCodes : $allCloCodes;
        
        // Get all CLO mappings for JavaScript
        $allCloMappings = [];
        foreach ($courseCodes as $code => $name) {
            try {
                $approved = \App\Models\CloPloMapping::forCourse($code)
                    ->allowedForAssessment()
                    ->pluck('clo_code')
                    ->toArray();
                $allCloMappings[$code] = !empty($approved) ? $approved : Assessment::getCloCodes($code);
            } catch (\Exception $e) {
                $allCloMappings[$code] = Assessment::getCloCodes($code);
            }
        }

        return view('admin.assessments.create', compact(
            'courseCode',
            'courseCodes',
            'assessmentTypes',
            'evaluatorRoles',
            'cloCodes',
            'allCloMappings'
        ));
    }

    /**
     * Store a newly created assessment.
     */
    public function store(Request $request): RedirectResponse
    {
        $isFyp = $request->input('course_code') === 'FYP';
        
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'in:PPE,IP,OSH,FYP,LI'],
            'assessment_name' => ['required', 'string', 'max:255'],
            'assessment_type' => ['required', 'string'],
            'clo_code' => $isFyp ? ['nullable', 'string'] : ['required', 'string'],
            'clos' => $isFyp ? ['required', 'array', 'min:1'] : ['nullable', 'array'],
            'clos.*.clo_code' => $isFyp ? ['required', 'string'] : ['nullable', 'string'],
            'clos.*.weight_percentage' => $isFyp ? ['required', 'numeric', 'min:0', 'max:100'] : ['nullable', 'numeric'],
            'weight_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'evaluator_role' => ['required', 'string', 'in:lecturer,at,ic,supervisor_li'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Check total percentage for the course
        $existingTotal = Assessment::where('course_code', $validated['course_code'])
            ->where('is_active', true)
            ->sum('weight_percentage');

        if (($existingTotal + $validated['weight_percentage']) > 100) {
            return back()
                ->withInput()
                ->withErrors(['weight_percentage' => 'Total percentage for this course cannot exceed 100%. Current total: ' . $existingTotal . '%']);
        }

        // Validate CLO codes
        $validClos = Assessment::getCloCodes($validated['course_code']);
        
        if ($isFyp && $request->has('clos')) {
            // Validate multiple CLOs for FYP
            $clos = $request->input('clos', []);
            $totalCloWeight = 0;
            $usedClos = [];
            
            foreach ($clos as $index => $clo) {
                if (!in_array($clo['clo_code'], $validClos)) {
                    return back()
                        ->withInput()
                        ->withErrors(["clos.{$index}.clo_code" => 'Invalid CLO code: ' . $clo['clo_code']]);
                }
                
                if (in_array($clo['clo_code'], $usedClos)) {
                    return back()
                        ->withInput()
                        ->withErrors(["clos.{$index}.clo_code" => 'Duplicate CLO code: ' . $clo['clo_code']]);
                }
                
                $usedClos[] = $clo['clo_code'];
                $totalCloWeight += floatval($clo['weight_percentage']);
            }
            
            // Validate that CLO weights sum to assessment weight
            if (abs($totalCloWeight - $validated['weight_percentage']) > 0.01) {
                return back()
                    ->withInput()
                    ->withErrors(['clos' => "Total CLO weight ({$totalCloWeight}%) must equal assessment weight ({$validated['weight_percentage']}%)"]);
            }
        } else {
            // Validate single CLO for other courses
            if (!in_array($validated['clo_code'], $validClos)) {
                return back()
                    ->withInput()
                    ->withErrors(['clo_code' => 'Invalid CLO code. Only approved CLOs can be used in assessments.']);
            }
        }

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');
        
        // For FYP, set a default CLO code (first one) for backward compatibility
        if ($isFyp && !isset($validated['clo_code']) && $request->has('clos')) {
            $validated['clo_code'] = $request->input('clos.0.clo_code');
        }

        $assessment = Assessment::create($validated);

        // Save multiple CLOs for FYP
        if ($isFyp && $request->has('clos')) {
            foreach ($request->input('clos', []) as $index => $clo) {
                AssessmentClo::create([
                    'assessment_id' => $assessment->id,
                    'clo_code' => $clo['clo_code'],
                    'weight_percentage' => $clo['weight_percentage'],
                    'order' => $index,
                ]);
            }
        }

        // Handle rubric questions if assessment type is Oral or Rubric
        if (in_array($validated['assessment_type'], ['Oral', 'Rubric']) && $request->has('rubrics')) {
            $this->saveRubrics($assessment, $request->input('rubrics', []), $validated['weight_percentage']);
        }

        return redirect()
            ->route('admin.assessments.index', ['course' => $validated['course_code']])
            ->with('success', 'Assessment created successfully.');
    }

    /**
     * Show the form for editing the specified assessment.
     */
    public function edit(Assessment $assessment): View
    {
        $courseCodes = Assessment::getCourseCodes();
        $assessmentTypes = Assessment::getAssessmentTypes();
        $evaluatorRoles = Assessment::getEvaluatorRoles();
        
        // Get approved CLO codes from CLO-PLO mappings
        // Use try-catch to handle cases where tables don't exist yet
        try {
            $approvedCloCodes = \App\Models\CloPloMapping::forCourse($assessment->course_code)
                ->allowedForAssessment()
                ->pluck('clo_code')
                ->toArray();
        } catch (\Exception $e) {
            $approvedCloCodes = [];
        }
        
        $allCloCodes = Assessment::getCloCodes($assessment->course_code);
        $cloCodes = !empty($approvedCloCodes) ? $approvedCloCodes : $allCloCodes;
        
        // Get all CLO mappings for JavaScript
        $allCloMappings = [];
        foreach ($courseCodes as $code => $name) {
            try {
                $approved = \App\Models\CloPloMapping::forCourse($code)
                    ->allowedForAssessment()
                    ->pluck('clo_code')
                    ->toArray();
                $allCloMappings[$code] = !empty($approved) ? $approved : Assessment::getCloCodes($code);
            } catch (\Exception $e) {
                $allCloMappings[$code] = Assessment::getCloCodes($code);
            }
        }
        
        // Load rubric questions if applicable
        $rubrics = $assessment->rubrics;
        
        // Load existing CLOs for FYP assessments
        $existingClos = $assessment->clos;

        return view('admin.assessments.edit', compact(
            'assessment',
            'courseCodes',
            'assessmentTypes',
            'evaluatorRoles',
            'cloCodes',
            'allCloMappings',
            'rubrics',
            'existingClos'
        ));
    }

    /**
     * Update the specified assessment.
     */
    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $isFyp = $request->input('course_code') === 'FYP' || $assessment->course_code === 'FYP';
        
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'in:PPE,IP,OSH,FYP,LI'],
            'assessment_name' => ['required', 'string', 'max:255'],
            'assessment_type' => ['required', 'string'],
            'clo_code' => $isFyp ? ['nullable', 'string'] : ['required', 'string'],
            'clos' => $isFyp ? ['required', 'array', 'min:1'] : ['nullable', 'array'],
            'clos.*.clo_code' => $isFyp ? ['required', 'string'] : ['nullable', 'string'],
            'clos.*.weight_percentage' => $isFyp ? ['required', 'numeric', 'min:0', 'max:100'] : ['nullable', 'numeric'],
            'weight_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'evaluator_role' => ['required', 'string', 'in:lecturer,at,ic,supervisor_li'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Check total percentage for the course (excluding current assessment)
        $existingTotal = Assessment::where('course_code', $validated['course_code'])
            ->where('is_active', true)
            ->where('id', '!=', $assessment->id)
            ->sum('weight_percentage');

        if (($existingTotal + $validated['weight_percentage']) > 100) {
            return back()
                ->withInput()
                ->withErrors(['weight_percentage' => 'Total percentage for this course cannot exceed 100%. Current total (excluding this): ' . $existingTotal . '%']);
        }

        // Get approved CLO codes from CLO-PLO mappings
        $approvedCloCodes = \App\Models\CloPloMapping::forCourse($validated['course_code'])
            ->allowedForAssessment()
            ->pluck('clo_code')
            ->toArray();
        
        $allCloCodes = Assessment::getCloCodes($validated['course_code']);
        $validClos = !empty($approvedCloCodes) ? $approvedCloCodes : $allCloCodes;
        
        if ($isFyp && $request->has('clos')) {
            // Validate multiple CLOs for FYP
            $clos = $request->input('clos', []);
            $totalCloWeight = 0;
            $usedClos = [];
            
            foreach ($clos as $index => $clo) {
                if (!in_array($clo['clo_code'], $validClos)) {
                    return back()
                        ->withInput()
                        ->withErrors(["clos.{$index}.clo_code" => 'Invalid CLO code: ' . $clo['clo_code'] . '. Only approved CLOs can be used in assessments.']);
                }
                
                if (in_array($clo['clo_code'], $usedClos)) {
                    return back()
                        ->withInput()
                        ->withErrors(["clos.{$index}.clo_code" => 'Duplicate CLO code: ' . $clo['clo_code']]);
                }
                
                $usedClos[] = $clo['clo_code'];
                $totalCloWeight += floatval($clo['weight_percentage']);
            }
            
            // Validate that CLO weights sum to assessment weight
            if (abs($totalCloWeight - $validated['weight_percentage']) > 0.01) {
                return back()
                    ->withInput()
                    ->withErrors(['clos' => "Total CLO weight ({$totalCloWeight}%) must equal assessment weight ({$validated['weight_percentage']}%)"]);
            }
        } else {
            // Validate single CLO for other courses
            if (!in_array($validated['clo_code'], $validClos)) {
                return back()
                    ->withInput()
                    ->withErrors(['clo_code' => 'Invalid CLO code. Only approved CLOs can be used in assessments.']);
            }
        }

        $validated['is_active'] = $request->has('is_active');
        
        // For FYP, set a default CLO code (first one) for backward compatibility
        if ($isFyp && !isset($validated['clo_code']) && $request->has('clos')) {
            $validated['clo_code'] = $request->input('clos.0.clo_code');
        }

        $assessment->update($validated);

        // Update multiple CLOs for FYP
        if ($isFyp && $request->has('clos')) {
            // Delete existing CLOs
            $assessment->clos()->delete();
            
            // Create new CLOs
            foreach ($request->input('clos', []) as $index => $clo) {
                AssessmentClo::create([
                    'assessment_id' => $assessment->id,
                    'clo_code' => $clo['clo_code'],
                    'weight_percentage' => $clo['weight_percentage'],
                    'order' => $index,
                ]);
            }
        }

        // Handle rubric questions if assessment type is Oral or Rubric
        if (in_array($validated['assessment_type'], ['Oral', 'Rubric']) && $request->has('rubrics')) {
            try {
                $this->saveRubrics($assessment, $request->input('rubrics', []), $validated['weight_percentage']);
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->withErrors(['rubrics' => $e->getMessage()]);
            }
        } else {
            // Remove all rubrics if assessment type changed
            $assessment->rubrics()->delete();
        }

        return redirect()
            ->route('admin.assessments.index', ['course' => $validated['course_code']])
            ->with('success', 'Assessment updated successfully.');
    }

    /**
     * Remove the specified assessment.
     */
    public function destroy(Assessment $assessment): RedirectResponse
    {
        $courseCode = $assessment->course_code;
        $assessment->delete();

        return redirect()
            ->route('admin.assessments.index', ['course' => $courseCode])
            ->with('success', 'Assessment deleted successfully.');
    }

    /**
     * Toggle active status of an assessment.
     */
    public function toggleActive(Assessment $assessment): RedirectResponse
    {
        $assessment->update(['is_active' => !$assessment->is_active]);

        return redirect()
            ->route('admin.assessments.index', ['course' => $assessment->course_code])
            ->with('success', 'Assessment status updated.');
    }

    /**
     * Save rubric questions for an assessment.
     */
    private function saveRubrics(Assessment $assessment, array $rubrics, float $assessmentWeight): void
    {
        // Filter out empty rubrics
        $rubrics = array_filter($rubrics, function($rubric) {
            return !empty($rubric['question_title']);
        });
        
        if (empty($rubrics)) {
            return; // No rubrics to save
        }
        
        // Validate rubric weights sum to assessment weight
        $totalRubricWeight = array_sum(array_column($rubrics, 'weight_percentage'));
        
        if (abs($totalRubricWeight - $assessmentWeight) > 0.01) {
            throw new \Exception("Sum of rubric weights ({$totalRubricWeight}%) must equal assessment weight ({$assessmentWeight}%).");
        }

        // Delete existing rubrics
        $assessment->rubrics()->delete();

        // For FYP assessments, get the first CLO if not provided in rubric
        $defaultCloCode = null;
        if ($assessment->course_code === 'FYP') {
            $firstAssessmentClo = $assessment->clos()->orderBy('order')->first();
            $defaultCloCode = $firstAssessmentClo ? $firstAssessmentClo->clo_code : $assessment->clo_code;
        }

        // Create new rubrics
        foreach ($rubrics as $index => $rubricData) {
            if (empty($rubricData['question_title'])) {
                continue; // Skip empty rubrics
            }

            // For FYP, use default CLO if not provided
            $cloCode = $rubricData['clo_code'] ?? $defaultCloCode;
            if (empty($cloCode)) {
                throw new \Exception("CLO code is required for rubric questions.");
            }

            AssessmentRubric::create([
                'assessment_id' => $assessment->id,
                'question_code' => $rubricData['question_code'] ?? 'Q' . ($index + 1),
                'question_title' => $rubricData['question_title'],
                'question_description' => $rubricData['question_description'] ?? null,
                'clo_code' => $cloCode,
                'weight_percentage' => $rubricData['weight_percentage'],
                'rubric_min' => $rubricData['rubric_min'] ?? 1,
                'rubric_max' => $rubricData['rubric_max'] ?? 5,
                'example_answer' => $rubricData['example_answer'] ?? null,
                'order' => $index,
            ]);
        }
    }
}
