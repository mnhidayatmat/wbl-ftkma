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
            $table->boolean('offer_received')->default(false)->after('follow_up_notes');
            $table->date('offer_received_date')->nullable()->after('offer_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_company_applications', function (Blueprint $table) {
            $table->dropColumn(['offer_received', 'offer_received_date']);
        });
    }
};
