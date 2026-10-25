@extends('mail.layout')

@section('content')
<h2>Response to Your Inquiry</h2>

<p>Dear {{ $inquiry->name }},</p>

<div style="background-color: #f8fafc; border-left: 4px solid #7c3aed; padding: 16px 20px; margin: 20px 0; border-radius: 4px; font-size: 15px; line-height: 1.7; color: #1e293b;">
    {!! nl2br(e($replyMessage)) !!}
</div>

<div class="detail-box" style="margin-top: 25px; background-color: #f3f4f6; border-radius: 6px; padding: 14px 18px; font-size: 13px; color: #4b5563;">
    <strong style="color: #111827;">Inquiry Reference:</strong> #{{ $inquiry->id }}<br>
    <strong style="color: #111827;">Original Subject:</strong> {{ $inquiry->subject }}<br>
    <strong style="color: #111827;">Submitted On:</strong> {{ $inquiry->created_at->format('F j, Y \a\t g:i A') }}
</div>

<p style="margin-top: 25px;">
    If you have further questions or need additional information, please feel free to reply directly to this email or visit our website.
</p>

<p style="margin-top: 20px;">
    Best regards,<br>
    <strong>{{ config('app.name', 'Top Grade London FC') }} Team</strong><br>
    <a href="{{ config('app.url') }}" style="color: #7c3aed; text-decoration: none; font-size: 13px;">{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'topgradelondonfc.co.uk' }}</a>
</p>
@endsection
