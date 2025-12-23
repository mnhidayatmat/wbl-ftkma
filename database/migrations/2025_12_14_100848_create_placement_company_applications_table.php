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
        Schema::create('placement_company_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('placement_tracking_id')->constrained('student_placement_tracking')->onDelete('cascade');
            $table->string('company_name');
            $table->date('application_deadline')->nullable();
            $table->string('application_method'); // job_portal, company_website, email, career_fair, referral
            $table->timestamps();
            
            $table->index('placement_tracking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_company_applications');
    }
};
