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
        Schema::create('fyp_rubric_overall_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('rubric_template_id')->constrained('fyp_rubric_templates')->onDelete('cascade');
            $table->text('overall_feedback')->nullable(); // Overall comments from evaluator
            $table->text('strengths')->nullable(); // What the student did well
            $table->text('areas_for_improvement')->nullable(); // Areas to improve
            $table->decimal('total_score', 5, 2)->nullable(); // Total weighted score for the rubric
            $table->decimal('percentage_score', 5, 2)->nullable(); // Percentage score (total_score / max_possible * 100)
            $table->enum('status', ['draft', 'submitted', 'released'])->default('draft');
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('student_id');
            $table->index('rubric_template_id');
            $table->index('status');

            // One feedback per student per rubric template
            $table->unique(['student_id', 'rubric_template_id'], 'student_rubric_feedback_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fyp_rubric_overall_feedback');
    }
};
