<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookableItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:bookable_items',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'capacity' => 'nullable|integer|min:1',
            'booking_label' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }
}
