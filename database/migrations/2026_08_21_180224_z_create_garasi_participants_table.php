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
        Schema::create('garasi_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garasi_activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('orang_tua_id')->nullable()->constrained('orang_tuas')->nullOnDelete();
            $table->boolean('attendance')->default(false);
            $table->boolean('mother_accompanied')->default(false);
            $table->string('toothbrushing_practice')->nullable(); // mandiri, dengan_bantuan, belum_mampu
            $table->string('brushing_frequency')->nullable(); // tidak_rutin, 1_kali, 2_kali, lebih_2_kali
            $table->string('use_toothpaste')->nullable(); // ya, tidak, tidak_diketahui
            $table->boolean('brushing_before_bed')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garasi_participants');
    }
};
