<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'Dashboard' => ['dashboard_view', 'view_stats', 'view_logs'],
            'Plots Inventory' => ['inventory_view', 'plot_create', 'plot_edit', 'plot_delete', 'plot_pricing_manage', 'plot_category_manage'],
            'Bookings' => ['booking_view_all', 'booking_create', 'booking_edit', 'booking_cancel', 'booking_reports', 'booking_plan_change'],
            'Recovery & Ledger' => ['recovery_dashboard_view', 'ledger_view', 'payment_add', 'payment_edit', 'payment_delete', 'payment_receipt_print', 'fee_management_view', 'fee_management_pay'],
            'Finance' => ['expense_view', 'expense_add', 'expense_edit', 'expense_delete', 'finance_reports_view'],
            'Transfer' => ['transfer_history_view', 'transfer_create', 'transfer_edit', 'transfer_delete'],
            'User Management' => ['user_view', 'user_create', 'user_edit', 'user_delete'],
            'Client List' => ['client_view', 'client_create', 'client_edit', 'client_delete'],
            'System Settings' => ['settings_view', 'role_manage', 'location_manage', 'society_config_manage', 'profile_edit']
        ];

        // 2. Insert Permissions
        foreach ($modules as $moduleName => $perms) {
            foreach ($perms as $p) {
                Permission::firstOrCreate([
                    'name' => $p,
                    'module' => $moduleName,
                    'guard_name' => 'web'
                ]);
            }
        }

        // 3. Auto-assign to Admin Role
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());
    }
    }

