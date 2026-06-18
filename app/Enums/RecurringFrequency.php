<?php

namespace App\Enums;

enum RecurringFrequency: string
{
    case Daily   = 'daily';
    case Weekly  = 'weekly';
    case Monthly = 'monthly';
    case Yearly  = 'yearly';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match($this) {
            RecurringFrequency::Daily   => 'Daily',
            RecurringFrequency::Weekly  => 'Weekly',
            RecurringFrequency::Monthly => 'Monthly',
            RecurringFrequency::Yearly  => 'Yearly',
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
