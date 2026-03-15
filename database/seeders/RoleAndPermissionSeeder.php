<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Super User permissions
            'manage_tenants',
            'view_tenants',
            'manage_subscriptions',

            // Restaurant Owner permissions
            'manage_restaurant',
            'manage_categories',
            'manage_products',
            'manage_orders',
            'manage_staff',
            'view_reports',
            'manage_settings',

            // defaulus rolles added more 

            'can_receive_order_status_notifications',
            'can_change_order_status',
            'can_recieve_settings_notifications',
            'can_recieve_items_notifications',
            'can_recieve_category_notifications',
            'can_recieve_staff_notifications',
            'can_recieve_reports_notifications',
            'manage_mail_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Create roles and assign permissions
        $superUserRole = Role::firstOrCreate(
            ['name' => 'super_user'],
            ['guard_name' => 'web']
        );
        $superUserRole->syncPermissions(Permission::all());

        $restaurantOwnerRole = Role::firstOrCreate(
            ['name' => 'restaurant_owner'],
            ['guard_name' => 'web']
        );

         $restaurantOwnerRole->syncPermissions(Permission::all());
        // $restaurantOwnerRole->syncPermissions([
        //     'manage_restaurant',
        //     'manage_categories',
        //     'manage_products',
        //     'manage_orders',
        //     'manage_staff',
        //     'view_reports',
        //     'manage_settings'
        // ]);
    }
} 