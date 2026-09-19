@extends('mail.layout')

@section('content')
<h2>Payment Authorization Failed</h2>

<p>Dear {{ $booking->participant_name }},</p>

<p>We were unable to process payment for your upcoming football session with TopGrade London FC. As a result, your booking reservation could not be confirmed.</p>

<div class="detail-box">
    <strong>Booking Reference:</strong> {{ $booking->reference }}<br>
    <strong>Activity:</strong> {{ $booking->bookableItem->name }}<br>
    <strong>Status:</strong> Unpaid / Cancelled<br>
    @if(!empty($reason))
        <strong>Reason:</strong> {{ $reason }}<br>
    @endif
</div>

<p>Your reserved place has been released back into available squad capacity. If you still wish to join this session, please visit our website to start a new booking with an alternative payment method.</p>

<p>If you have any questions or require assistance, please reply to this email or contact us at <a href="mailto:info@topgradelondonfc.co.uk">info@topgradelondonfc.co.uk</a>.</p>

<p>Best regards,<br>
<strong>TopGrade London FC Team</strong></p>
@endsection
