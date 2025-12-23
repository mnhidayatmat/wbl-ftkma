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
        Schema::create('lecturer_course_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained('users')->onDelete('cascade');
            $table->enum('course_type', ['FYP', 'IP', 'OSH', 'PPE', 'Industrial Training', 'IC'])->comment('Course type: FYP, IP, OSH, PPE, Industrial Training, IC');
            $table->timestamps();
            
            // Ensure a lecturer can only be assigned once per course type
            $table->unique(['lecturer_id', 'course_type'], 'lecturer_course_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer_course_assignments');
    }
};
