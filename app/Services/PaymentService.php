<?php

namespace App\Services;

use App\Models\Booking;

interface PaymentService
{
    /**
     * Determine whether the payment provider is configured with credentials.
     */
    public function isConfigured(): bool;

    /**
     * Create a checkout session or payment intent for a booking.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function createCheckoutSession(Booking $booking, array $options = []): array;

    /**
     * Handle incoming payment webhook events.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(array $payload, string $signature): bool;
}
