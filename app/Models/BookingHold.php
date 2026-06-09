<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingHold extends Model
{
     protected $table    = 'booking_holds';
    protected $fillable = ['booking_id', 'status', 'remarks', 'created_by'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper — is this booking currently on hold?
    public static function isOnHold(int $bookingId): bool
    {
        return self::where('booking_id', $bookingId)
                   ->where('status', 'hold')
                   ->exists();
    }

    // Helper — latest hold record for a booking
    public static function latestFor(int $bookingId): ?self
    {
        return self::where('booking_id', $bookingId)
                   ->latest()
                   ->first();
    }
}
