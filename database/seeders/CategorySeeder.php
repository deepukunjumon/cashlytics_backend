<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            // Income
            ['name' => 'Salary',      'type' => 'income',  'icon' => 'briefcase',   'color' => '#22c55e'],
            ['name' => 'Freelance',   'type' => 'income',  'icon' => 'laptop',      'color' => '#10b981'],
            ['name' => 'Investment',  'type' => 'income',  'icon' => 'trending-up', 'color' => '#06b6d4'],
            ['name' => 'Gift',        'type' => 'income',  'icon' => 'gift',        'color' => '#84cc16'],
            ['name' => 'Other Income','type' => 'income',  'icon' => 'plus-circle', 'color' => '#6b7280'],
            // Expense
            ['name' => 'Food',        'type' => 'expense', 'icon' => 'utensils',    'color' => '#f59e0b'],
            ['name' => 'Transport',   'type' => 'expense', 'icon' => 'car',         'color' => '#3b82f6'],
            ['name' => 'Housing',     'type' => 'expense', 'icon' => 'home',        'color' => '#8b5cf6'],
            ['name' => 'Health',      'type' => 'expense', 'icon' => 'heart-pulse', 'color' => '#ef4444'],
            ['name' => 'Shopping',    'type' => 'expense', 'icon' => 'shopping-bag','color' => '#ec4899'],
            ['name' => 'Education',   'type' => 'expense', 'icon' => 'book-open',   'color' => '#6366f1'],
            ['name' => 'Bills',       'type' => 'expense', 'icon' => 'receipt',     'color' => '#f97316'],
            ['name' => 'Entertainment','type'=> 'expense', 'icon' => 'tv-2',        'color' => '#a855f7'],
            ['name' => 'Other Expense','type'=> 'expense', 'icon' => 'minus-circle','color' => '#6b7280'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name'], 'user_id' => null],
                array_merge($cat, ['user_id' => null, 'is_system' => true, 'is_active' => true])
            );
        }
    }
}
