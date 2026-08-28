<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosyanduMonthlyParticipant extends Model
{
    protected $table = 'posyandu_monthly_participants';

    protected $fillable = [
        'posyandu_monthly_examination_id',
        'child_id',
        'orang_tua_id',
        'attendance',
        'weight',
        'height',
        'bmi',
        'bmi_category',
        'height_for_age_zscore',
        'stunting_status',
        'head_circumference',
        'head_circumference_result',
        'exclusive_breastfeeding',
        'mp_asi',
        'tb_cough',
        'tb_fever',
        'tb_weight_problem',
        'tb_close_contact',
        'tb_screening_result',
        'examination_status',
        'follow_up_recommendation',
        'follow_up_status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'attendance' => 'boolean',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'bmi' => 'decimal:2',
        'height_for_age_zscore' => 'decimal:2',
        'head_circumference' => 'decimal:2',
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(PosyanduMonthlyExamination::class, 'posyandu_monthly_examination_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(OrangTua::class, 'orang_tua_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
