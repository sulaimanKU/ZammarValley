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
            $table->decimal('registry_fee_amount',   15, 2)->nullable()->after('has_registry_fee');
            $table->decimal('development_fee_amount', 15, 2)->nullable()->after('has_development_fee');
            $table->decimal('security_fee_amount',   15, 2)->nullable()->after('has_security_fee');
        });
    }

    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            $table->dropColumn(['registry_fee_amount', 'development_fee_amount', 'security_fee_amount']);
        });
    }
};
