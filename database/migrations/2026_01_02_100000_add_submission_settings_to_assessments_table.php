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
        Schema::table('assessments', function (Blueprint $table) {
            // Student Submission Settings
            $table->boolean('requires_submission')->default(false)->after('is_active');
            $table->timestamp('submission_deadline')->nullable()->after('requires_submission');
            $table->json('allowed_file_types')->nullable()->after('submission_deadline');
            $table->unsignedInteger('max_file_size_mb')->default(10)->after('allowed_file_types');
            $table->unsignedTinyInteger('max_attempts')->default(1)->after('max_file_size_mb');

            // Late Submission Settings
            $table->boolean('allow_late_submission')->default(false)->after('max_attempts');
            $table->decimal('late_penalty_per_day', 5, 2)->nullable()->after('allow_late_submission');
            $table->unsignedInteger('max_late_days')->nullable()->after('late_penalty_per_day');

            // Declaration & Instructions
            $table->boolean('require_declaration')->default(true)->after('max_late_days');
            $table->text('submission_instructions')->nullable()->after('require_declaration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn([
                'requires_submission',
                'submission_deadline',
                'allowed_file_types',
                'max_file_size_mb',
                'max_attempts',
                'allow_late_submission',
                'late_penalty_per_day',
                'max_late_days',
                'require_declaration',
                'submission_instructions',
            ]);
        });
    }
};
