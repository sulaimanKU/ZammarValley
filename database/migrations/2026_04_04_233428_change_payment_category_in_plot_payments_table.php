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
        // Using raw SQL for ENUM modification is often more reliable in MySQL
        // when adding multiple specific values at once.
        DB::statement("
            ALTER TABLE `plot_payments`
            MODIFY COLUMN `payment_category`
            ENUM(
                'down_payment',
                'installment',
                'quarterly_installment',
                'processing_fee',
                'plot_balance',
                'fine',
                'security_fee',
                'maintenance_fee',
                'development_fee',
                'bifurcation_fee',
                'registry_fee',
                'others'
            ) NOT NULL
        ");
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_payments', function (Blueprint $table) {
             DB::statement("
            ALTER TABLE `plot_payments`
            MODIFY COLUMN `payment_category`
            ENUM(
                'down_payment',
                'installment',
                'processing_fee',
                'plot_balance',
                'fine',
                'security_fee',
                'development_fee',
                'registry_fee',
                'others'
            ) NOT NULL
        ");
        });
    }
};
