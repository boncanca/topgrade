<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Schedule;

test('homepage loads with featured activities and dynamic content page', function () {
    $activities = BookableItem::factory()->count(5)->create(['is_active' => true]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/Home')
        ->has('featuredActivities', 3)
        ->has('page')
        ->has('blocks')
    );
});

test('activities listing page loads', function () {
    $activities = BookableItem::factory()->count(15)->create(['is_active' => true]);

    $response = $this->get('/bookings');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/Activities')
        ->has('activities')
    );
});

test('training page loads with active activities', function () {
    BookableItem::factory()->count(3)->create(['is_active' => true]);

    $response = $this->get('/training');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/Training')
        ->has('activities')
    );
});

test('sitemap xml generates valid response', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertStatus(200);
    expect($response->getContent())->toContain('urlset')
        ->toContain('/training')
        ->toContain('/bookings');
});

test('activity detail page loads with available schedules', function () {
    $activity = BookableItem::factory()->create([
        'slug' => 'test-activity',
        'is_active' => true,
    ]);
    $schedule = Schedule::factory()->for($activity)->create(['status' => 'active']);

    $response = $this->get("/bookings/{$activity->slug}");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/ActivityDetail')
        ->has('activity')
        ->has('schedules')
    );
});

test('can create a booking as guest with schedule', function () {
    $activity = BookableItem::factory()->create(['is_active' => true]);
    $schedule = Schedule::factory()->for($activity)->create(['status' => 'active']);

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'John Doe',
        'participant_email' => 'john@example.com',
        'participant_phone' => '+1234567890',
        'timezone' => 'UTC',
        'notes' => 'Test booking',
    ]);

    $response->assertRedirect();

    $booking = Booking::where('participant_email', 'john@example.com')->first();
    $this->assertNotNull($booking);
    $this->assertEquals($activity->id, $booking->bookable_item_id);
    $this->assertEquals($schedule->id, $booking->schedule_id);
    $this->assertEquals(BookingStatus::Pending, $booking->status);
    $this->assertEquals(PaymentStatus::Unpaid, $booking->payment_status);
    $this->assertNotNull($booking->reference);
});

test('booking creates or matches contact by email', function () {
    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create(['status' => 'active']);

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Jane Smith',
        'participant_email' => 'jane@example.com',
        'participant_phone' => '+1987654321',
        'timezone' => 'UTC',
    ]);

    $response->assertRedirect();

    $contact = Contact::where('email', 'jane@example.com')->first();
    $this->assertNotNull($contact);
    $this->assertEquals('Jane', $contact->first_name);
    $this->assertEquals('Smith', $contact->last_name);
    $this->assertEquals('+1987654321', $contact->phone);

    $booking = Booking::where('participant_email', 'jane@example.com')->first();
    $this->assertNotNull($booking);
    $this->assertEquals($contact->id, $booking->contact_id);
});

test('booking reuses existing contact if email matches', function () {
    $existingContact = Contact::factory()->create([
        'email' => 'existing@example.com',
        'first_name' => 'Existing',
        'last_name' => 'User',
        'phone' => null,
    ]);

    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create(['status' => 'active']);

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Existing User Updated',
        'participant_email' => 'existing@example.com',
        'participant_phone' => '+2222222222',
        'timezone' => 'UTC',
    ]);

    $response->assertRedirect();

    $updatedContact = Contact::find($existingContact->id);
    $this->assertEquals(1, Contact::where('email', 'existing@example.com')->count());
    $this->assertEquals('+2222222222', $updatedContact->phone);

    $booking = Booking::where('participant_email', 'existing@example.com')->first();
    $this->assertNotNull($booking);
    $this->assertEquals($existingContact->id, $booking->contact_id);
});

