<?php

use App\Mail\BookingConfirmed;
use App\Mail\ContactReceivedAdminNotification;
use App\Mail\ContactReceivedCustomerNotification;
use App\Mail\NewBookingAdminNotification;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Inquiry;
use App\Models\Schedule;
use App\Services\PaymentService;
use App\Services\StripePaymentService;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

test('homepage renders public home component', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
        );
});

test('public about page renders correctly', function () {
    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/About')
        );
});

test('public contact page renders correctly', function () {
    $this->get('/contact')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Contact')
        );
});

test('contact form submission creates records and sends notifications', function () {
    Mail::fake();

    $response = $this->post('/contact', [
        'name' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'phone' => '07700900000',
        'company' => 'Local School',
        'position' => 'Head of PE',
        'subject' => 'Youth Squad Trials',
        'message' => 'Hello, I would like to inquire about booking trials for our U11 players.',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseHas('contacts', [
        'email' => 'jane.smith@example.com',
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'phone' => '07700900000',
    ]);

    $inquiry = Inquiry::where('email', 'jane.smith@example.com')->first();
    expect($inquiry)->not->toBeNull();
    expect($inquiry->subject)->toBe('Youth Squad Trials');
    expect($inquiry->status->value)->toBe('new');

    Mail::assertQueued(ContactReceivedAdminNotification::class, function ($mail) use ($inquiry) {
        return $mail->hasTo(config('topgrade.emails.info', 'info@topgradelondonfc.co.uk')) &&
            $mail->inquiry->id === $inquiry->id;
    });

    Mail::assertQueued(ContactReceivedCustomerNotification::class, function ($mail) use ($inquiry) {
        return $mail->hasTo('jane.smith@example.com') &&
            $mail->inquiry->id === $inquiry->id;
    });
});

test('contact form prevents duplicate rapid submissions', function () {
    Mail::fake();

    $payload = [
        'name' => 'Duplicate Tester',
        'email' => 'duplicate@example.com',
        'subject' => 'Trial Question',
        'message' => 'Testing duplicate submission guard.',
    ];

    $this->post('/contact', $payload)->assertRedirect();
    expect(Inquiry::where('email', 'duplicate@example.com')->count())->toBe(1);

    // Second immediate submission with identical content
    $this->post('/contact', $payload)->assertRedirect();
    expect(Inquiry::where('email', 'duplicate@example.com')->count())->toBe(1);
});

test('cms privacy page renders published content', function () {
    $pageType = ContentType::firstOrCreate(['slug' => 'page'], [
        'name' => 'Page',
        'kind' => 'collection',
        'template' => 'default',
        'is_system' => true,
        'is_active' => true,
    ]);

    Content::updateOrCreate(
        ['slug' => 'privacy'],
        [
            'content_type_id' => $pageType->id,
            'title' => 'Privacy Policy',
            'excerpt' => 'Privacy notice',
            'content' => '<p>Published privacy policy test</p>',
            'status' => 'published',
            'published_at' => now(),
        ]
    );

    $this->get('/privacy')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Privacy')
            ->has('page')
            ->where('page.slug', 'privacy')
        );
});

test('cms terms page renders published content', function () {
    $pageType = ContentType::firstOrCreate(['slug' => 'page'], [
        'name' => 'Page',
        'kind' => 'collection',
        'template' => 'default',
        'is_system' => true,
        'is_active' => true,
    ]);

    Content::updateOrCreate(
        ['slug' => 'terms'],
        [
            'content_type_id' => $pageType->id,
            'title' => 'Terms & Conditions',
            'excerpt' => 'Club terms',
            'content' => '<p>Published terms test</p>',
            'status' => 'published',
            'published_at' => now(),
        ]
    );

    $this->get('/terms')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Terms')
            ->has('page')
            ->where('page.slug', 'terms')
        );
});

test('unpublished cms pages return 404 on public route', function () {
    $pageType = ContentType::firstOrCreate(['slug' => 'page'], [
        'name' => 'Page',
        'kind' => 'collection',
        'template' => 'default',
        'is_system' => true,
        'is_active' => true,
    ]);

    Content::updateOrCreate(
        ['slug' => 'privacy'],
        [
            'content_type_id' => $pageType->id,
            'title' => 'Draft Privacy Policy',
            'excerpt' => 'Draft',
            'content' => '<p>Draft content</p>',
            'status' => 'draft',
            'published_at' => null,
        ]
    );

    $this->get('/privacy')->assertNotFound();
});

test('training schedule page renders correctly', function () {
    $this->get('/training')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Training')
        );
});

test('sitemap xml endpoint returns valid xml with public routes', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
    $content = $response->getContent();

    expect($content)->toContain('/training');
    expect($content)->toContain('/about');
    expect($content)->toContain('/privacy');
    expect($content)->toContain('/terms');
});

test('central topgrade email configuration resolves correct defaults', function () {
    expect(config('topgrade.emails.info'))->toBe('info@topgradelondonfc.co.uk');
    expect(config('topgrade.emails.no_reply'))->toBe('no-reply@topgradelondonfc.co.uk');
    expect(config('topgrade.emails.bookings'))->toBe('bookings@topgradelondonfc.co.uk');
});

test('stripe payment service foundation is registered in container', function () {
    $service = app(PaymentService::class);
    expect($service)->toBeInstanceOf(StripePaymentService::class);
    expect($service->isConfigured())->toBeBool();
});

test('booking flow dispatches both customer and admin notifications and rejects duplicates', function () {
    Mail::fake();

    $activity = BookableItem::factory()->create(['capacity' => 10, 'is_active' => true]);
    $schedule = Schedule::factory()->for($activity)->create([
        'capacity' => 5,
        'starts_at' => now()->addDays(2),
        'status' => 'active',
    ]);

    $payload = [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Alex Morgan',
        'participant_email' => 'alex.morgan@example.com',
        'participant_phone' => '07700900123',
        'timezone' => 'Europe/London',
        'notes' => 'Intro trial request',
    ];

    $response = $this->post('/bookings', $payload);
    $response->assertRedirect();

    $booking = Booking::where('participant_email', 'alex.morgan@example.com')->first();
    expect($booking)->not->toBeNull();

    // Verify customer notification sent
    Mail::assertQueued(BookingConfirmed::class, function ($mail) use ($booking) {
        return $mail->hasTo('alex.morgan@example.com') &&
            $mail->booking->id === $booking->id;
    });

    // Verify admin notification sent to bookings inbox
    Mail::assertQueued(NewBookingAdminNotification::class, function ($mail) use ($booking) {
        return $mail->hasTo(config('topgrade.emails.bookings', 'bookings@topgradelondonfc.co.uk')) &&
            $mail->booking->id === $booking->id;
    });

    // Submitting duplicate booking on same schedule should fail validation
    $duplicateResponse = $this->post('/bookings', $payload);
    $duplicateResponse->assertSessionHasErrors('participant_email');
});
