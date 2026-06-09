<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable()->after('remarks');
            $table->decimal('cancellation_refund', 15, 2)->nullable()->after('cancellation_reason');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_refund');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['cancellation_reason', 'cancellation_refund', 'cancelled_at', 'cancelled_by']);
        });
    }
};
