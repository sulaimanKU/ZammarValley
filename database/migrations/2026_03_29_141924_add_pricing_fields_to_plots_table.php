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
        Schema::table('plots', function (Blueprint $table) {
          if (!Schema::hasColumn('plots', 'base_price')) {
                $table->decimal('base_price', 15, 2)->nullable()->after('custom_price');
            }
            if (!Schema::hasColumn('plots', 'down_payment')) {
                $table->decimal('down_payment', 15, 2)->nullable()->after('base_price');
            }
            if (!Schema::hasColumn('plots', 'registry_fee')) {
                $table->decimal('registry_fee', 15, 2)->nullable()->after('down_payment');
            }
            if (!Schema::hasColumn('plots', 'total_installments')) {
                $table->unsignedSmallInteger('total_installments')->nullable()->after('registry_fee');
            }
            if (!Schema::hasColumn('plots', 'installment_amount')) {
                $table->decimal('installment_amount', 15, 2)->nullable()->after('total_installments');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            $table->dropColumn([
                'base_price', 'down_payment', 'registry_fee',
                'total_installments', 'installment_amount'
            ]);
        });
    }
};
