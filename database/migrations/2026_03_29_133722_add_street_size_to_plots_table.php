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
            // Only add if column doesn't already exist
            if (!Schema::hasColumn('plots', 'street_size')) {
                $table->unsignedTinyInteger('street_size')->nullable()->after('street_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            $table->dropColumn('street_size');
        });
    }
};
