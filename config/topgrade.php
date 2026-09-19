<?php

return [

    /*
    |--------------------------------------------------------------------------
    | TopGrade Official Email Addresses
    |--------------------------------------------------------------------------
    |
    | Centralized email configuration for human-facing conversations,
    | automated notifications, and booking notifications.
    |
    */

    'emails' => [
        // Human customer-facing communication, contact enquiries, general responses
        'info' => env('TOPGRADE_INFO_EMAIL', 'info@topgradelondonfc.co.uk'),

        // Automated notifications, system alerts, client transaction receipts
        'no_reply' => env('TOPGRADE_NO_REPLY_EMAIL', 'no-reply@topgradelondonfc.co.uk'),

        // Dedicated inbox for new website bookings
        'bookings' => env('TOPGRADE_BOOKINGS_EMAIL', 'bookings@topgradelondonfc.co.uk'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration Foundation
    |--------------------------------------------------------------------------
    |
    | Environment-driven configuration for future Stripe Checkout integration.
    | Secrets should never be committed directly to repository code.
    |
    */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

];
