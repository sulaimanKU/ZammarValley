<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlotPricingPlan extends Model
{
    protected $table = 'plot_pricing_plans';
    protected $guarded = [];

    public function category()
{
    return $this->belongsTo(PlotCategory::class, 'plot_category_id');
}
}
