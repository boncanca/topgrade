<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content_type_id' => 'required|exists:content_types,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:content_entries',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'metadata_json' => 'nullable|array',
            'blocks' => 'nullable|array',
            'blocks.*.uuid' => 'required_with:blocks|string',
            'blocks.*.type' => 'required_with:blocks|string',
            'blocks.*.payload' => 'required_with:blocks|array',
            'blocks.*.settings' => 'nullable|array',
            'seo.title' => 'nullable|string|max:255',
            'seo.description' => 'nullable|string|max:500',
            'seo.canonical_url' => 'nullable|url',
        ];
    }
}
