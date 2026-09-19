<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Mail\BookingConfirmed;
use App\Mail\NewBookingAdminNotification;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Content;
use App\Models\Inquiry;
use App\Models\Payment;
use App\Models\Schedule;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PublicBookingController
{
    public function home(): Response
    {
        $featuredActivities = BookableItem::where('is_active', true)
            ->limit(3)
            ->get();

        $pageContent = Content::published()
            ->where('slug', 'home')
            ->with(['blocks' => fn ($q) => $q->orderBy('sort_order')])
            ->first();

        return Inertia::render('Public/Home', [
            'featuredActivities' => $featuredActivities,
            'page' => $pageContent,
            'blocks' => $pageContent?->blocks ?? [],
        ]);
    }

    public function training(): Response
    {
        $activities = BookableItem::where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('Public/Training', [
            'activities' => $activities,
        ]);
    }

    public function articles(): Response
    {
        $paginated = Content::published()
            ->whereHas('contentType', fn ($q) => $q->where('slug', 'article'))
            ->orderByDesc('published_at')
            ->paginate(9);

        return Inertia::render('Public/Articles/Index', [
            'articles' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function articleShow(string $slug): Response
    {
        $article = Content::published()
            ->where('slug', $slug)
            ->whereHas('contentType', fn ($q) => $q->where('slug', 'article'))
            ->firstOrFail();

        return Inertia::render('Public/Articles/Show', [
            'article' => $article,
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Public/Contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        [$firstName, $lastName] = $this->parseParticipantName($validated['name']);

        $contact = Contact::firstOrCreate(
            ['email' => $validated['email']],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $validated['phone'] ?? null,
                'company' => $validated['company'] ?? null,
                'position' => $validated['position'] ?? null,
                'status' => 'active',
            ]
        );

        if (($validated['company'] ?? null) || ($validated['position'] ?? null) || ($validated['phone'] ?? null)) {
            $contact->update(array_filter([
                'phone' => $validated['phone'] ?? $contact->phone,
                'company' => $validated['company'] ?? $contact->company,
                'position' => $validated['position'] ?? $contact->position,
            ]));
        }

        Inquiry::create([
            'contact_id' => $contact->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function activities(): Response
    {
        $paginated = BookableItem::where('is_active', true)
            ->orderBy('name')
            ->paginate(12);

        return Inertia::render('Public/Activities', [
            'activities' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
                'links' => [
                    'first' => $paginated->url(1),
                    'last' => $paginated->url($paginated->lastPage()),
                    'prev' => $paginated->previousPageUrl(),
                    'next' => $paginated->nextPageUrl(),
                ],
            ],
        ]);
    }

    public function show(BookableItem $bookableItem): Response
    {
        $schedules = $bookableItem->schedules()
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->get()
            ->map(fn (Schedule $schedule) => [
                'id' => $schedule->id,
                'starts_at' => $schedule->starts_at->toIso8601String(),
                'ends_at' => $schedule->ends_at->toIso8601String(),
                'capacity' => $schedule->effectiveCapacity(),
                'location' => $schedule->location ?? $bookableItem->location,
                'available_spots' => max(0, $schedule->effectiveCapacity() - $schedule->bookedCount()),
            ]);

        return Inertia::render('Public/ActivityDetail', [
            'activity' => $bookableItem,
            'schedules' => $schedules,
        ]);
    }

    public function book(Request $request, PaymentService $paymentService): SymfonyResponse
    {
        $validated = $request->validate([
            'bookable_item_id' => 'required|exists:bookable_items,id',
            'schedule_id' => 'required|exists:schedules,id',
            'participant_name' => 'required|string|max:255',
            'participant_email' => 'required|email|max:255',
            'participant_phone' => 'nullable|string|max:20',
            'timezone' => 'required|timezone',
            'notes' => 'nullable|string|max:1000',
        ]);

        /** @var BookableItem $bookableItem */
        $bookableItem = BookableItem::findOrFail($validated['bookable_item_id']);
        $requiresPayment = (bool) $bookableItem->requires_payment && (float) ($bookableItem->price ?? 0) > 0;

        /** @var Booking $booking */
        /** @var Payment|null $payment */
        [$booking, $payment] = DB::transaction(function () use ($validated, $bookableItem, $requiresPayment) {
            /** @var Schedule $schedule */
            $schedule = Schedule::where('id', $validated['schedule_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Validate schedule belongs to activity
            if ((int) $schedule->bookable_item_id !== (int) $validated['bookable_item_id']) {
                throw ValidationException::withMessages([
                    'schedule_id' => 'Invalid schedule for this activity.',
                ]);
            }

            // Check schedule is not cancelled
            if ($schedule->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'schedule_id' => 'This session has been cancelled.',
                ]);
            }

            // Check schedule is not in the past
            if ($schedule->starts_at <= now()) {
                throw ValidationException::withMessages([
                    'schedule_id' => 'This session is in the past.',
                ]);
            }

            // Check capacity (counting confirmed + active pending reservations)
            if ($schedule->isFull()) {
                throw ValidationException::withMessages([
                    'schedule_id' => 'This session is now full.',
                ]);
            }

            // Prevent duplicate booking submission for the same participant email on this schedule
            $alreadyBooked = Booking::where('schedule_id', $schedule->id)
                ->where('participant_email', $validated['participant_email'])
                ->activeReservation()
                ->exists();

            if ($alreadyBooked) {
                throw ValidationException::withMessages([
                    'participant_email' => 'A booking has already been submitted for this email address on this session.',
                ]);
            }

            [$firstName, $lastName] = $this->parseParticipantName($validated['participant_name']);
            $phone = data_get($validated, 'participant_phone');
            $notes = data_get($validated, 'notes');

            $contact = Contact::firstOrCreate(
                ['email' => $validated['participant_email']],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'status' => 'active',
                ]
            );

            if ($phone && ! $contact->phone) {
                $contact->update(['phone' => $phone]);
            }

            $bookingReference = Booking::generateReference();

            if ($requiresPayment) {
                $expiresAt = now()->addMinutes(30);

                $createdBooking = Booking::create([
                    'bookable_item_id' => $schedule->bookable_item_id,
                    'schedule_id' => $schedule->id,
                    'contact_id' => $contact->id,
                    'reference' => $bookingReference,
                    'participant_name' => $validated['participant_name'],
                    'participant_email' => $validated['participant_email'],
                    'participant_phone' => $phone,
                    'scheduled_at' => $schedule->starts_at,
                    'timezone' => $validated['timezone'],
                    'notes' => $notes,
                    'amount' => $bookableItem->price,
                    'currency' => $bookableItem->currency ?? 'GBP',
                    'status' => BookingStatus::Pending->value,
                    'payment_status' => PaymentStatus::Unpaid->value,
                    'payment_expires_at' => $expiresAt,
                ]);

                $createdPayment = Payment::create([
                    'booking_id' => $createdBooking->id,
                    'reference' => Payment::generateReference(),
                    'gateway' => 'stripe',
                    'status' => 'pending',
                    'amount' => $createdBooking->amount,
                    'currency' => $createdBooking->currency,
                    'customer_email' => $createdBooking->participant_email,
                    'expires_at' => $expiresAt,
                ]);

                return [$createdBooking, $createdPayment];
            }

            // Free session (e.g. trial): confirms immediately, no payment required
            $createdBooking = Booking::create([
                'bookable_item_id' => $schedule->bookable_item_id,
                'schedule_id' => $schedule->id,
                'contact_id' => $contact->id,
                'reference' => $bookingReference,
                'participant_name' => $validated['participant_name'],
                'participant_email' => $validated['participant_email'],
                'participant_phone' => $phone,
                'scheduled_at' => $schedule->starts_at,
                'timezone' => $validated['timezone'],
                'notes' => $notes,
                'amount' => 0.00,
                'currency' => 'GBP',
                'status' => BookingStatus::Confirmed->value,
                'payment_status' => PaymentStatus::NotRequired->value,
                'payment_expires_at' => null,
            ]);

            return [$createdBooking, null];
        });

        // 1. FREE SESSION: confirm immediately & dispatch notifications
        if (! $requiresPayment) {
            Mail::to($booking->participant_email)->send(new BookingConfirmed($booking));

            $adminEmail = config('topgrade.emails.bookings', 'bookings@topgradelondonfc.co.uk');
            Mail::to($adminEmail)->send(new NewBookingAdminNotification($booking));

            return redirect()->route('sessions.confirmation', $booking->reference);
        }

        // 2. PAID SESSION: initiate checkout session with outbound idempotency
        // NOTE: No confirmation emails sent here. Verified Stripe webhook is authoritative.
        if (! $paymentService->isConfigured()) {
            throw ValidationException::withMessages([
                'schedule_id' => 'Payment gateway is currently being configured. Please contact info@topgradelondonfc.co.uk to reserve this session.',
            ]);
        }

        $session = $paymentService->createCheckoutSession($booking, ['payment' => $payment]);

        if (empty($session['url'])) {
            throw ValidationException::withMessages([
                'schedule_id' => 'Unable to redirect to checkout gateway. Please try again or contact the club.',
            ]);
        }

        return Inertia::location($session['url']);
    }

    private function parseParticipantName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);

        return [
            $parts[0],
            $parts[1] ?? '',
        ];
    }

    public function confirmation(Booking $booking): Response
    {
        return Inertia::render('Public/BookingConfirmation', [
            'booking' => $booking->load(['bookableItem', 'latestPayment']),
        ]);
    }
}
