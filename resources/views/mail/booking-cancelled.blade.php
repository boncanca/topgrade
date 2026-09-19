@extends('mail.layout')

@section('content')
<h2>Booking Reservation Cancelled</h2>

<p>Dear {{ $booking->participant_name }},</p>

<p>We are writing to let you know that your reservation for <strong>{{ $booking->bookableItem->name ?? 'your scheduled football session' }}</strong> has been cancelled.</p>

<div class="detail-box">
    <strong>Booking Reference:</strong> {{ $booking->reference }}<br>
    @if($booking->bookableItem)
    <strong>Activity:</strong> {{ $booking->bookableItem->name }}<br>
    @endif
    <strong>Cancelled Date:</strong> {{ now()->format('F j, Y') }}<br>
    @if(!empty($reason))
    <strong>Notice:</strong> {{ $reason }}<br>
    @endif
</div>

<p>Your reserved place has been returned to available squad capacity. If you have any questions or wish to book an alternative session, please contact our match desk at <a href="mailto:info@topgradelondonfc.co.uk">info@topgradelondonfc.co.uk</a>.</p>

<p>Best regards,<br>
<strong>TopGrade London FC Team</strong></p>
@endsection
