<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings_status', function (Blueprint $table) {
         DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','active','cancelled','completed','transferred') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings_status', function (Blueprint $table) {
             DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','active','cancelled','completed') NOT NULL DEFAULT 'pending'");
        });
    }
};
