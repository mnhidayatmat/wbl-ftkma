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
        Schema::create('student_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();

            // File Information
            $table->string('file_path');
            $table->string('file_name');
            $table->string('original_name');
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 100);

            // Submission Details
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->boolean('is_late')->default(false);
            $table->decimal('late_penalty_applied', 5, 2)->nullable();
            $table->boolean('declaration_accepted')->default(false);
            $table->text('student_remarks')->nullable();

            // Status
            $table->enum('status', ['draft', 'submitted', 'evaluated'])->default('submitted');
            $table->timestamp('submitted_at');

            $table->timestamps();

            // Unique constraint: one submission per attempt per student per assessment
            $table->unique(['student_id', 'assessment_id', 'attempt_number'], 'student_assessment_attempt_unique');

            // Indexes for common queries
            $table->index(['assessment_id', 'status']);
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_submissions');
    }
};
