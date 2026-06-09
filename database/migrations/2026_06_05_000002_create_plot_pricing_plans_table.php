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
        if (!Schema::hasTable('plot_pricing_plans')) {
            Schema::create('plot_pricing_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('plot_category_id')->constrained('plot_categories')->onDelete('cascade');
                $table->decimal('size', 8, 2);
                $table->string('unit')->default('Marla');
                $table->decimal('base_price', 15, 2);
                $table->decimal('down_payment', 15, 2);
                $table->decimal('processing_fee', 15, 2)->default(0);
                $table->unsignedSmallInteger('total_installments')->nullable();
                $table->decimal('installment_amount', 15, 2)->nullable();
                $table->date('effective_from')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plot_pricing_plans');
    }
};
