<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiEducation extends Model
{
    protected $table = 'garasi_educations';

    protected $fillable = [
        'garasi_participant_id',
        'brushing_education',
        'brushing_frequency_education',
        'fluoride_education',
        'child_toothbrush_selection',
        'mother_toothbrush_selection',
        'sugar_education',
        'dental_checkup_education',
        'home_care_education',
        'notes',
        'educator_id',
    ];

    protected $casts = [
        'brushing_education' => 'boolean',
        'brushing_frequency_education' => 'boolean',
        'fluoride_education' => 'boolean',
        'child_toothbrush_selection' => 'boolean',
        'mother_toothbrush_selection' => 'boolean',
        'sugar_education' => 'boolean',
        'dental_checkup_education' => 'boolean',
        'home_care_education' => 'boolean',
    ];

    public function participant()
    {
        return $this->belongsTo(GarasiParticipant::class, 'garasi_participant_id');
    }

    public function educator()
    {
        return $this->belongsTo(User::class, 'educator_id');
    }
}
