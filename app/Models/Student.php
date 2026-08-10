<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'instansi_id',
        'school_id',
        'nama_lengkap',
        'nik',
        'nisn',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_orang_tua',
        'nik_orang_tua',
        'no_hp_orang_tua',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    public function instansi()
    {
        return $this->belongsTo(
            Instansi::class
        );
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classHistories()
    {
        return $this->hasMany(
            StudentClassHistory::class
        );
    }

    public function activeClassHistory()
    {
        return $this->hasOne(
            StudentClassHistory::class
        )->where('aktif', true);
    }

    public function pemeriksaanGigis()
    {
        return $this->hasManyThrough(
            PemeriksaanGigi::class,
            StudentClassHistory::class,
            'student_id',
            'student_class_history_id',
            'id',
            'id'
        );
    }

    public function pemeriksaanMatas()
    {
        return $this->hasManyThrough(
            PemeriksaanMata::class,
            StudentClassHistory::class,
            'student_id',
            'student_class_history_id',
            'id',
            'id'
        );
    }

    public function pemeriksaanUmums()
    {
        return $this->hasManyThrough(
            PemeriksaanUmum::class,
            StudentClassHistory::class,
            'student_id',
            'student_class_history_id',
            'id',
            'id'
        );
    }

    public function pemeriksaanGizis()
    {
        return $this->hasManyThrough(
            PemeriksaanGizi::class,
            StudentClassHistory::class,
            'student_id',
            'student_class_history_id',
            'id',
            'id'
        );
    }
}

