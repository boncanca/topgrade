@extends('mail.layout')

@section('content')
<h2>Booking Confirmed</h2>

<p>Dear {{ $booking->participant_name }},</p>

<p>Great news! Your booking has been confirmed. We look forward to seeing you at the activity.</p>

<div class="detail-box">
    <strong>Booking Reference:</strong> {{ $booking->reference }}<br>
    <strong>Activity:</strong> {{ $booking->bookableItem->name }}<br>
    <strong>Scheduled Date:</strong> {{ $booking->scheduled_at->format('F j, Y') }}<br>
    <strong>Time:</strong> {{ $booking->scheduled_at->format('g:i A') }}<br>
    <strong>Location:</strong> {{ $booking->bookableItem->location ?? 'TBD' }}
</div>

<p>Please arrive 15 minutes early. If you need to make any changes or have questions, please contact us as soon as possible.</p>

<p>See you soon!<br>
<strong>{{ config('app.name') }} Team</strong></p>
@endsection
