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
        // MySQL doesn't support ALTER ENUM directly, so we use raw SQL
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'lecturer', 'industry', 'student', 'supervisor_li', 'at') DEFAULT 'student'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'lecturer', 'industry', 'student') DEFAULT 'student'");
    }
};
