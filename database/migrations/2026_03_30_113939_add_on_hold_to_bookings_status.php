<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY status ENUM(
        'pending',
        'active',
        'on_hold',
        'completed',
        'transferred',
        'cancelled',
        'pending_transfer',
        'partial_transferred',
        'swapped',
        'plot_relocated'
    ) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
