<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to alter the ENUM column to include the new value
        DB::statement("ALTER TABLE `resume_inspection_history` MODIFY COLUMN `action` ENUM('REVIEWED', 'APPROVED', 'REVISION_REQUESTED', 'COMMENT_ADDED', 'COMMENT_UPDATED', 'RESET') DEFAULT 'REVIEWED'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'RESET' from the ENUM (revert to original values)
        DB::statement("ALTER TABLE `resume_inspection_history` MODIFY COLUMN `action` ENUM('REVIEWED', 'APPROVED', 'REVISION_REQUESTED', 'COMMENT_ADDED', 'COMMENT_UPDATED') DEFAULT 'REVIEWED'");
    }
};
