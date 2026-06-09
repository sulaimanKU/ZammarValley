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
        Schema::table('bookings', function (Blueprint $table) {
                        $cols = [
                'previous_owner_name','previous_owner_cnic','previous_deed_no',
                'previous_transfer_date','previous_owner_cnic_doc','previous_sale_deed',
                'transferred_from_booking_id','booking_type','processing_fee',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $table->dropColumn($col);
                }
            }

            // Add registry fee and development fee
            if (!Schema::hasColumn('bookings', 'registry_fee')) {
                $table->decimal('registry_fee', 15, 2)->nullable()->after('monthly_installment');
            }
            if (!Schema::hasColumn('bookings', 'development_fee')) {
                $table->decimal('development_fee', 15, 2)->nullable()->after('registry_fee');
            }
            if (!Schema::hasColumn('bookings', 'quarterly_installments')) {
                $table->unsignedSmallInteger('quarterly_installments')->nullable()->after('development_fee');
            }
            if (!Schema::hasColumn('bookings', 'quarterly_amount')) {
                $table->decimal('quarterly_amount', 15, 2)->nullable()->after('quarterly_installments');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
