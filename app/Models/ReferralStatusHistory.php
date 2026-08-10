<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralStatusHistory extends Model
{
    protected $fillable = [
        'referral_id',
        'status_lama',
        'status_baru',
        'user_id',
        'catatan',
    ];

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
