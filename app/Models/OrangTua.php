<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    protected $table = 'orang_tuas';

    protected $fillable = [
        'instansi_id',
        'posyandu_id',
        'nama_lengkap',
        'nik',
        'tanggal_lahir',
        'no_wa',
        'alamat',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function instansi()
    {
        return $this->belongsTo(
            Instansi::class
        );
    }

    public function posyandu()
    {
        return $this->belongsTo(
            Posyandu::class
        );
    }

    public function children()
    {
        return $this->hasMany(
            Child::class
        );
    }
}
