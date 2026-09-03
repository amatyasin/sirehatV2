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

    protected static function booted(): void
    {
        static::saved(function (self $model) {
            if ($model->aktif) {
                // Nonaktifkan history lain milik siswa yang sama
                static::where('student_id', $model->student_id)
                    ->where('id', '!=', $model->id)
                    ->where('aktif', true)
                    ->update(['aktif' => false]);

                // Transfer pemeriksaan & rujukan dari history non-aktif milik siswa ke history aktif saat ini
                $inactiveIds = static::where('student_id', $model->student_id)
                    ->where('id', '!=', $model->id)
                    ->pluck('id');

                if ($inactiveIds->isNotEmpty()) {
                    if (! $model->pemeriksaanUmum()->exists()) {
                        $examId = PemeriksaanUmum::whereIn('student_class_history_id', $inactiveIds)
                            ->latest('id')
                            ->value('id');
                        if ($examId) {
                            PemeriksaanUmum::where('id', $examId)->update(['student_class_history_id' => $model->id]);
                        }
                    }

                    if (! $model->pemeriksaanGizi()->exists()) {
                        $examId = PemeriksaanGizi::whereIn('student_class_history_id', $inactiveIds)
                            ->latest('id')
                            ->value('id');
                        if ($examId) {
                            PemeriksaanGizi::where('id', $examId)->update(['student_class_history_id' => $model->id]);
                        }
                    }

                    if (! $model->pemeriksaanGigi()->exists()) {
                        $examId = PemeriksaanGigi::whereIn('student_class_history_id', $inactiveIds)
                            ->latest('id')
                            ->value('id');
                        if ($examId) {
                            PemeriksaanGigi::where('id', $examId)->update(['student_class_history_id' => $model->id]);
                        }
                    }

                    if (! $model->pemeriksaanMata()->exists()) {
                        $examId = PemeriksaanMata::whereIn('student_class_history_id', $inactiveIds)
                            ->latest('id')
                            ->value('id');
                        if ($examId) {
                            PemeriksaanMata::where('id', $examId)->update(['student_class_history_id' => $model->id]);
                        }
                    }

                    if (! $model->pemeriksaanTelinga()->exists()) {
                        $examId = PemeriksaanTelinga::whereIn('student_class_history_id', $inactiveIds)
                            ->latest('id')
                            ->value('id');
                        if ($examId) {
                            PemeriksaanTelinga::where('id', $examId)->update(['student_class_history_id' => $model->id]);
                        }
                    }

                    Referral::whereIn('student_class_history_id', $inactiveIds)
                        ->update(['student_class_history_id' => $model->id]);
                }
            }
        });
    }
}

