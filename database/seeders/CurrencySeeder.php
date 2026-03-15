<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['name' => 'USD', 'symbol' => '$'],
            ['name' => 'EUR', 'symbol' => '€'],
            ['name' => 'GBP', 'symbol' => '£'],
            ['name' => 'INR', 'symbol' => '₹'],
            ['name' => 'CAD', 'symbol' => 'C$'],
            ['name' => 'AUD', 'symbol' => 'A$'],
            ['name' => 'JPY', 'symbol' => '¥'],
            // Add more as needed
        ];
        foreach ($currencies as $currency) {
            Currency::updateOrCreate(['name' => $currency['name']], $currency);
        }
    }
}
