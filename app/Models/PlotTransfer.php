<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlotTransfer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'deed_no',
        'transfer_type',
        'transfer_date',
        'status',
        'from_booking_id',
        'from_customer_id',
        'plot_id',
        'to_customer_id',
        'to_booking_id',
        'swap_plot_id',
        'swap_from_booking_id',
        'ownership_percentage',
        'new_block',
        'new_plot_number',
        'transfer_fee',
        'transfer_fee_status',
        'transfer_fee_receipt_no',
        'remaining_balance_transferred',
        'reason',
        'notes',
        'witness1_name',
    'witness1_cnic',
    'witness1_address',
    'witness2_name',
    'witness2_cnic',
    'witness2_address',
        'approved_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'transfer_fee'  => 'decimal:2',
        'remaining_balance_transferred' => 'decimal:2',
        'ownership_percentage' => 'decimal:2',
    ];

    // Relationships
    public function fromBooking()
    {
        return $this->belongsTo(Booking::class, 'from_booking_id');
    }
    public function toBooking()
    {
        return $this->belongsTo(Booking::class, 'to_booking_id');
    }
    public function fromCustomer()
    {
        return $this->belongsTo(Customer::class, 'from_customer_id');
    }
    public function toCustomer()
    {
        return $this->belongsTo(Customer::class, 'to_customer_id');
    }
    public function plot()
    {
        return $this->belongsTo(Plot::class, 'plot_id');
    }
    public function swapPlot()
    {
        return $this->belongsTo(Plot::class, 'swap_plot_id');
    }
    public function swapFromBooking()
    {
        return $this->belongsTo(Booking::class, 'swap_from_booking_id');
    }

public function getStatusLabelAttribute(): string
{
    return match($this->status) {
        'pending'   => 'Pending Approval',
        'approved'  => 'Approved',
        'rejected'  => 'Rejected',
        'completed' => 'Completed',
        default     => ucfirst($this->status),
    };
}
    // Helpers
    public function getTypeLabel(): string
    {
        return match ($this->transfer_type) {
            'ownership' => 'Ownership Transfer',
            'swap'      => 'Plot Swap',
            'partial'   => 'Partial Transfer',
            'internal'  => 'Internal Transfer',
            default     => 'Transfer',
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending'   => '#ca8a04',
            'approved'  => '#1d4ed8',
            'completed' => '#16a34a',
            'rejected'  => '#dc2626',
            default     => '#64748b',
        };
    }

    public static function generateDeedNo(): string
    {
        $prefix = \App\Models\SystemConfig::get('deed_prefix', 'DEED');
    $year   = date('Y');
    $last   = static::whereYear('created_at', $year)->count() + 1;
    $seq    = str_pad($last, 4, '0', STR_PAD_LEFT);

    return "{$prefix}-{$year}-{$seq}";
    }
}
