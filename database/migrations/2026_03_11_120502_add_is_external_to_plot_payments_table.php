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
            $table->boolean('is_external')->default(false)->after('status');
            $table->string('external_note')->nullable()->after('is_external');
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
