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
        Schema::table('ppe_student_ic_marks', function (Blueprint $table) {
            $table->integer('question_no')->nullable()->after('clo');
            $table->integer('rubric_value')->nullable()->after('question_no'); // 1-5 scale
            // Keep mark for calculated value
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppe_student_ic_marks', function (Blueprint $table) {
            $table->dropColumn(['question_no', 'rubric_value']);
        });
    }
};

