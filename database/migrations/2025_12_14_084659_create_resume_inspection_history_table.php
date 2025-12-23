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
        Schema::create('resume_inspection_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('student_resume_inspections')->onDelete('cascade');
            $table->foreignId('reviewed_by')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['REVIEWED', 'APPROVED', 'REVISION_REQUESTED', 'COMMENT_ADDED', 'COMMENT_UPDATED'])->default('REVIEWED');
            $table->enum('status', ['PENDING', 'PASSED', 'FAILED', 'REVISION_REQUIRED'])->nullable();
            $table->text('comment')->nullable();
            $table->text('previous_comment')->nullable(); // Store previous comment for comparison
            $table->json('metadata')->nullable(); // Store additional context
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index('inspection_id');
            $table->index('reviewed_by');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_inspection_history');
    }
};
