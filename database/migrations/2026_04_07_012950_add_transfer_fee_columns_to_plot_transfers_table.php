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
            $table->decimal('transfer_fee', 15, 2)->default(0)->after('notes');
            $table->string('transfer_fee_status', 20)->default('pending')->after('transfer_fee');
            $table->string('transfer_fee_receipt_no', 100)->nullable()->after('transfer_fee_status');
        });
    }

    public function down(): void
    {
        Schema::table('plot_transfers', function (Blueprint $table) {
            $table->dropColumn(['transfer_fee', 'transfer_fee_status', 'transfer_fee_receipt_no']);
        });
    }
};
