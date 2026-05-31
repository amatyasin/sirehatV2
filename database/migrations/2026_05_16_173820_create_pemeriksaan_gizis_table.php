<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_gizis', function (
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
            | ANTROPOMETRI
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'berat_badan',
                5,
                2
            )->nullable();

            $table->decimal(
                'tinggi_badan',
                5,
                2
            )->nullable();

            $table->decimal(
                'lingkar_lengan',
                5,
                2
            )->nullable();

            $table->decimal(
                'lingkar_perut',
                5,
                2
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | PERHITUNGAN GIZI
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'imt',
                5,
                2
            )->nullable();

            $table->string(
                'status_gizi'
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | POLA MAKAN
            |--------------------------------------------------------------------------
            */

            $table->string(
                'sarapan'
            )->nullable();

            $table->string(
                'konsumsi_sayur'
            )->nullable();

            $table->string(
                'minuman_manis'
            )->nullable();

            $table->string(
                'fast_food'
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | AKTIVITAS FISIK
            |--------------------------------------------------------------------------
            */

            $table->string(
                'aktivitas_fisik'
            )->nullable();

            $table->string(
                'screen_time'
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
            'pemeriksaan_gizis'
        );
    }
};