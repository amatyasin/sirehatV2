<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [

        'instansi_id',

        'kecamatan_id',

        'kelurahan_id',

        'nama_sekolah',

        'npsn',

        'alamat',

    ];

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(
            Instansi::class
        );
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(
            Kecamatan::class
        );
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(
            Kelurahan::class
        );
    }

    public function schoolClasses()
    {
        return $this->hasMany(
            SchoolClass::class
        );
    }

    public function students(): HasMany
    {
        return $this->hasMany(
            Student::class
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(
            User::class
        );
    }
}
