<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Contact;
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
