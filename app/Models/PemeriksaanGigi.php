<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class PemeriksaanGigi extends Model
{
    protected $fillable = [

        'student_class_history_id',

        'tanggal_pemeriksaan',

        'celah_bibir_langit',

        'luka_sudut_mulut',

        'sariawan',

        'lidah_kotor',

        'luka_lain_di_mulut',

        'gigi_berlubang',

        'jumlah_gigi_berlubang',

        'gusi_berdarah',

        'gusi_bengkak',

        'gigi_kotor_plak',

        'karang_gigi',

        'susunan_gigi_tidak_teratur',

        'penglihatan_loupe',

        'pendengaran',

        'kursi_roda',

        'tongkat_kruk',

        'kaki_tangan_mata_protese',

        'dirujuk_ke_fasyankes',

        'keterangan_rujukan',

    ];

    protected $casts = [

        'tanggal_pemeriksaan' => 'date',

    ];

    public function studentClassHistory(): BelongsTo
    {
        return $this->belongsTo(
            StudentClassHistory::class,
            'student_class_history_id'
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

    public function getSemesterAttribute(): ?string
    {
        return $this
            ->studentClassHistory
            ?->semester;
    }

    public function getNamaSekolahAttribute(): ?string
    {
        return $this
            ->studentClassHistory
            ?->school
            ?->nama_sekolah;
    }

    public function getNamaKelasAttribute(): ?string
    {
        return $this
            ->studentClassHistory
            ?->schoolClass
            ?->nama_kelas;
    }

    public function getNamaSiswaAttribute(): ?string
    {
        return $this
            ->studentClassHistory
            ?->student
            ?->nama_lengkap;
    }

    public function getNisnAttribute(): ?string
    {
        return $this
            ->studentClassHistory
            ?->student
            ?->nisn;
    }
}
