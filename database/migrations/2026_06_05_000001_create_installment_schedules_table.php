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
        Schema::create('installment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installment_plan_id')->constrained('installment_plans')->onDelete('cascade');
            
            $table->string('label'); // e.g., "Down Payment", "Month 1", "Quarter 1"
            $table->enum('type', ['down_payment', 'monthly', 'quarterly', 'possession', 'other']);
            $table->decimal('amount', 15, 2);
            $table->unsignedSmallInteger('due_month'); // Relative month from booking (0 for down payment)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_schedules');
    }
};
