<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bookable_item_id' => 'required|exists:bookable_items,id',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'scheduled_at' => 'required|date|after:now',
            'timezone' => 'required|string|timezone',
            'participant_name' => 'required|string|max:255',
            'participant_email' => 'required|email|max:255',
            'participant_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'payment_status' => 'nullable|in:unpaid,pending,paid,refunded',
            'metadata' => 'nullable|json',
        ];
    }
}
