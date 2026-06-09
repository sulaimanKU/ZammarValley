<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder_missing extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $missing = [
            // ── Plots Inventory ────────────────────────────────────
            ['name' => 'block_manage',           'module' => 'Plots Inventory'],

            // ── Recovery & Ledger ──────────────────────────────────
            ['name' => 'fee_management_view',    'module' => 'Recovery & Ledger'],
            ['name' => 'fee_management_pay',     'module' => 'Recovery & Ledger'],

            // ── Transfer ───────────────────────────────────────────
            ['name' => 'transfer_approve',       'module' => 'Transfer'],

            // ── Bookings ───────────────────────────────────────────
            ['name' => 'booking_docs_view',      'module' => 'Bookings'],
            ['name' => 'possession_letter_view', 'module' => 'Bookings'],
        ];

        foreach ($missing as $perm) {
            Permission::firstOrCreate(
                ['name'       => $perm['name'],   'guard_name' => 'web'],
                ['module'     => $perm['module']]
            );
            $this->command->info("✓  {$perm['name']}");
        }

        // Auto-assign ALL permissions to Admin so nothing is ever missed
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());
        $this->command->info('Admin role synced with all permissions.');

        $this->command->info('All missing permissions inserted.');
    }
}
