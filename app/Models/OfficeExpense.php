<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeExpense extends Model
{
  protected $fillable = [
    'voucher_no',
    'category',
    'amount',
    'expense_date',
    'paid_to',
    'payment_method',
    'reference_no',
    'payment_proof',
    'status',
    'remarks',
    'type',
    'fund_source',
];

    public static function generateVoucherNo(string $type = 'expense'): string
    {
        $prefix = match($type) {
            'income'    => 'INC',
            'inventory' => 'INV',
            default     => 'EXP',
        };
        $year  = date('Y');
        $count = static::whereYear('expense_date', $year)->count() + 1;
        return $prefix . '-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
    public $timestamps = false;
   protected $casts = [
    'expense_date' => 'datetime',
];

}
