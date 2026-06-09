<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPlanChange extends Model
{
    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
