<?php

namespace App\Http\Controllers;

use App\Enums\InquiryStatusEnum;
use App\Http\Requests\UpdateInquiryRequest;
use App\Mail\InquiryQuickReply;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class InquiryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Inquiries/Index', [
            'inquiries' => Inquiry::latest()->paginate(15),
        ]);
    }

    public function show(Inquiry $inquiry): Response
    {
        return Inertia::render('Inquiries/Show', [
            'inquiry' => $inquiry->load(['contact', 'repliedBy']),
        ]);
    }

    public function edit(Inquiry $inquiry): Response
    {
        return Inertia::render('Inquiries/Edit', [
            'inquiry' => $inquiry->load(['contact', 'repliedBy']),
        ]);
    }

    public function update(UpdateInquiryRequest $request, Inquiry $inquiry): RedirectResponse
    {
        $inquiry->update($request->validated());

        return redirect()->route('inquiries.edit', $inquiry)
            ->with('success', 'Inquiry updated successfully');
    }

    public function reply(Request $request, Inquiry $inquiry): RedirectResponse
    {
        if (! $request->user()?->isDevAdmin()) {
            abort(403, 'Unauthorized. In-panel quick replies are restricted to developer administrators.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:3', 'max:5000'],
            'subject' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['new', 'open', 'in_progress', 'resolved', 'closed', 'archived'])],
        ]);

        $subject = ! empty($validated['subject'])
            ? $validated['subject']
            : (str_starts_with(strtolower($inquiry->subject), 're:') ? $inquiry->subject : 'Re: '.$inquiry->subject);

        // Deliver email to the recipient
        Mail::to($inquiry->email, $inquiry->name)
            ->send(new InquiryQuickReply(
                inquiry: $inquiry,
                replyMessage: $validated['message'],
                replySubject: $subject,
                sender: $request->user(),
            ));

        $newStatus = $validated['status'] ?? InquiryStatusEnum::Resolved->value;

        $inquiry->update([
            'reply_message' => $validated['message'],
            'replied_by_id' => $request->user()->id,
            'responded_at' => now(),
            'status' => $newStatus,
        ]);

        if (function_exists('activity')) {
            activity()
                ->performedOn($inquiry)
                ->causedBy($request->user())
                ->log("Sent in-panel quick reply to {$inquiry->name} ({$inquiry->email}) for inquiry #{$inquiry->id}");
        }

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', "Quick reply sent successfully to {$inquiry->email}.");
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry deleted successfully');
    }
}
