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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('course_code', 10); // PPE, IP, OSH, FYP, LI
            $table->string('assessment_name');
            $table->string('assessment_type'); // Assignment, Report, Presentation, Oral, Rubric, Logbook
            $table->string('clo_code', 10); // CLO1, CLO2, CLO3, CLO4
            $table->decimal('weight_percentage', 5, 2); // e.g. 20.00
            $table->string('evaluator_role', 20); // lecturer, at, ic, supervisor_li
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('course_code');
            $table->index('evaluator_role');
            $table->index('is_active');
            $table->index(['course_code', 'evaluator_role', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
