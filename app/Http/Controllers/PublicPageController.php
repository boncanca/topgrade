<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Content;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController
{
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

    public function safeguarding(): Response
    {
        $page = Content::published()->where('slug', 'safeguarding')->first();

        return Inertia::render('Public/Safeguarding', [
            'page' => $page,
        ]);
    }

    public function accessibility(): Response
    {
        $page = Content::published()->where('slug', 'accessibility')->first();

        return Inertia::render('Public/Accessibility', [
            'page' => $page,
        ]);
    }

    public function cookies(): Response
    {
        $page = Content::published()->where('slug', 'cookies')->first();

        return Inertia::render('Public/Cookies', [
            'page' => $page,
        ]);
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
