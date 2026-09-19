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
     * Create a checkout session for a booking with outbound idempotency.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function createCheckoutSession(Booking $booking, array $options = []): array;

    /**
     * Verify the cryptographic webhook signature.
     */
    public function verifyWebhookSignature(string $rawPayload, string $signatureHeader): bool;

    /**
     * Handle incoming payment webhook events.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(array $payload, string $signature = ''): bool;
}
