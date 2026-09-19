<?php

namespace App\Http\Controllers;

use App\Mail\ContactReceivedAdminNotification;
use App\Mail\ContactReceivedCustomerNotification;
use App\Models\Contact;
use App\Models\Content;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        $adminRecipient = config('topgrade.emails.info', 'info@topgradelondonfc.co.uk');
        Mail::to($adminRecipient)->send(new ContactReceivedAdminNotification($inquiry));
        Mail::to($inquiry->email)->send(new ContactReceivedCustomerNotification($inquiry));

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function privacy(): Response
    {
        $page = Content::published()
            ->whereIn('slug', ['privacy-policy', 'privacy'])
            ->with('blocks')
            ->firstOrFail();

        return Inertia::render('Public/Privacy', [
            'page' => $page,
        ]);
    }

    public function terms(): Response
    {
        $page = Content::published()
            ->whereIn('slug', ['terms-and-conditions', 'terms'])
            ->with('blocks')
            ->firstOrFail();

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
