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

  Schema::table('plots', function (Blueprint $table) {
        // 1. SAFELY Drop old columns only if they still exist
        if (Schema::hasColumn('plots', 'block_name')) {
            $table->dropColumn('block_name');
        }
        if (Schema::hasColumn('plots', 'size')) {
            $table->dropColumn('size');
        }
        if (Schema::hasColumn('plots', 'price')) {
            $table->dropColumn('price');
        }
        if (Schema::hasColumn('plots', 'plot_feature')) {
            $table->dropColumn('plot_feature');
        }

        // 2. Add the new columns (using nullable() to prevent the "Integrity violation" error)
        if (!Schema::hasColumn('plots', 'block_id')) {
            $table->foreignId('block_id')->nullable()->after('plot_category_id')->constrained('blocks')->onDelete('cascade');
        }

        if (!Schema::hasColumn('plots', 'plot_feature_id')) {
            $table->foreignId('plot_feature_id')->nullable()->after('status')->constrained('property_features')->onDelete('set null');
        }

        if (!Schema::hasColumn('plots', 'price_type')) {
            $table->string('price_type')->default('Standard')->after('plot_feature_id');
        }
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
