<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentClassHistory extends Model
{
    protected $fillable = [

        'student_id',
        'school_id',
        'school_class_id',
        'academic_year_id',
        'semester',
        'aktif',

    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            Student::class
        );
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(
            SchoolClass::class
        );
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(
            AcademicYear::class
        );
    }

    // HasMany (existing)
    public function pemeriksaanUmums(): HasMany
    {
        return $this->hasMany(
            PemeriksaanUmum::class
        );
    }

    public function pemeriksaanGigis(): HasMany
    {
        return $this->hasMany(
            PemeriksaanGigi::class
        );
    }

    public function pemeriksaanGizis(): HasMany
    {
        return $this->hasMany(
            PemeriksaanGizi::class
        );
    }

    public function pemeriksaanMatas(): HasMany
    {
        return $this->hasMany(
            PemeriksaanMata::class
        );
    }

    // HasOne (singular - for unique constraint tables)
    public function pemeriksaanUmum(): HasOne
    {
        return $this->hasOne(PemeriksaanUmum::class, 'student_class_history_id');
    }

    public function pemeriksaanGizi(): HasOne
    {
        return $this->hasOne(PemeriksaanGizi::class, 'student_class_history_id');
    }

    public function pemeriksaanGigi(): HasOne
    {
        return $this->hasOne(PemeriksaanGigi::class, 'student_class_history_id');
    }

    public function pemeriksaanMata(): HasOne
    {
        return $this->hasOne(PemeriksaanMata::class, 'student_class_history_id');
    }

    public function pemeriksaanTelinga(): HasOne
    {
        return $this->hasOne(PemeriksaanTelinga::class, 'student_class_history_id');
    }
}
