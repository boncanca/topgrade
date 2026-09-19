@extends('mail.layout')

@section('content')
<h2>Thank You For Contacting TopGrade London FC</h2>

<p>Dear {{ $inquiry->name }},</p>

<p>Thank you for getting in touch with TopGrade London FC. We have received your message regarding "<strong>{{ $inquiry->subject }}</strong>" and our club staff will review it shortly.</p>

<div class="detail-box">
    <strong>Enquiry Subject:</strong> {{ $inquiry->subject }}<br>
    <strong>Received Date:</strong> {{ $inquiry->created_at?->format('F j, Y · g:i A') }}<br>
    <strong>Your Message:</strong><br>
    <p style="white-space: pre-line; margin-top: 4px;">{{ $inquiry->message }}</p>
</div>

<p>If you have any urgent enquiries regarding training sessions or trials, feel free to reply directly to this email at <a href="mailto:info@topgradelondonfc.co.uk">info@topgradelondonfc.co.uk</a>.</p>

<p>Best regards,<br>
<strong>TopGrade London FC Team</strong></p>
@endsection
