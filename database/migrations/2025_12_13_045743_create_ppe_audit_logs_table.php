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
        Schema::create('ppe_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // e.g., 'assessment_created', 'score_edited', 'moderation_applied', 'result_finalised'
            $table->string('action_type'); // 'assessment', 'evaluation', 'moderation', 'finalisation', 'export', 'schedule'
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_role');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->foreignId('assessment_id')->nullable()->constrained('assessments')->onDelete('set null');
            $table->text('description');
            $table->json('metadata')->nullable(); // Store additional context (old values, new values, etc.)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index('action');
            $table->index('action_type');
            $table->index('user_id');
            $table->index('student_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppe_audit_logs');
    }
};
