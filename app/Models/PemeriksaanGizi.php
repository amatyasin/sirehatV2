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
}
