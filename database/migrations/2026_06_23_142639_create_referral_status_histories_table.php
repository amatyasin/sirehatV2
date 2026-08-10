<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_status_histories', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('referral_id')
                ->constrained('referrals')
                ->cascadeOnDelete();
                
            $table->string('status_lama')->nullable();
            $table->string('status_baru');
            
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
                
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            
            $table->index('referral_id', 'idx_status_history_referral_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_status_histories');
    }
};
