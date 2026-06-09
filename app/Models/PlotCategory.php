<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlotCategory extends Model
{
    protected $table = 'plot_categories';
 protected $fillable = [
        'name',
        'prefix',
        'property_type',

    ];

   public function plots(){
    return $this->hasMany(Plot::class);
   }

   public function pricingPlans()
{
    return $this->hasMany(PlotPricingPlan::class, 'plot_category_id');
}
}
