<?php

namespace App\Enums;

enum AccountType: string
{
    case Cash           = 'cash';
    case CreditCard     = 'credit_card';
    case SavingsAccount = 'savings_account';
    case Investments    = 'investments';
    case Other          = 'other';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match($this) {
            AccountType::Cash           => 'Cash',
            AccountType::CreditCard     => 'Credit Card',
            AccountType::SavingsAccount => 'Savings Account',
            AccountType::Investments    => 'Investments',
            AccountType::Other          => 'Other',
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
