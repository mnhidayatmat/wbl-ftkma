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
        Schema::create('osh_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // e.g., 'mark_submitted', 'schedule_updated'
            $table->string('action_type'); // e.g., 'evaluation', 'schedule'
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_role')->nullable();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->foreignId('assessment_id')->nullable()->constrained('assessments')->onDelete('set null');
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['action', 'action_type']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('osh_audit_logs');
    }
};
