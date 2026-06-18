<?php

namespace App\Enums;

enum BudgetPeriod: string
{
    case Monthly = 'monthly';
    case Yearly  = 'yearly';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match($this) {
            BudgetPeriod::Monthly => 'Monthly',
            BudgetPeriod::Yearly  => 'Yearly',
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
