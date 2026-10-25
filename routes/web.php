<?php

use App\Http\Controllers\BookableItemController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MomentController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\PublicMomentController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\UserController;
use App\Models\Content;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Authentication & entry redirects
Route::redirect('/login', '/auth/login')->name('login.redirect');
Route::redirect('/admin', '/auth/login')->name('admin.redirect');

// Legacy & ghost route redirects to canonical locations
Route::redirect('/news', '/articles');
Route::redirect('/news/{any}', '/articles');
Route::redirect('/gallery', '/moments');
Route::redirect('/gallery/{any}', '/moments');
Route::redirect('/teams', '/#teams');
Route::redirect('/fixtures', '/#matchday');

// Public homepage & editorial
Route::get('/', [PublicPageController::class, 'home'])->name('home');

// Stripe webhook endpoint (verified via HMAC signature and CSRF-exempt)
Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle'])->name('webhooks.stripe');

// Public club information & governance pages
Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicPageController::class, 'submitContact'])->name('contact.store');
// Privacy Policy (canonical /privacy-policy with /privacy alias)
Route::get('/privacy-policy', [PublicPageController::class, 'privacy'])->name('privacy');
Route::get('/privacy', [PublicPageController::class, 'privacy']);

// Terms & Conditions (canonical /terms-and-conditions with /terms alias)
Route::get('/terms-and-conditions', [PublicPageController::class, 'terms'])->name('terms');
Route::get('/terms', [PublicPageController::class, 'terms']);

// Editorial: Articles
Route::get('/articles', [PublicBookingController::class, 'articles'])->name('articles.index');
Route::get('/articles/{slug}', [PublicBookingController::class, 'articleShow'])->name('articles.show');

// Editorial: Moments / Galleries
Route::get('/moments', [PublicMomentController::class, 'index'])->name('moments.index');
Route::get('/moments/{slug}', [PublicMomentController::class, 'show'])->name('moments.show');

// Public club activities & booking sessions
Route::get('/activities', [PublicBookingController::class, 'activities'])->name('activities.index');
Route::get('/bookings', [PublicBookingController::class, 'activities'])->name('sessions.index');
Route::get('/bookings/confirmation/{booking:reference}', [PublicBookingController::class, 'confirmation'])->name('sessions.confirmation');
Route::get('/bookings/{bookableItem:slug}', [PublicBookingController::class, 'show'])->name('sessions.show');
Route::post('/bookings', [PublicBookingController::class, 'book'])->name('sessions.book');
Route::get('/training', [PublicBookingController::class, 'training'])->name('training');

// XML Sitemap (cached dynamic XML response via SitemapService)
Route::get('/sitemap.xml', function (SitemapService $sitemapService) {
    $xml = Cache::remember('sitemap.xml', now()->addHours(12), function () use ($sitemapService) {
        return $sitemapService->render();
    });

    return response($xml, 200, [
        'Content-Type' => 'text/xml; charset=UTF-8',
    ]);
})->name('sitemap');

// Local & Testing Matchday Error Page Previews (strictly disabled in production)
if (app()->environment(['local', 'testing'])) {
    Route::get('/errors/{code}', function (int $code) {
        abort_if(! in_array($code, [403, 404, 419, 500, 503]), 404);

        return Inertia::render('Error', ['status' => $code])
            ->toResponse(request())
            ->setStatusCode($code);
    })->name('errors.preview');
}

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

        // Articles direct alias
        Route::get('articles', fn () => redirect()->route('content.index', ['type' => 'article']))->name('articles.index');

        // User management
        Route::resource('users', UserController::class)->names('dashboard.users');

        // Audit & System logs
        Route::get('logs', [LogsController::class, 'index'])->name('dashboard.logs.index');
        Route::post('logs/clear', [LogsController::class, 'clear'])->name('dashboard.logs.clear');

        // Unified Editorial Gallery Management
        Route::name('dashboard.')->group(function () {
            Route::resource('galleries', MomentController::class)
                ->parameters(['galleries' => 'moment'])
                ->names('galleries');
            Route::post('galleries/{moment}/media/reorder', [MomentController::class, 'reorderMedia'])->name('galleries.media.reorder');
            Route::delete('galleries/{moment}/media/{media}', [MomentController::class, 'destroyMedia'])->name('galleries.media.destroy');

            // Resource routes for moments (supporting legacy endpoints & test suite)
            Route::resource('moments', MomentController::class);
            Route::post('moments/{moment}/media/reorder', [MomentController::class, 'reorderMedia'])->name('moments.media.reorder');
            Route::delete('moments/{moment}/media/{media}', [MomentController::class, 'destroyMedia'])->name('moments.media.destroy');

            // Legacy dashboard alias redirects
            Route::redirect('teams', '/dashboard/bookable-items');
            Route::redirect('fixtures', '/dashboard/bookings');
            Route::redirect('media', '/dashboard/galleries');
        });
    });
});

require __DIR__.'/settings.php';
