<?php

namespace App\Enums;

enum CategoryType: string
{
    case Income  = 'income';
    case Expense = 'expense';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match($this) {
            CategoryType::Income  => 'Income',
            CategoryType::Expense => 'Expense',
        };
    }

    /**
     * Return all enum values as a plain array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}