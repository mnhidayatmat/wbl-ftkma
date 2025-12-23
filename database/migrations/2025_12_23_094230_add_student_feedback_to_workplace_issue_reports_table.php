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
            $table->text('student_feedback')->nullable()->after('resolution_notes');
            $table->timestamp('student_feedback_at')->nullable()->after('closed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workplace_issue_reports', function (Blueprint $table) {
            $table->dropColumn(['student_feedback', 'student_feedback_at']);
        });
    }
};
