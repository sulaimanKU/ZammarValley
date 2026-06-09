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
            $cols = ['pricing_plan_id', 'custom_price', 'registry_fee', 'installment_frequency'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('plots', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
                 $table->unsignedBigInteger('pricing_plan_id')->nullable();
            $table->decimal('custom_price', 15, 2)->nullable();
            $table->decimal('registry_fee', 15, 2)->nullable();
            $table->unsignedTinyInteger('installment_frequency')->default(1)->nullable();
        });
    }
};
