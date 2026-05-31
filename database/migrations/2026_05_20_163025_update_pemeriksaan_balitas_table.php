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
            'pemeriksaan_balitas',
            function (Blueprint $table) {

                /*
                |--------------------------------------------------------------------------
                | HAPUS FIELD LAMA
                |--------------------------------------------------------------------------
                */

                $table->dropColumn([

                    'lingkar_kepala',
                    'lingkar_lengan',

                    'status_bb_u',
                    'status_bb_tb',

                    'stunting',

                    'asi_eksklusif',
                    'imunisasi_lengkap',

                    'perkembangan',

                    'hb0',
                    'bcg',

                    'polio',
                    'dpt',

                    'campak',
                    'mr',

                    'motorik',
                    'bicara',
                    'sosial',

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

                /*
                |--------------------------------------------------------------------------
                | ANTROPOMETRI
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'status_stunting'
                )
                    ->nullable()
                    ->after('status_tb_u');

                /*
                |--------------------------------------------------------------------------
                | DEMOGRAFI & RISIKO
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'disabilitas',
                    ['Y', 'N']
                )
                    ->default('N')
                    ->after('status_lingkar_kepala');

                $table->enum(
                    'riwayat_kencing_manis',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->enum(
                    'makan_pagi_sudah_banyak',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->enum(
                    'makan_banyak_makanan_manis',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->enum(
                    'mengalami_penurunan_berat_badan',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->enum(
                    'riwayat_diabetes_orangtua',
                    ['Y', 'N']
                )
                    ->default('N');

                /*
                |--------------------------------------------------------------------------
                | IMUNISASI
                |--------------------------------------------------------------------------
                */

                $imunisasiFields = [

                    'imunisasi_hepatitis_b',

                    'imunisasi_bcg_bulan_1',

                    'imunisasi_polio_dosis_1',
                    'imunisasi_polio_dosis_2',
                    'imunisasi_polio_dosis_3',
                    'imunisasi_polio_dosis_4',

                    'imunisasi_dpt_hb_hib_dosis_1',
                    'imunisasi_dpt_hb_hib_dosis_2',
                    'imunisasi_dpt_hb_hib_dosis_3',
                    'imunisasi_dpt_hb_hib_dosis_4',

                    'imunisasi_pcv_dosis_1',
                    'imunisasi_pcv_dosis_2',

                    'imunisasi_rotavirus_dosis_1',
                    'imunisasi_rotavirus_dosis_2',
                    'imunisasi_rotavirus_dosis_3',

                    'imunisasi_campak_rubella_dosis_1',
                    'imunisasi_campak_rubella_dosis_2',

                ];

                foreach (
                    $imunisasiFields as $field
                ) {

                    $table->enum(
                        $field,
                        ['Y', 'N']
                    )
                        ->default('N');

                }

                /*
                |--------------------------------------------------------------------------
                | TUMBUH KEMBANG
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'indikasi_gpph',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->string(
                    'hasil_gpph'
                )
                    ->nullable();

                $table->enum(
                    'indikasi_kmpe',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->string(
                    'hasil_kmpe'
                )
                    ->nullable();

                $table->string(
                    'hasil_kpsp'
                )
                    ->nullable();

                $table->string(
                    'hasil_perilaku'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | PEMERIKSAAN BALITA & APRAS
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'hasil_tes_daya_dengar'
                )
                    ->nullable();

                $table->string(
                    'hasil_pemeriksaan_tes_daya_lihat'
                )
                    ->nullable();

                $table->string(
                    'pemeriksaan_mata'
                )
                    ->nullable();

                $table->string(
                    'serumen_impaksi'
                )
                    ->nullable();

                $table->string(
                    'infeksi_telinga'
                )
                    ->nullable();

                $table->string(
                    'jumlah_gigi_karies'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | TB
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'tb_batuk'
                )
                    ->nullable();

                $table->enum(
                    'tb_bb_turun',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->enum(
                    'tb_demam',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->enum(
                    'tb_lesu',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->enum(
                    'tb_kelenjar',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->enum(
                    'tb_rontgen',
                    ['Y', 'N']
                )
                    ->default('N');

                $table->string(
                    'tb_kontak'
                )
                    ->nullable();

                $table->string(
                    'tb_metode'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | PENYAKIT TROPIS
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'hasil_frambusia'
                )
                    ->nullable();

                $table->string(
                    'hasil_kusta'
                )
                    ->nullable();

                $table->string(
                    'hasil_skabies'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | RUJUKAN
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'catatan'
                )
                    ->nullable()
                    ->after(
                        'keterangan_rujukan'
                    );

                /*
                |--------------------------------------------------------------------------
                | INDEX
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'tanggal_pemeriksaan'
                );

                $table->index(
                    'status_stunting'
                );

                $table->index(
                    'dirujuk_ke_fasyankes'
                );

            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};