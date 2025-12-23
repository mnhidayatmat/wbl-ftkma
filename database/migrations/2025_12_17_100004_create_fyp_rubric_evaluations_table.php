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
        Schema::create('fyp_rubric_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('rubric_template_id')->constrained('fyp_rubric_templates')->onDelete('cascade');
            $table->foreignId('rubric_element_id')->constrained('fyp_rubric_elements')->onDelete('cascade');
            $table->foreignId('selected_level_id')->nullable()->constrained('fyp_rubric_level_descriptors')->onDelete('set null');
            $table->integer('selected_level')->nullable(); // Level selected (1-5)
            $table->decimal('score', 5, 2)->nullable(); // Actual score given
            $table->decimal('weighted_score', 5, 2)->nullable(); // Score * (weight/100) for CLO calculation
            $table->text('remarks')->nullable(); // Per-element comments
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('student_id');
            $table->index('rubric_template_id');
            $table->index('rubric_element_id');
            $table->index('evaluated_by');

            // One evaluation per student per element
            $table->unique(['student_id', 'rubric_element_id'], 'student_rubric_element_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fyp_rubric_evaluations');
    }
};
