<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_telingas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_class_history_id')
                ->constrained('student_class_histories')
                ->cascadeOnDelete();
            $table->date('tanggal_pemeriksaan');
            
            $table->string('telinga_luar_kanan')->default('Normal');
            $table->string('telinga_luar_kiri')->default('Normal');
            
            $table->enum('gangguan_pendengaran_kanan', ['Y', 'N'])->default('N');
            $table->enum('gangguan_pendengaran_kiri', ['Y', 'N'])->default('N');
            
            $table->enum('serumen_kanan', ['Y', 'N'])->default('N');
            $table->enum('serumen_kiri', ['Y', 'N'])->default('N');
            
            $table->enum('dirujuk_ke_fasyankes', ['Y', 'N'])->default('N');
            $table->text('keterangan_rujukan')->nullable();
            
            $table->timestamps();
            
            // Unique check per history for consistency
            $table->unique('student_class_history_id', 'uk_pemeriksaan_telinga_student_class_history');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_telingas');
    }
};
