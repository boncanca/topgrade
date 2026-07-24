<?php

use App\Models\Inquiry;

test('can create inquiry', function () {
    $inquiry = Inquiry::create([
        'name' => 'John Smith',
        'email' => 'john@example.com',
        'phone' => '07123456789',
        'subject' => 'Training Inquiry',
        'message' => 'I would like to know more about the academy programs.',
        'status' => 'new',
    ]);

    $this->assertDatabaseHas('inquiries', [
        'id' => $inquiry->id,
        'name' => 'John Smith',
        'email' => 'john@example.com',
        'status' => 'new',
    ]);
});

test('inquiry has default status of new', function () {
    $inquiry = Inquiry::create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'subject' => 'Coaching Question',
        'message' => 'Do you offer one-on-one coaching?',
    ]);

    expect($inquiry->status->value)->toBe('new');
});

test('can mark inquiry as open', function () {
    $inquiry = Inquiry::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'subject' => 'Test Subject',
        'message' => 'Test message',
    ]);

    $inquiry->update(['status' => 'open']);

    $this->assertDatabaseHas('inquiries', [
        'id' => $inquiry->id,
        'status' => 'open',
    ]);
});

test('can mark inquiry as resolved', function () {
    $inquiry = Inquiry::create([
        'name' => 'Another User',
        'email' => 'another@example.com',
        'subject' => 'Feedback',
        'message' => 'Great service!',
    ]);

    $inquiry->update(['status' => 'resolved']);

    $this->assertDatabaseHas('inquiries', [
        'id' => $inquiry->id,
        'status' => 'resolved',
    ]);
});

test('inquiry includes timestamps', function () {
    $inquiry = Inquiry::create([
        'name' => 'Test Person',
        'email' => 'test.person@example.com',
        'subject' => 'Information Request',
        'message' => 'Please send more information',
    ]);

    expect($inquiry->created_at)->not->toBeNull();
    expect($inquiry->updated_at)->not->toBeNull();
});
