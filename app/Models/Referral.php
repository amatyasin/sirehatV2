<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Referral extends Model
{
    protected $fillable = [
        'student_class_history_id',
        'pemeriksaan_type',
        'pemeriksaan_id',
        'jenis_pemeriksaan',
        'alasan_rujukan',
        'status_rujukan',
        'tujuan_rujukan',
        'petugas_pemeriksa',
        'tanggal_pemeriksaan',
        'tanggal_rujukan',
        'catatan_tindak_lanjut',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'tanggal_rujukan' => 'date',
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
            'id', // Local key on student_class_histories
            'id', // Local key on students
            'student_class_history_id', // Foreign key on student_class_histories
            'student_id' // Foreign key on students
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

    public function pemeriksaan(): MorphTo
    {
        return $this->morphTo();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReferralStatusHistory::class)->orderBy('created_at', 'desc');
    }
}
