<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add student reply action types to the ENUM column
        DB::statement("ALTER TABLE `resume_inspection_history` MODIFY COLUMN `action` ENUM('REVIEWED', 'APPROVED', 'REVISION_REQUESTED', 'COMMENT_ADDED', 'COMMENT_UPDATED', 'RESET', 'STUDENT_REPLY', 'STUDENT_REPLY_UPDATED') DEFAULT 'REVIEWED'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove student reply actions from the ENUM (revert to previous values)
        DB::statement("ALTER TABLE `resume_inspection_history` MODIFY COLUMN `action` ENUM('REVIEWED', 'APPROVED', 'REVISION_REQUESTED', 'COMMENT_ADDED', 'COMMENT_UPDATED', 'RESET') DEFAULT 'REVIEWED'");
    }
};
