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
        Schema::table(
            'pemeriksaan_gigis',
            function (Blueprint $table) {

                $table->enum(
                    'celah_bibir_langit',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'tanggal_pemeriksaan'
                );

                $table->enum(
                    'luka_lain_di_mulut',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'lidah_kotor'
                );

                $table->enum(
                    'gusi_berdarah',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'jumlah_gigi_berlubang'
                );

                $table->enum(
                    'gusi_bengkak',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'gusi_berdarah'
                );

                $table->enum(
                    'gigi_kotor_plak',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'gusi_bengkak'
                );

                $table->enum(
                    'susunan_gigi_tidak_teratur',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'karang_gigi'
                );

                $table->enum(
                    'penglihatan_loupe',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'susunan_gigi_tidak_teratur'
                );

                $table->enum(
                    'pendengaran',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'penglihatan_loupe'
                );

                $table->enum(
                    'kursi_roda',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'pendengaran'
                );

                $table->enum(
                    'tongkat_kruk',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'kursi_roda'
                );

                $table->enum(
                    'kaki_tangan_mata_protese',
                    ['Y', 'N']
                )

                ->nullable()

                ->after(
                    'tongkat_kruk'
                );

            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'pemeriksaan_gigis',
            function (Blueprint $table) {

                $table->dropColumn([

                    'celah_bibir_langit',

                    'luka_lain_di_mulut',

                    'gusi_berdarah',

                    'gusi_bengkak',

                    'gigi_kotor_plak',

                    'susunan_gigi_tidak_teratur',

                    'penglihatan_loupe',

                    'pendengaran',

                    'kursi_roda',

                    'tongkat_kruk',

                    'kaki_tangan_mata_protese',

                ]);

            }
        );
    }
};