<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
