@extends('mail.layout')

@section('content')
@if((float) $booking->amount > 0 && ($booking->payment_status?->value ?? (string) $booking->payment_status) !== 'paid')
<h2>Booking Reserved · Awaiting Bank Transfer</h2>

<p>Dear {{ $booking->participant_name }},</p>

<p>Thank you for booking with TopGrade London FC! Your place has been reserved. To complete your booking, please transfer the session fee using the club bank account details below:</p>

<div class="detail-box">
    <strong>Bank:</strong> {{ $bank['bank_name'] ?? config('topgrade.bank.bank_name', "LLOYD'S BANK") }}<br>
    <strong>Account Name:</strong> {{ $bank['account_name'] ?? config('topgrade.bank.account_name', 'TOPGRADE LONDON FC') }}<br>
    <strong>Sort Code:</strong> {{ $bank['sort_code'] ?? config('topgrade.bank.sort_code', '30-99-50') }}<br>
    <strong>Account Number:</strong> {{ $bank['account_number'] ?? config('topgrade.bank.account_number', '20184968') }}<br>
    <strong>Payment Reference:</strong> {{ $booking->reference }}<br>
    <strong>Amount:</strong> £{{ number_format((float) $booking->amount, 2) }}
</div>

<p><strong>Payment Instructions:</strong> Please make sure you use your Booking Reference (<strong>{{ $booking->reference }}</strong>) as the transfer reference so we can match and activate your place promptly.</p>

<div class="detail-box">
    <strong>Booking Reference:</strong> {{ $booking->reference }}<br>
    <strong>Activity:</strong> {{ $booking->bookableItem->name }}<br>
    <strong>Scheduled Date:</strong> {{ $booking->scheduled_at->format('F j, Y') }}<br>
    <strong>Time:</strong> {{ $booking->scheduled_at->format('g:i A') }}<br>
    <strong>Location:</strong> {{ $booking->bookableItem->location ?? 'TBD' }}
</div>
@else
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
@endif

<p>Please arrive 15 minutes early. If you need to make any changes or have questions, please contact us as soon as possible.</p>

<p>See you soon!<br>
<strong>{{ config('app.name') }} Team</strong></p>
@endsection
