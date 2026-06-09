<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'security_fee_start_date')) {
                $table->date('security_fee_start_date')->nullable()->after('has_security_fee');
            }
            if (!Schema::hasColumn('bookings', 'security_fee_end_date')) {
                $table->date('security_fee_end_date')->nullable()->after('security_fee_start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['security_fee_start_date', 'security_fee_end_date']);
        });
    }
};
