<?php

namespace App\Http\Controllers\Academic\PPE;

use App\Http\Controllers\Controller;
use App\Models\CloPloMapping;
use App\Models\CloPloRelationship;
use App\Models\CourseCloSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PpeCloPloController extends Controller
{
    private const COURSE_CODE = 'PPE';
    private const COURSE_NAME = 'Professional Practice & Ethics';

    /**
     * Display the CLO-PLO Analysis page.
     */
    public function index(): View
    {
        // Only Admin, Coordinator, and Lecturer can access
        if (!auth()->user()->isAdmin() && !auth()->user()->isCoordinator() && !auth()->user()->isLecturer()) {
            abort(403, 'Unauthorized access.');
        }

        // Get CLO count for this course
        $cloCount = CourseCloSetting::getCloCount(self::COURSE_CODE);

        // Get all CLO mappings for this course
        $cloMappings = CloPloMapping::forCourse(self::COURSE_CODE)
            ->with('ploRelationships')
            ->orderBy('clo_code')
            ->get();

        // Get all available CLO codes for this course (from database settings)
        $availableCloCodes = CourseCloSetting::getCloCodes(self::COURSE_CODE);
        
        // Get existing CLO codes
        $existingCloCodes = $cloMappings->pluck('clo_code')->toArray();
        
        // Find missing CLOs that need to be initialized
        $missingCloCodes = array_diff($availableCloCodes, $existingCloCodes);

        // Get all PLO codes (PLO1 to PLO12)
        $ploCodes = $this->getPloCodes();

        return view('academic.clo-plo.index', [
            'cloMappings' => $cloMappings,
            'missingCloCodes' => $missingCloCodes,
            'ploCodes' => $ploCodes,
            'courseCode' => self::COURSE_CODE,
            'courseName' => self::COURSE_NAME,
            'cloCount' => $cloCount,
        ]);
    }

    /**
     * Update CLO count for this course.
     */
    public function updateCount(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'clo_count' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        CourseCloSetting::updateCloCount(
            self::COURSE_CODE,
            $validated['clo_count'],
            auth()->id()
        );

        return redirect()->route('academic.ppe.clo-plo.index')
            ->with('success', 'CLO count updated successfully to ' . $validated['clo_count'] . '.');
    }

    /**
     * Store a new CLO mapping.
     */
    public function store(Request $request): RedirectResponse
    {
        // Only Admin can create/edit
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'clo_code' => ['required', 'string', 'max:10'],
            'clo_description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'allow_for_assessment' => ['boolean'],
            'plo_codes' => ['required', 'array', 'min:1'],
            'plo_codes.*' => ['required', 'string', 'max:10'],
            'plo_descriptions' => ['nullable', 'array'],
            'plo_descriptions.*' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated) {
            $mapping = CloPloMapping::updateOrCreate(
                [
                    'course_code' => self::COURSE_CODE,
                    'clo_code' => $validated['clo_code'],
                ],
                [
                    'clo_description' => $validated['clo_description'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,
                    'allow_for_assessment' => $validated['allow_for_assessment'] ?? false,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]
            );

            // Delete existing PLO relationships
            $mapping->ploRelationships()->delete();

            // Create new PLO relationships with descriptions
            $ploCodes = $validated['plo_codes'];
            $ploDescriptions = $validated['plo_descriptions'] ?? [];
            
            foreach ($ploCodes as $index => $ploCode) {
                CloPloRelationship::create([
                    'clo_plo_mapping_id' => $mapping->id,
                    'plo_code' => $ploCode,
                    'plo_description' => $ploDescriptions[$index] ?? null,
                ]);
            }
        });

        return redirect()->route('academic.ppe.clo-plo.index')
            ->with('success', 'CLO-PLO mapping updated successfully.');
    }

    /**
     * Update an existing CLO mapping.
     */
    public function update(Request $request, CloPloMapping $cloPloMapping): RedirectResponse
    {
        // Only Admin can update
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Verify course code matches
        if ($cloPloMapping->course_code !== self::COURSE_CODE) {
            abort(403, 'Invalid CLO mapping for this course.');
        }

        $validated = $request->validate([
            'clo_description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'allow_for_assessment' => ['boolean'],
            'plo_codes' => ['required', 'array', 'min:1'],
            'plo_codes.*' => ['required', 'string', 'max:10'],
            'plo_descriptions' => ['nullable', 'array'],
            'plo_descriptions.*' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($cloPloMapping, $validated) {
            $cloPloMapping->update([
                'clo_description' => $validated['clo_description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'allow_for_assessment' => $validated['allow_for_assessment'] ?? false,
                'updated_by' => auth()->id(),
            ]);

            // Delete existing PLO relationships
            $cloPloMapping->ploRelationships()->delete();

            // Create new PLO relationships with descriptions
            // plo_codes comes as an array from the form (plo_codes[0], plo_codes[1], etc.)
            $ploCodes = $validated['plo_codes'];
            $ploDescriptions = $validated['plo_descriptions'] ?? [];
            
            foreach ($ploCodes as $index => $ploCode) {
                CloPloRelationship::create([
                    'clo_plo_mapping_id' => $cloPloMapping->id,
                    'plo_code' => $ploCode,
                    'plo_description' => $ploDescriptions[$index] ?? null,
                ]);
            }
        });

        return redirect()->route('academic.ppe.clo-plo.index')
            ->with('success', 'CLO-PLO mapping updated successfully.');
    }

    /**
     * Get all PLO codes (PLO1 to PLO12).
     */
    private function getPloCodes(): array
    {
        return array_map(function ($i) {
            return 'PLO' . $i;
        }, range(1, 12));
    }
}
