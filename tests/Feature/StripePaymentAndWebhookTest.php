<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Mail\BookingConfirmed;
use App\Mail\NewBookingAdminNotification;
use App\Mail\PaymentReceived;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Config::set('services.stripe.key', 'pk_test_sample_key');
    Config::set('services.stripe.secret', 'sk_test_sample_secret');
    Config::set('services.stripe.webhook_secret', 'whsec_sample_webhook_secret');
});

function generateStripeSignatureHeader(string $payload, string $secret, ?int $timestamp = null): string
{
    $t = $timestamp ?? time();
    $signedPayload = "{$t}.{$payload}";
    $v1 = hash_hmac('sha256', $signedPayload, $secret);

    return "t={$t},v1={$v1}";
}

test('free booking confirms immediately with payment_status not_required and dispatches emails', function () {
    Mail::fake();

    $activity = BookableItem::factory()->create([
        'price' => 0.00,
        'requires_payment' => false,
        'is_active' => true,
    ]);
    $schedule = Schedule::factory()->for($activity)->create(['status' => 'active']);

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Free Trial Player',
        'participant_email' => 'freetrial@example.com',
        'participant_phone' => '+447000000001',
        'timezone' => 'Europe/London',
    ]);

    $booking = Booking::where('participant_email', 'freetrial@example.com')->firstOrFail();
    $response->assertRedirect(route('sessions.confirmation', $booking->reference));

    expect($booking->status)->toBe(BookingStatus::Confirmed);
    expect($booking->payment_status)->toBe(PaymentStatus::NotRequired);
    expect($booking->payment_expires_at)->toBeNull();

    Mail::assertQueued(BookingConfirmed::class, fn ($mail) => $mail->hasTo('freetrial@example.com'));
    Mail::assertQueued(NewBookingAdminNotification::class, fn ($mail) => $mail->hasTo(config('topgrade.emails.bookings')));
});

test('paid booking creates pending booking with payment_expires_at and initiates stripe checkout with idempotency key', function () {
    Mail::fake();

    Http::fake([
        'https://api.stripe.com/v1/checkout/sessions' => Http::response([
            'id' => 'cs_test_session_abc123',
            'url' => 'https://checkout.stripe.com/c/pay/cs_test_session_abc123',
        ], 200),
    ]);

    $activity = BookableItem::factory()->create([
        'name' => 'Weekly Elite Training',
        'price' => 25.00,
        'currency' => 'GBP',
        'requires_payment' => true,
        'is_active' => true,
    ]);
    $schedule = Schedule::factory()->for($activity)->create(['status' => 'active']);

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Paid Athlete',
        'participant_email' => 'paidathlete@example.com',
        'participant_phone' => '+447000000002',
        'timezone' => 'Europe/London',
    ]);

    $booking = Booking::where('participant_email', 'paidathlete@example.com')->firstOrFail();
    expect($booking->status)->toBe(BookingStatus::Pending);
    expect($booking->payment_status)->toBe(PaymentStatus::Unpaid);
    expect($booking->payment_expires_at)->not->toBeNull();
    expect($booking->amount)->toEqual(25.00);

    // Internal payment record created
    $payment = Payment::where('booking_id', $booking->id)->firstOrFail();
    expect($payment->status)->toBe('pending');
    expect($payment->gateway_session_id)->toBe('cs_test_session_abc123');
    expect($payment->reference)->toStartWith('PAY');

    // Invariant: No confirmation emails sent until payment verified by webhook
    Mail::assertNothingQueued();

    // Verify Stripe HTTP call received Idempotency-Key and minor units (2500 pence)
    Http::assertSent(function ($request) use ($payment) {
        return $request->url() === 'https://api.stripe.com/v1/checkout/sessions' &&
            $request->header('Idempotency-Key')[0] === 'topgrade-checkout-'.$payment->reference &&
            $request['client_reference_id'] === $payment->reference &&
            (int) $request['line_items'][0]['price_data']['unit_amount'] === 2500;
    });
});

test('browser returning from checkout does not mark booking as confirmed or paid', function () {
    $activity = BookableItem::factory()->create(['requires_payment' => true, 'price' => 20.00]);
    $schedule = Schedule::factory()->for($activity)->create();

    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
        'payment_expires_at' => now()->addMinutes(20),
    ]);

    // Returning to confirmation URL with query parameters does NOT confirm booking
    $response = $this->get(route('sessions.confirmation', $booking->reference).'?session_id=cs_test_mock');
    $response->assertOk();

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Pending);
    expect($booking->payment_status)->toBe(PaymentStatus::Unpaid);
});

