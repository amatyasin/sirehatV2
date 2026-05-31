<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class PemeriksaanMata extends Model
{
    protected $fillable = [

        'student_class_history_id',

        'tanggal_pemeriksaan',

        'visus_kanan',

        'visus_kiri',

        'pakai_kacamata',

        'buta_warna',

        'mata_merah',

        'mata_berair',

        'nyeri_mata',

        'gatal_mata',

        'mata_bengkak',

        'mata_belekan',

        'dirujuk_ke_fasyankes',

        'keterangan_rujukan',

    ];

    protected $casts = [

        'tanggal_pemeriksaan' => 'date',

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
}
