<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiScreening extends Model
{
    protected $fillable = [
        'garasi_participant_id',
        'toothache',
        'sensitive_teeth',
        'bleeding_gums',
        'swollen_gums',
        'bad_breath',
        'mouth_sores',
        'chewing_difficulty',
        'oral_hygiene',
        'plaque',
        'cavities',
        'broken_teeth',
        'red_gums',
        'swollen_gums_observed',
        'other_findings',
        'risk_level',
        'recommendation',
        'notes',
        'examiner_id',
    ];

    protected $casts = [
        'toothache' => 'boolean',
        'sensitive_teeth' => 'boolean',
        'bleeding_gums' => 'boolean',
        'swollen_gums' => 'boolean',
        'bad_breath' => 'boolean',
        'mouth_sores' => 'boolean',
        'chewing_difficulty' => 'boolean',
        'plaque' => 'boolean',
        'cavities' => 'boolean',
        'broken_teeth' => 'boolean',
        'red_gums' => 'boolean',
        'swollen_gums_observed' => 'boolean',
    ];

    public function participant()
    {
        return $this->belongsTo(GarasiParticipant::class, 'garasi_participant_id');
    }

    public function examiner()
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }
}
