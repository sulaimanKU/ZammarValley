<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $table    = 'fee_payments';
    protected $fillable = [
        'booking_fee_id',
        'booking_id',
        'amount',
        'paid_date',
        'receipt_no',
        'payment_mode',
        'transfer_id',
        'notes',
        'is_refunded',
        'refund_amount',
        'refund_date',
        'refund_note',
    ];
    protected $casts = ['paid_date' => 'date'];

    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'booking_fee_id');
    }
    public function booking()
    {
        return $this->belongsTo(\App\Models\Booking::class);
    }
    public function transfer()
    {
        return $this->belongsTo(PlotTransfer::class, 'transfer_id');
    }
  public function bookingFee()
{
    // Point this to booking_fee_id so it locks onto the specific category row
    return $this->belongsTo(\App\Models\BookingFee::class, 'booking_fee_id');
}
}
