<?php

namespace App\Enums;

enum CancellationReason: string
{
    case PaymentExpired = 'payment_expired';
    case PaymentFailed = 'payment_failed';
    case CustomerAbandoned = 'customer_abandoned';
    case AdminCancelled = 'admin_cancelled';
    case Manual = 'manual';

    /**
     * Human-friendly label for customer communications.
     */
    public function label(): string
    {
        return match ($this) {
            self::PaymentExpired => 'Session Checkout Expired',
            self::PaymentFailed => 'Payment Authorization Failed',
            self::CustomerAbandoned => 'Checkout Abandoned',
            self::AdminCancelled => 'Cancelled by TopGrade Match Desk',
            self::Manual => 'Booking Cancelled',
        };
    }

    /**
     * Clear customer-facing explanation.
     */
    public function customerExplanation(): string
    {
        return match ($this) {
            self::PaymentExpired => 'Your booking reservation timed out before payment was completed. Your reserved spot has been returned to squad capacity.',
            self::PaymentFailed => 'Your payment provider was unable to authorize the booking transaction. No payment has been processed.',
            self::CustomerAbandoned => 'The checkout session was not completed.',
            self::AdminCancelled => 'This session booking has been cancelled by the TopGrade London FC administration.',
            self::Manual => 'This booking has been cancelled.',
        };
    }
}
