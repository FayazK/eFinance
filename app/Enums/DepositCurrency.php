<?php

declare(strict_types=1);

namespace App\Enums;

enum DepositCurrency: string
{
    case PKR = 'PKR';
    case USD = 'USD';

    /**
     * Get human-readable label for the deposit method
     */
    public function label(): string
    {
        return match ($this) {
            self::PKR => 'PKR - Local Bank Transfer',
            self::USD => 'USD - Payoneer',
        };
    }

    /**
     * Get full description for the deposit method
     */
    public function description(): string
    {
        return match ($this) {
            self::PKR => 'Payment in Pakistani Rupee via local bank transfer',
            self::USD => 'Payment in US Dollar via Payoneer (requires exchange rate)',
        };
    }

    /**
     * Get all deposit methods as array for dropdowns
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function toArray(): array
    {
        return array_map(
            fn (self $method) => [
                'value' => $method->value,
                'label' => $method->label(),
            ],
            self::cases()
        );
    }
}
