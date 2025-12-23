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
        Schema::create('ppe_assessment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('clo'); // CLO1, CLO2, etc.
            $table->decimal('weight', 5, 2); // Percentage weight
            $table->decimal('max_mark', 5, 2); // Maximum marks
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppe_assessment_settings');
    }
};

