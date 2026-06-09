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
        Schema::create('plot_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->decimal('base_price',15,2);
            $table->decimal('down_payment', 15, 2)->default(0);
            $table->decimal('processing_fee', 15, 2)->default(0);
            $table->integer('total_installments')->default(36);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plot_categories');
    }
};
