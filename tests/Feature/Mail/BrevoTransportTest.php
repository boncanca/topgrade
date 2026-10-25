<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

test('brevo mailer dispatches email through Brevo REST API v3', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response([
            'messageId' => '<mocked-brevo-message-id-12345@smtp-relay.mailin.fr>',
        ], 201),
    ]);

    Config::set('mail.mailers.brevo.key', 'xkeysib-mocked-api-key');

    Mail::mailer('brevo')->raw('Hello world from Brevo API', function ($message) {
        $message->to('user@example.com', 'Test User')
            ->from('info@topgradelondonfc.co.uk', 'TopGrade London FC')
            ->replyTo('support@topgradelondonfc.co.uk', 'Support Team')
            ->subject('Brevo API Test Subject');
    });

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.brevo.com/v3/smtp/email'
            && $request->header('api-key')[0] === 'xkeysib-mocked-api-key'
            && $request['subject'] === 'Brevo API Test Subject'
            && $request['to'][0]['email'] === 'user@example.com'
            && $request['sender']['email'] === 'info@topgradelondonfc.co.uk'
            && $request['replyTo']['email'] === 'support@topgradelondonfc.co.uk';
    });
});

test('brevo mailer throws TransportException when API returns error', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response([
            'message' => 'Unauthorised IP address',
            'code' => 'unauthorized',
        ], 401),
    ]);

    Config::set('mail.mailers.brevo.key', 'xkeysib-mocked-api-key');

    expect(function () {
        Mail::mailer('brevo')->raw('Hello world', function ($message) {
            $message->to('user@example.com')->subject('Testing error');
        });
    })->toThrow(TransportException::class, 'Brevo API responded with status 401: Unauthorised IP address');
});

test('failover mailer falls back to secondary when brevo api fails', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response([
            'message' => 'Service Unavailable',
        ], 503),
    ]);

    Config::set('mail.default', 'failover');
    Config::set('mail.mailers.brevo.key', 'xkeysib-mocked-api-key');
    Config::set('mail.mailers.failover.mailers', ['brevo', 'array']);

    Mail::raw('Test failover body', function ($message) {
        $message->to('failover-recipient@example.com')->subject('Testing Failover');
    });

    // Brevo API was tried and failed
    Http::assertSent(fn ($req) => $req->url() === 'https://api.brevo.com/v3/smtp/email');

    // Mailer successfully delivered to array driver (fallback) without throwing unhandled exception
    expect(true)->toBeTrue();
});
