<?php

use App\Enums\BookingStatus;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Schedule;
use App\Models\User;
use App\Services\PaymentService;

test('full operational journey: admin creates activity and schedule, visitor books, admin confirms and completes', function () {
    $admin = User::factory()->admin()->create();

    // 1. Admin creates Activity
    $activityResponse = $this->actingAs($admin)->post('/dashboard/bookable-items', [
        'name' => 'Junior Academy Trial',
        'slug' => 'junior-academy-trial',
        'description' => 'Professional football training trial for youth players.',
        'duration_minutes' => 60,
        'location' => 'Pitch 1 - North Ground',
        'price' => 15.00,
        'currency' => 'GBP',
        'capacity' => 10,
        'booking_label' => 'Book Trial',
        'is_active' => true,
    ]);

    $activityResponse->assertRedirect('/dashboard/bookable-items');
    $activity = BookableItem::where('slug', 'junior-academy-trial')->firstOrFail();
    expect($activity->name)->toBe('Junior Academy Trial');

    // 2. Admin adds a Schedule to that Activity
    $startsAt = now()->addDays(5)->setHour(10)->setMinute(0)->setSecond(0);
    $endsAt = (clone $startsAt)->addMinutes(60);

    $scheduleResponse = $this->actingAs($admin)->post("/dashboard/bookable-items/{$activity->id}/schedules", [
        'starts_at' => $startsAt->format('Y-m-d\TH:i'),
        'ends_at' => $endsAt->format('Y-m-d\TH:i'),
        'capacity' => 5,
        'location' => 'Pitch 1 - North Ground',
        'status' => 'active',
    ]);

    $scheduleResponse->assertRedirect("/dashboard/bookable-items/{$activity->id}/schedules");
    $schedule = Schedule::where('bookable_item_id', $activity->id)->firstOrFail();
    expect($schedule->effectiveCapacity())->toBe(5);
    expect($schedule->isAvailable())->toBeTrue();

    // 3. Public visitor views the Activity page
    $publicPageResponse = $this->get("/bookings/{$activity->slug}");
    $publicPageResponse->assertOk();
    $publicPageResponse->assertInertia(fn ($page) => $page
        ->component('Public/ActivityDetail')
        ->where('activity.name', 'Junior Academy Trial')
        ->has('schedules', 1)
        ->where('schedules.0.id', $schedule->id)
        ->where('schedules.0.available_spots', 5)
    );

    // 4. Visitor submits a booking with contact information
    $mockPayment = Mockery::mock(PaymentService::class);
    $mockPayment->shouldReceive('isConfigured')->andReturn(true);
    $mockPayment->shouldReceive('createCheckoutSession')->andReturnUsing(function ($booking) {
        return [
            'id' => 'cs_test_op',
            'url' => "/bookings/confirmation/{$booking->reference}",
            'reference' => 'PAY-OP123',
        ];
    });
    app()->instance(PaymentService::class, $mockPayment);

    $bookingResponse = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Oliver Taylor',
        'participant_email' => 'oliver.taylor@example.com',
        'participant_phone' => '+44 7911 123456',
        'timezone' => 'Europe/London',
        'notes' => 'Goalkeeper gloves required',
    ]);

    $booking = Booking::where('participant_email', 'oliver.taylor@example.com')->firstOrFail();
    $bookingResponse->assertRedirect("/bookings/confirmation/{$booking->reference}");

    // 5. Booking and Contact linked correctly, scheduled_at derived server-side
    expect($booking->status)->toBe(BookingStatus::Pending);
    expect($booking->scheduled_at->toIso8601String())->toBe($schedule->starts_at->toIso8601String());
    $contact = Contact::where('email', 'oliver.taylor@example.com')->firstOrFail();
    expect($booking->contact_id)->toBe($contact->id);
    expect($contact->first_name)->toBe('Oliver');
    expect($contact->last_name)->toBe('Taylor');

    // 6. Visitor views booking confirmation screen
    $confirmationResponse = $this->get("/bookings/confirmation/{$booking->reference}");
    $confirmationResponse->assertOk();
    $confirmationResponse->assertInertia(fn ($page) => $page
        ->component('Public/BookingConfirmation')
        ->where('booking.reference', $booking->reference)
        ->where('booking.participant_name', 'Oliver Taylor')
        ->where('booking.bookable_item.name', 'Junior Academy Trial')
    );

    // 7. Admin views the Booking in dashboard index and show screens
    $adminIndexResponse = $this->actingAs($admin)->get('/dashboard/bookings');
    $adminIndexResponse->assertOk();
    $adminIndexResponse->assertInertia(fn ($page) => $page
        ->component('Bookings/Index')
        ->has('bookings.data', 1)
        ->where('bookings.data.0.reference', $booking->reference)
    );

    $adminShowResponse = $this->actingAs($admin)->get("/dashboard/bookings/{$booking->id}");
    $adminShowResponse->assertOk();
    $adminShowResponse->assertInertia(fn ($page) => $page
        ->component('Bookings/Show')
        ->where('booking.id', $booking->id)
        ->where('booking.status', 'pending')
    );

    // 8. Admin confirms the booking
    $confirmResponse = $this->actingAs($admin)->post("/dashboard/bookings/{$booking->id}/confirm");
    $confirmResponse->assertRedirect("/dashboard/bookings/{$booking->id}");
    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Confirmed);

    // Public page now reflects 4 available spots (5 - 1 confirmed)
    $updatedPublicResponse = $this->get("/bookings/{$activity->slug}");
    $updatedPublicResponse->assertInertia(fn ($page) => $page
        ->component('Public/ActivityDetail')
        ->where('schedules.0.available_spots', 4)
    );

    // 9. Admin marks booking as complete
    $completeResponse = $this->actingAs($admin)->post("/dashboard/bookings/{$booking->id}/complete");
    $completeResponse->assertRedirect("/dashboard/bookings/{$booking->id}");
    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Completed);
});

