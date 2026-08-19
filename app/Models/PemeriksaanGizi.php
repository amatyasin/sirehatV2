<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class PemeriksaanGizi extends Model
{
    protected $fillable = [

        'student_class_history_id',
        'tanggal_pemeriksaan',
        'berat_badan',
        'tinggi_badan',
        'lingkar_lengan',
        'lingkar_perut',
        'imt',
        'status_gizi',
        'gula_darah_sewaktu',
        'status_gula',
        'tanda_klinis_anemia',
        'hemoglobin',
        'status_anemia',
        'dirujuk_ke_fasyankes',
        'keterangan_rujukan',

    ];

    protected $casts = [

        'tanggal_pemeriksaan' => 'date',
        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
        'lingkar_lengan' => 'decimal:2',
        'lingkar_perut' => 'decimal:2',
        'imt' => 'decimal:2',
        'gula_darah_sewaktu' => 'decimal:1',
        'hemoglobin' => 'decimal:2',
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

    public function getStatusRujukanAttribute(): string
    {
        return $this->dirujuk_ke_fasyankes === 'Y'
            ? 'Dirujuk'
            : 'Tidak Dirujuk';
    }

    public function getStatusAnemiaAttribute(): string
    {
        return $this->status_anemia ?? (
            $this->tanda_klinis_anemia === 'Y' ? 'Anemia' : 'Normal'
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
