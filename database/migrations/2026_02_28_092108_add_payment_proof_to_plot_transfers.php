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
          $table->string('payment_proof')->nullable()->after('paid_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_transfers', function (Blueprint $table) {
            $table->dropColumn('payment_proof');
        });
    }
};
