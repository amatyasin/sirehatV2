<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $fillable = [

        'school_id',

        'nama_kelas',

        'urutan',

    ];

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function studentClassHistories()
    {
        return $this->hasMany(
            StudentClassHistory::class
        );
    }
}
