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
        Schema::table('workplace_issue_reports', function (Blueprint $table) {
            // Add company_id to link workplace issues to the student's placement company
            $table->foreignId('company_id')->nullable()->after('group_id')->constrained('companies')->onDelete('set null');

            // Add index for querying issues by company
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workplace_issue_reports', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
