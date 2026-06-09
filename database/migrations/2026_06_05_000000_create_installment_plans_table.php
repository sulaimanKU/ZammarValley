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
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_category_id')->constrained('plot_categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            
            // Pricing Details
            $table->decimal('total_price', 15, 2);
            $table->decimal('down_payment', 15, 2);
            $table->decimal('processing_fee', 15, 2)->default(0);
            
            // Monthly Installments
            $table->unsignedSmallInteger('total_months')->default(0);
            $table->decimal('monthly_amount', 15, 2)->default(0);
            
            // Quarterly Installments (Optional)
            $table->unsignedSmallInteger('total_quarters')->default(0);
            $table->decimal('quarterly_amount', 15, 2)->default(0);
            
            // Other possible payments (Balloon/Final)
            $table->decimal('possession_fee', 15, 2)->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_plans');
    }
};
