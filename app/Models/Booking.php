<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $table = 'bookings';
public function createdBy() {
    return $this->belongsTo(\App\Models\User::class, 'created_by');
}
public function cancelledBy() {
    return $this->belongsTo(\App\Models\User::class, 'cancelled_by');
}
protected $casts = [
    'has_registry_fee'    => 'boolean',
    'has_development_fee' => 'boolean',
    'booking_date'        => 'date',
    'security_fee_start_date' => 'date',
    'security_fee_end_date'   => 'date',
    'cancelled_at'        => 'datetime',
    'cancellation_refund' => 'decimal:2',
];
protected $guarded = [];
    public function customer() {
    return $this->belongsTo(Customer::class);
}

public function payments() {
    return $this->hasMany(PlotPayment::class);
}
public function feePayments()
{
    return $this->hasMany(\App\Models\FeePayment::class);
}
public function plot() {
    return $this->belongsTo(Plot::class);
}

public function transfersFrom()
{
    return $this->hasMany(\App\Models\PlotTransfer::class, 'from_booking_id');
}

public function transfersTo()
{
    return $this->hasMany(\App\Models\PlotTransfer::class, 'to_booking_id');
}
public function bookingFees()
{
    return $this->hasMany(\App\Models\BookingFee::class);
}
public function holds()
{
    return $this->hasMany(\App\Models\BookingHold::class);
}

public function planChanges()
{
    return $this->hasMany(\App\Models\BookingPlanChange::class)->latest();
}

public function activeHold()
{
    return $this->hasOne(\App\Models\BookingHold::class)->where('status', 'hold')->latest();
}

public function isOnHold(): bool
{
    return $this->holds()->where('status', 'hold')->exists();
}
public function getStatusLabelAttribute(): string
{
    return match($this->status) {
        'pending'             => 'Pending',
        'active'              => 'Active',
        'completed'           => 'Completed',
        'transferred'         => 'Ownership Transferred',
        'cancelled'           => 'Cancelled',
        'pending_transfer'    => 'Pending Transfer',
        'partial_transferred' => 'Partially Transferred',
        'swapped'             => 'Plot Swapped',
        'plot_relocated'      => 'Plot Relocated',
        default               => ucfirst($this->status),
    };
}

public function getStatusColorAttribute(): string
{
    return match($this->status) {
        'pending'             => 'warning',
        'active'              => 'success',
        'completed'           => 'teal',
        'transferred'         => 'primary',
        'cancelled'           => 'danger',
        'pending_transfer'    => 'warning',
        'partial_transferred' => 'purple',
        'swapped'             => 'cyan',
        'plot_relocated'      => 'orange',
        default               => 'secondary',
    };
}

public function paidForFee(int $bookingFeeId): float
{
    return (float) $this->feePayments
        ->where('booking_fee_id', $bookingFeeId)
        ->sum('amount');
}



}
