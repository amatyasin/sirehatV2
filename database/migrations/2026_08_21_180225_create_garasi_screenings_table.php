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
        Schema::create('garasi_screenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garasi_participant_id')->constrained()->cascadeOnDelete();
            $table->boolean('toothache')->default(false);
            $table->boolean('sensitive_teeth')->default(false);
            $table->boolean('bleeding_gums')->default(false);
            $table->boolean('swollen_gums')->default(false);
            $table->boolean('bad_breath')->default(false);
            $table->boolean('mouth_sores')->default(false);
            $table->boolean('chewing_difficulty')->default(false);
            $table->string('oral_hygiene')->nullable();
            $table->boolean('plaque')->default(false);
            $table->boolean('cavities')->default(false);
            $table->boolean('broken_teeth')->default(false);
            $table->boolean('red_gums')->default(false);
            $table->boolean('swollen_gums_observed')->default(false);
            $table->text('other_findings')->nullable();
            $table->string('risk_level')->nullable();
            $table->text('recommendation')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('examiner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garasi_screenings');
    }
};
