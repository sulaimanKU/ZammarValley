<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    protected $guarded = [];
    public function category()  {

    return $this->belongsTo(PlotCategory::class,'plot_category_id');
    }
    public function pricingPlan()
    {
        return $this->belongsTo(PlotPricingPlan::class, 'pricing_plan_id');
    }
    public function bookings() {
    return $this->hasMany(Booking::class);
}

    public function getFinalPriceAttribute()
    {
        $base = $this->custom_price ?? $this->base_price ?? $this->pricingPlan?->base_price;
        $discount = (float) ($this->discount_amount ?? 0);
        return $base !== null ? max(0, (float) $base - $discount) : null;
    }

    public function hasDiscount(): bool
    {
        return !empty($this->discount_amount) && (float) $this->discount_amount > 0;
    }
}
