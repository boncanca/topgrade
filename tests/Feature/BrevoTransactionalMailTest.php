<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Jobs\SendTransactionalBookingEmailJob;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use App\Services\Mail\BrevoMailService;
use App\Services\Mail\TransactionalMailService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Config::set('services.stripe.key', 'pk_test_sample_key');
    Config::set('services.stripe.secret', 'sk_test_sample_secret');
    Config::set('services.stripe.webhook_secret', 'whsec_sample_webhook_secret');
    Config::set('services.brevo.key', 'xkeysib-test-fake-key-12345');
    Config::set('services.brevo.from_email', 'no-reply@topgradelondonfc.co.uk');
    Config::set('services.brevo.from_name', 'TopGrade London FC');
    Config::set('services.brevo.bookings_from_name', 'TopGrade London FC Bookings');
    Config::set('services.brevo.bookings_email', 'bookings@topgradelondonfc.co.uk');
    Config::set('services.brevo.reply_to_email', 'info@topgradelondonfc.co.uk');
    Cache::flush();
});

if (! function_exists('generateStripeSignatureHeader')) {
    function generateStripeSignatureHeader(string $payload, string $secret, ?int $timestamp = null): string
    {
        $t = $timestamp ?? time();
        $signedPayload = "{$t}.{$payload}";
        $v1 = hash_hmac('sha256', $signedPayload, $secret);

        return "t={$t},v1={$v1}";
    }
}

test('brevo mail service formats transactional payload and authenticates via api-key header', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response([
            'messageId' => '<20260919.topgrade.test.001@smtp-relay.brevo.com>',
        ], 201),
    ]);

    $activity = BookableItem::factory()->create(['name' => 'Elite Academy Session', 'price' => 30.00]);
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'participant_name' => 'Bukayo Saka',
        'participant_email' => 'bukayo.saka@example.com',
        'status' => BookingStatus::Confirmed,
        'payment_status' => PaymentStatus::Paid,
    ]);

    /** @var BrevoMailService $service */
    $service = app(TransactionalMailService::class);
    $sent = $service->sendBookingConfirmation($booking);

    expect($sent)->toBeTrue();

    Http::assertSent(function ($request) {
        $data = $request->data();

        return $request->url() === 'https://api.brevo.com/v3/smtp/email'
            && $request->hasHeader('api-key', 'xkeysib-test-fake-key-12345')
            && $request->hasHeader('Content-Type', 'application/json')
            && $data['sender']['name'] === 'TopGrade London FC'
            && $data['sender']['email'] === 'no-reply@topgradelondonfc.co.uk'
            && $data['to'][0]['name'] === 'Bukayo Saka'
            && $data['to'][0]['email'] === 'bukayo.saka@example.com'
            && $data['replyTo']['name'] === 'TopGrade London FC'
            && $data['replyTo']['email'] === 'info@topgradelondonfc.co.uk'
            && str_contains($data['subject'], 'Your Booking Is Confirmed')
            && in_array('topgrade', $data['tags'], true)
            && in_array('booking_confirmation', $data['tags'], true)
            && ! empty($data['htmlContent']);
    });
});

test('free booking submission triggers customer confirmation and admin notification via brevo', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response(['messageId' => '<msg-free-123>'], 201),
    ]);

    $activity = BookableItem::factory()->create([
        'price' => 0.00,
        'requires_payment' => false,
        'is_active' => true,
    ]);
    $schedule = Schedule::factory()->for($activity)->create(['status' => 'active']);

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Declan Rice',
        'participant_email' => 'declan.rice@example.com',
        'participant_phone' => '+447000000041',
        'timezone' => 'Europe/London',
    ]);

    $booking = Booking::where('participant_email', 'declan.rice@example.com')->firstOrFail();
    $response->assertRedirect(route('sessions.confirmation', $booking->reference));

    // Two emails sent: customer confirmation and admin notice
    Http::assertSentCount(2);

    Http::assertSent(function ($request) {
        return $request['to'][0]['email'] === 'declan.rice@example.com'
            && str_contains($request['subject'], 'Your Booking Is Confirmed');
    });

    Http::assertSent(function ($request) {
        return $request['to'][0]['email'] === 'bookings@topgradelondonfc.co.uk'
            && str_contains($request['subject'], 'New Booking Received');
    });
});

test('stripe webhook checkout.session.completed sends payment receipt and admin notification via brevo', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response(['messageId' => '<msg-paid-456>'], 201),
    ]);

    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
        'participant_name' => 'Martin Odegaard',
        'participant_email' => 'odegaard@example.com',
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_brevo_webhook_1',
        'status' => 'pending',
        'amount' => 40.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
    ]);

    $payloadArray = [
        'id' => 'evt_test_brevo_completed_1',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_brevo_webhook_1',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_brevo_1',
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

    Http::assertSentCount(2);

    Http::assertSent(function ($request) {
        return $request['to'][0]['email'] === 'odegaard@example.com'
            && str_contains($request['subject'], 'Payment Confirmed');
    });

    Http::assertSent(function ($request) {
        return $request['to'][0]['email'] === 'bookings@topgradelondonfc.co.uk'
            && str_contains($request['subject'], 'New Booking Received');
    });
});

