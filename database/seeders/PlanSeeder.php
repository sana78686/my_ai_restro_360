<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'name' => 'Basic',
                'stripe_product_id' => 'prod_basic_001',
                'stripe_price_id' => 'price_basic_001',
                'price' => 1000,
                'currency' => 'usd',
                'interval' => 'month',
                'features' => json_encode(['5 Projects', 'Basic Support', 'Access to community']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'stripe_product_id' => 'prod_pro_001',
                'stripe_price_id' => 'price_pro_001',
                'price' => 2900,
                'currency' => 'usd',
                'interval' => 'month',
                'features' => json_encode(['Unlimited Projects', 'Priority Support', 'Team Access', 'Advanced analytics']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'stripe_product_id' => 'prod_enterprise_001',
                'stripe_price_id' => 'price_enterprise_001',
                'price' => 9900,
                'currency' => 'usd',
                'interval' => 'month',
                'features' => json_encode(['All Pro features', 'Dedicated Manager', 'Custom Integrations', 'SLA Support']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
