<?php

namespace App\Support;

class Money
{
    /**
     * Convert a decimal amount to currency minor units (e.g. £25.00 -> 2500 pence).
     */
    public static function toMinorUnits(float|int|string|null $amount, string $currency = 'GBP'): int
    {
        $numeric = (float) ($amount ?? 0);

        // Zero-decimal currencies can be added here if needed; GBP/EUR/USD use 100 minor units
        $multiplier = match (strtoupper($currency)) {
            'JPY', 'KRW', 'UGX', 'VND' => 1,
            'BHD', 'JOD', 'KWD', 'OMR', 'TND' => 1000,
            default => 100,
        };

        return (int) round($numeric * $multiplier);
    }

    /**
     * Convert minor units back to decimal float (e.g. 2500 -> 25.00).
     */
    public static function fromMinorUnits(int $minorUnits, string $currency = 'GBP'): float
    {
        $divisor = match (strtoupper($currency)) {
            'JPY', 'KRW', 'UGX', 'VND' => 1,
            'BHD', 'JOD', 'KWD', 'OMR', 'TND' => 1000,
            default => 100,
        };

        return round($minorUnits / $divisor, 2);
    }
}
