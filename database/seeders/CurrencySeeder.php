<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $currencies = [
            ['code' => 'INR', 'symbol' => '₹',  'name' => 'Indian Rupee'],
            ['code' => 'USD', 'symbol' => '$',   'name' => 'US Dollar'],
            ['code' => 'EUR', 'symbol' => '€',   'name' => 'Euro'],
            ['code' => 'GBP', 'symbol' => '£',   'name' => 'British Pound'],
            ['code' => 'AED', 'symbol' => 'د.إ', 'name' => 'UAE Dirham'],
            ['code' => 'SGD', 'symbol' => 'S$',  'name' => 'Singapore Dollar'],
            ['code' => 'AUD', 'symbol' => 'A$',  'name' => 'Australian Dollar'],
            ['code' => 'CAD', 'symbol' => 'C$',  'name' => 'Canadian Dollar'],
            ['code' => 'JPY', 'symbol' => '¥',   'name' => 'Japanese Yen'],
            ['code' => 'CHF', 'symbol' => 'Fr',  'name' => 'Swiss Franc'],
            ['code' => 'CNY', 'symbol' => '¥',   'name' => 'Chinese Yuan'],
            ['code' => 'MYR', 'symbol' => 'RM',  'name' => 'Malaysian Ringgit'],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(['code' => $currency['code']], $currency);
        }
    }
}
