<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            
            // Link to student class history
            $table->foreignId('student_class_history_id')
                ->constrained('student_class_histories')
                ->cascadeOnDelete();
                
            // Polymorphic link to source checkup
            $table->string('pemeriksaan_type')->nullable();
            $table->unsignedBigInteger('pemeriksaan_id')->nullable();
            
            // Cached/Categorized fields for performance
            $table->string('jenis_pemeriksaan'); // e.g., 'Gizi', 'Gigi dan Mulut', 'Mata', 'Telinga', 'Umum'
            $table->text('alasan_rujukan');
            
            // Status and actions
            $table->enum('status_rujukan', [
                'Belum Dirujuk',
                'Sudah Dirujuk',
                'Dalam Tindak Lanjut',
                'Selesai'
            ])->default('Belum Dirujuk');
            
            $table->string('tujuan_rujukan')->nullable();
            $table->string('petugas_pemeriksa')->nullable();
            
            // Cached dates for fast query ranges
            $table->date('tanggal_pemeriksaan');
            $table->date('tanggal_rujukan')->nullable();
            
            $table->text('catatan_tindak_lanjut')->nullable();
            
            $table->timestamps();
            
            // Indexes for speed on large datasets (> 100,000 records)
            $table->index('student_class_history_id', 'idx_referral_student_class_history_id');
            $table->index(['pemeriksaan_type', 'pemeriksaan_id'], 'idx_referral_pemeriksaan_morph');
            $table->index('jenis_pemeriksaan', 'idx_referral_jenis_pemeriksaan');
            $table->index('status_rujukan', 'idx_referral_status_rujukan');
            $table->index('tanggal_pemeriksaan', 'idx_referral_tanggal_pemeriksaan');
            $table->index('tanggal_rujukan', 'idx_referral_tanggal_rujukan');
            
            // Ensure unique referral per checkup polymorphic pair
            $table->unique(['pemeriksaan_type', 'pemeriksaan_id'], 'uk_referral_pemeriksaan_morph');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
