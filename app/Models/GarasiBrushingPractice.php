<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiBrushingPractice extends Model
{
    protected $fillable = [
        'garasi_participant_id',
        'together_brushing',
        'practice_ability',
        'brushing_frequency',
        'brushing_before_bed',
        'mother_accompaniment_frequency',
        'use_toothpaste',
        'toothpaste_brand',
        'tool_used',
        'tool_other_description',
    ];

    protected $casts = [
        'together_brushing' => 'boolean',
        'brushing_before_bed' => 'boolean',
    ];

    public function participant()
    {
        return $this->belongsTo(GarasiParticipant::class, 'garasi_participant_id');
    }
}
