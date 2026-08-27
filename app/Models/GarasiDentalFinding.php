<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiDentalFinding extends Model
{
    protected $fillable = [
        'garasi_participant_id',
        'tooth_number',
        'condition',
        'notes',
    ];

    public function participant()
    {
        return $this->belongsTo(GarasiParticipant::class, 'garasi_participant_id');
    }
}