test('booking confirmation page shows correct details', function () {
    $activity = BookableItem::factory()->create(['name' => 'Test Activity']);
    $schedule = Schedule::factory()->for($activity)->create();
    $contact = Contact::factory()->create();
    $booking = Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'contact_id' => $contact->id,
        'participant_name' => 'Jane Smith',
        'participant_email' => 'jane@example.com',
    ]);

    $response = $this->get("/bookings/confirmation/{$booking->reference}");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/BookingConfirmation')
        ->has('booking')
        ->where('booking.participant_name', 'Jane Smith')
    );
});

test('booking rejects when schedule is at capacity', function () {
    $activity = BookableItem::factory()->create(['capacity' => 20]);
    $schedule = Schedule::factory()->for($activity)->create([
        'capacity' => 2,
        'status' => 'active',
    ]);

    Booking::factory(2)->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => 'confirmed',
    ]);

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Over Capacity',
        'participant_email' => 'overcapacity@example.com',
        'timezone' => 'UTC',
    ]);

    $response->assertSessionHasErrors('schedule_id');
});

test('booking rejects cancelled schedule', function () {
    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create(['status' => 'cancelled']);

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'John Doe',
        'participant_email' => 'john@example.com',
        'timezone' => 'UTC',
    ]);

    $response->assertSessionHasErrors('schedule_id');
});

test('booking rejects past schedule', function () {
    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create([
        'status' => 'active',
        'starts_at' => now()->subDays(1),
    ]);

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'John Doe',
        'participant_email' => 'john@example.com',
        'timezone' => 'UTC',
    ]);

    $response->assertSessionHasErrors('schedule_id');
});

test('booking validation requires participant name', function () {
    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => '',
        'participant_email' => 'john@example.com',
        'timezone' => 'UTC',
    ]);

    $response->assertSessionHasErrors('participant_name');
});

test('booking validation requires valid email', function () {
    $activity = BookableItem::factory()->create();
    $schedule = Schedule::factory()->for($activity)->create();

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'John Doe',
        'participant_email' => 'invalid-email',
        'timezone' => 'UTC',
    ]);

    $response->assertSessionHasErrors('participant_email');
});

test('booking reference is unique', function () {
    $activity = BookableItem::factory()->create();
    $schedule1 = Schedule::factory()->for($activity)->create(['status' => 'active']);
    $schedule2 = Schedule::factory()->for($activity)->create(['status' => 'active']);

    $response1 = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule1->id,
        'participant_name' => 'John Doe',
        'participant_email' => 'john@example.com',
        'timezone' => 'UTC',
    ]);

    $response2 = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule2->id,
        'participant_name' => 'Jane Doe',
        'participant_email' => 'jane@example.com',
        'timezone' => 'UTC',
    ]);

    $response1->assertRedirect();
    $response2->assertRedirect();

    $bookings = Booking::all();
    $this->assertCount(2, $bookings);
    $this->assertNotEquals($bookings[0]->reference, $bookings[1]->reference);
});

test('user can submit contact form inquiry', function () {
    $response = $this->post('/contact', [
        'name' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'phone' => '+44 7123 456789',
        'company' => 'Topgrade Athletics',
        'position' => 'Head Coach',
        'subject' => 'general',
        'message' => 'Hello, I have a question about academy training schedules.',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('inquiries', [
        'email' => 'jane.smith@example.com',
        'name' => 'Jane Smith',
        'subject' => 'general',
    ]);
    $this->assertDatabaseHas('contacts', [
        'email' => 'jane.smith@example.com',
        'company' => 'Topgrade Athletics',
        'position' => 'Head Coach',
    ]);
});

test('activity detail page does not auto-create schedules when none exist', function () {
    $activity = BookableItem::factory()->create([
        'slug' => 'empty-activity',
        'is_active' => true,
    ]);

    $response = $this->get("/bookings/{$activity->slug}");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/ActivityDetail')
        ->has('activity')
        ->has('schedules', 0)
    );

    expect(Schedule::where('bookable_item_id', $activity->id)->count())->toBe(0);
});

test('scheduled_at is derived from schedule and cannot be overridden by client', function () {
    $activity = BookableItem::factory()->create(['is_active' => true]);
    $startsAt = now()->addDays(5)->setHour(14)->setMinute(0)->setSecond(0);
    $schedule = Schedule::factory()->for($activity)->create([
        'starts_at' => $startsAt,
        'status' => 'active',
    ]);

    $clientAttemptedDate = now()->addDays(30)->toIso8601String();

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'John Doe',
        'participant_email' => 'johnderived@example.com',
        'scheduled_at' => $clientAttemptedDate,
        'timezone' => 'UTC',
    ]);

    $response->assertRedirect();

    $booking = Booking::where('participant_email', 'johnderived@example.com')->first();
    expect($booking)->not->toBeNull();
    expect($booking->scheduled_at->toIso8601String())->toBe($startsAt->toIso8601String());
    expect($booking->scheduled_at->toIso8601String())->not->toBe($clientAttemptedDate);
});

