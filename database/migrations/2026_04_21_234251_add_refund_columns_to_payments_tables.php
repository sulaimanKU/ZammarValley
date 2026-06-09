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
        Schema::table('plot_payments', function (Blueprint $table) {
            $table->boolean('is_refunded')->default(false)->after('amount_paid');
            $table->decimal('refund_amount', 12, 2)->nullable()->after('is_refunded');
            $table->date('refund_date')->nullable()->after('refund_amount');
            $table->string('refund_note', 500)->nullable()->after('refund_date');
        });

        Schema::table('fee_payments', function (Blueprint $table) {
            $table->boolean('is_refunded')->default(false)->after('amount');
            $table->decimal('refund_amount', 12, 2)->nullable()->after('is_refunded');
            $table->date('refund_date')->nullable()->after('refund_amount');
            $table->string('refund_note', 500)->nullable()->after('refund_date');
        });
    }

    public function down(): void
    {
        Schema::table('plot_payments', function (Blueprint $table) {
            $table->dropColumn(['is_refunded', 'refund_amount', 'refund_date', 'refund_note']);
        });
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropColumn(['is_refunded', 'refund_amount', 'refund_date', 'refund_note']);
        });
    }
};
