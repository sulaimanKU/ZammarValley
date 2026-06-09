<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix existing transfer bookings that got booking_type = 'First Allotment'
     * by default because the column was added with a DEFAULT value and the
     * TransferController never explicitly set it.
     *
     * Rule: any booking with parent_booking_id IS NOT NULL is a transfer child
     * and should have booking_type = 'Transfer'.
     */
    public function up(): void
    {
        DB::table('bookings')
            ->whereNotNull('parent_booking_id')
            ->update(['booking_type' => 'Transfer']);
    }

    public function down(): void
    {
        // Not reversible — we cannot know which were manually set vs auto-fixed.
    }
};
