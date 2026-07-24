@extends('mail.layout')

@section('content')
<h2>Booking Received</h2>

<p>Dear {{ $booking->participant_name }},</p>

<p>Thank you for your booking request. We have received your submission and will review it shortly. Below are the details of your booking:</p>

<div class="detail-box">
    <strong>Booking Reference:</strong> {{ $booking->reference }}<br>
    <strong>Activity:</strong> {{ $booking->bookableItem->name }}<br>
    <strong>Scheduled Date:</strong> {{ $booking->scheduled_at->format('F j, Y') }}<br>
    <strong>Time:</strong> {{ $booking->scheduled_at->format('g:i A') }}<br>
    <strong>Status:</strong> {{ ucfirst($booking->status->value) }}
</div>

<p>We will contact you shortly at {{ $booking->participant_email }} or {{ $booking->participant_phone }} to confirm the details and any additional information.</p>

<p>If you have any questions about your booking, please don't hesitate to reach out to us.</p>

<p>Best regards,<br>
<strong>{{ config('app.name') }} Team</strong></p>
@endsection
