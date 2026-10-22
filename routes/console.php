<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Expire pending booking reservations that have exceeded their 30-minute window
Schedule::command('bookings:expire-pending')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Daily sitemap cache refresh
Schedule::command('sitemap:generate')
    ->daily()
    ->withoutOverlapping();
