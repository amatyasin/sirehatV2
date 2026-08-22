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
        Schema::create('garasi_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posyandu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instansi_id')->constrained()->cascadeOnDelete();
            $table->date('activity_date');
            $table->string('location')->nullable();
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, ongoing, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garasi_activities');
    }
};
