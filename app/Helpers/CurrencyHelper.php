<?php

declare(strict_types=1);

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Get the currency symbol for a given currency code
     */
    public static function getSymbol(string $code): string
    {
        return match (strtoupper($code)) {
            'USD' => '$',
            'PKR' => 'Rs',
            'EUR' => '€',
            'GBP' => '£',
            'AED' => 'د.إ',
            default => $code,
        };
    }

    /**
     * Get the currency name for a given currency code
     */
    public static function getName(string $code): string
    {
        return match (strtoupper($code)) {
            'USD' => 'US Dollar',
            'PKR' => 'Pakistani Rupee',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'AED' => 'UAE Dirham',
            default => $code,
        };
    }

    /**
     * Get all supported currencies
     *
     * @return array<string, array{code: string, name: string, symbol: string}>
     */
    public static function getSupportedCurrencies(): array
    {
        return [
            'USD' => [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
            ],
            'PKR' => [
                'code' => 'PKR',
                'name' => 'Pakistani Rupee',
                'symbol' => 'Rs',
            ],
            'EUR' => [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
            ],
            'GBP' => [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
            ],
            'AED' => [
                'code' => 'AED',
                'name' => 'UAE Dirham',
                'symbol' => 'د.إ',
            ],
        ];
    }

    /**
     * Format an amount with currency symbol
     */
    public static function format(float|int $amount, string $currencyCode, bool $useSymbol = true): string
    {
        $formattedAmount = number_format((float) $amount, 2);
        $display = $useSymbol ? self::getSymbol($currencyCode) : $currencyCode;

        // For currencies where symbol comes after (like PKR), adjust formatting
        return match (strtoupper($currencyCode)) {
            'PKR', 'EUR' => $formattedAmount.' '.$display,
            default => $display.' '.$formattedAmount,
        };
    }
}
