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
        Schema::table('plot_categories', function (Blueprint $table) {
            // 1. Remove columns you no longer want
            // We remove 'location' because it's better handled in a Cities/Blocks table
            // We remove 'type' because 'property_type' covers it
            $table->dropColumn(['location', 'type']);

            // 2. Add the new specialized columns
            $table->integer('size')->after('property_type'); // The number (5, 10)
            $table->string('unit')->after('size'); // Marla, Kanal, Sqft
            $table->decimal('installment_amount', 15, 2)->after('total_installments');

            // Optional: Adding a prefix for plot numbering (e.g., "RES" for Residential)
            $table->string('prefix')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
