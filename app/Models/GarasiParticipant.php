<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiParticipant extends Model
{
    protected $fillable = [
        'garasi_activity_id',
        'child_id',
        'orang_tua_id',
        'attendance',
        'mother_accompanied',
        'toothbrushing_practice',
        'brushing_frequency',
        'use_toothpaste',
        'brushing_before_bed',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'attendance' => 'boolean',
        'mother_accompanied' => 'boolean',
        'brushing_before_bed' => 'boolean',
    ];

    public function activity()
    {
        return $this->belongsTo(GarasiActivity::class, 'garasi_activity_id');
    }

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'orang_tua_id');
    }

    public function screening()
    {
        return $this->hasOne(GarasiScreening::class, 'garasi_participant_id');
    }

    public function education()
    {
        return $this->hasOne(GarasiEducation::class, 'garasi_participant_id');
    }

    public function referral()
    {
        return $this->hasOne(GarasiReferral::class, 'garasi_participant_id');
    }
}
