<?php

use App\Mail\ContactReceivedAdminNotification;
use App\Mail\ContactReceivedCustomerNotification;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Inquiry;
use App\Models\Moment;
use App\Models\Schedule;
use App\Services\Mail\TransactionalMailService;
use App\Services\PaymentService;
use App\Services\SitemapService;
use App\Services\StripePaymentService;
use Illuminate\Support\Facades\Cache;
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

test('cms privacy page renders published content via canonical and alias routes', function () {
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

    // Canonical route
    $this->get('/privacy-policy')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Privacy')
            ->has('page')
            ->where('page.slug', 'privacy')
        );

    // Alias route
    $this->get('/privacy')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Privacy')
            ->has('page')
            ->where('page.slug', 'privacy')
        );
});

test('cms terms page renders published content via canonical and alias routes', function () {
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

    // Canonical route
    $this->get('/terms-and-conditions')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Terms')
            ->has('page')
            ->where('page.slug', 'terms')
        );

    // Alias route
    $this->get('/terms')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Terms')
            ->has('page')
            ->where('page.slug', 'terms')
        );
});

test('cms pages resolve when cms slug uses hyphenated canonical name', function () {
    $pageType = ContentType::firstOrCreate(['slug' => 'page'], [
        'name' => 'Page',
        'kind' => 'collection',
        'template' => 'default',
        'is_system' => true,
        'is_active' => true,
    ]);

    Content::updateOrCreate(
        ['slug' => 'privacy-policy'],
        [
            'content_type_id' => $pageType->id,
            'title' => 'Privacy Policy Canonical Slug',
            'excerpt' => 'Privacy notice',
            'content' => '<p>Canonical slug content</p>',
            'status' => 'published',
            'published_at' => now(),
        ]
    );

    $this->get('/privacy-policy')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Privacy')
            ->where('page.slug', 'privacy-policy')
        );

    $this->get('/privacy')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Privacy')
            ->where('page.slug', 'privacy-policy')
        );
});

test('unpublished cms pages return 404 on public routes', function () {
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

    $this->get('/privacy-policy')->assertNotFound();
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
    expect($content)->toContain('/privacy-policy');
    expect($content)->toContain('/terms-and-conditions');

    // Verify response consistency
    $secondResponse = $this->get('/sitemap.xml');
    $secondResponse->assertOk();
    expect($secondResponse->getContent())->toBe($content);
});

test('sitemap xml dynamic fallback caches rendered output when static file is absent', function () {
    Cache::flush();
    $sitemapService = app(SitemapService::class);
    $xml = Cache::remember('sitemap.xml', now()->addHours(12), fn () => $sitemapService->render());

    expect(Cache::has('sitemap.xml'))->toBeTrue();
    expect($xml)->toContain('<urlset');
    expect($xml)->toContain('/about');
});

test('sitemap generate artisan command writes valid sitemap file to disk', function () {
    $tempPath = storage_path('framework/testing/test_sitemap.xml');
    if (file_exists($tempPath)) {
        unlink($tempPath);
    }

    $this->artisan('sitemap:generate', ['--path' => $tempPath])
        ->assertSuccessful();

    expect(file_exists($tempPath))->toBeTrue();
    $content = file_get_contents($tempPath);
    expect($content)->toContain('<urlset');
    expect($content)->toContain('/training');
    expect($content)->toContain('/about');

    unlink($tempPath);
});

test('sitemap uses configured canonical host for static and dynamic urls', function () {
    config(['app.url' => 'https://topgradelondonfc.co.uk']);
    Cache::forget('sitemap.xml');
    $moment = Moment::factory()->create();

    $response = $this->get('https://untrusted.example/sitemap.xml')->assertOk();
    $xml = simplexml_load_string($response->getContent());
    $locations = $xml->xpath('//*[local-name()="loc"]');

    expect($locations)->not->toBeEmpty();
    foreach ($locations as $location) {
        expect((string) $location)->toStartWith('https://topgradelondonfc.co.uk/');
    }
    expect($response->getContent())
        ->toContain('https://topgradelondonfc.co.uk/moments/'.$moment->slug)
        ->not->toContain('untrusted.example')
        ->not->toContain('topgrade.test');
});

test('sitemap generate refreshes cache without creating a static public sitemap', function () {
    Cache::put('sitemap.xml', 'stale sitemap');

    $this->artisan('sitemap:generate')->assertSuccessful();

    expect(Cache::get('sitemap.xml'))->toContain('<urlset');
    expect(is_file(public_path('sitemap.xml')))->toBeFalse();
    expect($this->get('/sitemap.xml')->assertOk()->getContent())->toBe(Cache::get('sitemap.xml'));
});

test('robots txt file is valid under google robots exclusion protocol spec', function () {
    $robotsPath = public_path('robots.txt');
    expect(file_exists($robotsPath))->toBeTrue();

    $content = file_get_contents($robotsPath);
    expect($content)->not->toBeEmpty();
    expect(strlen($content))->toBeLessThan(500 * 1024); // Within Google's 500 KiB limit
    expect(mb_check_encoding($content, 'UTF-8'))->toBeTrue();

    // Verify standard directives
    expect($content)->toContain('User-agent: *');
    expect($content)->toContain('Allow: /');
    expect($content)->toContain('Disallow: /dashboard');
    expect($content)->toContain('Disallow: /admin');
    expect($content)->toContain('Disallow: /auth/');
    expect($content)->toContain('Disallow: /bookings/confirmation/');
    expect($content)->toContain('Disallow: /webhooks/');
    expect($content)->toContain('Disallow: /settings');
    expect($content)->toContain('Sitemap: https://topgradelondonfc.co.uk/sitemap.xml');
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
    $mailService = Mockery::mock(TransactionalMailService::class);
    $mailService->shouldReceive('sendBookingConfirmation')->once()->andReturn(true);
    $mailService->shouldReceive('sendBookingNotification')->once()->andReturn(true);
    app()->instance(TransactionalMailService::class, $mailService);

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

    // Submitting duplicate booking on same schedule should fail validation
    $duplicateResponse = $this->post('/bookings', $payload);
    $duplicateResponse->assertSessionHasErrors('participant_email');
});
