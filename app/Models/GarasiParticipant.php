<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarasiParticipant extends Model
{
    protected $fillable = [
        'garasi_activity_id',
        'child_id',
        'orang_tua_id',
        'status',
        'attendance',
        'mother_accompanied',
        'mother_accompanied_brushing',
        'toothbrushing_practice',
        'brushing_frequency',
        'use_toothpaste',
        'brushing_before_bed',
        'follow_up_scheduled_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'attendance' => 'boolean',
        'mother_accompanied' => 'boolean',
        'mother_accompanied_brushing' => 'boolean',
        'brushing_before_bed' => 'boolean',
        'follow_up_scheduled_date' => 'date',
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

    public function brushingPractice()
    {
        return $this->hasOne(GarasiBrushingPractice::class, 'garasi_participant_id');
    }

    public function screening()
    {
        return $this->hasOne(GarasiScreening::class, 'garasi_participant_id');
    }

    public function dentalIndex()
    {
        return $this->hasOne(GarasiDentalIndex::class, 'garasi_participant_id');
    }

    public function dentalFindings()
    {
        return $this->hasMany(GarasiDentalFinding::class, 'garasi_participant_id');
    }

    public function education()
    {
        return $this->hasOne(GarasiEducation::class, 'garasi_participant_id');
    }

    public function treatment()
    {
        return $this->hasOne(GarasiTreatment::class, 'garasi_participant_id');
    }

    public function referral()
    {
        return $this->hasOne(GarasiReferral::class, 'garasi_participant_id');
    }

    public function followUps()
    {
        return $this->hasMany(GarasiFollowUp::class, 'garasi_participant_id');
    }

    /**
     * Compute current overall status dynamically or helper
     */
    public function computeStatus(): string
    {
        if (!$this->attendance) {
            return 'absent';
        }

        if ($this->referral && $this->referral->referral_needed) {
            if ($this->referral->status === 'completed') {
                return $this->follow_up_scheduled_date ? 'follow_up' : 'completed';
            }
            return 'referred';
        }

        if ($this->screening) {
            return $this->follow_up_scheduled_date ? 'follow_up' : 'completed';
        }

        return 'pending';
    }
}
