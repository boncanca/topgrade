<?php

use App\Enums\InquiryStatusEnum;
use App\Mail\InquiryQuickReply;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('dev admin can send in-panel quick reply to an inquiry', function () {
    Mail::fake();

    $devAdmin = User::factory()->dev()->create();

    $inquiry = Inquiry::create([
        'name' => 'Sarah Connor',
        'email' => 'sarah@example.com',
        'subject' => 'Trial Session Inquiry',
        'message' => 'Can my son join the U10 squad trial?',
        'status' => 'new',
    ]);

    $response = $this->actingAs($devAdmin)->post(route('inquiries.reply', $inquiry), [
        'subject' => 'Re: Trial Session Inquiry',
        'message' => 'We would love to invite your son for a trial session next Tuesday.',
        'status' => 'resolved',
    ]);

    $response->assertRedirect(route('inquiries.show', $inquiry));

    $inquiry->refresh();
    expect($inquiry->reply_message)->toBe('We would love to invite your son for a trial session next Tuesday.')
        ->and($inquiry->replied_by_id)->toBe($devAdmin->id)
        ->and($inquiry->status)->toBe(InquiryStatusEnum::Resolved)
        ->and($inquiry->responded_at)->not->toBeNull();

    Mail::assertQueued(InquiryQuickReply::class, function (InquiryQuickReply $mail) {
        return $mail->hasTo('sarah@example.com')
            && $mail->replyMessage === 'We would love to invite your son for a trial session next Tuesday.'
            && $mail->replySubject === 'Re: Trial Session Inquiry';
    });
});

test('regular admin is forbidden from sending in-panel quick reply', function () {
    Mail::fake();

    $regularAdmin = User::factory()->admin()->create();

    $inquiry = Inquiry::create([
        'name' => 'Alex Miller',
        'email' => 'alex@example.com',
        'subject' => 'General Question',
        'message' => 'What time is training?',
        'status' => 'new',
    ]);

    $response = $this->actingAs($regularAdmin)->post(route('inquiries.reply', $inquiry), [
        'subject' => 'Re: General Question',
        'message' => 'Unauthorized attempt',
        'status' => 'resolved',
    ]);

    $response->assertForbidden();
    Mail::assertNothingQueued();
});

test('quick reply requires a valid message', function () {
    $devAdmin = User::factory()->dev()->create();

    $inquiry = Inquiry::create([
        'name' => 'Tom Davis',
        'email' => 'tom@example.com',
        'subject' => 'Venue inquiry',
        'message' => 'Where do you play?',
        'status' => 'new',
    ]);

    $response = $this->actingAs($devAdmin)->post(route('inquiries.reply', $inquiry), [
        'message' => '',
    ]);

    $response->assertSessionHasErrors(['message']);
});
