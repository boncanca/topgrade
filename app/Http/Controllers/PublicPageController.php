<?php

namespace App\Http\Controllers;

use App\Mail\ContactReceivedAdminNotification;
use App\Mail\ContactReceivedCustomerNotification;
use App\Models\BookableItem;
use App\Models\Contact;
use App\Models\Content;
use App\Models\Inquiry;
use App\Models\Moment;
use App\Models\Staff;
use App\Models\Team;
use App\Models\TrainingSession;
use App\Models\Venue;
use App\Settings\ClubSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController
{
    public function home(): Response
    {
        $pageContent = Content::published()
            ->where('slug', 'home')
            ->with([
                'blocks' => fn ($q) => $q->orderBy('sort_order'),
                'media',
                'seo',
            ])
            ->first();

        // Extract ContentBlocks by canonical type (with temporary fallback during migration)
        $heroBlock = $pageContent?->blocks->firstWhere('type', 'hero')?->payload ?? [];
        $featureListBlock = $pageContent?->blocks->firstWhere('type', 'feature_list')?->payload
            ?? $pageContent?->blocks->firstWhere('type', 'pillars')?->payload
            ?? [];
        $quickStats = $heroBlock['stats'] ?? $pageContent?->blocks->firstWhere('type', 'quick_stats')?->payload['items'] ?? [];

        $heroVideo = $pageContent?->getMedia('videos')->first();
        $heroPoster = $pageContent?->getMedia('images')->where('name', 'hero-poster')->first()
            ?? $pageContent?->getMedia('images')->first();

        // Dynamic club squads from Team domain model
        $squads = Team::active()
            ->orderBy('sort_order')
            ->with('media')
            ->get()
            ->map(fn (Team $team) => [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
                'age_group' => $team->age_group,
                'stage' => $team->stage,
                'description' => $team->description,
                'visual_variant' => $team->visual_variant,
                'image_url' => $team->getFirstMediaUrl('image'),
            ]);

        // Dynamic Moments Ribbon from actual published/featured Moment records
        $momentsRibbon = Moment::published()
            ->featured()
            ->orderBy('sort_order')
            ->with('media')
            ->get()
            ->map(fn (Moment $moment, int $idx) => [
                'id' => $moment->id,
                'num' => str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT),
                'title' => strtoupper($moment->title),
                'slug' => $moment->slug,
                'description' => $moment->description,
                'image_url' => $moment->getFirstMediaUrl('gallery'),
            ]);

        // Dynamic Recurring Training Schedule from TrainingSession domain model
        $trainingSchedule = TrainingSession::active()
            ->with('venue')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('days_label')
            ->map(function ($sessions, $daysLabel) {
                $first = $sessions->first();

                return [
                    'days' => $daysLabel,
                    'badge' => $first?->badge ?? 'Midweek Training',
                    'sessions' => $sessions->map(fn ($s) => [
                        'age' => $s->age_group,
                        'time' => $s->time_label,
                    ])->values()->all(),
                    'venue' => [
                        'name' => $first?->venue?->name,
                        'facility' => $first?->venue?->facility,
                        'address' => $first?->venue?->address.', '.$first?->venue?->locality.' '.$first?->venue?->postal_code,
                        'surface' => $first?->venue?->surface,
                    ],
                ];
            })->values()->all();

        $featuredActivities = BookableItem::where('is_active', true)
            ->limit(3)
            ->get();

        $extraBlocks = $pageContent?->blocks
            ->filter(fn ($b) => ! in_array($b->type, ['hero', 'feature_list', 'pillars', 'quick_stats']))
            ->values() ?? [];

        return Inertia::render('Public/Home', [
            'page' => $pageContent,
            'blocks' => $extraBlocks,
            'seo' => $pageContent?->seo,
            'hero' => [
                'eyebrow' => $heroBlock['eyebrow'] ?? 'MORE THAN FOOTBALL',
                'headline' => $heroBlock['headline'] ?? 'DEVELOP YOUR FOOTBALL FUTURE',
                'description' => $heroBlock['description'] ?? 'A youth football club in London helping young players develop through training, teamwork and playing experience.',
                'primary_cta' => $heroBlock['primary_cta'] ?? [
                    'label' => 'Book a Trial',
                    'url' => '/bookings/free-trial-session',
                ],
                'video_url' => $heroVideo?->getUrl(),
                'poster_url' => $heroPoster?->getUrl(),
            ],
            'pillars' => $featureListBlock['items'] ?? [],
            'quickStats' => $quickStats,
            'squads' => $squads,
            'momentsRibbon' => $momentsRibbon,
            'trainingSchedule' => $trainingSchedule,
            'featuredActivities' => $featuredActivities,
        ]);
    }

    public function about(): Response
    {
        $pageContent = Content::published()
            ->where('slug', 'about')
            ->with([
                'blocks' => fn ($q) => $q->orderBy('sort_order'),
                'media',
                'seo',
            ])
            ->first();

        $valuesBlock = $pageContent?->blocks->firstWhere('type', 'values')?->payload ?? [];

        $venues = Venue::active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Venue $v) => [
                'id' => $v->id,
                'name' => $v->name,
                'facility' => $v->facility,
                'address' => $v->address.', '.$v->locality.' '.$v->postal_code,
                'surface' => $v->surface,
                'details' => $v->details,
            ]);

        $staff = Staff::active()
            ->orderBy('sort_order')
            ->with('media')
            ->get()
            ->map(fn (Staff $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'role' => $s->role,
                'bio' => $s->bio,
                'qualifications' => $s->qualifications,
                'photo_url' => $s->getFirstMediaUrl('photo'),
            ]);

        return Inertia::render('Public/About', [
            'page' => $pageContent,
            'seo' => $pageContent?->seo,
            'values' => $valuesBlock['items'] ?? [],
            'facilities' => $venues,
            'staff' => $staff,
        ]);
    }

    public function contact(): Response
    {
        $pageContent = Content::published()
            ->where('slug', 'contact')
            ->with('seo')
            ->first();

        return Inertia::render('Public/Contact', [
            'page' => $pageContent,
            'seo' => $pageContent?->seo,
        ]);
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

        // Rate-limiting / Duplicate submission prevention (identical inquiry within 60s)
        $recentDuplicate = Inquiry::where('email', $validated['email'])
            ->where('message', $validated['message'])
            ->where('created_at', '>=', now()->subSeconds(60))
            ->first();

        if ($recentDuplicate) {
            return back()->with('success', 'Your message has already been received. Thank you!');
        }

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

        $inquiry = Inquiry::create([
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

        $adminRecipient = app(ClubSettings::class)->email ?? config('topgrade.emails.info', 'info@topgradelondonfc.co.uk');
        Mail::to($adminRecipient)->send(new ContactReceivedAdminNotification($inquiry));
        Mail::to($inquiry->email)->send(new ContactReceivedCustomerNotification($inquiry));

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function privacy(): Response
    {
        $page = Content::published()
            ->whereIn('slug', ['privacy-policy', 'privacy'])
            ->with(['blocks', 'seo'])
            ->firstOrFail();

        return Inertia::render('Public/Privacy', [
            'page' => $page,
            'seo' => $page->seo,
        ]);
    }

    public function terms(): Response
    {
        $page = Content::published()
            ->whereIn('slug', ['terms-and-conditions', 'terms'])
            ->with(['blocks', 'seo'])
            ->firstOrFail();

        return Inertia::render('Public/Terms', [
            'page' => $page,
            'seo' => $page->seo,
        ]);
    }

    /**
     * Parse full name into [firstName, lastName].
     *
     * @return array{0: string, 1: string}
     */
    private function parseParticipantName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);
        $firstName = $parts[0] ?? 'Participant';
        $lastName = $parts[1] ?? '';

        return [$firstName, $lastName];
    }
}
