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
        // Add status column to companies if not exists
        if (! Schema::hasColumn('companies', 'status')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->enum('status', ['Active', 'Inactive', 'Archived'])->default('Active')->after('website');
            });
        }

        Schema::create('company_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->enum('agreement_type', ['MoU', 'MoA', 'LOI']);
            $table->string('agreement_title')->nullable();
            $table->string('reference_no')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('signed_date')->nullable();
            $table->enum('status', ['Active', 'Expired', 'Terminated', 'Pending', 'Draft'])->default('Draft');
            $table->string('faculty')->nullable();
            $table->string('programme')->nullable();
            $table->text('remarks')->nullable();
            $table->string('document_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index('company_id');
            $table->index('agreement_type');
            $table->index('status');
            $table->index('end_date');
            $table->index(['company_id', 'agreement_type']);

            // Prevent duplicate: same company + same type + same reference number
            $table->unique(['company_id', 'agreement_type', 'reference_no'], 'unique_company_agreement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_agreements');

        if (Schema::hasColumn('companies', 'status')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
