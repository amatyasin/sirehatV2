<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiTreatment extends Model
{
    protected $fillable = [
        'garasi_participant_id',
        'education',
        'observation',
        'filling',
        'extraction',
        'scaling',
        'root_canal',
        'prosthesis',
        'treatment_other',
        'treatment_other_description',
        'notes',
    ];

    protected $casts = [
        'education' => 'boolean',
        'observation' => 'boolean',
        'filling' => 'boolean',
        'extraction' => 'boolean',
        'scaling' => 'boolean',
        'root_canal' => 'boolean',
        'prosthesis' => 'boolean',
        'treatment_other' => 'boolean',
    ];

    public function participant()
    {
        return $this->belongsTo(GarasiParticipant::class, 'garasi_participant_id');
    }
}