test('pending booking reserves schedule capacity until payment_expires_at', function () {
    $activity = BookableItem::factory()->create(['capacity' => 1]);
    $schedule = Schedule::factory()->for($activity)->create([
        'capacity' => 1,
        'status' => 'active',
    ]);

    // Active unexpired pending booking
    Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_expires_at' => now()->addMinutes(15),
    ]);

    expect($schedule->isFull())->toBeTrue();

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Second Visitor',
        'participant_email' => 'second@example.com',
        'timezone' => 'UTC',
    ]);

    $response->assertSessionHasErrors('schedule_id');
});

test('expired pending booking releases schedule capacity for new bookings', function () {
    $activity = BookableItem::factory()->create(['capacity' => 1]);
    $schedule = Schedule::factory()->for($activity)->create([
        'capacity' => 1,
        'status' => 'active',
    ]);

    // Expired pending booking
    Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_expires_at' => now()->subMinutes(5),
    ]);

    expect($schedule->isFull())->toBeFalse();

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'New Visitor',
        'participant_email' => 'newvisitor@example.com',
        'timezone' => 'UTC',
    ]);

    $response->assertRedirect();
    expect(Booking::where('participant_email', 'newvisitor@example.com')->exists())->toBeTrue();
});

test('bookings:expire-pending console command marks expired pending bookings and payments as cancelled', function () {
    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();

    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
        'payment_expires_at' => now()->subMinutes(10),
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway' => 'stripe',
        'status' => 'pending',
        'amount' => 20.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
        'expires_at' => now()->subMinutes(10),
    ]);

    $this->artisan('bookings:expire-pending')
        ->expectsOutputToContain('Expired 1 pending booking(s).')
        ->assertExitCode(0);

    $booking->refresh();
    $payment->refresh();

    expect($booking->status)->toBe(BookingStatus::Cancelled);
    expect($booking->payment_status)->toBe(PaymentStatus::Cancelled);
    expect($payment->status)->toBe('cancelled');
});

test('stripe webhook rejects requests with missing or invalid signature', function () {
    $payload = json_encode(['id' => 'evt_test_1', 'type' => 'checkout.session.completed']);

    // Missing signature
    $responseNoSig = $this->call('POST', '/webhooks/stripe', [], [], [], [], $payload);
    $responseNoSig->assertStatus(400);

    // Invalid signature
    $responseBadSig = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => 't=1234567,v1=invalidsignature',
    ], $payload);
    $responseBadSig->assertStatus(400);
});

test('stripe webhook rejects requests with expired timestamp', function () {
    $payload = json_encode(['id' => 'evt_test_expired', 'type' => 'checkout.session.completed']);
    $expiredTimestamp = time() - 400; // >300s
    $sigHeader = generateStripeSignatureHeader($payload, config('services.stripe.webhook_secret'), $expiredTimestamp);

    $response = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $payload);

    $response->assertStatus(400);
});

test('stripe webhook is strictly idempotent and atomically deduplicates repeated events', function () {
    Mail::fake();

    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_dedup_1',
        'status' => 'pending',
        'amount' => 30.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
    ]);

    $payloadArray = [
        'id' => 'evt_test_dedup_123',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_dedup_1',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_dedup_1',
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'booking_reference' => $booking->reference,
                    'payment_reference' => $payment->reference,
                ],
            ],
        ],
    ];
    $rawPayload = json_encode($payloadArray);
    $sigHeader = generateStripeSignatureHeader($rawPayload, config('services.stripe.webhook_secret'));

    // First webhook delivery
    $res1 = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $rawPayload);
    $res1->assertStatus(200);
    $res1->assertJson(['status' => 'success']);

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Confirmed);
    expect($booking->payment_status)->toBe(PaymentStatus::Paid);
    Mail::assertQueued(PaymentReceived::class, 1);

    // Second webhook delivery (same event_id)
    $res2 = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $rawPayload);
    $res2->assertStatus(200);
    $res2->assertJson(['status' => 'already_processed']);

    // Mails still queued only 1 time total
    Mail::assertQueued(PaymentReceived::class, 1);
});

test('stripe webhook checkout.session.completed confirms booking only when payment_status is paid', function () {
    Mail::fake();

    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_paid_1',
        'status' => 'pending',
        'amount' => 45.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
    ]);

    $payloadArray = [
        'id' => 'evt_test_paid_456',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_paid_1',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_paid_intent',
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'booking_reference' => $booking->reference,
                    'payment_reference' => $payment->reference,
                ],
            ],
        ],
    ];
    $rawPayload = json_encode($payloadArray);
    $sigHeader = generateStripeSignatureHeader($rawPayload, config('services.stripe.webhook_secret'));

    $res = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $rawPayload);
    $res->assertStatus(200);

    $booking->refresh();
    $payment->refresh();

    expect($booking->status)->toBe(BookingStatus::Confirmed);
    expect($booking->payment_status)->toBe(PaymentStatus::Paid);
    expect($payment->status)->toBe('succeeded');
    expect($payment->gateway_payment_intent_id)->toBe('pi_test_paid_intent');

    Mail::assertQueued(PaymentReceived::class, fn ($mail) => $mail->hasTo($booking->participant_email));
    Mail::assertQueued(NewBookingAdminNotification::class, fn ($mail) => $mail->hasTo(config('topgrade.emails.bookings')));
});

