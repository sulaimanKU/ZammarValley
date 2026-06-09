<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallmentSchedule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_month' => 'integer',
    ];

    /**
     * Get the plan this installment belongs to.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(InstallmentPlan::class, 'installment_plan_id');
    }
}
