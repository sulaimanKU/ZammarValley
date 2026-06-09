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
        Schema::create('plot_transfers', function (Blueprint $table) {
            $table->id();
             // Transfer identity
            $table->string('deed_no')->unique();          // e.g. TRF-2025-001
            $table->enum('transfer_type', [
                'ownership',   // Plot transfer: A → B
                'swap',        // Two customers swap plots
                'partial',     // Ownership split (%)
                'internal',    // Change block/plot number
            ]);
            $table->date('transfer_date');
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected'])
                  ->default('pending');

            // FROM (original owner / booking)
            $table->foreignId('from_booking_id')->constrained('bookings')->onDelete('restrict');
            $table->foreignId('from_customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('plot_id')->constrained('plots')->onDelete('restrict');

            // TO (new owner) — nullable for internal transfers
            $table->foreignId('to_customer_id')->nullable()->constrained('customers')->onDelete('restrict');
            $table->foreignId('to_booking_id')->nullable()->constrained('bookings')->onDelete('set null');

            // SWAP specific — second plot being swapped
            $table->foreignId('swap_plot_id')->nullable()->constrained('plots')->onDelete('restrict');
            $table->foreignId('swap_from_booking_id')->nullable()->constrained('bookings')->onDelete('restrict');

            // PARTIAL transfer specific
            $table->decimal('ownership_percentage', 5, 2)->nullable(); // e.g. 50.00

            // INTERNAL transfer specific
            $table->string('new_block')->nullable();
            $table->string('new_plot_number')->nullable();

            // Financials
            $table->decimal('transfer_fee', 12, 2)->default(0);
            $table->enum('transfer_fee_status', ['paid', 'pending', 'waived'])->default('pending');
            $table->string('transfer_fee_receipt_no')->nullable();
            $table->decimal('remaining_balance_transferred', 12, 2)->default(0); // balance passed to new owner

            // Notes
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('approved_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plot_transfers');
    }
};
