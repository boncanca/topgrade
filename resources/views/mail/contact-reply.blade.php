@extends('mail.layout')

@section('content')
<h2>We've Received Your Inquiry</h2>

<p>Dear {{ $contact->first_name }},</p>

<p>Thank you for reaching out to us. We have received your inquiry and will get back to you as soon as possible.</p>

<div class="detail-box">
    <strong>Subject:</strong> {{ $inquiry->subject }}<br>
    <strong>Reference:</strong> {{ $inquiry->id }}<br>
    <strong>Submitted:</strong> {{ $inquiry->created_at->format('F j, Y \a\t g:i A') }}
</div>

<p>We typically respond to inquiries within 24-48 hours during business days. Your message is important to us, and we appreciate your patience.</p>

<p>If your matter is urgent, please don't hesitate to call us directly.</p>

<p>Best regards,<br>
<strong>{{ config('app.name') }} Team</strong></p>
@endsection
