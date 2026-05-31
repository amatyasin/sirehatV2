<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_umums', function (Blueprint $table) {

            $table->id();

            $table->foreignId(
                'student_class_history_id'
            )
                ->constrained()
                ->cascadeOnDelete();

            $table->enum(
                'jenis_kelamin',
                ['L', 'P']
            )->nullable();

            $table->enum(
                'sudah_menstruasi',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'mengalami_keputihan',
                ['Y', 'N']
            )->nullable();

            $table->text('alamat')->nullable();

            $table->date(
                'tanggal_pemeriksaan'
            )->nullable();

            $table->string(
                'tekanan_darah'
            )->nullable();

            $table->string(
                'denyut_nadi'
            )->nullable();

            $table->string(
                'frekuensi_pernapasan'
            )->nullable();

            $table->string(
                'suhu'
            )->nullable();

            $table->string(
                'bising_jantung'
            )->nullable();

            $table->string(
                'bising_paru'
            )->nullable();

            $table->string(
                'keadaan_rambut'
            )->nullable();

            $table->enum(
                'bercak_keputihan',
                ['Y', 'N']
            )->nullable();

            $table->string(
                'bercak_putih_mati_rasa'
            )->nullable();

            $table->enum(
                'kulit_bersisik',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'kulit_ada_memar',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'kulit_ada_luka_sayatan',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'kulit_ada_luka_koreng',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'luka_koreng_sukar_sembuh',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'bekas_suntikan',
                ['Y', 'N']
            )->nullable();

            $table->string(
                'risiko_merokok'
            )->nullable();

            $table->enum(
                'merokok_setahun',
                ['Y', 'N']
            )->nullable();

            $table->enum(
                'jenis_rokok',
                [
                    'konvensional',
                    'elektrik',
                    'keduanya'
                ]
            )->nullable();

            $table->integer(
                'jumlah_rokok'
            )->nullable();

            $table->integer(
                'lama_merokok'
            )->nullable();

            $table->string(
                'telinga_luar'
            )->nullable();

            $table->string(
                'sarapan'
            )->nullable();

            $table->string(
                'kondisi_kuku'
            )->nullable();

            $table->enum(
                'dirujuk_ke_fasyankes',
                ['Y', 'N']
            )->nullable();

            $table->string(
                'keterangan_rujukan'
            )->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'pemeriksaan_umums'
        );
    }
};