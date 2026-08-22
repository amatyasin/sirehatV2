<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiActivity extends Model
{
    protected $fillable = [
        'posyandu_id',
        'instansi_id',
        'activity_date',
        'location',
        'officer_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function participants()
    {
        return $this->hasMany(GarasiParticipant::class, 'garasi_activity_id');
    }
}
