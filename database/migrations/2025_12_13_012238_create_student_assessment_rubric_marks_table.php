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
        if (!Schema::hasTable('student_assessment_rubric_marks')) {
            Schema::create('student_assessment_rubric_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('assessment_rubric_id')->constrained('assessment_rubrics')->onDelete('cascade');
            $table->integer('rubric_score'); // The selected rubric score (e.g., 1-5)
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Ensure one mark per student per rubric question
            $table->unique(['student_id', 'assessment_rubric_id'], 'student_rubric_unique');
            
            // Indexes
            $table->index('student_id');
            $table->index('assessment_rubric_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assessment_rubric_marks');
    }
};
