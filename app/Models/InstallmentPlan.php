<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallmentPlan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'total_price' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
        'quarterly_amount' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category this plan belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PlotCategory::class, 'plot_category_id');
    }

    /**
     * Get the individual installment schedule for this plan.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(InstallmentSchedule::class);
    }
}
