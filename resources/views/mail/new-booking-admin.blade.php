@extends('mail.layout')

@section('content')
<h2>New Website Booking Received</h2>

<p>A new booking request has been submitted through the TopGrade London FC website.</p>

<div class="detail-box">
    <strong>Booking Reference:</strong> {{ $booking->reference }}<br>
    <strong>Session / Activity:</strong> {{ $booking->bookableItem?->name }}<br>
    <strong>Participant Name:</strong> {{ $booking->participant_name }}<br>
    <strong>Participant Email:</strong> {{ $booking->participant_email }}<br>
    <strong>Participant Phone:</strong> {{ $booking->participant_phone ?? 'N/A' }}<br>
    <strong>Scheduled Date:</strong> {{ $booking->scheduled_at?->format('F j, Y · g:i A') }}<br>
    <strong>Status:</strong> {{ ucfirst($booking->status?->value ?? (string) $booking->status) }}<br>
    @if($booking->notes)
    <strong>Notes / Details:</strong> {{ $booking->notes }}<br>
    @endif
</div>

<p>You can manage this booking directly from the TopGrade Dashboard.</p>

<p><strong>TopGrade London FC Automated Booking System</strong></p>
@endsection
