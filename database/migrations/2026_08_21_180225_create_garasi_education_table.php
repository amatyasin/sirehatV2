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
        Schema::create('garasi_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garasi_participant_id')->constrained()->cascadeOnDelete();
            $table->boolean('brushing_education')->default(false);
            $table->boolean('brushing_frequency_education')->default(false);
            $table->boolean('fluoride_education')->default(false);
            $table->boolean('sugar_education')->default(false);
            $table->boolean('dental_checkup_education')->default(false);
            $table->boolean('home_care_education')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('educator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garasi_educations');
    }
};
