<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Get all tenants
$tenants = Tenant::all();

foreach ($tenants as $tenant) {
    echo "Processing tenant: " . $tenant->id . "\n";
    
    // Initialize tenancy for this tenant
    tenancy()->initialize($tenant);
    
    try {
        // Check if the column already exists
        if (Schema::hasColumn('settings', 'available_delivery_location')) {
            echo "Column already exists in tenant: " . $tenant->id . "\n";
            continue;
        }
        
        // Add the column
        Schema::table('settings', function ($table) {
            $table->json('available_delivery_location')->nullable()->after('is_active');
        });
        
        echo "Successfully added column to tenant: " . $tenant->id . "\n";
        
        // Record the migration
        DB::table('migrations')->insert([
            'migration' => '2025_09_04_050019_add_available_delivery_location_to_settings_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        
        echo "Migration recorded for tenant: " . $tenant->id . "\n";
        
    } catch (Exception $e) {
        echo "Error processing tenant " . $tenant->id . ": " . $e->getMessage() . "\n";
    }
    
    // End tenancy
    tenancy()->end();
}

echo "Migration process completed!\n";
