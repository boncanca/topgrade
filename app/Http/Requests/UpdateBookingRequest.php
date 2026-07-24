<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bookable_item_id' => 'sometimes|required|exists:bookable_items,id',
            'status' => 'sometimes|required|in:pending,confirmed,completed,cancelled',
            'scheduled_at' => 'sometimes|required|date|after:now',
            'timezone' => 'sometimes|required|string|timezone',
            'participant_name' => 'sometimes|required|string|max:255',
            'participant_email' => 'sometimes|required|email|max:255',
            'participant_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'payment_status' => 'nullable|in:unpaid,pending,paid,refunded',
            'metadata' => 'nullable|json',
        ];
    }
}
