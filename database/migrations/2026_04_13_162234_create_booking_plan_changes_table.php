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
        Schema::create('booking_plan_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users');

            // Monthly installment snapshot before and after
            $table->unsignedSmallInteger('old_installments');
            $table->unsignedSmallInteger('new_installments');
            $table->decimal('old_monthly_amount', 15, 2)->default(0);
            $table->decimal('new_monthly_amount', 15, 2)->default(0);

            // Context at the moment of change
            $table->unsignedSmallInteger('installments_paid')->default(0);
            $table->decimal('remaining_balance', 15, 2)->default(0);

            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_plan_changes');
    }
};
