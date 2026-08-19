<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class PemeriksaanUmum extends Model
{
    protected $table =
        'pemeriksaan_umums';

    protected $fillable = [

        'student_class_history_id',

        'jenis_kelamin',

        'sudah_menstruasi',

        'mengalami_keputihan',

        'alamat',

        'tanggal_pemeriksaan',

        'tekanan_darah',

        'denyut_nadi',

        'frekuensi_pernapasan',

        'suhu',

        'bising_jantung',

        'bising_paru',

        'keadaan_rambut',

        'bercak_keputihan',

        'bercak_putih_mati_rasa',

        'kulit_bersisik',

        'kulit_ada_memar',

        'kulit_ada_luka_sayatan',

        'kulit_ada_luka_koreng',

        'luka_koreng_sukar_sembuh',

        'bekas_suntikan',

        'risiko_merokok',

        'merokok_setahun',

        'jenis_rokok',

        'jumlah_rokok',

        'lama_merokok',

        'telinga_luar',

        'sarapan',

        'kondisi_kuku',

        'dirujuk_ke_fasyankes',

        'keterangan_rujukan',

    ];

    protected $casts = [

        'tanggal_pemeriksaan' => 'date',

        'jumlah_rokok' => 'integer',

        'lama_merokok' => 'integer',

    ];

    public function studentClassHistory(): BelongsTo
    {
        return $this->belongsTo(
            StudentClassHistory::class
        );
    }

    public function student(): HasOneThrough
    {
        return $this->hasOneThrough(

            Student::class,

            StudentClassHistory::class,

            'id',
            'id',

            'student_class_history_id',
            'student_id'

        );
    }

    public function school(): HasOneThrough
    {
        return $this->hasOneThrough(

            School::class,

            StudentClassHistory::class,

            'id',
            'id',

            'student_class_history_id',
            'school_id'

        );
    }

    public function schoolClass(): HasOneThrough
    {
        return $this->hasOneThrough(

            SchoolClass::class,

            StudentClassHistory::class,

            'id',
            'id',

            'student_class_history_id',
            'school_class_id'

        );
    }

    public function academicYear(): HasOneThrough
    {
        return $this->hasOneThrough(

            AcademicYear::class,

            StudentClassHistory::class,

            'id',
            'id',

            'student_class_history_id',
            'academic_year_id'

        );
    }

    protected static function booted(): void
    {
        static::saved(function ($model) {
            app(\App\Services\Referral\ReferralService::class)->syncReferral($model);
        });

        static::deleted(function ($model) {
            \App\Models\Referral::where('pemeriksaan_type', get_class($model))
                ->where('pemeriksaan_id', $model->id)
                ->delete();
        });
    }
}
