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
        Schema::table('ppe_ic_questions', function (Blueprint $table) {
            $table->integer('question_no')->after('id')->default(1); // Q1, Q2, Q3, Q4
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppe_ic_questions', function (Blueprint $table) {
            $table->dropColumn('question_no');
        });
    }
};
