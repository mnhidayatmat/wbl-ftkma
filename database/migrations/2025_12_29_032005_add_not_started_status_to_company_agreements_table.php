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
        // Modify the enum to add 'Not Started' status
        DB::statement("ALTER TABLE company_agreements MODIFY COLUMN status ENUM('Not Started', 'Draft', 'Pending', 'Active', 'Expired', 'Terminated') DEFAULT 'Not Started'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE company_agreements MODIFY COLUMN status ENUM('Active', 'Expired', 'Terminated', 'Pending', 'Draft') DEFAULT 'Draft'");
    }
};
