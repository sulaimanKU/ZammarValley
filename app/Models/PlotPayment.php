<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlotPayment extends Model
{
    protected $guarded = [];

    public function booking(){
        return $this->belongsTo(Booking::class);
    }
    public function plot(){
        return $this->belongsTo(Plot::class);
    }
}
