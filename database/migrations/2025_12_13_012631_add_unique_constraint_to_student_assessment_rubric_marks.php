<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists and constraint doesn't exist
        if (Schema::hasTable('student_assessment_rubric_marks')) {
            // Check if unique constraint already exists
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'student_assessment_rubric_marks' 
                AND CONSTRAINT_TYPE = 'UNIQUE'
                AND CONSTRAINT_NAME = 'student_rubric_unique'
            ");

            if (empty($constraints)) {
                Schema::table('student_assessment_rubric_marks', function (Blueprint $table) {
                    $table->unique(['student_id', 'assessment_rubric_id'], 'student_rubric_unique');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_assessment_rubric_marks')) {
            Schema::table('student_assessment_rubric_marks', function (Blueprint $table) {
                $table->dropUnique('student_rubric_unique');
            });
        }
    }
};
