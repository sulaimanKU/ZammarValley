<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingFee extends Model
{
protected $table    = 'booking_fees';
    protected $fillable = [
        'booking_id', 'fee_type', 'amount',
        'paid_amount', 'status', 'transfer_id',
    ];

    // fee_type values: 'registry', 'development', 'security', 'transfer'
    public static array $meta = [
        'registry'    => ['label'=>'Registry Fee',    'icon'=>'📋','color'=>'#1d4ed8','bg'=>'#eff6ff'],
        'development' => ['label'=>'Development Fee', 'icon'=>'🏗️','color'=>'#16a34a','bg'=>'#f0fdf4'],
        'security'    => ['label'=>'Security Fee',    'icon'=>'🔒','color'=>'#7c3aed','bg'=>'#fdf4ff'],
        'transfer'    => ['label'=>'Transfer Fee',    'icon'=>'🤝','color'=>'#ca8a04','bg'=>'#fefce8'],
    ];

    public function booking()  { return $this->belongsTo(Booking::class); }
    public function payments() { return $this->hasMany(FeePayment::class, 'booking_fee_id'); }

    public function getMetaAttribute(): array
    {
        return self::$meta[$this->fee_type]
            ?? ['label' => ucfirst($this->fee_type), 'icon' => '💳', 'color' => '#475569', 'bg' => '#f1f5f9'];
    }

    public function getRemainingAttribute(): float
    {
        return max(0, (float)$this->amount - (float)$this->paid_amount);
    }

    public function getIsSettledAttribute(): bool
    {
        // Security: monthly recurring — only settles via explicit 'paid' status.
        if ($this->fee_type === 'security') {
            return $this->status === 'paid';
        }

        // All other types (registry, development, transfer): settled when status='paid'
        // or when paid_amount meets/exceeds the billed amount.
        if ($this->status === 'paid') {
            return true;
        }

        if ((float)$this->amount > 0) {
            return (float)$this->paid_amount >= (float)$this->amount;
        }

        return false;
    }
}
