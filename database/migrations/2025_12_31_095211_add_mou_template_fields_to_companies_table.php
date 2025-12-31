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
        Schema::table('companies', function (Blueprint $table) {
            // MoU Template Manual Input Variables
            $table->string('mou_company_number')->nullable()->after('ic_position');
            $table->string('mou_company_shortname')->nullable()->after('mou_company_number');
            $table->string('mou_signed_behalf_name')->nullable()->after('mou_company_shortname');
            $table->string('mou_signed_behalf_position')->nullable()->after('mou_signed_behalf_name');
            $table->string('mou_witness_name')->nullable()->after('mou_signed_behalf_position');
            $table->string('mou_witness_position')->nullable()->after('mou_witness_name');
            $table->string('mou_generated_path')->nullable()->after('mou_witness_position');
            $table->timestamp('mou_generated_at')->nullable()->after('mou_generated_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'mou_company_number',
                'mou_company_shortname',
                'mou_signed_behalf_name',
                'mou_signed_behalf_position',
                'mou_witness_name',
                'mou_witness_position',
                'mou_generated_path',
                'mou_generated_at',
            ]);
        });
    }
};
