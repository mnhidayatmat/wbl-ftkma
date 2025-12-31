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
        Schema::table('student_placement_tracking', function (Blueprint $table) {
            $table->string('offer_letter_path')->nullable()->after('confirmation_proof_path');
            $table->boolean('company_details_completed')->default(false)->after('offer_letter_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_placement_tracking', function (Blueprint $table) {
            $table->dropColumn(['offer_letter_path', 'company_details_completed']);
        });
    }
};
