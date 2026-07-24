<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookableItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:bookable_items,slug,'.$this->route('bookable_item')->id,
            'description' => 'nullable|string',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'currency' => 'sometimes|required|string|size:3',
            'capacity' => 'nullable|integer|min:1',
            'booking_label' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }
}
