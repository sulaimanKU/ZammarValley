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
        Schema::table('fee_payments', function (Blueprint $table) {
               $table->string('receipt_no', 100)->nullable()->after('paid_date');
            $table->enum('payment_mode', ['cash','bank_transfer','cheque','online'])
                  ->default('cash')->after('receipt_no');
            $table->foreignId('transfer_id')->nullable()->constrained('plot_transfers')
                  ->nullOnDelete()->after('payment_mode');
            $table->text('notes')->nullable()->after('transfer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
             $table->dropConstrainedForeignId('transfer_id');
            $table->dropColumn(['receipt_no','payment_mode','notes']);
        });
    }
};
