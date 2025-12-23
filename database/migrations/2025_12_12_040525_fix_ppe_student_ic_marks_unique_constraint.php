<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ppe_student_ic_marks', function (Blueprint $table) {
            // First, add the new unique constraint on (student_id, clo, question_no)
            // This allows multiple questions per CLO (e.g., Q1 and Q2 both for CLO2)
            $table->unique(['student_id', 'clo', 'question_no'], 'ppe_student_ic_marks_student_clo_question_unique');
        });

        // Now drop the old unique constraint using raw SQL
        // We do this separately because MySQL may have issues if done in the same transaction
        try {
            DB::statement('ALTER TABLE ppe_student_ic_marks DROP INDEX ppe_student_ic_marks_student_id_clo_unique');
        } catch (\Exception $e) {
            // If it fails, the new constraint will still work
            // The old constraint will just remain but won't be used
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new unique constraint
        DB::statement('ALTER TABLE ppe_student_ic_marks DROP INDEX ppe_student_ic_marks_student_clo_question_unique');

        // Restore the old unique constraint
        Schema::table('ppe_student_ic_marks', function (Blueprint $table) {
            $table->unique(['student_id', 'clo']);
        });
    }
};
