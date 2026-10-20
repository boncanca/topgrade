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

        $content = $request->filled('content_id')
            ? Content::findOrFail($request->integer('content_id'))
            : Content::firstOrFail();

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
     * Delete a block-images media item by its UUID.
     *
     * Restricted strictly to block-images to prevent accidental or unauthorized deletion
     * of gallery images, cover images, or other model media through this generic endpoint.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $media = Media::where('uuid', $uuid)->firstOrFail();

        abort_unless(
            $media->collection_name === 'block-images' && $media->model_type === Content::class,
            403,
            'Only CMS block content media can be deleted through this endpoint.'
        );

        $media->delete();

        return response()->json(['deleted' => true]);
    }
}
