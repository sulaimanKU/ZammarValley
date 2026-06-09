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
        Schema::create('system_config', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

     DB::table('system_config')->insert([
            ['key'=>'society_name',          'value'=>'Zamar Valley',                           'group'=>'general'],
            ['key'=>'society_tagline',        'value'=>'Premium Housing Project',                'group'=>'general'],
            ['key'=>'society_phone',          'value'=>'+92-300-0000000',                        'group'=>'general'],
            ['key'=>'society_email',          'value'=>'info@zamarvalley.com',                   'group'=>'general'],
            ['key'=>'society_address',        'value'=>'',                                        'group'=>'general'],
            ['key'=>'society_logo',           'value'=>'',                                        'group'=>'general'],
            ['key'=>'default_plot_sizes',     'value'=>'3,5,7,10,20',                            'group'=>'general'],
            ['key'=>'default_plot_unit',      'value'=>'Marla',                                   'group'=>'general'],
            ['key'=>'currency_symbol',        'value'=>'PKR',                                     'group'=>'finance'],
            ['key'=>'default_transfer_fee',   'value'=>'50000',                                   'group'=>'finance'],
            ['key'=>'late_fine_percent',      'value'=>'2',                                       'group'=>'finance'],
            ['key'=>'installment_grace_days', 'value'=>'10',                                      'group'=>'finance'],
            ['key'=>'receipt_prefix',         'value'=>'REC',                                     'group'=>'documents'],
            ['key'=>'booking_id_prefix',      'value'=>'ZV',                                      'group'=>'documents'],
            ['key'=>'deed_prefix',            'value'=>'DEED',                                    'group'=>'documents'],
            ['key'=>'doc_watermark_text',     'value'=>'Zamar Valley Official',                   'group'=>'documents'],
            ['key'=>'qr_on_documents',        'value'=>'1',                                       'group'=>'documents'],
            ['key'=>'show_logo_on_receipt',   'value'=>'1',                                       'group'=>'documents'],
            ['key'=>'receipt_footer_note',    'value'=>'Thank you for investing in Zamar Valley.','group'=>'documents'],
        ]);
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_config');
    }
};
