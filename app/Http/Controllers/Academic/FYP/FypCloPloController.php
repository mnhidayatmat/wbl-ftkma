<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Http\Controllers\Controller;
use App\Models\CloPloMapping;
use App\Models\CloPloRelationship;
use App\Models\CourseCloSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FypCloPloController extends Controller
{
    private const COURSE_CODE = 'FYP';

    private const COURSE_NAME = 'Final Year Project';

    public function index(): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isCoordinator() && ! auth()->user()->isLecturer() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get CLO count for this course
        $cloCount = CourseCloSetting::getCloCount(self::COURSE_CODE);

        $cloMappings = CloPloMapping::forCourse(self::COURSE_CODE)
            ->with(['ploRelationships' => function ($query) {
                $query->orderBy('plo_code');
            }])
            ->orderBy('clo_code')
            ->get();

        $availableCloCodes = CourseCloSetting::getCloCodes(self::COURSE_CODE);
        $existingCloCodes = $cloMappings->pluck('clo_code')->toArray();
        $missingCloCodes = array_diff($availableCloCodes, $existingCloCodes);
        $ploCodes = array_map(fn ($i) => 'PLO'.$i, range(1, 12));

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
        if (! auth()->user()->isAdmin()) {
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

        return redirect()->route('academic.fyp.clo-plo.index')
            ->with('success', 'CLO count updated successfully to '.$validated['clo_count'].'.');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
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
                ['course_code' => self::COURSE_CODE, 'clo_code' => $validated['clo_code']],
                [
                    'clo_description' => $validated['clo_description'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,
                    'allow_for_assessment' => $validated['allow_for_assessment'] ?? false,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]
            );

            $mapping->ploRelationships()->delete();
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

        return redirect()->route('academic.fyp.clo-plo.index')
            ->with('success', 'CLO-PLO mapping updated successfully.');
    }

    public function update(Request $request, CloPloMapping $cloPloMapping): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

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

            $cloPloMapping->ploRelationships()->delete();
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

        return redirect()->route('academic.fyp.clo-plo.index')
            ->with('success', 'CLO-PLO mapping updated successfully.');
    }

    public function destroy(CloPloMapping $cloPloMapping): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($cloPloMapping->course_code !== self::COURSE_CODE) {
            abort(403, 'Invalid CLO mapping for this course.');
        }

        DB::transaction(function () use ($cloPloMapping) {
            $cloPloMapping->ploRelationships()->delete();
            $cloPloMapping->delete();
        });

        return redirect()->route('academic.fyp.clo-plo.index')
            ->with('success', 'CLO-PLO mapping deleted successfully.');
    }
}
