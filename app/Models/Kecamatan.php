<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    protected $table =
        'kecamatans';

    protected $fillable = [

        'nama_kecamatan',

    ];

    public function kelurahans(): HasMany
    {
        return $this->hasMany(
            Kelurahan::class
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(
            User::class
        );
    }

    public function getJumlahKelurahanAttribute(): int
    {
        return $this->kelurahans()->count();
    }

    /**
     * Hitung jumlah puskesmas unik yang menaungi kelurahan di kecamatan ini.
     */
    public function getJumlahPuskesmasAttribute(): int
    {
        return $this->kelurahans()
            ->whereNotNull('instansi_id')
            ->distinct('instansi_id')
            ->count('instansi_id');
    }
}
