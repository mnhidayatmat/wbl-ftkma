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
        Schema::create('workplace_issue_reports', function (Blueprint $table) {
            $table->id();

            // Student Information
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('wbl_groups')->onDelete('set null');

            // Issue Details
            $table->string('title', 255);
            $table->text('description');
            $table->enum('category', [
                'safety_health',
                'harassment_discrimination',
                'work_environment',
                'supervision_guidance',
                'custom'
            ]);
            $table->string('custom_category', 255)->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);

            // Location & Date
            $table->string('location', 255)->nullable();
            $table->date('incident_date')->nullable();
            $table->time('incident_time')->nullable();

            // Status Tracking
            $table->enum('status', ['new', 'under_review', 'in_progress', 'resolved', 'closed'])
                  ->default('new');

            // Coordinator/Admin Response
            $table->text('coordinator_comment')->nullable();
            $table->text('resolution_notes')->nullable();

            // Tracking Fields
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');

            // Timestamps
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('in_progress_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // Audit Fields
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('student_id');
            $table->index('group_id');
            $table->index('status');
            $table->index('severity');
            $table->index('category');
            $table->index('submitted_at');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workplace_issue_reports');
    }
};