test('operational capacity lifecycle: capacity exhaustion, rejection, cancellation, and re-opening', function () {
    $admin = User::factory()->admin()->create();

    $activity = BookableItem::factory()->create([
        'capacity' => 10,
        'is_active' => true,
    ]);

    $startsAt = now()->addDays(3)->setHour(18)->setMinute(0);
    $schedule = Schedule::factory()->for($activity)->create([
        'starts_at' => $startsAt,
        'ends_at' => (clone $startsAt)->addHour(),
        'capacity' => 2,
        'status' => 'active',
    ]);

    // Booking 1 created and confirmed
    $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Player One',
        'participant_email' => 'one@example.com',
        'timezone' => 'UTC',
    ])->assertRedirect();
    $booking1 = Booking::where('participant_email', 'one@example.com')->firstOrFail();
    $this->actingAs($admin)->post("/dashboard/bookings/{$booking1->id}/confirm")->assertRedirect();

    // Booking 2 created and confirmed
    $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Player Two',
        'participant_email' => 'two@example.com',
        'timezone' => 'UTC',
    ])->assertRedirect();
    $booking2 = Booking::where('participant_email', 'two@example.com')->firstOrFail();
    $this->actingAs($admin)->post("/dashboard/bookings/{$booking2->id}/confirm")->assertRedirect();

    // Schedule is now full
    expect($schedule->isFull())->toBeTrue();
    expect($schedule->isAvailable())->toBeFalse();

    // Public page shows 0 available spots
    $this->get("/bookings/{$activity->slug}")
        ->assertInertia(fn ($page) => $page
            ->where('schedules.0.available_spots', 0)
        );

    // Booking 3 attempted by another visitor is rejected due to full capacity
    $rejectedResponse = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Player Three',
        'participant_email' => 'three@example.com',
        'timezone' => 'UTC',
    ]);
    $rejectedResponse->assertSessionHasErrors('schedule_id');
    expect(Booking::where('participant_email', 'three@example.com')->exists())->toBeFalse();

    // Admin cancels Booking 1
    $this->actingAs($admin)->post("/dashboard/bookings/{$booking1->id}/cancel")->assertRedirect();
    $booking1->refresh();
    expect($booking1->status)->toBe(BookingStatus::Cancelled);

    // Capacity is now re-opened (1 spot available)
    expect($schedule->isFull())->toBeFalse();
    expect($schedule->isAvailable())->toBeTrue();

    // Player 3 can now successfully book
    $acceptedResponse = $this->post('/bookings', [
        'bookable_item_id' => $activity->id,
        'schedule_id' => $schedule->id,
        'participant_name' => 'Player Three',
        'participant_email' => 'three@example.com',
        'timezone' => 'UTC',
    ]);
    $acceptedResponse->assertRedirect();
    $booking3 = Booking::where('participant_email', 'three@example.com')->firstOrFail();
    expect($booking3)->not->toBeNull();

    // Admin confirms Booking 3, reaching capacity again
    $this->actingAs($admin)->post("/dashboard/bookings/{$booking3->id}/confirm")->assertRedirect();
    expect($schedule->isFull())->toBeTrue();

    // Admin completes Booking 2 (completed booking maintains capacity lock)
    $this->actingAs($admin)->post("/dashboard/bookings/{$booking2->id}/complete")->assertRedirect();
    $booking2->refresh();
    expect($booking2->status)->toBe(BookingStatus::Completed);
    expect($schedule->isFull())->toBeTrue();
});
