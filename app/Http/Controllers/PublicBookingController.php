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
use Illuminate\Support\Facades\Mail;
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
        if ($bookableItem->is_active && $bookableItem->schedules()->where('status', '!=', 'cancelled')->where('starts_at', '>=', now())->count() === 0) {
            for ($i = 1; $i <= 3; $i++) {
                $startsAt = now()->addDays($i * 3 + 1)->setHour(10)->setMinute(0)->setSecond(0);
                $endsAt = (clone $startsAt)->addMinutes($bookableItem->duration_minutes ?? 60);

                Schedule::create([
                    'bookable_item_id' => $bookableItem->id,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'capacity' => $bookableItem->capacity ?? 15,
                    'location' => $bookableItem->location ?? 'Main Ground',
                    'status' => 'active',
                ]);
            }
        }

        $schedules = $bookableItem->schedules()
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->get()
            ->map(fn ($schedule) => [
                'id' => $schedule->id,
                'starts_at' => $schedule->starts_at->toIso8601String(),
                'ends_at' => $schedule->ends_at->toIso8601String(),
                'capacity' => $schedule->capacity ?? $bookableItem->capacity,
                'location' => $schedule->location ?? $bookableItem->location,
                'available_spots' => ($schedule->capacity ?? $bookableItem->capacity) - $schedule->bookings()
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->count(),
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

        $activity = BookableItem::findOrFail($validated['bookable_item_id']);
        $schedule = Schedule::findOrFail($validated['schedule_id']);

        // Validate schedule belongs to activity
        if ($schedule->bookable_item_id !== $activity->id) {
            return back()
                ->withInput()
                ->withErrors(['schedule_id' => 'Invalid schedule for this activity.']);
        }

        // Check schedule is not cancelled or in the past
        if ($schedule->status === 'cancelled') {
            return back()
                ->withInput()
                ->withErrors(['schedule_id' => 'This session has been cancelled.']);
        }

        if ($schedule->starts_at < now()) {
            return back()
                ->withInput()
                ->withErrors(['schedule_id' => 'This session is in the past.']);
        }

        // Check capacity
        $confirmedCount = $schedule->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->count();

        $capacity = $schedule->capacity ?? $activity->capacity;

        if ($confirmedCount >= $capacity) {
            return back()
                ->withInput()
                ->withErrors(['schedule_id' => 'This session is now full.']);
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

        $booking = Booking::create([
            'bookable_item_id' => $validated['bookable_item_id'],
            'schedule_id' => $validated['schedule_id'],
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
