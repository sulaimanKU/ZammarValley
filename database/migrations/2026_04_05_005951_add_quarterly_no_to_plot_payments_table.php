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
            $table->integer('quarterly_no')->nullable()->after('installment_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_payments', function (Blueprint $table) {
            //
        });
    }
};
