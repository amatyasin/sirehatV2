<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiReferral extends Model
{
    protected $fillable = [
        'garasi_participant_id',
        'referral_date',
        'reason',
        'destination',
        'status',
        'follow_up_date',
        'follow_up_result',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'referral_date' => 'date',
        'follow_up_date' => 'date',
    ];

    public function participant()
    {
        return $this->belongsTo(GarasiParticipant::class, 'garasi_participant_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
