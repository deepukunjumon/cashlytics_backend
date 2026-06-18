<?php

namespace App\Enums;

enum TransactionType: string
{
    case Income     = 'income';
    case Expense    = 'expense';
    case Transfer   = 'transfer';
    case Adjustment = 'adjustment';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match($this) {
            TransactionType::Income     => 'Income',
            TransactionType::Expense    => 'Expense',
            TransactionType::Transfer   => 'Transfer',
            TransactionType::Adjustment => 'Adjustment',
        };
    }

    /**
     * Get the color associated with the type.
     */
    public function color(): string
    {
        return match($this) {
            TransactionType::Income     => '#22c55e', // green-500
            TransactionType::Expense    => '#ef4444', // red-500
            TransactionType::Transfer   => '#3b82f6', // blue-500
            TransactionType::Adjustment => '#8b5cf6', // violet-500
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