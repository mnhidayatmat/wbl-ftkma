<?php

namespace Database\Seeders;

use App\Models\FYP\FypRubricElement;
use App\Models\FYP\FypRubricLevelDescriptor;
use App\Models\FYP\FypRubricTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FypRubricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Based on FYP Assessment Mapping:
     * - Written Report (55% total): Mid-Term + End-Term
     *   - Mid-Term FYP (Pt. 1): AT = 5%, IC = 15%
     *   - End-Term FYP (Pt. 2): AT = 25%, IC = 10%
     *
     * CLO Mapping:
     * - CLO 1 & 7: Proposed Methodology
     * - CLO 4: Problem Statement, Objectives & Scope
     * - CLO 5: Flowchart & Gantt Chart
     * - CLO 6: Literature Review/Survey
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Clear existing templates
            FypRubricTemplate::where('course_code', 'FYP')->delete();

            // Create 4 main rubric templates (AT and IC for Mid-Term and Final)
            $this->createMidTermAtRubric();
            $this->createMidTermIcRubric();
            $this->createFinalAtRubric();
            $this->createFinalIcRubric();
        });
    }

    /**
     * Mid-Term Written Report - AT Evaluation (5% total)
     *
     * Weight distribution:
     * - CLO 1 & 7 (Methodology): 2%
     * - CLO 4 (Problem, Objectives, Scope): 1%
     * - CLO 5 (Flowchart, Gantt): 1%
     * - CLO 6 (Literature): 1%
     */
    private function createMidTermAtRubric(): void
    {
        $template = FypRubricTemplate::create([
            'name' => 'Mid-Term Written Report',
            'code' => 'FYP_MID_WRITTEN_AT',
            'assessment_type' => 'Written',
            'phase' => 'Mid-Term',
            'evaluator_role' => 'at',
            'course_code' => 'FYP',
            'description' => 'Academic Tutor (AT) rubric for evaluating FYP Part 1 Mid-Term Written Report. Total contribution: 5%',
            'total_weight' => 100.00,
            'component_marks' => 5.00,
            'is_active' => true,
        ]);

        // Elements with proper CLO mapping and weight distribution
        // Total must equal 100% (which represents the 5% component)
        $elements = [
            // CLO 4: Problem Statement, Objectives & Scope (1% = 20% of 5%)
            ['code' => 'PS', 'name' => 'Problem Statement', 'clo' => 'CLO4', 'weight' => 6.67, 'contribution' => 0.33],
            ['code' => 'OBJ', 'name' => 'Project Objectives', 'clo' => 'CLO4', 'weight' => 6.67, 'contribution' => 0.33],
            ['code' => 'SCO', 'name' => 'Project Scope', 'clo' => 'CLO4', 'weight' => 6.66, 'contribution' => 0.34],

            // CLO 5: Flowchart & Gantt Chart (1% = 20% of 5%)
            ['code' => 'FC', 'name' => 'Project Flowchart', 'clo' => 'CLO5', 'weight' => 10.00, 'contribution' => 0.50],
            ['code' => 'GC', 'name' => 'Project Gantt-Chart', 'clo' => 'CLO5', 'weight' => 10.00, 'contribution' => 0.50],

            // CLO 6: Literature Survey (1% = 20% of 5%)
            ['code' => 'LR', 'name' => 'Literature Survey', 'clo' => 'CLO6', 'weight' => 20.00, 'contribution' => 1.00],

            // CLO 1 & 7: Proposed Methodology (2% = 40% of 5%)
            ['code' => 'MTH', 'name' => 'Proposed Methodology', 'clo' => 'CLO1,CLO7', 'weight' => 40.00, 'contribution' => 2.00],
        ];

        $this->createElementsWithDescriptors($template, $elements);
    }

    /**
     * Mid-Term Written Report - IC Evaluation (15% total)
     *
     * Weight distribution:
     * - CLO 1 & 7 (Methodology): 6%
     * - CLO 4 (Problem, Objectives, Scope): 3%
     * - CLO 5 (Flowchart, Gantt): 3%
     * - CLO 6 (Literature): 3%
     */
    private function createMidTermIcRubric(): void
    {
        $template = FypRubricTemplate::create([
            'name' => 'Mid-Term Written Report',
            'code' => 'FYP_MID_WRITTEN_IC',
            'assessment_type' => 'Written',
            'phase' => 'Mid-Term',
            'evaluator_role' => 'ic',
            'course_code' => 'FYP',
            'description' => 'Industry Coach (IC) rubric for evaluating FYP Part 1 Mid-Term Written Report. Total contribution: 15%',
            'total_weight' => 100.00,
            'component_marks' => 15.00,
            'is_active' => true,
        ]);

        // Elements with proper CLO mapping and weight distribution
        // Total must equal 100% (which represents the 15% component)
        $elements = [
            // CLO 4: Problem Statement, Objectives & Scope (3% = 20% of 15%)
            ['code' => 'PS', 'name' => 'Problem Statement', 'clo' => 'CLO4', 'weight' => 6.67, 'contribution' => 1.00],
            ['code' => 'OBJ', 'name' => 'Project Objectives', 'clo' => 'CLO4', 'weight' => 6.67, 'contribution' => 1.00],
            ['code' => 'SCO', 'name' => 'Project Scope', 'clo' => 'CLO4', 'weight' => 6.66, 'contribution' => 1.00],

            // CLO 5: Flowchart & Gantt Chart (3% = 20% of 15%)
            ['code' => 'FC', 'name' => 'Project Flowchart', 'clo' => 'CLO5', 'weight' => 10.00, 'contribution' => 1.50],
            ['code' => 'GC', 'name' => 'Project Gantt-Chart', 'clo' => 'CLO5', 'weight' => 10.00, 'contribution' => 1.50],

            // CLO 6: Literature Survey (3% = 20% of 15%)
            ['code' => 'LR', 'name' => 'Literature Survey', 'clo' => 'CLO6', 'weight' => 20.00, 'contribution' => 3.00],

            // CLO 1 & 7: Proposed Methodology (6% = 40% of 15%)
            ['code' => 'MTH', 'name' => 'Proposed Methodology', 'clo' => 'CLO1,CLO7', 'weight' => 40.00, 'contribution' => 6.00],
        ];

        $this->createElementsWithDescriptors($template, $elements);
    }

    /**
     * End-Term (Final) Written Report - AT Evaluation (25% total)
     *
     * Weight distribution:
     * - CLO 1 & 7 (Methodology): 10%
     * - CLO 4 (Problem, Objectives, Scope): 5%
     * - CLO 5 (Flowchart, Gantt): 5%
     * - CLO 6 (Literature): 5%
     */
    private function createFinalAtRubric(): void
    {
        $template = FypRubricTemplate::create([
            'name' => 'End-Term Written Report',
            'code' => 'FYP_FINAL_WRITTEN_AT',
            'assessment_type' => 'Written',
            'phase' => 'Final',
            'evaluator_role' => 'at',
            'course_code' => 'FYP',
            'description' => 'Academic Tutor (AT) rubric for evaluating FYP Part 2 End-Term Written Report. Total contribution: 25%',
            'total_weight' => 100.00,
            'component_marks' => 25.00,
            'is_active' => true,
        ]);

        // Elements with proper CLO mapping and weight distribution
        $elements = [
            // CLO 4: Problem Statement, Objectives & Scope (5% = 20% of 25%)
            ['code' => 'PS', 'name' => 'Problem Statement', 'clo' => 'CLO4', 'weight' => 6.67, 'contribution' => 1.67],
            ['code' => 'OBJ', 'name' => 'Project Objectives', 'clo' => 'CLO4', 'weight' => 6.67, 'contribution' => 1.67],
            ['code' => 'SCO', 'name' => 'Project Scope', 'clo' => 'CLO4', 'weight' => 6.66, 'contribution' => 1.66],

            // CLO 5: Flowchart & Gantt Chart (5% = 20% of 25%)
            ['code' => 'FC', 'name' => 'Project Flowchart', 'clo' => 'CLO5', 'weight' => 10.00, 'contribution' => 2.50],
            ['code' => 'GC', 'name' => 'Project Gantt-Chart', 'clo' => 'CLO5', 'weight' => 10.00, 'contribution' => 2.50],

            // CLO 6: Literature Survey (5% = 20% of 25%)
            ['code' => 'LR', 'name' => 'Literature Survey', 'clo' => 'CLO6', 'weight' => 20.00, 'contribution' => 5.00],

            // CLO 1 & 7: Proposed Methodology (10% = 40% of 25%)
            ['code' => 'MTH', 'name' => 'Proposed Methodology', 'clo' => 'CLO1,CLO7', 'weight' => 40.00, 'contribution' => 10.00],
        ];

        $this->createElementsWithDescriptors($template, $elements);
    }

    /**
     * End-Term (Final) Written Report - IC Evaluation (10% total)
     *
     * Weight distribution:
     * - CLO 1 & 7 (Methodology): 4%
     * - CLO 4 (Problem, Objectives, Scope): 2%
     * - CLO 5 (Flowchart, Gantt): 2%
     * - CLO 6 (Literature): 2%
     */
    private function createFinalIcRubric(): void
    {
        $template = FypRubricTemplate::create([
            'name' => 'End-Term Written Report',
            'code' => 'FYP_FINAL_WRITTEN_IC',
            'assessment_type' => 'Written',
            'phase' => 'Final',
            'evaluator_role' => 'ic',
            'course_code' => 'FYP',
            'description' => 'Industry Coach (IC) rubric for evaluating FYP Part 2 End-Term Written Report. Total contribution: 10%',
            'total_weight' => 100.00,
            'component_marks' => 10.00,
            'is_active' => true,
        ]);

        // Elements with proper CLO mapping and weight distribution
        $elements = [
            // CLO 4: Problem Statement, Objectives & Scope (2% = 20% of 10%)
            ['code' => 'PS', 'name' => 'Problem Statement', 'clo' => 'CLO4', 'weight' => 6.67, 'contribution' => 0.67],
            ['code' => 'OBJ', 'name' => 'Project Objectives', 'clo' => 'CLO4', 'weight' => 6.67, 'contribution' => 0.67],
            ['code' => 'SCO', 'name' => 'Project Scope', 'clo' => 'CLO4', 'weight' => 6.66, 'contribution' => 0.66],

            // CLO 5: Flowchart & Gantt Chart (2% = 20% of 10%)
            ['code' => 'FC', 'name' => 'Project Flowchart', 'clo' => 'CLO5', 'weight' => 10.00, 'contribution' => 1.00],
            ['code' => 'GC', 'name' => 'Project Gantt-Chart', 'clo' => 'CLO5', 'weight' => 10.00, 'contribution' => 1.00],

            // CLO 6: Literature Survey (2% = 20% of 10%)
            ['code' => 'LR', 'name' => 'Literature Survey', 'clo' => 'CLO6', 'weight' => 20.00, 'contribution' => 2.00],

            // CLO 1 & 7: Proposed Methodology (4% = 40% of 10%)
            ['code' => 'MTH', 'name' => 'Proposed Methodology', 'clo' => 'CLO1,CLO7', 'weight' => 40.00, 'contribution' => 4.00],
        ];

        $this->createElementsWithDescriptors($template, $elements);
    }

    /**
     * Helper method to create elements with detailed descriptors
     */
    private function createElementsWithDescriptors(FypRubricTemplate $template, array $elements): void
    {
        $descriptorTemplates = $this->getDescriptorTemplates();

        foreach ($elements as $index => $elementData) {
            $element = FypRubricElement::create([
                'rubric_template_id' => $template->id,
                'element_code' => $elementData['code'],
                'name' => $elementData['name'],
                'clo_code' => $elementData['clo'],
                'weight_percentage' => $elementData['weight'],
                'contribution_to_grade' => $elementData['contribution'],
                'order' => $index,
            ]);

            // Get descriptors for this element
            $descriptors = $descriptorTemplates[$elementData['code']] ?? $this->getDefaultDescriptors($elementData['name']);

            foreach ($descriptors as $level => $descriptor) {
                FypRubricLevelDescriptor::create([
                    'rubric_element_id' => $element->id,
                    'level' => $level,
                    'label' => $descriptor['label'],
                    'descriptor' => $descriptor['desc'],
                    'score_value' => $level,
                ]);
            }
        }
    }

    /**
     * Get detailed descriptors based on the rubric form image
     */
    private function getDescriptorTemplates(): array
    {
        return [
            'PS' => [
                1 => ['label' => 'AWARE', 'desc' => 'Problem statement lacks clarity, relevance, specificity, and originality. Major revision needed.'],
                2 => ['label' => 'LIMITED', 'desc' => 'Problem statement lacks clarity, relevance, specificity, and innovation. Significant revision needed.'],
                3 => ['label' => 'FAIR', 'desc' => 'Problem statement needs improvement in clarity, relevance, specificity, and originality.'],
                4 => ['label' => 'GOOD', 'desc' => 'Problem statement is well-structured and effective. Succinctly identifies the research problem, its relevance, and potential contributions.'],
                5 => ['label' => 'EXCELLENT', 'desc' => 'Problem statement is clearly formulated, detailed, realistic, and feasible with clear opportunity for contributions.'],
            ],
            'OBJ' => [
                1 => ['label' => 'AWARE', 'desc' => 'Objectives, measurability, relevance, and feasibility are ambiguous. Complete redefinition and improvement needed.'],
                2 => ['label' => 'LIMITED', 'desc' => 'Objectives lack clarity, alignment, measurability, relevance, and feasibility. Significant revision needed for improvement.'],
                3 => ['label' => 'FAIR', 'desc' => 'Objectives somewhat clear but need refinement for clarity and specificity. Measurability issues require improvement.'],
                4 => ['label' => 'GOOD', 'desc' => 'Clear, specific objectives with minor alignment and clarity issues. Mostly measurable, relevant, and feasible.'],
                5 => ['label' => 'EXCELLENT', 'desc' => 'Clear, specific, well-defined objectives aligned with problem statement, measurable, and feasible.'],
            ],
            'SCO' => [
                1 => ['label' => 'AWARE', 'desc' => 'Scope unclear, overly general, disregards stakeholder input. Complete redefinition needed for clarity and feasibility.'],
                2 => ['label' => 'LIMITED', 'desc' => 'Scope lacks clarity, depth, feasibility, stakeholder input, and boundary definitions. Major revision needed.'],
                3 => ['label' => 'FAIR', 'desc' => 'Scope somewhat clear but needs refinement for clarity. Coverage and feasibility require improvement.'],
                4 => ['label' => 'GOOD', 'desc' => 'Clear, specific scope with minor clarification needed. Mostly deep and comprehensive, feasible with key challenges addressed.'],
                5 => ['label' => 'EXCELLENT', 'desc' => 'Clear, specific, comprehensive project scope, feasible, incorporates stakeholder input for completeness.'],
            ],
            'FC' => [
                1 => ['label' => 'AWARE', 'desc' => 'Milestones need substantial revision for clarity, relevance, measurability, and achievability.'],
                2 => ['label' => 'LIMITED', 'desc' => 'Milestones lack clarity, relevance, measurability, achievability. Significant revision necessary for improvement.'],
                3 => ['label' => 'FAIR', 'desc' => 'Milestones somewhat clear but to be refined for clarity. Timeline planning requires improvement. Achievability within timeframes needs additional attention.'],
                4 => ['label' => 'GOOD', 'desc' => 'Clear and specific milestones with measurable objectives. Mostly relevant, achievable within specified timeframes.'],
                5 => ['label' => 'EXCELLENT', 'desc' => 'Clear, specific and relevant milestones with measurable objectives and deliverables. Realistically achievable within specified timeframes.'],
            ],
            'GC' => [
                1 => ['label' => 'AWARE', 'desc' => 'Timeline unclear, unrealistic, disregards dependencies, lacks flexibility. Requires complete redefinition.'],
                2 => ['label' => 'LIMITED', 'desc' => 'Timeline lacks clarity, realism, organization. Needs significant revision, attention to dependencies, flexibility.'],
                3 => ['label' => 'FAIR', 'desc' => 'Somewhat clear, lacking specificity in tasks or deadlines. Project feasibility somewhat compromised. Dependencies and constraints not fully considered.'],
                4 => ['label' => 'GOOD', 'desc' => 'Clear, detailed, well-organized timeline with specific timelines. Mostly realistic and feasible, with minor details missing. Provides some flexibility for adjustments.'],
                5 => ['label' => 'EXCELLENT', 'desc' => 'Clear, detailed, well-organized timeline with specific timelines. Realistic resources planning. Flexible with reliable mitigation plan.'],
            ],
            'LR' => [
                1 => ['label' => 'AWARE', 'desc' => 'Sources lack relevance, recency, credibility. Survey lacks major themes. Analysis and synthesis absent.'],
                2 => ['label' => 'LIMITED', 'desc' => 'Limited relevant sources, themes covered. Analysis lacks rigor, structure unclear.'],
                3 => ['label' => 'FAIR', 'desc' => 'Few sources relevant, recent, reputable. Covers some key themes, theories.'],
                4 => ['label' => 'GOOD', 'desc' => 'Relevant, recent, reputable sources. Covers key themes. Mostly rigorous analysis.'],
                5 => ['label' => 'EXCELLENT', 'desc' => 'Relevant, recent, reputable sources. Covers key themes. Rigorous analysis. Clear critique. Well-organized. Integrated with objectives.'],
            ],
            'MTH' => [
                1 => ['label' => 'AWARE', 'desc' => 'Methodology not aligned, inappropriate, lacking rigor, validity, feasibility, and detail.'],
                2 => ['label' => 'LIMITED', 'desc' => 'Limited alignment with objectives, marginally appropriate methods, lack rigor.'],
                3 => ['label' => 'FAIR', 'desc' => 'Methodology somewhat aligns with objectives, somewhat appropriate, somewhat feasible.'],
                4 => ['label' => 'GOOD', 'desc' => 'Methodology mostly aligns with objectives, appropriate, mostly rigorous with feasible plan of actions.'],
                5 => ['label' => 'EXCELLENT', 'desc' => 'Methodology well-aligned with objectives, appropriate, rigorous, feasible, detailed data plan.'],
            ],
        ];
    }

    /**
     * Default descriptors if specific ones not found
     */
    private function getDefaultDescriptors(string $elementName): array
    {
        return [
            1 => ['label' => 'AWARE', 'desc' => "{$elementName} needs major revision. Does not meet minimum requirements."],
            2 => ['label' => 'LIMITED', 'desc' => "{$elementName} is below expectations. Significant improvement needed."],
            3 => ['label' => 'FAIR', 'desc' => "{$elementName} meets basic requirements but needs improvement."],
            4 => ['label' => 'GOOD', 'desc' => "{$elementName} is well-executed with minor improvements needed."],
            5 => ['label' => 'EXCELLENT', 'desc' => "{$elementName} exceeds expectations. Outstanding quality."],
        ];
    }
}
