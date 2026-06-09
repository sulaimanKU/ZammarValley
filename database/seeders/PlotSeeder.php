<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
      public function run(): void
    {
        $now     = now();
        $sector  = 'H-17';
        $society = 'Zamar Valley';
        $city    = 'Islamabad';

        // ── Get real IDs from your DB ──────────────────────────
        $categories = DB::table('plot_categories')->pluck('id')->toArray();
        if (empty($categories)) {
            $this->command->error('No categories found. Please seed categories first.');
            return;
        }

        $blocks = [
            'Main Service Road Block',
            'Executive 2 Block',
            'Executive 1 Block',
            'Civic Center Block',
            'Prime Block',
            'A Block',
            'B Block',
            'C Block',
            'Enclave Block',
            'H Block',
            'Zamar Green Block',
            'New Green Block',
            'Overseas Block',
            'VIP Block',
            'VIP 2 Block',
        ];

        // Your actual feature names from DB image
        $features = [
            'Corner',
            'Park Facing',
            'Main Road',
            'West Open',
            'Near Masjid',
            'Commercial',
            'Shop',
            'Plaza',
            'Main Boulevard',
            null,
        ];

        $statuses = [
            'available',
            'available',
            'available',
            'sold',
            'booked',
            'reserved',
            'available',
            'sold',
        ];

        $streets = [
            'Street 1','Street 2','Street 3','Street 4','Street 5',
            'Street 6','Street 7','Street 8','Street 9','Street 10',
        ];

        // 12 payment scenarios
        $scenarios = [
            // 1. Cash — no down payment
            [
                'price_type'=>'cash','base_price'=>2500000,
                'down_payment'=>null,'quarterly_installments'=>null,
                'quarterly_amount'=>null,'total_installments'=>null,
                'installment_amount'=>null,
                'size'=>5,'unit'=>'Marla','street_size'=>30,
                'desc'=>'Cash - No Down Payment',
            ],
            // 2. Cash — with down payment
            [
                'price_type'=>'cash','base_price'=>4500000,
                'down_payment'=>1500000,'quarterly_installments'=>null,
                'quarterly_amount'=>null,'total_installments'=>null,
                'installment_amount'=>null,
                'size'=>7,'unit'=>'Marla','street_size'=>40,
                'desc'=>'Cash - With Down Payment',
            ],
            // 3. Instalment — no down, monthly only
            [
                'price_type'=>'installment','base_price'=>3600000,
                'down_payment'=>null,'quarterly_installments'=>null,
                'quarterly_amount'=>null,'total_installments'=>36,
                'installment_amount'=>100000,
                'size'=>5,'unit'=>'Marla','street_size'=>25,
                'desc'=>'Instalment - No Down - Monthly Only (36×100,000)',
            ],
            // 4. Instalment — with down, monthly only
            [
                'price_type'=>'installment','base_price'=>4800000,
                'down_payment'=>800000,'quarterly_installments'=>null,
                'quarterly_amount'=>null,'total_installments'=>24,
                'installment_amount'=>166667,
                'size'=>7,'unit'=>'Marla','street_size'=>30,
                'desc'=>'Instalment - With Down - Monthly Only (24×166,667)',
            ],
            // 5. Client Example 1: Down+Quarterly+Monthly
            [
                'price_type'=>'installment','base_price'=>4000000,
                'down_payment'=>1000000,'quarterly_installments'=>6,
                'quarterly_amount'=>275000,'total_installments'=>18,
                'installment_amount'=>75000,
                'size'=>5,'unit'=>'Marla','street_size'=>35,
                'desc'=>'Instalment - 1,000,000 down + 6×275,000 quarterly + 18×75,000 monthly',
            ],
            // 6. Client Example 2: Down+Quarterly+Monthly
            [
                'price_type'=>'installment','base_price'=>4000000,
                'down_payment'=>1500000,'quarterly_installments'=>6,
                'quarterly_amount'=>250000,'total_installments'=>12,
                'installment_amount'=>83333,
                'size'=>7,'unit'=>'Marla','street_size'=>40,
                'desc'=>'Instalment - 1,500,000 down + 6×250,000 quarterly + 12×83,333 monthly',
            ],
            // 7. Quarterly only — no monthly
            [
                'price_type'=>'installment','base_price'=>6000000,
                'down_payment'=>1000000,'quarterly_installments'=>20,
                'quarterly_amount'=>250000,'total_installments'=>null,
                'installment_amount'=>null,
                'size'=>10,'unit'=>'Marla','street_size'=>50,
                'desc'=>'Instalment - Quarterly Only (20×250,000)',
            ],
            // 8. No down — quarterly + monthly
            [
                'price_type'=>'installment','base_price'=>5000000,
                'down_payment'=>null,'quarterly_installments'=>8,
                'quarterly_amount'=>250000,'total_installments'=>12,
                'installment_amount'=>250000,
                'size'=>7,'unit'=>'Marla','street_size'=>40,
                'desc'=>'Instalment - No Down - 8×250,000 quarterly + 12×250,000 monthly',
            ],
            // 9. 1 Kanal — large down + quarterly + monthly
            [
                'price_type'=>'installment','base_price'=>12000000,
                'down_payment'=>4000000,'quarterly_installments'=>8,
                'quarterly_amount'=>500000,'total_installments'=>24,
                'installment_amount'=>125000,
                'size'=>1,'unit'=>'Kanal','street_size'=>60,
                'desc'=>'Instalment - 1 Kanal - 4,000,000 down + 8×500,000 qtr + 24×125,000 monthly',
            ],
            // 10. 2 Kanal cash no down
            [
                'price_type'=>'cash','base_price'=>22000000,
                'down_payment'=>null,'quarterly_installments'=>null,
                'quarterly_amount'=>null,'total_installments'=>null,
                'installment_amount'=>null,
                'size'=>2,'unit'=>'Kanal','street_size'=>70,
                'desc'=>'Cash - 2 Kanal - No Down Payment',
            ],
            // 11. Token down — long monthly
            [
                'price_type'=>'installment','base_price'=>2400000,
                'down_payment'=>400000,'quarterly_installments'=>null,
                'quarterly_amount'=>null,'total_installments'=>40,
                'installment_amount'=>50000,
                'size'=>3,'unit'=>'Marla','street_size'=>20,
                'desc'=>'Instalment - Token Down 400,000 + 40×50,000 monthly',
            ],
            // 12. No down — quarterly short term
            [
                'price_type'=>'installment','base_price'=>3200000,
                'down_payment'=>null,'quarterly_installments'=>8,
                'quarterly_amount'=>400000,'total_installments'=>null,
                'installment_amount'=>null,
                'size'=>5,'unit'=>'Marla','street_size'=>25,
                'desc'=>'Instalment - No Down - 8×400,000 quarterly short term',
            ],
        ];

        $plots  = [];
        $plotNo = 2001;

        foreach ($blocks as $bi => $block) {
            foreach ($scenarios as $si => $sc) {
                $plots[] = [
                    // Rotate through your real category IDs
                    'plot_category_id'       => $categories[($bi + $si) % count($categories)],
                    'city'                   => $city,
                    'society'                => $society,
                    'sector'                 => $sector,
                    'block'                  => $block,
                    'street_number'          => $streets[($bi + $si) % count($streets)],
                    'street_size'            => $sc['street_size'],
                    'plot_number'            => (string) $plotNo++,
                    'size'                   => $sc['size'],
                    'unit'                   => $sc['unit'],
                    'status'                 => $statuses[($bi + $si) % count($statuses)],
                    'price_type'             => $sc['price_type'],
                    'base_price'             => $sc['base_price'],
                    'down_payment'           => $sc['down_payment'],
                    'quarterly_installments' => $sc['quarterly_installments'],
                    'quarterly_amount'       => $sc['quarterly_amount'],
                    'total_installments'     => $sc['total_installments'],
                    'installment_amount'     => $sc['installment_amount'],
                    'property_features'      => $features[($bi + $si) % count($features)],
                    'description'            => $sc['desc'],
                    'plot_image'             => null,
                    'created_at'             => $now,
                    'updated_at'             => $now,
                ];
            }
        }

        foreach (array_chunk($plots, 100) as $chunk) {
            DB::table('plots')->insert($chunk);
        }

        $this->command->info('✓ Plots seeded: '.count($plots).' records across '.count($blocks).' blocks.');
        $this->command->info('✓ Used '.count($categories).' real category IDs: '.implode(', ', $categories));
    }
}
