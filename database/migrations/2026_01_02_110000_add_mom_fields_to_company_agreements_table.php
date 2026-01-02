<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add Minute of Meeting (MoM) fields to company_agreements table.
     */
    public function up(): void
    {
        Schema::table('company_agreements', function (Blueprint $table) {
            $table->boolean('mom_mentioned')->default(false)->after('remarks');
            $table->string('mom_document_path')->nullable()->after('mom_mentioned');
            $table->date('mom_date')->nullable()->after('mom_document_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_agreements', function (Blueprint $table) {
            $table->dropColumn(['mom_mentioned', 'mom_document_path', 'mom_date']);
        });
    }
};
