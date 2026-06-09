<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plot_payments', function (Blueprint $table) {
            // Stores the waived/discounted amount alongside each payment.
            // For lump-sum settlements the early-payment discount lives here
            // instead of a separate DISC- record, so real cash vs waived amount
            // can always be separated in reports.
            $table->decimal('discount_amount', 15, 2)->default(0)->after('amount_paid');
        });
    }

    public function down(): void
    {
        Schema::table('plot_payments', function (Blueprint $table) {
            $table->dropColumn('discount_amount');
        });
    }
};
