<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Posyandu extends Model
{
    protected $table =
        'posyandus';

    protected $fillable = [

        'instansi_id',

        'kelurahan_id',

        'nama_posyandu',

        'alamat',

        'penanggung_jawab',

        'no_wa',

        'rt',

        'rw',

        'kode_pos',

        'aktif',

    ];

    protected $casts = [

        'aktif' => 'boolean',

    ];

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(
            Instansi::class
        );
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(
            Kelurahan::class
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            Child::class
        );
    }

    public function monthlyExaminations(): HasMany
    {
        return $this->hasMany(
            PosyanduMonthlyExamination::class,
            'posyandu_id'
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
        return $this->kelurahan
            ?->kecamatan
            ?->nama_kecamatan;
    }

    public function getNamaKelurahanAttribute(): ?string
    {
        return $this->kelurahan
            ?->nama_kelurahan;
    }
}
