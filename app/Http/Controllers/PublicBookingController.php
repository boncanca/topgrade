<?php

namespace App\Http\Controllers;

use App\Mail\BookingReceived;
use App\Models\BookableItem;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Content;
use App\Models\Inquiry;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PublicBookingController
{
    public function home(): Response
    {
        $featuredActivities = BookableItem::where('is_active', true)
            ->limit(3)
            ->get();

        $pageContent = Content::published()
            ->where('slug', 'home')
            ->with(['blocks', 'seo'])
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

    public function about(): Response
    {
        return Inertia::render('Public/About');
    }

    public function privacy(): Response
    {
        $page = Content::published()->where('slug', 'privacy')->first();

        return Inertia::render('Public/Privacy', [
            'page' => $page,
        ]);
    }

    public function terms(): Response
    {
        $page = Content::published()->where('slug', 'terms')->first();

        return Inertia::render('Public/Terms', [
            'page' => $page,
        ]);
    }

    public function articles(): Response
    {
        $articles = Content::published()
            ->whereHas('contentType', fn ($q) => $q->where('slug', 'article'))
            ->latest('published_at')
            ->paginate(12);

        return Inertia::render('Public/Articles/Index', [
            'articles' => $articles,
        ]);
    }

    public function articleShow(string $slug): Response
    {
        $article = Content::published()
            ->whereHas('contentType', fn ($q) => $q->where('slug', 'article'))
            ->where('slug', $slug)
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

    public function book(Request $request): RedirectResponse
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

        $booking = DB::transaction(function () use ($validated) {
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

            // Check capacity
            if ($schedule->isFull()) {
                throw ValidationException::withMessages([
                    'schedule_id' => 'This session is now full.',
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

            return Booking::create([
                'bookable_item_id' => $schedule->bookable_item_id,
                'schedule_id' => $schedule->id,
                'contact_id' => $contact->id,
                'reference' => Booking::generateReference(),
                'participant_name' => $validated['participant_name'],
                'participant_email' => $validated['participant_email'],
                'participant_phone' => $phone,
                'scheduled_at' => $schedule->starts_at,
                'timezone' => $validated['timezone'],
                'notes' => $notes,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);
        });

        Mail::to($booking->participant_email)->send(new BookingReceived($booking));

        return redirect()->route('sessions.confirmation', $booking->reference);
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
            'booking' => $booking->load('bookableItem'),
        ]);
    }
}
