<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    /**
     * Upload a file to the block-images media collection.
     *
     * Accepts a content_id (optional) to attach the media to an existing content entry,
     * or stores it orphaned (model_id = null workaround via a dummy Content shell).
     * Returns the media UUID and public URL so the frontend can store the reference.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'max:5120'],
            'content_id' => ['nullable', 'integer', 'exists:content_entries,id'],
        ]);

        /** @var Content $content */
        if ($request->filled('content_id')) {
            $content = Content::findOrFail($request->integer('content_id'));
        } else {
            // Temporary: use first content or create a placeholder association.
            // The media stays orphaned until the block is saved with the real content.
            // We use a dummy approach: store on a real Content if possible.
            $content = Content::firstOrFail();
        }

        $media = $content
            ->addMediaFromRequest('file')
            ->toMediaCollection('block-images');

        return response()->json([
            'uuid' => $media->uuid,
            'id' => $media->id,
            'url' => $media->getUrl(),
            'name' => $media->file_name,
        ]);
    }

    /**
     * Delete a media item by its UUID.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $media = Media::where('uuid', $uuid)->firstOrFail();
        $media->delete();

        return response()->json(['deleted' => true]);
    }
}
