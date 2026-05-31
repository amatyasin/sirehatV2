<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'pemeriksaan_balitas',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | RELASI ANAK
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'child_id'
                )

                    ->constrained()

                    ->cascadeOnDelete();

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
                    'lingkar_kepala',
                    5,
                    2
                )->nullable();

                $table->decimal(
                    'lingkar_lengan',
                    5,
                    2
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | STATUS GIZI
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'status_bb_u'
                )->nullable();

                $table->string(
                    'status_tb_u'
                )->nullable();

                $table->string(
                    'status_bb_tb'
                )->nullable();

                $table->string(
                    'status_imt_u'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | STUNTING
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'stunting',
                    ['Y', 'N']
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | ASI & IMUNISASI
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'asi_eksklusif',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'imunisasi_lengkap',
                    ['Y', 'N']
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | PERKEMBANGAN
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'perkembangan'
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

            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'pemeriksaan_balitas'
        );
    }
};