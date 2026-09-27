<?php

namespace App\Http\Requests;

use App\Models\Moment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMomentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        /** @var Moment|null $moment */
        $moment = $this->route('moment');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('moments', 'slug')->ignore($moment?->id)],
            'description' => ['nullable', 'string'],
            'cover_media_id' => ['nullable', 'integer', 'exists:media,id'],
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
