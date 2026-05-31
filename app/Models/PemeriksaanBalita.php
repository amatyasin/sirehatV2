<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanBalita extends Model
{
    protected $table = 'pemeriksaan_balitas';

    protected $fillable = [

        'child_id',
        'posyandu_id',

        'tanggal_pemeriksaan',
        'bulan_pemeriksaan',
        'tahun_pemeriksaan',

        'umur_bulan',

        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'lingkar_lengan',

        'imt',

        'zscore_bb_u',
        'zscore_tb_u',
        'zscore_bb_tb',
        'zscore_imt_u',
        'zscore_lingkar_kepala',

        'status_bb_u',
        'status_tb_u',
        'status_bb_tb',
        'status_imt_u',
        'status_lingkar_kepala',
        'status_stunting',

        'disabilitas',

        'riwayat_diabetes_orangtua',
        'makan_banyak_makanan_manis',
        'makan_pagi_sudah_banyak',
        'mengalami_penurunan_berat_badan',
        'riwayat_kencing_manis',

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

        'indikasi_gpph',
        'hasil_gpph',

        'indikasi_kmpe',
        'hasil_kmpe',

        'hasil_kpsp',
        'hasil_perilaku',

        'hasil_tes_daya_dengar',
        'hasil_pemeriksaan_tes_daya_lihat',

        'pemeriksaan_mata',
        'serumen_impaksi',
        'infeksi_telinga',

        'jumlah_gigi_karies',

        'tb_batuk',
        'tb_bb_turun',
        'tb_demam',
        'tb_lesu',
        'tb_kelenjar',
        'tb_rontgen',
        'tb_kontak',
        'tb_metode',

        'hasil_pemeriksaan_tb',

        'hasil_frambusia',
        'hasil_kusta',
        'hasil_skabies',

        'dirujuk_ke_fasyankes',
        'keterangan_rujukan',

        'catatan',
    ];

    protected $casts = [

        'tanggal_pemeriksaan' => 'date',

        'umur_bulan' => 'decimal:2',

        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',

        'lingkar_kepala' => 'decimal:2',
        'lingkar_lengan' => 'decimal:2',

        'imt' => 'decimal:2',

        'zscore_bb_u' => 'decimal:2',
        'zscore_tb_u' => 'decimal:2',
        'zscore_bb_tb' => 'decimal:2',
        'zscore_imt_u' => 'decimal:2',
        'zscore_lingkar_kepala' => 'decimal:2',
    ];

    protected $appends = [

        'umur_saat_pemeriksaan',

    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(
            Child::class
        );
    }

    public function posyandu(): BelongsTo
    {
        return $this->belongsTo(
            Posyandu::class
        );
    }

    public function getUmurSaatPemeriksaanAttribute(): ?string
    {
        if (
            ! $this->child ||
            ! $this->child->tanggal_lahir ||
            ! $this->tanggal_pemeriksaan
        ) {
            return null;
        }

        $lahir = Carbon::parse(
            $this->child->tanggal_lahir
        );

        $periksa = Carbon::parse(
            $this->tanggal_pemeriksaan
        );

        $diff = $lahir->diff($periksa);

        return $diff->y.' Tahun '
            .$diff->m.' Bulan';
    }

    public function isStunting(): bool
    {
        return in_array(
            strtolower(
                $this->status_stunting ?? ''
            ),

            [

                'pendek',
                'sangat pendek',
                'stunting',
                'severely stunted',

            ]

        );
    }

    public function isSevereStunting(): bool
    {
        return in_array(

            strtolower(
                $this->status_stunting ?? ''
            ),

            [

                'sangat pendek',
                'severely stunted',

            ]

        );
    }

    public function isObesitas(): bool
    {
        return str_contains(

            strtolower(
                $this->status_imt_u ?? ''
            ),

            'obes'

        );
    }

    public function isKurus(): bool
    {
        return str_contains(

            strtolower(
                $this->status_imt_u ?? ''
            ),

            'kurus'

        );
    }

    public function isDirujuk(): bool
    {
        return $this->dirujuk_ke_fasyankes === 'Y';
    }

    public function scopeDirujuk(
        Builder $query
    ): Builder {

        return $query->where(
            'dirujuk_ke_fasyankes',
            'Y'
        );
    }

    public function scopeStunting(
        Builder $query
    ): Builder {

        return $query->whereIn(

            'status_stunting',

            [

                'Pendek',
                'Sangat Pendek',

            ]

        );
    }

    public function scopeObesitas(
        Builder $query
    ): Builder {

        return $query->where(

            'status_imt_u',
            'like',
            '%Obes%'

        );
    }
}
