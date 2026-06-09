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
        Schema::create('booking_holds', function (Blueprint $table) {

            $table->id();

        // Relation with bookings
        $table->foreignId('booking_id')
              ->constrained()
              ->onDelete('cascade');

        // Status: hold or active
        $table->enum('status', ['hold', 'active'])->default('hold');

        // Reason for hold/unhold
        $table->text('remarks')->nullable();

        // कौन user ne hold lagaya (optional)
        $table->foreignId('created_by')
              ->nullable()
              ->constrained('users')
              ->onDelete('set null');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_holds');
    }
};
