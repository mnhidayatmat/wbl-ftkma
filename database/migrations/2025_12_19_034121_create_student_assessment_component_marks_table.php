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
        Schema::create('student_assessment_component_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('assessment_components')->onDelete('cascade');
            $table->integer('rubric_score')->nullable(); // 1-5 rating
            $table->text('remarks')->nullable(); // Optional remarks per component
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Ensure one mark per student per component
            $table->unique(['student_id', 'assessment_id', 'component_id']);

            // Indexes for performance
            $table->index('student_id');
            $table->index('assessment_id');
            $table->index('component_id');
            $table->index('evaluated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assessment_component_marks');
    }
};
