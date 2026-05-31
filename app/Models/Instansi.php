<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instansi extends Model
{
    protected $table =
        'instansis';

    protected $fillable = [

        'kecamatan_id',

        'nama_instansi',

        'alamat',

        'telepon',

        'status',

    ];

    protected $casts = [

        'status' => 'boolean',

    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(
            Kecamatan::class
        );
    }

    public function kelurahans(): HasMany
    {
        return $this->hasMany(
            Kelurahan::class
        );
    }

    public function schools(): HasMany
    {
        return $this->hasMany(
            School::class
        );
    }

    public function posyandus(): HasMany
    {
        return $this->hasMany(
            Posyandu::class
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(
            User::class
        );
    }

    public function getNamaKecamatanAttribute(): ?string
    {
        return $this->kecamatan
            ?->nama_kecamatan;
    }

    public function getJumlahSekolahAttribute(): int
    {
        return $this->schools()
            ->count();
    }

    public function getJumlahPosyanduAttribute(): int
    {
        return $this->posyandus()
            ->count();
    }

    public function getJumlahKelurahanAttribute(): int
    {
        return $this->kelurahans()
            ->count();
    }

    public function getJumlahUsersAttribute(): int
    {
        return $this->users()
            ->count();
    }
}
