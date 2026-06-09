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
        Schema::table('office_expenses', function (Blueprint $table) {
            $table->enum('type', ['expense', 'income', 'inventory'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('office_expenses', function (Blueprint $table) {
            //
        });
    }
};
