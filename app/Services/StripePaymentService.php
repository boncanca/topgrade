<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Mail\NewBookingAdminNotification;
use App\Mail\PaymentReceived;
use App\Models\Booking;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
     * Create a Stripe Checkout session for a booking with outbound idempotency.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function createCheckoutSession(Booking $booking, array $options = []): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Stripe credentials (STRIPE_KEY and STRIPE_SECRET) are not configured.');
        }

        /** @var Payment $payment */
        $payment = $options['payment'] ?? $booking->latestPayment;

        if (! $payment) {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'reference' => Payment::generateReference(),
                'gateway' => 'stripe',
                'status' => 'pending',
                'amount' => $booking->amount ?? 0,
                'currency' => $booking->currency ?? 'GBP',
                'customer_email' => $booking->participant_email,
                'expires_at' => $booking->payment_expires_at ?? now()->addMinutes(30),
            ]);
        }

        $currency = strtolower($booking->currency ?? 'gbp');
        $unitAmount = Money::toMinorUnits($booking->amount, $currency);
        $itemName = $booking->bookableItem?->name ?? 'TopGrade London FC Training Session';

        // Stripe requires Checkout session expires_at to be between 30 minutes and 24 hours.
        // We set 31 minutes (1860s) to comfortably meet the 1800s minimum without clock drift failure.
        $stripeExpiresAt = time() + 1860;

        $params = [
            'mode' => 'payment',
            'client_reference_id' => $payment->reference,
            'customer_email' => $booking->participant_email,
            'expires_at' => $stripeExpiresAt,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => $unitAmount,
                        'product_data' => [
                            'name' => $itemName,
                            'description' => "Booking reference: {$booking->reference}",
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'metadata' => [
                'booking_id' => (string) $booking->id,
                'booking_reference' => $booking->reference,
                'payment_reference' => $payment->reference,
            ],
            'success_url' => route('sessions.confirmation', $booking->reference).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('sessions.confirmation', $booking->reference).'?cancelled=1',
        ];

        $response = Http::withToken(config('services.stripe.secret'))
            ->withHeaders([
                'Idempotency-Key' => 'topgrade-checkout-'.$payment->reference,
            ])
            ->asForm()
            ->post('https://api.stripe.com/v1/checkout/sessions', $params);

        if (! $response->successful()) {
            $errorMessage = $response->json('error.message') ?? 'Failed to communicate with Stripe API.';
            Log::error('Stripe Checkout session creation failed', [
                'booking_id' => $booking->id,
                'payment_reference' => $payment->reference,
                'status' => $response->status(),
                'error' => $response->json(),
            ]);

            throw new RuntimeException('Stripe Checkout error: '.$errorMessage);
        }

        $sessionData = $response->json();
        $sessionId = (string) ($sessionData['id'] ?? '');
        $sessionUrl = (string) ($sessionData['url'] ?? '');

        $payment->update([
            'gateway_session_id' => $sessionId,
        ]);

        return [
            'id' => $sessionId,
            'url' => $sessionUrl,
            'reference' => $payment->reference,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
        ];
    }

    /**
     * Verify Stripe webhook signature with 300s timestamp tolerance.
     */
    public function verifyWebhookSignature(string $rawPayload, string $signatureHeader): bool
    {
        $secret = config('services.stripe.webhook_secret');

        if (empty($secret)) {
            Log::warning('Stripe webhook received but STRIPE_WEBHOOK_SECRET is not configured.');

            return false;
        }

        $items = explode(',', $signatureHeader);
        $timestamp = null;
        $signatures = [];

        foreach ($items as $item) {
            $parts = explode('=', trim($item), 2);
            if (count($parts) === 2) {
                if ($parts[0] === 't') {
                    $timestamp = (int) $parts[1];
                } elseif ($parts[0] === 'v1') {
                    $signatures[] = $parts[1];
                }
            }
        }

        if (! $timestamp || empty($signatures)) {
            return false;
        }

        // 300 seconds tolerance
        if (abs(time() - $timestamp) > 300) {
            Log::warning('Stripe webhook signature timestamp is outside acceptable 300s tolerance.', [
                'timestamp' => $timestamp,
                'now' => time(),
            ]);

            return false;
        }

        $signedPayload = "{$timestamp}.{$rawPayload}";
        $expectedSignature = hash_hmac('sha256', $signedPayload, $secret);

        foreach ($signatures as $sig) {
            if (hash_equals($expectedSignature, $sig)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle incoming Stripe webhook event.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(array $payload, string $signature = ''): bool
    {
        $eventType = $payload['type'] ?? '';
        $dataObject = $payload['data']['object'] ?? [];

        Log::info("Handling Stripe webhook event: {$eventType}", [
            'id' => $payload['id'] ?? null,
        ]);

        return match ($eventType) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($dataObject),
            'checkout.session.expired' => $this->handleCheckoutSessionExpired($dataObject),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($dataObject),
            default => true,
        };
    }

    /**
     * Handle checkout.session.completed event.
     *
     * @param  array<string, mixed>  $session
     */
    private function handleCheckoutSessionCompleted(array $session): bool
    {
        $paymentStatus = $session['payment_status'] ?? '';

        // Strict invariant: only confirmed if paid
        if ($paymentStatus !== 'paid') {
            Log::info('Stripe Checkout session completed but payment_status is not paid.', [
                'session_id' => $session['id'] ?? null,
                'payment_status' => $paymentStatus,
            ]);

            return true;
        }

        $sessionId = $session['id'] ?? null;
        $paymentRef = $session['metadata']['payment_reference'] ?? null;
        $bookingRef = $session['metadata']['booking_reference'] ?? null;

        /** @var Payment|null $payment */
        $payment = Payment::where('gateway_session_id', $sessionId)
            ->when($paymentRef, fn ($q) => $q->orWhere('reference', $paymentRef))
            ->first();

        /** @var Booking|null $booking */
        $booking = null;

        if ($payment) {
            $booking = $payment->booking;
        } elseif ($bookingRef) {
            $booking = Booking::where('reference', $bookingRef)->first();
        }

        if (! $booking) {
            Log::warning('No booking found for completed Stripe Checkout session.', [
                'session_id' => $sessionId,
                'metadata' => $session['metadata'] ?? [],
            ]);

            return false;
        }

        $wasAlreadyPaid = ($booking->payment_status === PaymentStatus::Paid);

        DB::transaction(function () use ($payment, $booking, $session) {
            $paymentIntentId = $session['payment_intent'] ?? null;

            if ($payment) {
                $payment->update([
                    'status' => 'succeeded',
                    'gateway_payment_intent_id' => $paymentIntentId,
                ]);
            }

            if ($booking->status === BookingStatus::Pending) {
                $booking->update([
                    'status' => BookingStatus::Confirmed->value,
                    'payment_status' => PaymentStatus::Paid->value,
                ]);
            } else {
                $booking->update([
                    'payment_status' => PaymentStatus::Paid->value,
                ]);
            }
        });

        // Dispatch notifications only on initial payment confirmation
        if (! $wasAlreadyPaid) {
            try {
                Mail::to($booking->participant_email)->send(new PaymentReceived($booking));

                $adminEmail = config('topgrade.emails.bookings', 'bookings@topgradelondonfc.co.uk');
                Mail::to($adminEmail)->send(new NewBookingAdminNotification($booking));
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch booking emails after payment confirmation.', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return true;
    }

    /**
     * Handle checkout.session.expired event.
     *
     * @param  array<string, mixed>  $session
     */
    private function handleCheckoutSessionExpired(array $session): bool
    {
        $sessionId = $session['id'] ?? null;
        $paymentRef = $session['metadata']['payment_reference'] ?? null;
        $bookingRef = $session['metadata']['booking_reference'] ?? null;

        $payment = Payment::where('gateway_session_id', $sessionId)
            ->when($paymentRef, fn ($q) => $q->orWhere('reference', $paymentRef))
            ->first();

        $booking = $payment?->booking;
        if (! $booking && $bookingRef) {
            $booking = Booking::where('reference', $bookingRef)->first();
        }

        if ($payment && $payment->status === 'pending') {
            $payment->update(['status' => 'cancelled']);
        }

        if ($booking && $booking->status === BookingStatus::Pending) {
            $booking->update([
                'status' => BookingStatus::Cancelled->value,
                'payment_status' => PaymentStatus::Cancelled->value,
            ]);
            Log::info("Cancelled expired booking reservation {$booking->reference} due to session expiry.");
        }

        return true;
    }

    /**
     * Handle payment_intent.payment_failed event.
     *
     * @param  array<string, mixed>  $paymentIntent
     */
    private function handlePaymentIntentFailed(array $paymentIntent): bool
    {
        $intentId = $paymentIntent['id'] ?? null;

        $payment = Payment::where('gateway_payment_intent_id', $intentId)->first();
        $booking = $payment?->booking;

        if ($payment && $payment->status === 'pending') {
            $payment->update(['status' => 'failed']);
        }

        if ($booking && $booking->status === BookingStatus::Pending) {
            $booking->update([
                'status' => BookingStatus::Cancelled->value,
                'payment_status' => PaymentStatus::Failed->value,
            ]);
            Log::info("Cancelled booking reservation {$booking->reference} due to payment intent failure.");
        }

        return true;
    }
}
