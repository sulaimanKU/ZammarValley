<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    protected $table    = 'fee_payments';
    protected $fillable = [
        'booking_fee_id',   // FK → booking_fees.id (the fee type: 1=registry etc.)
        'booking_id',
        'amount',
        'paid_date',
        'receipt_no',
        'payment_mode',
        'transfer_id',
        'notes',
    ];
    protected $casts = ['paid_date' => 'date'];

    public function booking()  { return $this->belongsTo(Booking::class); }
    public function transfer() { return $this->belongsTo(PlotTransfer::class, 'transfer_id'); }
}
