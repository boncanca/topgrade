@extends('mail.layout')

@section('content')
<h2>New Contact Enquiry Received</h2>

<p>A new message has been submitted through the TopGrade London FC website contact form.</p>

<div class="detail-box">
    <strong>From:</strong> {{ $inquiry->name }} &lt;{{ $inquiry->email }}&gt;<br>
    @if($inquiry->phone)
    <strong>Phone:</strong> {{ $inquiry->phone }}<br>
    @endif
    <strong>Subject:</strong> {{ $inquiry->subject }}<br>
    <strong>Received:</strong> {{ $inquiry->created_at?->format('F j, Y · g:i A') }}<br>
    <br>
    <strong>Message:</strong><br>
    <p style="white-space: pre-line; margin-top: 4px;">{{ $inquiry->message }}</p>
</div>

<p>You can reply directly to this email to respond to the sender.</p>

<p><strong>TopGrade London FC Club Notification System</strong></p>
@endsection
