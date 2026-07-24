<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInquiryRequest;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
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
            'inquiry' => $inquiry->load('contact'),
        ]);
    }

    public function edit(Inquiry $inquiry): Response
    {
        return Inertia::render('Inquiries/Edit', [
            'inquiry' => $inquiry->load('contact'),
        ]);
    }

    public function update(UpdateInquiryRequest $request, Inquiry $inquiry): RedirectResponse
    {
        $inquiry->update($request->validated());

        return redirect()->route('inquiries.edit', $inquiry)
            ->with('success', 'Inquiry updated successfully');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry deleted successfully');
    }
}
