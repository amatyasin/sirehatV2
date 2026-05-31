<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_matas', function (
            Blueprint $table
        ) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELASI SISWA
            |--------------------------------------------------------------------------
            */

            $table->foreignId(
                'student_class_history_id'
            )->constrained()->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | TANGGAL
            |--------------------------------------------------------------------------
            */

            $table->date(
                'tanggal_pemeriksaan'
            );

            /*
            |--------------------------------------------------------------------------
            | PEMERIKSAAN PENGLIHATAN
            |--------------------------------------------------------------------------
            */

            $table->string(
                'visus_kanan'
            )->nullable();

            $table->string(
                'visus_kiri'
            )->nullable();

            $table->enum(
                'pakai_kacamata',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'buta_warna',
                ['Y', 'N']
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | KELUHAN MATA
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'mata_merah',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'mata_berair',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'nyeri_mata',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'gatal_mata',
                ['Y', 'N']
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | KEBIASAAN
            |--------------------------------------------------------------------------
            */

            $table->string(
                'screen_time'
            )->nullable();

            $table->string(
                'membaca_sambil_tiduran'
            )->nullable();

            $table->string(
                'pencahayaan_belajar'
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | RUJUKAN
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'dirujuk_ke_fasyankes',
                ['Y', 'N']
            )->nullable();

            $table->text(
                'keterangan_rujukan'
            )->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'pemeriksaan_matas'
        );
    }
};