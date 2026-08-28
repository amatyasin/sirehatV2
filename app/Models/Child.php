<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $fillable = [
        'instansi_id',
        'posyandu_id',
        'orang_tua_id',
        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'aktif',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'aktif' => 'boolean',
    ];

    protected $appends = [
        'umur_bulan',
        'umur_tahun',
    ];

    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class);
    }

    public function pemeriksaanBalitas()
    {
        return $this->hasMany(PemeriksaanBalita::class);
    }

    public function posyanduMonthlyParticipants()
    {
        return $this->hasMany(PosyanduMonthlyParticipant::class, 'child_id');
    }

    public function getUmurBulanAttribute(): ?int
    {
        if (! $this->tanggal_lahir) {
            return null;
        }

        return $this->tanggal_lahir->diffInMonths(now());
    }

    public function getUmurTahunAttribute(): ?int
    {
        if (! $this->tanggal_lahir) {
            return null;
        }

        return $this->tanggal_lahir->age;
    }

    public function scopeAktif(
        $query
    ) {

        return $query->where(
            'aktif',
            true
        );
    }

    public function isBalita(): bool
    {
        return $this->umur_bulan <= 59;
    }

    public function isApras(): bool
    {
        return $this->umur_bulan >= 60;
    }
}