test('booking rejects schedule belonging to another activity', function () {
    $activity1 = BookableItem::factory()->create(['is_active' => true]);
    $activity2 = BookableItem::factory()->create(['is_active' => true]);
    $schedule2 = Schedule::factory()->for($activity2)->create(['status' => 'active']);

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity1->id,
        'schedule_id' => $schedule2->id,
        'participant_name' => 'Cross Activity Attempter',
        'participant_email' => 'cross@example.com',
        'timezone' => 'UTC',
    ]);

    $response->assertSessionHasErrors('schedule_id');
    expect(Booking::where('participant_email', 'cross@example.com')->exists())->toBeFalse();
});

test('completing a booking does not release capacity', function () {
    $activity = BookableItem::factory()->create(['capacity' => 10]);
    $schedule = Schedule::factory()->for($activity)->create([
        'capacity' => 1,
        'status' => 'active',
    ]);

    Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Completed,
    ]);

    expect($schedule->isFull())->toBeTrue();

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Over Capacity User',
        'participant_email' => 'overcapacityuser@example.com',
        'timezone' => 'UTC',
    ]);

    $response->assertSessionHasErrors('schedule_id');
});

test('cancelled booking frees capacity for new booking', function () {
    $activity = BookableItem::factory()->create(['capacity' => 10]);
    $schedule = Schedule::factory()->for($activity)->create([
        'capacity' => 1,
        'status' => 'active',
    ]);

    Booking::factory()->for($schedule)->create([
        'bookable_item_id' => $activity->id,
        'status' => BookingStatus::Cancelled,
    ]);

    expect($schedule->isFull())->toBeFalse();

    $response = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'New Customer',
        'participant_email' => 'newcustomer@example.com',
        'timezone' => 'UTC',
    ]);

    $response->assertRedirect();
    expect(Booking::where('participant_email', 'newcustomer@example.com')->exists())->toBeTrue();
});

test('activities alias route loads activities listing page', function () {
    $response = $this->get('/activities');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/Activities')
        ->has('activities')
    );
});

test('privacy policy page loads', function () {
    $response = $this->get('/privacy');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/Privacy')
        ->has('page')
    );
});

test('terms and conditions page loads', function () {
    $response = $this->get('/terms');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/Terms')
        ->has('page')
    );
});

test('articles listing page loads', function () {
    $response = $this->get('/articles');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/Articles/Index')
        ->has('articles')
    );
});

test('single article page loads when published', function () {
    $articleType = ContentType::firstOrCreate(
        ['slug' => 'article'],
        ['name' => 'Article', 'kind' => 'collection', 'template' => 'article', 'is_system' => true, 'is_active' => true]
    );

    $article = Content::create([
        'content_type_id' => $articleType->id,
        'title' => 'TopGrade London FC Season Kickoff',
        'slug' => 'topgrade-london-fc-season-kickoff',
        'excerpt' => 'Welcome to the new club season.',
        'content' => 'Full article content for club members.',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->get("/articles/{$article->slug}");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Public/Articles/Show')
        ->has('article')
    );
});

test('single article returns 404 if draft or not found', function () {
    $response = $this->get('/articles/non-existent-article');

    $response->assertStatus(404);
});
