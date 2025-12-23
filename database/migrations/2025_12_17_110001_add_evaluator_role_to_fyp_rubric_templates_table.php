<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fyp_rubric_templates', function (Blueprint $table) {
            // Add evaluator role to support separate AT and IC rubrics
            $table->enum('evaluator_role', ['at', 'ic'])->default('at')->after('phase');
            // Add component marks percentage (the total marks this rubric contributes to overall grade)
            $table->decimal('component_marks', 5, 2)->default(0)->after('total_weight');
            
            // Update unique constraint to allow same code with different evaluator roles
            $table->dropUnique('fyp_rubric_templates_code_unique');
            $table->unique(['code', 'evaluator_role'], 'fyp_rubric_templates_code_role_unique');
        });

        // Also add AT/IC weight columns to elements for clarity
        Schema::table('fyp_rubric_elements', function (Blueprint $table) {
            // The weight_percentage already exists, but let's add a computed contribution column
            $table->decimal('contribution_to_grade', 5, 2)->nullable()->after('weight_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fyp_rubric_templates', function (Blueprint $table) {
            $table->dropUnique('fyp_rubric_templates_code_role_unique');
            $table->unique('code', 'fyp_rubric_templates_code_unique');
            $table->dropColumn(['evaluator_role', 'component_marks']);
        });

        Schema::table('fyp_rubric_elements', function (Blueprint $table) {
            $table->dropColumn('contribution_to_grade');
        });
    }
};
