@extends('mail.layout')

@section('content')
<h2>Booking Cancelled</h2>

<p>Dear {{ $booking->participant_name }},</p>

<p>We wanted to let you know that your booking has been cancelled as requested.</p>

<div class="detail-box">
    <strong>Booking Reference:</strong> {{ $booking->reference }}<br>
    <strong>Activity:</strong> {{ $booking->bookableItem->name }}<br>
    <strong>Cancelled Date:</strong> {{ now()->format('F j, Y') }}
</div>

@if($reason)
<p><strong>Reason for cancellation:</strong> {{ $reason }}</p>
@endif

<p>If you have any questions or would like to rebook in the future, please feel free to contact us.</p>

<p>Best regards,<br>
<strong>{{ config('app.name') }} Team</strong></p>
@endsection
