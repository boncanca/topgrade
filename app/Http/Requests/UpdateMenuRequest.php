<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:menus,slug,'.$this->route('menu')->id,
            'location' => 'sometimes|required|string|max:255',
            'items' => 'nullable|array',
            'items.*.label' => 'required|string|max:255',
            'items.*.url' => 'nullable|string|max:255',
            'items.*.target' => 'nullable|string|max:50',
            'items.*.sort_order' => 'nullable|integer',
        ];
    }
}
