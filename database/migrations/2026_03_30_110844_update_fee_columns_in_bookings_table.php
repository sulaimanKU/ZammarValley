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
             // Remove amount columns
        if (Schema::hasColumn('bookings', 'registry_fee')) {
            $table->dropColumn('registry_fee');
        }
        if (Schema::hasColumn('bookings', 'development_fee')) {
            $table->dropColumn('development_fee');
        }

        // Add yes/no flags instead
        $table->boolean('has_registry_fee')->default(false)->after('quarterly_amount');
        $table->boolean('has_development_fee')->default(false)->after('has_registry_fee');
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
