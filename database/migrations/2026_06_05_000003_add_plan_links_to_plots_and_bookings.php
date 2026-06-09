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
            if (!Schema::hasColumn('plots', 'installment_plan_id')) {
                $table->foreignId('installment_plan_id')->nullable()->constrained('installment_plans')->onDelete('set null');
            }
            if (!Schema::hasColumn('plots', 'pricing_plan_id')) {
                $table->foreignId('pricing_plan_id')->nullable()->constrained('plot_pricing_plans')->onDelete('set null');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'installment_plan_id')) {
                $table->foreignId('installment_plan_id')->nullable()->constrained('installment_plans')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            $table->dropForeign(['installment_plan_id']);
            $table->dropColumn('installment_plan_id');
            $table->dropForeign(['pricing_plan_id']);
            $table->dropColumn('pricing_plan_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['installment_plan_id']);
            $table->dropColumn('installment_plan_id');
        });
    }
};
