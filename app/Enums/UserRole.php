<?php

namespace App\Enums;

enum UserRole: string
{
    case User       = 'user';
    case Superadmin = 'superadmin';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match($this) {
            UserRole::User       => 'User',
            UserRole::Superadmin => 'Super Admin',
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
