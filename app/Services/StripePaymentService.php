<?php

namespace App\Services;

use App\Models\Booking;
use RuntimeException;

class StripePaymentService implements PaymentService
{
    /**
     * Determine if Stripe API credentials are configured.
     */
    public function isConfigured(): bool
    {
        return ! empty(config('services.stripe.secret')) && ! empty(config('services.stripe.key'));
    }

    /**
     * Create a Stripe Checkout session foundation for a booking.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function createCheckoutSession(Booking $booking, array $options = []): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Stripe credentials (STRIPE_KEY and STRIPE_SECRET) are not configured.');
        }

        // Foundation structure for Stripe Checkout session integration
        return [
            'booking_id' => $booking->id,
            'reference' => $booking->reference,
            'status' => 'pending_payment_gateway',
            'amount' => (int) round(($booking->bookableItem?->price ?? 0) * 100),
            'currency' => strtolower($booking->bookableItem?->currency ?? 'gbp'),
            'customer_email' => $booking->participant_email,
        ];
    }

    /**
     * Handle incoming Stripe webhook event signatures and payloads.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(array $payload, string $signature): bool
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        if (empty($webhookSecret)) {
            throw new RuntimeException('Stripe webhook secret (STRIPE_WEBHOOK_SECRET) is not configured.');
        }

        // Webhook verification foundation
        return true;
    }
}
