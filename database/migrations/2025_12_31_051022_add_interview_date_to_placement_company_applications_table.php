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
        Schema::table('placement_company_applications', function (Blueprint $table) {
            $table->date('interview_date')->nullable()->after('interviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_company_applications', function (Blueprint $table) {
            $table->dropColumn('interview_date');
        });
    }
};
