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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'mobile'))           $table->string('mobile')->nullable()->after('phone');
    if (!Schema::hasColumn('customers', 'phone_off'))        $table->string('phone_off')->nullable()->after('mobile');
    if (!Schema::hasColumn('customers', 'phone_res'))        $table->string('phone_res')->nullable()->after('phone_off');
    if (!Schema::hasColumn('customers', 'residential_address')) $table->string('residential_address')->nullable()->after('address');
    if (!Schema::hasColumn('customers', 'postal_address'))   $table->string('postal_address')->nullable()->after('residential_address');
    if (!Schema::hasColumn('customers', 'occupation'))       $table->string('occupation')->nullable()->after('postal_address');
    if (!Schema::hasColumn('customers', 'age'))              $table->unsignedTinyInteger('age')->nullable()->after('occupation');
    if (!Schema::hasColumn('customers', 'nationality'))      $table->string('nationality')->default('Pakistani')->after('age');
    if (!Schema::hasColumn('customers', 'nominee_name'))     $table->string('nominee_name')->nullable()->after('nationality');
    if (!Schema::hasColumn('customers', 'nominee_relation')) $table->string('nominee_relation')->nullable()->after('nominee_name');
    if (!Schema::hasColumn('customers', 'nominee_cnic'))     $table->string('nominee_cnic')->nullable()->after('nominee_relation');
    if (!Schema::hasColumn('customers', 'nominee_address'))  $table->string('nominee_address')->nullable()->after('nominee_cnic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
};
