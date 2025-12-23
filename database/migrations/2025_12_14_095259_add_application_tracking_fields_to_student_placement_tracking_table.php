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
            // Application tracking fields
            $table->integer('companies_applied_count')->default(0)->after('status');
            $table->date('first_application_date')->nullable()->after('companies_applied_count');
            $table->date('last_application_date')->nullable()->after('first_application_date');
            $table->json('application_methods')->nullable()->after('last_application_date'); // Store array of methods
            $table->text('application_notes')->nullable()->after('application_methods');
            $table->timestamp('applied_status_set_at')->nullable()->after('application_notes'); // When status was set to APPLIED
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_placement_tracking', function (Blueprint $table) {
            $table->dropColumn([
                'companies_applied_count',
                'first_application_date',
                'last_application_date',
                'application_methods',
                'application_notes',
                'applied_status_set_at',
            ]);
        });
    }
};
