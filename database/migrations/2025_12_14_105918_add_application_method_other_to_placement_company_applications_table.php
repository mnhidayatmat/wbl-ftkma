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
            $table->string('application_method_other')->nullable()->after('application_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_company_applications', function (Blueprint $table) {
            $table->dropColumn('application_method_other');
        });
    }
};
