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
        Schema::create('workplace_issue_report_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_report_id')->constrained('workplace_issue_reports')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->enum('action', [
                'CREATED',
                'STATUS_CHANGED',
                'COMMENT_ADDED',
                'COMMENT_UPDATED',
                'ASSIGNED',
                'REVIEWED',
                'IN_PROGRESS',
                'RESOLVED',
                'CLOSED',
                'REOPENED',
                'ATTACHMENT_ADDED'
            ]);

            $table->string('status')->nullable();
            $table->text('comment')->nullable();
            $table->text('previous_comment')->nullable();
            $table->json('metadata')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamps();

            $table->index('issue_report_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workplace_issue_report_history');
    }
};
