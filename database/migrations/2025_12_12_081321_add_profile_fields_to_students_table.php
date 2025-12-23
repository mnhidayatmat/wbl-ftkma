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
        Schema::table('students', function (Blueprint $table) {
            $table->string('mobile_phone')->nullable()->after('background');
            $table->string('resume_pdf_path')->nullable()->after('mobile_phone');
            $table->foreignId('academic_advisor_id')->nullable()->after('resume_pdf_path')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['academic_advisor_id']);
            $table->dropColumn(['mobile_phone', 'resume_pdf_path', 'academic_advisor_id']);
        });
    }
};
