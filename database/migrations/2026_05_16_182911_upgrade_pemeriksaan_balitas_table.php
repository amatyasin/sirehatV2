<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'pemeriksaan_balitas',

            function (Blueprint $table) {

                /*
                |--------------------------------------------------------------------------
                | ANTROPOMETRI TAMBAHAN
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'imt',
                    5,
                    2
                )->nullable();

                $table->string(
                    'status_lingkar_kepala'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | RISIKO DIABETES
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'riwayat_diabetes',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'sering_haus',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'sering_lapar',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'penurunan_berat_badan',
                    ['Y', 'N']
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | IMUNISASI DETAIL
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'polio1',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'polio2',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'polio3',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'polio4',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'dpt1',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'dpt2',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'dpt3',
                    ['Y', 'N']
                )->nullable();

            }

        );
    }

    public function down(): void
    {
        Schema::table(
            'pemeriksaan_balitas',

            function (Blueprint $table) {

                $table->dropColumn([

                    'imt',
                    'status_lingkar_kepala',

                    'riwayat_diabetes',
                    'sering_haus',
                    'sering_lapar',
                    'penurunan_berat_badan',

                    'polio1',
                    'polio2',
                    'polio3',
                    'polio4',

                    'dpt1',
                    'dpt2',
                    'dpt3',

                ]);

            }

        );
    }
};