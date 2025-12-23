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
        Schema::table('ip_audit_logs', function (Blueprint $table) {
            $table->string('action')->after('id');
            $table->string('action_type')->after('action');
            $table->foreignId('user_id')->after('action_type')->constrained('users')->onDelete('cascade');
            $table->string('user_role')->after('user_id');
            $table->foreignId('student_id')->nullable()->after('user_role')->constrained('students')->onDelete('set null');
            $table->foreignId('assessment_id')->nullable()->after('student_id')->constrained('assessments')->onDelete('set null');
            $table->text('description')->after('assessment_id');
            $table->json('metadata')->nullable()->after('description');
            $table->string('ip_address')->nullable()->after('metadata');
            $table->string('user_agent')->nullable()->after('ip_address');

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
        Schema::table('ip_audit_logs', function (Blueprint $table) {
            $table->dropIndex(['action']);
            $table->dropIndex(['action_type']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['student_id']);
            $table->dropIndex(['created_at']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['student_id']);
            $table->dropForeign(['assessment_id']);
            $table->dropColumn([
                'action',
                'action_type',
                'user_id',
                'user_role',
                'student_id',
                'assessment_id',
                'description',
                'metadata',
                'ip_address',
                'user_agent',
            ]);
        });
    }
};
