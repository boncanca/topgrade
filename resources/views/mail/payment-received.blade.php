@extends('mail.layout')

@section('content')
<h2>Payment Received</h2>

<p>Dear {{ $booking->participant_name }},</p>

<p>Thank you! We have received your payment for your booking. Your transaction has been processed successfully.</p>

<div class="detail-box">
    <strong>Booking Reference:</strong> {{ $booking->reference }}<br>
    <strong>Activity:</strong> {{ $booking->bookableItem->name }}<br>
    <strong>Amount:</strong> {{ $booking->currency }} {{ number_format($booking->amount, 2) }}<br>
    <strong>Date:</strong> {{ now()->format('F j, Y') }}
</div>

<p>Your booking is now confirmed, and you should receive a confirmation email shortly with all the details.</p>

<p>Thank you for choosing {{ config('app.name') }}. We look forward to an amazing experience with you!</p>

<p>Best regards,<br>
<strong>{{ config('app.name') }} Team</strong></p>
@endsection
