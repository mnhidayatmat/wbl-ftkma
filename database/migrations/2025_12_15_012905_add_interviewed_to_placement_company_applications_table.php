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
            $table->boolean('interviewed')->default(false)->after('application_method_other');
            $table->timestamp('interviewed_at')->nullable()->after('interviewed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_company_applications', function (Blueprint $table) {
            $table->dropColumn(['interviewed', 'interviewed_at']);
        });
    }
};
