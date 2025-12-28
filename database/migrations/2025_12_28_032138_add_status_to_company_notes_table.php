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
        Schema::table('company_notes', function (Blueprint $table) {
            $table->string('action_status')->default('pending')->after('next_action_date');
            // pending, completed, dismissed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_notes', function (Blueprint $table) {
            $table->dropColumn('action_status');
        });
    }
};
