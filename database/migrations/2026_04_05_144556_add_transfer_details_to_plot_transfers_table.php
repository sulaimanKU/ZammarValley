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
              if (!Schema::hasColumn('plot_transfers', 'transfer_fee')) {
                $table->decimal('transfer_fee', 12, 2)->default(0)->after('notes');
            }
            if (!Schema::hasColumn('plot_transfers', 'transfer_fee_status')) {
                $table->enum('transfer_fee_status', ['pending','paid','waived'])->default('pending')->after('transfer_fee');
            }
            if (!Schema::hasColumn('plot_transfers', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('transfer_fee_status');
            }

            // ── Witness 1
            if (!Schema::hasColumn('plot_transfers', 'witness1_name')) {
                $table->string('witness1_name')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('plot_transfers', 'witness1_cnic')) {
                $table->string('witness1_cnic', 20)->nullable()->after('witness1_name');
            }
            if (!Schema::hasColumn('plot_transfers', 'witness1_address')) {
                $table->string('witness1_address')->nullable()->after('witness1_cnic');
            }

            // ── Witness 2
            if (!Schema::hasColumn('plot_transfers', 'witness2_name')) {
                $table->string('witness2_name')->nullable()->after('witness1_address');
            }
            if (!Schema::hasColumn('plot_transfers', 'witness2_cnic')) {
                $table->string('witness2_cnic', 20)->nullable()->after('witness2_name');
            }
            if (!Schema::hasColumn('plot_transfers', 'witness2_address')) {
                $table->string('witness2_address')->nullable()->after('witness2_cnic');
            }

            // ── Consideration amount (how much seller received)
            if (!Schema::hasColumn('plot_transfers', 'consideration_amount')) {
                $table->decimal('consideration_amount', 12, 2)->nullable()->after('witness2_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_transfers', function (Blueprint $table) {
            $table->dropColumn([
                'transfer_fee','transfer_fee_status','payment_method',
                'witness1_name','witness1_cnic','witness1_address',
                'witness2_name','witness2_cnic','witness2_address',
                'consideration_amount',
            ]);
        });
    }
};
