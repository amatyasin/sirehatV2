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
                | IMUNISASI
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'hb0',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'bcg',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'polio',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'dpt',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'campak',
                    ['Y', 'N']
                )->nullable();

                $table->enum(
                    'mr',
                    ['Y', 'N']
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | PERKEMBANGAN
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'motorik',
                    [
                        'baik',
                        'kurang',
                    ]
                )->nullable();

                $table->enum(
                    'bicara',
                    [
                        'baik',
                        'kurang',
                    ]
                )->nullable();

                $table->enum(
                    'sosial',
                    [
                        'baik',
                        'kurang',
                    ]
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

                    'hb0',
                    'bcg',
                    'polio',
                    'dpt',
                    'campak',
                    'mr',

                    'motorik',
                    'bicara',
                    'sosial',

                ]);

            }

        );
    }
};