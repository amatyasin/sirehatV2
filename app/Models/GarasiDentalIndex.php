<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiDentalIndex extends Model
{
    protected $fillable = [
        'garasi_participant_id',
        'dentition_type',
        'decay_perm_D',
        'missing_perm_M',
        'filling_perm_F',
        'dmft_score',
        'decay_prim_d',
        'extracted_prim_e',
        'filled_prim_f',
        'deft_score',
    ];

    public function participant()
    {
        return $this->belongsTo(GarasiParticipant::class, 'garasi_participant_id');
    }
}