test('stripe webhook payment_intent.payment_failed cancels booking and sends clear customer failure email', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response(['messageId' => '<msg-fail-789>'], 201),
    ]);

    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
        'participant_name' => 'Gabriel Jesus',
        'participant_email' => 'jesus@example.com',
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_payment_intent_id' => 'pi_test_fail_101',
        'status' => 'pending',
        'amount' => 35.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
    ]);

    $payloadArray = [
        'id' => 'evt_test_brevo_failed_1',
        'type' => 'payment_intent.payment_failed',
        'data' => [
            'object' => [
                'id' => 'pi_test_fail_101',
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
    expect($booking->status)->toBe(BookingStatus::Cancelled);
    expect($booking->payment_status)->toBe(PaymentStatus::Failed);

    Http::assertSentCount(1);
    Http::assertSent(function ($request) {
        return $request['to'][0]['email'] === 'jesus@example.com'
            && str_contains($request['subject'], 'Payment Authorization Failed')
            && str_contains($request['htmlContent'], 'unable to authorize the booking transaction');
    });
});

test('scheduler and stripe expiry race condition deduplicates and sends exactly one cancellation email', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response(['messageId' => '<msg-expire-race>'], 201),
    ]);

    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
        'payment_expires_at' => now()->subMinutes(5),
        'participant_name' => 'William Saliba',
        'participant_email' => 'saliba@example.com',
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_brevo_race_expiry',
        'status' => 'pending',
        'amount' => 30.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
        'expires_at' => now()->subMinutes(5),
    ]);

    // 1. Console command runs first and expires the booking
    $this->artisan('bookings:expire-pending')->assertExitCode(0);

    Http::assertSentCount(1);
    Http::assertSent(function ($request) {
        return $request['to'][0]['email'] === 'saliba@example.com'
            && str_contains($request['subject'], 'Booking Cancelled');
    });

    // 2. Stripe checkout.session.expired webhook arrives second
    $payloadArray = [
        'id' => 'evt_test_brevo_race_expired_2',
        'type' => 'checkout.session.expired',
        'data' => [
            'object' => [
                'id' => 'cs_test_brevo_race_expiry',
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

    // ZERO additional emails dispatched
    Http::assertSentCount(1);
});

test('email job is idempotent and prevents duplicate sends across worker retries', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response(['messageId' => '<msg-idempotent-1>'], 201),
    ]);

    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Confirmed,
        'payment_status' => PaymentStatus::Paid,
        'participant_name' => 'Jurrien Timber',
        'participant_email' => 'timber@example.com',
    ]);

    $job = new SendTransactionalBookingEmailJob($booking->id, 'payment-received');
    $service = app(TransactionalMailService::class);

    // First attempt
    $job->handle($service);
    Http::assertSentCount(1);

    // Second worker execution (simulating job retry after worker crash or duplicate event)
    $job->handle($service);
    Http::assertSentCount(1); // Still exactly 1
});

test('brevo http failure is isolated and does not roll back or fail the stripe webhook', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response(['message' => 'Internal Service Error'], 500),
    ]);

    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Pending,
        'payment_status' => PaymentStatus::Unpaid,
        'participant_name' => 'Kai Havertz',
        'participant_email' => 'havertz@example.com',
    ]);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'reference' => Payment::generateReference(),
        'gateway_session_id' => 'cs_test_brevo_error_isolation',
        'status' => 'pending',
        'amount' => 45.00,
        'currency' => 'GBP',
        'customer_email' => $booking->participant_email,
    ]);

    $payloadArray = [
        'id' => 'evt_test_brevo_error_isolation',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_brevo_error_isolation',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_brevo_error',
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

    // Webhook succeeds with 200 despite Brevo 500
    $res = $this->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $sigHeader,
    ], $rawPayload);
    $res->assertStatus(200);

    // Database state is safely confirmed
    $booking->refresh();
    $payment->refresh();
    expect($booking->status)->toBe(BookingStatus::Confirmed);
    expect($booking->payment_status)->toBe(PaymentStatus::Paid);
    expect($payment->status)->toBe('succeeded');
});

test('missing brevo api key in production throws runtime exception', function () {
    Config::set('services.brevo.key', null);

    $service = new BrevoMailService(apiKey: '');

    // Temporarily mock environment to production
    app()->detectEnvironment(fn () => 'production');

    $booking = Booking::factory()->create();

    expect(fn () => $service->sendBookingConfirmation($booking))
        ->toThrow(RuntimeException::class, 'BREVO_API_KEY is not configured for production');
});
