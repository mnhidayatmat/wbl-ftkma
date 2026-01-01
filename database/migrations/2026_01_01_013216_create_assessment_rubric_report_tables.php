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
        // Main rubric report per assessment
        Schema::create('assessment_rubric_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->unique()->constrained('assessments')->onDelete('cascade');
            $table->enum('input_type', ['manual', 'file']);
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Elements for manual input
        Schema::create('assessment_rubric_report_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_report_id')->constrained('assessment_rubric_reports')->onDelete('cascade');
            $table->string('element_name');
            $table->text('criteria_keywords')->nullable();
            $table->decimal('weight_percentage', 5, 2)->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Rating definitions per element
        Schema::create('assessment_rubric_report_descriptors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->constrained('assessment_rubric_report_elements')->onDelete('cascade');
            $table->tinyInteger('level'); // 1=Aware, 2=Limited, 3=Fair, 4=Good, 5=Excellent
            $table->string('label'); // "Aware", "Limited", etc.
            $table->text('descriptor'); // Rating definition text
            $table->timestamps();

            // Unique constraint: one descriptor per level per element
            $table->unique(['element_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_rubric_report_descriptors');
        Schema::dropIfExists('assessment_rubric_report_elements');
        Schema::dropIfExists('assessment_rubric_reports');
    }
};
