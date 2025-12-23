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
            $table->timestamp('applied_at')->nullable()->after('applied_status_set_at');
            $table->timestamp('interviewed_at')->nullable()->after('applied_at');
            $table->timestamp('offer_received_at')->nullable()->after('interviewed_at');
            $table->timestamp('accepted_at')->nullable()->after('offer_received_at');
            $table->timestamp('confirmed_at')->nullable()->after('accepted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_placement_tracking', function (Blueprint $table) {
            $table->dropColumn([
                'applied_at',
                'interviewed_at',
                'offer_received_at',
                'accepted_at',
                'confirmed_at',
            ]);
        });
    }
};
