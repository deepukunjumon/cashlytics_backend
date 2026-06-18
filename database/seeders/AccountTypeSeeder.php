<?php

namespace Database\Seeders;

use App\Models\AccountTypeMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $types = [
            ['slug' => 'cash',            'name' => 'Cash',            'icon' => 'banknote',      'color' => '#22c55e', 'sort_order' => 1],
            ['slug' => 'savings_account', 'name' => 'Savings Account', 'icon' => 'piggy-bank',    'color' => '#3b82f6', 'sort_order' => 2],
            ['slug' => 'credit_card',     'name' => 'Credit Card',     'icon' => 'credit-card',   'color' => '#f59e0b', 'sort_order' => 3],
            ['slug' => 'investments',     'name' => 'Investments',     'icon' => 'trending-up',   'color' => '#8b5cf6', 'sort_order' => 4],
            ['slug' => 'other',           'name' => 'Other',           'icon' => 'wallet',        'color' => '#6b7280', 'sort_order' => 5],
        ];

        foreach ($types as $type) {
            AccountTypeMaster::firstOrCreate(['slug' => $type['slug']], array_merge($type, ['is_active' => true]));
        }
    }
}