test('stripe webhook checkout.session.completed does not confirm booking if payment_status is unpaid', function () {
    Mail::fake();

    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_unpaid_1',
        'status' => 'pending',
        'amount' => 45.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
    ]);

    $payloadArray = [
        'id' => 'evt_test_unpaid_456',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_unpaid_1',
                'payment_status' => 'unpaid', // Async payment method not settled
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'booking_reference' => $booking->reference,
                    'payment_reference' => $payment->reference,
                ],
            ],
        ],
    ];
    $rawPayload = json_encode($payloadArray);
    $sigHeader = generateStripeSignatureHeader($rawPayload, config('services.stripe.webhook_secret'));

    $res = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $rawPayload);
    $res->assertStatus(200);

    $booking->refresh();
    $payment->refresh();

    expect($booking->status)->toBe(BookingStatus::Pending);
    expect($booking->payment_status)->toBe(PaymentStatus::Unpaid);
    expect($payment->status)->toBe('pending');
    Mail::assertNothingQueued();
});

test('stripe webhook checkout.session.expired marks payment cancelled and booking cancelled', function () {
    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_expired_1',
        'status' => 'pending',
        'amount' => 45.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
    ]);

    $payloadArray = [
        'id' => 'evt_test_expired_789',
        'type' => 'checkout.session.expired',
        'data' => [
            'object' => [
                'id' => 'cs_test_expired_1',
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'booking_reference' => $booking->reference,
                    'payment_reference' => $payment->reference,
                ],
            ],
        ],
    ];
    $rawPayload = json_encode($payloadArray);
    $sigHeader = generateStripeSignatureHeader($rawPayload, config('services.stripe.webhook_secret'));

    $res = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $rawPayload);
    $res->assertStatus(200);

    $booking->refresh();
    $payment->refresh();

    expect($payment->status)->toBe('cancelled');
    expect($booking->status)->toBe(BookingStatus::Cancelled);
    expect($booking->payment_status)->toBe(PaymentStatus::Cancelled);
});

test('stripe webhook payment_intent.payment_failed marks payment failed and booking cancelled', function () {
    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_payment_intent_id' => 'pi_test_failed_1',
        'status' => 'pending',
        'amount' => 45.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
    ]);

    $payloadArray = [
        'id' => 'evt_test_failed_999',
        'type' => 'payment_intent.payment_failed',
        'data' => [
            'object' => [
                'id' => 'pi_test_failed_1',
            ],
        ],
    ];
    $rawPayload = json_encode($payloadArray);
    $sigHeader = generateStripeSignatureHeader($rawPayload, config('services.stripe.webhook_secret'));

    $res = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $rawPayload);
    $res->assertStatus(200);

    $booking->refresh();
    $payment->refresh();

    expect($payment->status)->toBe('failed');
    expect($booking->status)->toBe(BookingStatus::Cancelled);
    expect($booking->payment_status)->toBe(PaymentStatus::Failed);
});

test('expiry race convergence: bookings:expire-pending runs first, then stripe checkout.session.expired arrives second', function () {
    $activity = BookableItem::factory()->create(['capacity' => 2]);
    $schedule = Schedule::factory()->for($activity)->create(['capacity' => 2, 'status' => 'active']);

    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
        'payment_expires_at' => now()->subMinutes(5),
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_race_1',
        'status' => 'pending',
        'amount' => 25.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
        'expires_at' => now()->subMinutes(5),
    ]);

    // 1. Application scheduled command runs first
    $this->artisan('bookings:expire-pending')->assertExitCode(0);

    $booking->refresh();
    $payment->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled);
    expect($booking->payment_status)->toBe(PaymentStatus::Cancelled);
    expect($payment->status)->toBe('cancelled');
    expect($schedule->bookedCount())->toBe(0);

    // 2. Stripe checkout.session.expired arrives second
    $payloadArray = [
        'id' => 'evt_test_race_exp_1',
        'type' => 'checkout.session.expired',
        'data' => [
            'object' => [
                'id' => 'cs_test_race_1',
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'booking_reference' => $booking->reference,
                    'payment_reference' => $payment->reference,
                ],
            ],
        ],
    ];
    $rawPayload = json_encode($payloadArray);
    $sigHeader = generateStripeSignatureHeader($rawPayload, config('services.stripe.webhook_secret'));

    $res = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $rawPayload);
    $res->assertStatus(200);

    // State remains cancelled, no errors thrown, capacity count remains exactly 0
    $booking->refresh();
    $payment->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled);
    expect($booking->payment_status)->toBe(PaymentStatus::Cancelled);
    expect($payment->status)->toBe('cancelled');
    expect($schedule->bookedCount())->toBe(0);
});

