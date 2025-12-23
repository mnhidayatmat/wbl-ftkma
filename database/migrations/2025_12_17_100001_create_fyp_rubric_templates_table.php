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
        Schema::create('fyp_rubric_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Mid-Term Written Report", "Final Oral Presentation"
            $table->string('code', 50)->unique(); // e.g., "FYP_MID_WRITTEN", "FYP_FINAL_ORAL"
            $table->enum('assessment_type', ['Written', 'Oral']); // Report or Presentation
            $table->enum('phase', ['Mid-Term', 'Final']); // Mid or Final assessment
            $table->string('course_code', 10)->default('FYP'); // Course this rubric belongs to
            $table->text('description')->nullable();
            $table->decimal('total_weight', 5, 2)->default(100.00); // Should always be 100%
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false); // Lock when marks have been released
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('course_code');
            $table->index(['assessment_type', 'phase']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fyp_rubric_templates');
    }
};
