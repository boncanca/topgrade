<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMomentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:moments,slug'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'external_link' => ['nullable', 'url', 'max:255'],
            'external_link_label' => ['nullable', 'string', 'max:100'],
            'people' => ['nullable', 'array'],
            'people.*.name' => ['required_with:people', 'string', 'max:100'],
            'people.*.role' => ['nullable', 'string', 'max:100'],
            'people.*.url' => ['nullable', 'url', 'max:255'],
            'people.*.visible' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:15360'],
            'seo.title' => ['nullable', 'string', 'max:255'],
            'seo.description' => ['nullable', 'string', 'max:500'],
            'seo.canonical_url' => ['nullable', 'url'],
        ];
    }
}
