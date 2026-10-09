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

    /*
    |--------------------------------------------------------------------------
    | Administrator Bootstrap Credentials
    |--------------------------------------------------------------------------
    |
    | Configuration for deterministic administrator account synchronization.
    |
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'TopGrade Club Admin'),
        'email' => env('ADMIN_EMAIL', 'info@topgradelondonfc.co.uk'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    'dev_admin' => [
        'name' => env('DEV_ADMIN_NAME', 'Technical Super Admin'),
        'email' => env('DEV_ADMIN_EMAIL', 'tomc@trupabranding.com'),
        'password' => env('DEV_ADMIN_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Club Bank Account Details for Payments
    |--------------------------------------------------------------------------
    |
    | Official club bank transfer credentials displayed on booking confirmations
    | and transactional emails.
    |
    */

    'bank' => [
        'account_name' => env('BANK_ACCOUNT_NAME', 'TOPGRADE LONDON FC'),
        'bank_name' => env('BANK_NAME', "LLOYD'S BANK"),
        'sort_code' => env('BANK_SORT_CODE', '30-99-50'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '20184968'),
        'payment_instructions' => env('BANK_PAYMENT_INSTRUCTIONS', 'Please use your Booking Reference as the payment reference when making the transfer.'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Driver ('bank_transfer' or 'stripe')
    |--------------------------------------------------------------------------
    |
    | Toggle between direct manual bank transfer and automated Stripe checkout.
    | Defaults to 'bank_transfer'.
    |
    */
    'payments' => [
        'driver' => env('PAYMENT_DRIVER', 'bank_transfer'),
    ],

];
