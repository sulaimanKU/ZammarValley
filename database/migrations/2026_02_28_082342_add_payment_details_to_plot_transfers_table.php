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
        Schema::table('plot_transfers', function (Blueprint $table) {

    $table->string('payment_method')->nullable()->after('transfer_fee_receipt_no');
    $table->date('fee_paid_date')->nullable()->after('payment_method');
    $table->string('paid_by')->nullable()->after('fee_paid_date');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_transfers', function (Blueprint $table) {
            //
        });
    }
};
