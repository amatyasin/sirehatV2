<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_gigis', function (Blueprint $table) {

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
            | RONGGA MULUT
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'sariawan',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'luka_sudut_mulut',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'lidah_kotor',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'radang_gusi',
                ['Y', 'N']
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | GIGI
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'gigi_berlubang',
                ['Y', 'N']
            )->nullable();

            $table->integer(
                'jumlah_gigi_berlubang'
            )->nullable();

            $table->enum(
                'karang_gigi',
                ['Y', 'N']
            )->nullable();

            $table->string(
                'susunan_gigi'
            )->nullable();

            $table->enum(
                'gigi_hilang',
                ['Y', 'N']
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | KEBIASAAN
            |--------------------------------------------------------------------------
            */

            $table->string(
                'sikat_gigi'
            )->nullable();

            $table->string(
                'pakai_pasta_gigi'
            )->nullable();

            $table->string(
                'sikat_gigi_sebelum_tidur'
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
            'pemeriksaan_gigis'
        );
    }
};