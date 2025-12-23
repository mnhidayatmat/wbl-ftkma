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
            $table->boolean('checklist_merged_pdf')->default(false)->after('resume_file_path');
            $table->boolean('checklist_document_order')->default(false)->after('checklist_merged_pdf');
            $table->boolean('checklist_resume_concise')->default(false)->after('checklist_document_order');
            $table->boolean('checklist_achievements_highlighted')->default(false)->after('checklist_resume_concise');
            $table->boolean('checklist_poster_includes_required')->default(false)->after('checklist_achievements_highlighted');
            $table->boolean('checklist_poster_pages_limit')->default(false)->after('checklist_poster_includes_required');
            $table->boolean('checklist_own_work_ready')->default(false)->after('checklist_poster_pages_limit');
            $table->timestamp('checklist_confirmed_at')->nullable()->after('checklist_own_work_ready');
            $table->string('checklist_ip_address')->nullable()->after('checklist_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_resume_inspections', function (Blueprint $table) {
            $table->dropColumn([
                'checklist_merged_pdf',
                'checklist_document_order',
                'checklist_resume_concise',
                'checklist_achievements_highlighted',
                'checklist_poster_includes_required',
                'checklist_poster_pages_limit',
                'checklist_own_work_ready',
                'checklist_confirmed_at',
                'checklist_ip_address',
            ]);
        });
    }
};
