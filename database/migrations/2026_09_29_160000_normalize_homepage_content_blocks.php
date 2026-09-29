<?php

use App\Models\Content;
use App\Models\ContentBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $homePage = Content::where('slug', 'home')->first();

        if (! $homePage) {
            return;
        }

        // 1. Normalize 'pillars' to canonical 'feature_list'
        ContentBlock::where('content_id', $homePage->id)
            ->where('type', 'pillars')
            ->each(function (ContentBlock $block) {
                $block->update([
                    'type' => 'feature_list',
                ]);
            });

        // 2. Merge 'quick_stats' payload into 'hero' block payload
        $quickStatsBlocks = ContentBlock::where('content_id', $homePage->id)
            ->where('type', 'quick_stats')
            ->get();

        $heroBlock = ContentBlock::where('content_id', $homePage->id)
            ->where('type', 'hero')
            ->first();

        foreach ($quickStatsBlocks as $quickStatsBlock) {
            if ($heroBlock && ! empty($quickStatsBlock->payload['items'])) {
                $currentHeroPayload = $heroBlock->payload ?? [];

                // Only set stats if not already set or empty
                if (empty($currentHeroPayload['stats'])) {
                    $currentHeroPayload['stats'] = $quickStatsBlock->payload['items'];
                    $heroBlock->update(['payload' => $currentHeroPayload]);
                }
            }

            // Delete obsolete quick_stats block after processing
            $quickStatsBlock->delete();
        }
    }

    public function down(): void
    {
        $homePage = Content::where('slug', 'home')->first();

        if (! $homePage) {
            return;
        }

        $featureBlock = ContentBlock::where('content_id', $homePage->id)
            ->where('type', 'feature_list')
            ->first();

        if ($featureBlock) {
            $featureBlock->update(['type' => 'pillars']);
        }
    }
};
