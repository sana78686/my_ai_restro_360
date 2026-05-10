<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Runs in each tenant database (not the central DB).
 * Central super-user roles live in RoleAndPermissionSeeder on the landlord connection.
 */
class TenantRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionNames = [
            'manage_restaurant',
            'manage_categories',
            'manage_products',
            'manage_orders',
            'manage_staff',
            'view_reports',
            'manage_settings',
            'can_receive_order_status_notifications',
            'can_change_order_status',
            'can_recieve_settings_notifications',
            'can_recieve_items_notifications',
            'can_recieve_category_notifications',
            'can_recieve_staff_notifications',
            'can_recieve_reports_notifications',
            'manage_mail_logs',
        ];

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );
        }

        $defaultRoles = ['restaurant_owner', 'manager', 'kitchen', 'order_taker', 'delivery_boy'];
        foreach ($defaultRoles as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }

        $owner = Role::findByName('restaurant_owner', 'web');
        $owner->syncPermissions(Permission::query()->where('guard_name', 'web')->get());
    }
}
