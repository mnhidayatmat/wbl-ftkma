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
        Schema::create('student_course_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('course_type', ['FYP', 'IP', 'OSH', 'PPE', 'Industrial Training', 'IC'])->comment('Course type: FYP, IP, OSH, PPE, Industrial Training, IC');
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Ensure a student can only have one lecturer assignment per course type
            $table->unique(['student_id', 'course_type'], 'student_course_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course_assignments');
    }
};
