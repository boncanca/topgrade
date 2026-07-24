<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;
use App\Models\Content;
use App\Models\ContentBlock;
use App\Models\ContentType;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Content/Index', [
            'content' => Content::with('contentType')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Content/Create', [
            'contentTypes' => ContentType::where('is_active', true)->get(),
        ]);
    }

    public function store(StoreContentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $seoData = $data['seo'] ?? [];
        $blocksData = $data['blocks'] ?? [];
        unset($data['seo'], $data['blocks']);

        $content = Content::create($data);

        if (! empty($seoData)) {
            $content->seo()->create($seoData);
        }

        $this->syncBlocks($content, $blocksData);

        return redirect()->route('content.index')
            ->with('success', 'Content created successfully');
    }

    public function show(Content $content): Response
    {
        return Inertia::render('Content/Show', [
            'content' => $content->load(['contentType', 'seo', 'blocks']),
        ]);
    }

    public function edit(Content $content): Response
    {
        return Inertia::render('Content/Edit', [
            'content' => $content->load(['contentType', 'seo', 'blocks']),
            'contentTypes' => ContentType::where('is_active', true)->get(),
        ]);
    }

    public function update(UpdateContentRequest $request, Content $content): RedirectResponse
    {
        $data = $request->validated();
        $seoData = $data['seo'] ?? [];
        $blocksData = $data['blocks'] ?? [];
        unset($data['seo'], $data['blocks']);

        $content->update($data);

        if (! empty($seoData)) {
            $content->seo()->updateOrCreate([], $seoData);
        }

        $this->syncBlocks($content, $blocksData);

        return redirect()->route('content.show', $content)
            ->with('success', 'Content updated successfully');
    }

    public function destroy(Content $content): RedirectResponse
    {
        $content->delete();

        return redirect()->route('content.index')
            ->with('success', 'Content deleted successfully');
    }

    /**
     * Sync content blocks: upsert by UUID (preserving order), delete removed ones.
     *
     * @param  array<int, array{uuid: string, type: string, payload: array<string, mixed>, settings: array<string, mixed>|null}>  $blocksData
     */
    private function syncBlocks(Content $content, array $blocksData): void
    {
        if (empty($blocksData)) {
            $content->blocks()->delete();

            return;
        }

        $incomingUuids = array_column($blocksData, 'uuid');

        // Delete blocks that are no longer in the payload
        $content->blocks()
            ->whereNotIn('uuid', $incomingUuids)
            ->delete();

        foreach ($blocksData as $sortOrder => $blockData) {
            ContentBlock::updateOrCreate(
                ['uuid' => $blockData['uuid'], 'content_id' => $content->id],
                [
                    'type' => $blockData['type'],
                    'payload' => $blockData['payload'],
                    'settings' => $blockData['settings'] ?? [],
                    'sort_order' => $sortOrder,
                ]
            );
        }
    }
}
