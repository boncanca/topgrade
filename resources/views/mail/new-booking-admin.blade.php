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
    <strong>Fee:</strong> £{{ number_format((float) ($booking->amount ?? 0), 2) }}<br>
    <strong>Payment Status:</strong> {{ ucfirst($booking->payment_status?->value ?? (string) $booking->payment_status) }}<br>
    <strong>Status:</strong> {{ ucfirst($booking->status?->value ?? (string) $booking->status) }}<br>
    @if($booking->notes)
    <strong>Notes / Details:</strong> {{ $booking->notes }}<br>
    @endif
</div>

@if((float) $booking->amount > 0 && ($booking->payment_status?->value ?? (string) $booking->payment_status) !== 'paid')
<p style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 10px 14px; margin: 16px 0; color: #92400e;">
    <strong>Awaiting Bank Transfer:</strong> Customer was instructed to transfer to TopGrade London FC Lloyd's Bank account using Reference: <strong>{{ $booking->reference }}</strong>. Once payment is received, please confirm this booking in the Dashboard.
</p>
@endif

<p>You can manage this booking directly from the TopGrade Dashboard.</p>

<p><strong>TopGrade London FC Automated Booking System</strong></p>
@endsection
