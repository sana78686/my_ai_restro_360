<?php

namespace Database\Seeders;

use Database\Seeders\Tenant\TenantRolesAndPermissionsSeeder;
use Illuminate\Database\Seeder;

/**
 * Entry point for php artisan tenants:seed (new tenant DBs after migrate).
 * Keeps central DatabaseSeeder separate (super_user, plans, etc.).
 */
class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantRolesAndPermissionsSeeder::class,
        ]);
    }
}
