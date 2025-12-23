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
        Schema::table('wbl_groups', function (Blueprint $table) {
            $table->enum('status', ['ACTIVE', 'COMPLETED'])->default('ACTIVE')->after('end_date');
            $table->timestamp('completed_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wbl_groups', function (Blueprint $table) {
            $table->dropColumn(['status', 'completed_at']);
        });
    }
};
