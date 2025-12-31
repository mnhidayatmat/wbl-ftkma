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
            $table->boolean('decline_sent')->default(false)->after('decline_notes');
            $table->boolean('is_accepted')->default(false)->after('decline_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_company_applications', function (Blueprint $table) {
            $table->dropColumn(['decline_sent', 'is_accepted']);
        });
    }
};
