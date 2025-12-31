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
            $table->string('medical_checkup_path')->nullable()->after('confirmation_proof_path');
            $table->timestamp('medical_checkup_uploaded_at')->nullable()->after('medical_checkup_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_placement_tracking', function (Blueprint $table) {
            $table->dropColumn(['medical_checkup_path', 'medical_checkup_uploaded_at']);
        });
    }
};
