<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PemeriksaanTelinga extends Model
{
    protected $fillable = [
        'student_class_history_id',
        'tanggal_pemeriksaan',
        'telinga_luar_kanan',
        'telinga_luar_kiri',
        'gangguan_pendengaran_kanan',
        'gangguan_pendengaran_kiri',
        'serumen_kanan',
        'serumen_kiri',
        'dirujuk_ke_fasyankes',
        'keterangan_rujukan'
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
    ];

    public function studentClassHistory(): BelongsTo
    {
        return $this->belongsTo(StudentClassHistory::class);
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

    public function referral(): MorphOne
    {
        return $this->morphOne(Referral::class, 'pemeriksaan');
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
