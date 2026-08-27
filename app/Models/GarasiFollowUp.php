<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiFollowUp extends Model
{
    protected $fillable = [
        'garasi_participant_id',
        'previous_participant_id',
        'follow_up_date',
        'behavior_change',
        'behavior_change_description',
        'mother_accompaniment_change',
        'dental_condition_change',
        'referral_status',
        'notes',
        'evaluator_id',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function participant()
    {
        return $this->belongsTo(GarasiParticipant::class, 'garasi_participant_id');
    }

    public function previousParticipant()
    {
        return $this->belongsTo(GarasiParticipant::class, 'previous_participant_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
