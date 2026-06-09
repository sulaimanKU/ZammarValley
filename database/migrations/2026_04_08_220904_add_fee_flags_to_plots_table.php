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
            $table->boolean('has_registry_fee')->default(false)->after('status');
            $table->boolean('has_development_fee')->default(false)->after('has_registry_fee');
            $table->boolean('has_security_fee')->default(false)->after('has_development_fee');
        });
    }

    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            $table->dropColumn(['has_registry_fee', 'has_development_fee', 'has_security_fee']);
        });
    }
};
