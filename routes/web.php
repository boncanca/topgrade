<?php

use App\Http\Controllers\BookableItemController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [PublicBookingController::class, 'home'])->name('home');
Route::get('/about', [PublicBookingController::class, 'about'])->name('about');
Route::get('/contact', [PublicBookingController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicBookingController::class, 'submitContact'])->name('contact.store');
Route::get('/privacy', [PublicBookingController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicBookingController::class, 'terms'])->name('terms');

// Editorial: Articles
Route::get('/articles', [PublicBookingController::class, 'articles'])->name('articles.index');
Route::get('/articles/{slug}', [PublicBookingController::class, 'articleShow'])->name('articles.show');

// Public club activities & booking sessions
Route::get('/activities', [PublicBookingController::class, 'activities'])->name('activities.index');
Route::get('/bookings', [PublicBookingController::class, 'activities'])->name('sessions.index');
Route::get('/bookings/confirmation/{booking:reference}', [PublicBookingController::class, 'confirmation'])->name('sessions.confirmation');
Route::get('/bookings/{bookableItem:slug}', [PublicBookingController::class, 'show'])->name('sessions.show');
Route::post('/bookings', [PublicBookingController::class, 'book'])->name('sessions.book');
Route::get('/training', [PublicBookingController::class, 'training'])->name('training');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('dashboard')->group(function () {
        // Content management
        Route::resource('content', ContentController::class);
        Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
        Route::delete('media/{uuid}', [MediaController::class, 'destroy'])->name('media.destroy');

        // Menu management
        Route::resource('menus', MenuController::class);

        // Booking management
        Route::resource('bookable-items', BookableItemController::class);
        Route::resource('bookable-items.schedules', ScheduleController::class)->scoped();
        Route::resource('bookings', BookingController::class)->only(['index', 'show']);

        // Booking workflow
        Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
        Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

        // Contacts
        Route::resource('contacts', ContactController::class);

        // Inquiries
        Route::resource('inquiries', InquiryController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    });
});

require __DIR__.'/settings.php';
