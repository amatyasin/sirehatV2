<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelurahan extends Model
{
    protected $table =
        'kelurahans';

    protected $fillable = [

        'kecamatan_id',

        'instansi_id',

        'nama_kelurahan',

        'aktif',

    ];

    protected $casts = [

        'aktif' => 'boolean',

    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(
            Kecamatan::class
        );
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(
            Instansi::class
        );
    }

    public function posyandus(): HasMany
    {
        return $this->hasMany(
            Posyandu::class
        );
    }

    public function getNamaKecamatanAttribute(): ?string
    {
        return $this->kecamatan
            ?->nama_kecamatan;
    }

    public function getNamaInstansiAttribute(): ?string
    {
        return $this->instansi
            ?->nama_instansi;
    }
}
