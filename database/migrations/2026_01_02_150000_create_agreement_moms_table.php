<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create agreement_moms table to store MoM documents that can be linked to multiple companies.
     */
    public function up(): void
    {
        // Create the main MoM table
        Schema::create('agreement_moms', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->date('meeting_date');
            $table->string('document_path');
            $table->string('document_name')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Create pivot table to link MoMs to multiple companies
        Schema::create('agreement_mom_company', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_mom_id')->constrained('agreement_moms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['agreement_mom_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_mom_company');
        Schema::dropIfExists('agreement_moms');
    }
};
