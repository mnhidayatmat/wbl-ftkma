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
        Schema::table('student_resume_inspections', function (Blueprint $table) {
            // Add approved_at column
            $table->timestamp('approved_at')->nullable()->after('reviewed_at');
        });

        // Update enum values - MySQL doesn't support ALTER ENUM directly, so we'll modify the column
        DB::statement("ALTER TABLE student_resume_inspections MODIFY COLUMN status ENUM('PENDING', 'PASSED', 'FAILED', 'REVISION_REQUIRED') DEFAULT 'PENDING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_resume_inspections', function (Blueprint $table) {
            $table->dropColumn('approved_at');
        });

        // Revert enum to original values
        DB::statement("ALTER TABLE student_resume_inspections MODIFY COLUMN status ENUM('PENDING', 'PASSED', 'FAILED') DEFAULT 'PENDING'");
    }
};