test('expiry race convergence: stripe checkout.session.expired arrives first, then bookings:expire-pending runs second', function () {
    $activity = BookableItem::factory()->create(['capacity' => 2]);
    $schedule = Schedule::factory()->for($activity)->create(['capacity' => 2, 'status' => 'active']);

    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
        'payment_expires_at' => now()->subMinutes(5),
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_race_2',
        'status' => 'pending',
        'amount' => 25.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
        'expires_at' => now()->subMinutes(5),
    ]);

    // 1. Stripe checkout.session.expired arrives first
    $payloadArray = [
        'id' => 'evt_test_race_exp_2',
        'type' => 'checkout.session.expired',
        'data' => [
            'object' => [
                'id' => 'cs_test_race_2',
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'booking_reference' => $booking->reference,
                    'payment_reference' => $payment->reference,
                ],
            ],
        ],
    ];
    $rawPayload = json_encode($payloadArray);
    $sigHeader = generateStripeSignatureHeader($rawPayload, config('services.stripe.webhook_secret'));

    $res = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $rawPayload);
    $res->assertStatus(200);

    $booking->refresh();
    $payment->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled);
    expect($booking->payment_status)->toBe(PaymentStatus::Cancelled);
    expect($payment->status)->toBe('cancelled');
    expect($schedule->bookedCount())->toBe(0);

    // 2. Application scheduled command runs second
    $this->artisan('bookings:expire-pending')
        ->expectsOutputToContain('Expired 0 pending booking(s).')
        ->assertExitCode(0);

    expect($schedule->bookedCount())->toBe(0);
});

test('replayed or duplicate webhook produces exactly one customer receipt and one admin notification', function () {
    Mail::fake();

    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_replay_99',
        'status' => 'pending',
        'amount' => 50.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
    ]);

    $payload1 = [
        'id' => 'evt_test_replay_first',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_replay_99',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_replay_intent',
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'booking_reference' => $booking->reference,
                    'payment_reference' => $payment->reference,
                ],
            ],
        ],
    ];
    $rawPayload1 = json_encode($payload1);
    $sigHeader1 = generateStripeSignatureHeader($rawPayload1, config('services.stripe.webhook_secret'));

    // First delivery
    $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader1,
    ], $rawPayload1)->assertStatus(200);

    // Second delivery with a distinct event_id (simulating Stripe re-sending or webhook replay)
    $payload2 = $payload1;
    $payload2['id'] = 'evt_test_replay_second';
    $rawPayload2 = json_encode($payload2);
    $sigHeader2 = generateStripeSignatureHeader($rawPayload2, config('services.stripe.webhook_secret'));

    $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader2,
    ], $rawPayload2)->assertStatus(200);

    // Invariant: exactly 1 customer receipt and 1 admin notice dispatched in total
    Mail::assertQueued(PaymentReceived::class, 1);
    Mail::assertQueued(NewBookingAdminNotification::class, 1);
});

test('stripe checkout.session.expired does not cancel an already confirmed booking', function () {
    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();

    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Confirmed,
        'payment_status' => PaymentStatus::Paid,
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_late_expired',
        'status' => 'succeeded',
        'amount' => 50.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
    ]);

    $payload = [
        'id' => 'evt_test_late_exp',
        'type' => 'checkout.session.expired',
        'data' => [
            'object' => [
                'id' => 'cs_test_late_expired',
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'booking_reference' => $booking->reference,
                    'payment_reference' => $payment->reference,
                ],
            ],
        ],
    ];
    $rawPayload = json_encode($payload);
    $sigHeader = generateStripeSignatureHeader($rawPayload, config('services.stripe.webhook_secret'));

    $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $rawPayload)->assertStatus(200);

    $booking->refresh();
    $payment->refresh();

    // Invariant: confirmed booking and succeeded payment are untouched
    expect($booking->status)->toBe(BookingStatus::Confirmed);
    expect($booking->payment_status)->toBe(PaymentStatus::Paid);
    expect($payment->status)->toBe('succeeded');
});
