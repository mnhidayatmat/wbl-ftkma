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
        Schema::table('student_resume_inspections', function (Blueprint $table) {
            $table->text('student_reply')->nullable()->after('coordinator_comment');
            $table->timestamp('student_replied_at')->nullable()->after('student_reply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_resume_inspections', function (Blueprint $table) {
            $table->dropColumn(['student_reply', 'student_replied_at']);
        });
    }
};
