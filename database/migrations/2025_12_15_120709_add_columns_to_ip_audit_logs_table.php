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
            if (! Schema::hasColumn('ip_audit_logs', 'action')) {
                $table->string('action')->after('id');
                $table->index('action');
            }
            if (! Schema::hasColumn('ip_audit_logs', 'action_type')) {
                $table->string('action_type')->after('action');
                $table->index('action_type');
            }
            if (! Schema::hasColumn('ip_audit_logs', 'user_id')) {
                $table->foreignId('user_id')->after('action_type')->constrained('users')->onDelete('cascade');
                $table->index('user_id');
            }
            if (! Schema::hasColumn('ip_audit_logs', 'user_role')) {
                $table->string('user_role')->after('user_id');
            }
            if (! Schema::hasColumn('ip_audit_logs', 'student_id')) {
                $table->foreignId('student_id')->nullable()->after('user_role')->constrained('students')->onDelete('set null');
                $table->index('student_id');
            }
            if (! Schema::hasColumn('ip_audit_logs', 'assessment_id')) {
                $table->foreignId('assessment_id')->nullable()->after('student_id')->constrained('assessments')->onDelete('set null');
            }
            if (! Schema::hasColumn('ip_audit_logs', 'description')) {
                $table->text('description')->after('assessment_id');
            }
            if (! Schema::hasColumn('ip_audit_logs', 'metadata')) {
                $table->json('metadata')->nullable()->after('description');
            }
            if (! Schema::hasColumn('ip_audit_logs', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('metadata');
            }
            if (! Schema::hasColumn('ip_audit_logs', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('ip_address');
            }
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
