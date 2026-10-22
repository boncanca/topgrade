<?php

namespace App\Http\Requests;

use App\Models\Moment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateMomentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
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
            'images' => ['nullable', 'array', 'max:15'],
            'images.*' => ['file', 'image', 'max:15360'],
            'seo.title' => ['nullable', 'string', 'max:255'],
            'seo.description' => ['nullable', 'string', 'max:500'],
            'seo.canonical_url' => ['nullable', 'url'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->has('images') || $validator->errors()->has('images.*')) {
                return;
            }

            $images = $this->file('images');
            if (is_array($images)) {
                $totalSize = 0;
                foreach ($images as $file) {
                    if ($file instanceof UploadedFile && $file->isValid()) {
                        $totalSize += $file->getSize();
                    }
                }

                if ($totalSize > 50 * 1024 * 1024) {
                    $validator->errors()->add(
                        'images',
                        'Total batch size cannot exceed 50MB. Please upload photos in smaller batches.'
                    );
                }
            }
        });
    }
}
