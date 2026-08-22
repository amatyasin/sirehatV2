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
        Schema::create('garasi_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garasi_participant_id')->constrained()->cascadeOnDelete();
            $table->date('referral_date');
            $table->text('reason');
            $table->string('destination')->nullable();
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->date('follow_up_date')->nullable();
            $table->text('follow_up_result')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garasi_referrals');
    }
};
