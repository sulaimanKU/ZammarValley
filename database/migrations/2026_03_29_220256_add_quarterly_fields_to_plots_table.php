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
               if (!Schema::hasColumn('plots', 'quarterly_installments')) {
                $table->unsignedSmallInteger('quarterly_installments')->nullable()->after('down_payment');
            }
            if (!Schema::hasColumn('plots', 'quarterly_amount')) {
                $table->decimal('quarterly_amount', 15, 2)->nullable()->after('quarterly_installments');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
              $table->dropColumn(['quarterly_installments', 'quarterly_amount']);
        });
    }
};
