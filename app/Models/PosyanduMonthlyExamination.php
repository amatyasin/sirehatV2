<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosyanduMonthlyExamination extends Model
{
    protected $table = 'posyandu_monthly_examinations';

    protected $fillable = [
        'posyandu_id',
        'examination_date',
        'month',
        'year',
        'location',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'examination_date' => 'date',
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function posyandu(): BelongsTo
    {
        return $this->belongsTo(Posyandu::class, 'posyandu_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(PosyanduMonthlyParticipant::class, 'posyandu_monthly_examination_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
