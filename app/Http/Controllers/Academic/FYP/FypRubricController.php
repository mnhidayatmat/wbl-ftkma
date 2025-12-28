<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\FYP\FypRubricElement;
use App\Models\FYP\FypRubricLevelDescriptor;
use App\Models\FYP\FypRubricTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FypRubricController extends Controller
{
    /**
     * Display a listing of rubric templates.
     */
    public function index(Request $request): View
    {
        // Only Admin can manage rubric templates
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $query = FypRubricTemplate::with(['elements', 'creator'])
            ->forCourse('FYP');

        // Filter by assessment type
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        // Filter by phase
        if ($request->filled('phase')) {
            $query->forPhase($request->phase);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        $templates = $query->orderBy('phase')
            ->orderBy('assessment_type')
            ->orderBy('name')
            ->get();

        // Calculate total weight for each template
        $templates->each(function ($template) {
            $template->calculated_weight = $template->calculateTotalWeight();
            $template->is_weight_valid = $template->isWeightValid();
            $template->element_count = $template->elements->count();
        });

        return view('academic.fyp.rubrics.index', compact('templates'));
    }

    /**
     * Show the form for creating a new rubric template.
     */
    public function create(): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $assessmentTypes = FypRubricTemplate::ASSESSMENT_TYPES;
        $phases = FypRubricTemplate::PHASES;
        $performanceLevels = FypRubricTemplate::PERFORMANCE_LEVELS;
        $cloCodes = Assessment::getCloCodes('FYP');

        return view('academic.fyp.rubrics.create', compact(
            'assessmentTypes',
            'phases',
            'performanceLevels',
            'cloCodes'
        ));
    }

    /**
     * Store a newly created rubric template.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:fyp_rubric_templates,code'],
            'assessment_type' => ['required', 'in:Written,Oral'],
            'phase' => ['required', 'in:Mid-Term,Final'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $template = FypRubricTemplate::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'assessment_type' => $validated['assessment_type'],
            'phase' => $validated['phase'],
            'course_code' => 'FYP',
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('academic.fyp.rubrics.edit', $template)
            ->with('success', 'Rubric template created successfully. Now add elements to define the rubric criteria.');
    }

    /**
     * Display the specified rubric template.
     */
    public function show(FypRubricTemplate $rubric): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $rubric->load(['elements.levelDescriptors', 'creator']);

        $totalWeight = $rubric->calculateTotalWeight();
        $isWeightValid = $rubric->isWeightValid();
        $elementsByClo = $rubric->getElementsByClo();

        return view('academic.fyp.rubrics.show', compact(
            'rubric',
            'totalWeight',
            'isWeightValid',
            'elementsByClo'
        ));
    }

    /**
     * Show the form for editing the specified rubric template.
     */
    public function edit(FypRubricTemplate $rubric): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        if ($rubric->is_locked) {
            return redirect()->route('academic.fyp.rubrics.show', $rubric)
                ->with('error', 'This rubric template is locked and cannot be edited because marks have been released.');
        }

        $rubric->load(['elements.levelDescriptors']);

        $assessmentTypes = FypRubricTemplate::ASSESSMENT_TYPES;
        $phases = FypRubricTemplate::PHASES;
        $performanceLevels = FypRubricTemplate::PERFORMANCE_LEVELS;
        $cloCodes = Assessment::getCloCodes('FYP');
        $totalWeight = $rubric->calculateTotalWeight();
        $isWeightValid = $rubric->isWeightValid();

        return view('academic.fyp.rubrics.edit', compact(
            'rubric',
            'assessmentTypes',
            'phases',
            'performanceLevels',
            'cloCodes',
            'totalWeight',
            'isWeightValid'
        ));
    }

    /**
     * Update the specified rubric template.
     */
    public function update(Request $request, FypRubricTemplate $rubric): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        if ($rubric->is_locked) {
            return redirect()->back()
                ->with('error', 'This rubric template is locked and cannot be edited.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:fyp_rubric_templates,code,'.$rubric->id],
            'assessment_type' => ['required', 'in:Written,Oral'],
            'phase' => ['required', 'in:Mid-Term,Final'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $rubric->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'assessment_type' => $validated['assessment_type'],
            'phase' => $validated['phase'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('academic.fyp.rubrics.edit', $rubric)
            ->with('success', 'Rubric template updated successfully.');
    }

    /**
     * Remove the specified rubric template.
     */
    public function destroy(FypRubricTemplate $rubric): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        if ($rubric->is_locked) {
            return redirect()->back()
                ->with('error', 'This rubric template is locked and cannot be deleted.');
        }

        // Check if there are any evaluations
        if ($rubric->evaluations()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete this rubric template because it has existing evaluations.');
        }

        $rubric->delete();

        return redirect()->route('academic.fyp.rubrics.index')
            ->with('success', 'Rubric template deleted successfully.');
    }

    /**
     * Add an element to a rubric template.
     */
    public function addElement(Request $request, FypRubricTemplate $rubric): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        if ($rubric->is_locked) {
            return redirect()->back()
                ->with('error', 'This rubric template is locked and cannot be modified.');
        }

        $validated = $request->validate([
            'element_code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'clo_code' => ['required', 'string', 'max:10'],
            'weight_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        // Check for duplicate element code
        if ($rubric->elements()->where('element_code', $validated['element_code'])->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An element with this code already exists in this rubric.');
        }

        // Check if total weight would exceed 100%
        $currentTotal = $rubric->calculateTotalWeight();
        if (($currentTotal + $validated['weight_percentage']) > 100.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Adding this element would exceed 100% total weight. Current total: {$currentTotal}%");
        }

        DB::transaction(function () use ($rubric, $validated) {
            // Get max order
            $maxOrder = $rubric->elements()->max('order') ?? 0;

            // Create element
            $element = $rubric->elements()->create([
                'element_code' => $validated['element_code'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'clo_code' => $validated['clo_code'],
                'weight_percentage' => $validated['weight_percentage'],
                'order' => $maxOrder + 1,
            ]);

            // Create default level descriptors
            $element->createDefaultDescriptors();
        });

        return redirect()->route('academic.fyp.rubrics.edit', $rubric)
            ->with('success', 'Element added successfully.');
    }

    /**
     * Update an element.
     */
    public function updateElement(Request $request, FypRubricTemplate $rubric, FypRubricElement $element): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        if ($rubric->is_locked) {
            return redirect()->back()
                ->with('error', 'This rubric template is locked and cannot be modified.');
        }

        $validated = $request->validate([
            'element_code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'clo_code' => ['required', 'string', 'max:10'],
            'weight_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        // Check for duplicate element code (excluding current)
        if ($rubric->elements()->where('element_code', $validated['element_code'])->where('id', '!=', $element->id)->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An element with this code already exists in this rubric.');
        }

        // Check if total weight would exceed 100%
        $currentTotal = $rubric->calculateTotalWeight() - $element->weight_percentage;
        if (($currentTotal + $validated['weight_percentage']) > 100.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Updating this element would exceed 100% total weight.');
        }

        $element->update($validated);

        return redirect()->route('academic.fyp.rubrics.edit', $rubric)
            ->with('success', 'Element updated successfully.');
    }

    /**
     * Delete an element.
     */
    public function deleteElement(FypRubricTemplate $rubric, FypRubricElement $element): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        if ($rubric->is_locked) {
            return redirect()->back()
                ->with('error', 'This rubric template is locked and cannot be modified.');
        }

        // Check if there are evaluations for this element
        if ($element->evaluations()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete this element because it has existing evaluations.');
        }

        $element->delete();

        return redirect()->route('academic.fyp.rubrics.edit', $rubric)
            ->with('success', 'Element deleted successfully.');
    }

    /**
     * Update level descriptors for an element.
     */
    public function updateDescriptors(Request $request, FypRubricTemplate $rubric, FypRubricElement $element): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        if ($rubric->is_locked) {
            return redirect()->back()
                ->with('error', 'This rubric template is locked and cannot be modified.');
        }

        $validated = $request->validate([
            'descriptors' => ['required', 'array'],
            'descriptors.*.level' => ['required', 'integer', 'min:1', 'max:5'],
            'descriptors.*.label' => ['required', 'string', 'max:50'],
            'descriptors.*.descriptor' => ['required', 'string', 'max:1000'],
            'descriptors.*.score_value' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);

        DB::transaction(function () use ($element, $validated) {
            foreach ($validated['descriptors'] as $data) {
                FypRubricLevelDescriptor::updateOrCreate(
                    [
                        'rubric_element_id' => $element->id,
                        'level' => $data['level'],
                    ],
                    [
                        'label' => $data['label'],
                        'descriptor' => $data['descriptor'],
                        'score_value' => $data['score_value'],
                    ]
                );
            }
        });

        return redirect()->route('academic.fyp.rubrics.edit', $rubric)
            ->with('success', 'Level descriptors updated successfully.');
    }

    /**
     * Reorder elements.
     */
    public function reorderElements(Request $request, FypRubricTemplate $rubric): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        if ($rubric->is_locked) {
            return redirect()->back()
                ->with('error', 'This rubric template is locked and cannot be modified.');
        }

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'exists:fyp_rubric_elements,id'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['order'] as $position => $elementId) {
                FypRubricElement::where('id', $elementId)->update(['order' => $position]);
            }
        });

        return redirect()->route('academic.fyp.rubrics.edit', $rubric)
            ->with('success', 'Elements reordered successfully.');
    }

    /**
     * Duplicate a rubric template.
     */
    public function duplicate(FypRubricTemplate $rubric): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        DB::transaction(function () use ($rubric, &$newTemplate) {
            // Create new template
            $newTemplate = $rubric->replicate();
            $newTemplate->name = $rubric->name.' (Copy)';
            $newTemplate->code = $rubric->code.'_COPY_'.time();
            $newTemplate->is_locked = false;
            $newTemplate->created_by = auth()->id();
            $newTemplate->save();

            // Copy elements and descriptors
            foreach ($rubric->elements as $element) {
                $newElement = $element->replicate();
                $newElement->rubric_template_id = $newTemplate->id;
                $newElement->save();

                foreach ($element->levelDescriptors as $descriptor) {
                    $newDescriptor = $descriptor->replicate();
                    $newDescriptor->rubric_element_id = $newElement->id;
                    $newDescriptor->save();
                }
            }
        });

        return redirect()->route('academic.fyp.rubrics.edit', $newTemplate)
            ->with('success', 'Rubric template duplicated successfully. Please update the name and code.');
    }
}
