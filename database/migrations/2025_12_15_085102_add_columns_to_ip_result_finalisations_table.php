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
        Schema::table('ip_result_finalisations', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->after('id')->constrained('students')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->after('student_id')->constrained('wbl_groups')->onDelete('cascade');
            $table->enum('finalisation_scope', ['student', 'group', 'course'])->default('student')->after('group_id');
            $table->boolean('is_finalised')->default(false)->after('finalisation_scope');
            $table->text('notes')->nullable()->after('is_finalised');
            $table->foreignId('finalised_by')->after('notes')->constrained('users')->onDelete('cascade');
            $table->timestamp('finalised_at')->nullable()->after('finalised_by');
            
            $table->index('student_id');
            $table->index('group_id');
            $table->index('is_finalised');
            $table->unique(['student_id', 'is_finalised'], 'unique_student_finalisation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ip_result_finalisations', function (Blueprint $table) {
            $table->dropUnique('unique_student_finalisation');
            $table->dropIndex(['student_id']);
            $table->dropIndex(['group_id']);
            $table->dropIndex(['is_finalised']);
            $table->dropForeign(['student_id']);
            $table->dropForeign(['group_id']);
            $table->dropForeign(['finalised_by']);
            $table->dropColumn([
                'student_id',
                'group_id',
                'finalisation_scope',
                'is_finalised',
                'notes',
                'finalised_by',
                'finalised_at'
            ]);
        });
    }
};
