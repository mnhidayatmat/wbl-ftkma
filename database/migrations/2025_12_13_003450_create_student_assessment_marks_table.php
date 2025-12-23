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
        Schema::create('student_assessment_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->decimal('mark', 5, 2)->nullable(); // The mark entered by evaluator
            $table->decimal('max_mark', 5, 2)->default(100); // Maximum possible mark (can be customized per assessment)
            $table->text('remarks')->nullable(); // Optional remarks/feedback
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null'); // Who entered the mark
            $table->timestamps();

            // Ensure one mark per student per assessment
            $table->unique(['student_id', 'assessment_id']);

            // Indexes for performance
            $table->index('student_id');
            $table->index('assessment_id');
            $table->index('evaluated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assessment_marks');
    }
};
