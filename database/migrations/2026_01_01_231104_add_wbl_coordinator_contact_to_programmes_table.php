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
        Schema::table('programmes', function (Blueprint $table) {
            $table->string('wbl_coordinator_name')->nullable()->after('wbl_coordinator_role');
            $table->string('wbl_coordinator_email')->nullable()->after('wbl_coordinator_name');
            $table->string('wbl_coordinator_phone', 20)->nullable()->after('wbl_coordinator_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropColumn(['wbl_coordinator_name', 'wbl_coordinator_email', 'wbl_coordinator_phone']);
        });
    }
};
