<?php

namespace Database\Seeders\Content;

use App\Models\Moment;
use Illuminate\Database\Seeder;

class MomentsSeeder extends Seeder
{
    public function run(): void
    {
        $momentsData = [
            [
                'title' => 'Midweek Technical Training',
                'slug' => 'midweek-technical-training',
                'description' => 'Youth squad training focusing on ball mastery, quick transitions, and small-sided tactical games under floodlights.',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'featured' => true,
                'sort_order' => 1,
                'images' => [
                    'images/club/youth-squad-team-topgrade-london-fc.webp',
                    'images/club/u7-u8-squad-photo-topgrade-london-fc.webp',
                    'images/club/training-agility-drills-topgrade-london-fc.webp',
                ],
            ],
            [
                'title' => 'Matchday – Saturday League Fixture',
                'slug' => 'matchday-saturday-league-fixture',
                'description' => 'Competitive league match action at Hackney Marshes. Technical passing, disciplined pressing, and team celebration.',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'featured' => true,
                'sort_order' => 2,
                'images' => [
                    'images/club/matchday-fixture-action-topgrade-london-fc.webp',
                    'images/club/foundation-phase-match-action-topgrade-london-fc.webp',
                    'images/club/squad-celebration-trophy-topgrade-london-fc.webp',
                ],
            ],
            [
                'title' => 'Squad Mentoring & Tactical Coaching',
                'slug' => 'squad-mentoring-tactical-coaching',
                'description' => 'On-pitch guidance and tactical positional awareness sessions led by coaching staff.',
                'status' => 'published',
                'published_at' => now()->subDays(9),
                'featured' => true,
                'sort_order' => 3,
                'images' => [
                    'images/club/tactical-coaching-briefing-topgrade-london-fc.webp',
                    'images/club/player-match-action-topgrade-london-fc.webp',
                ],
            ],
        ];

        foreach ($momentsData as $data) {
            $images = $data['images'] ?? [];
            unset($data['images']);

            $moment = Moment::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            // Populate media collection from shipped canonical assets if gallery is empty
            if ($moment->getMedia('gallery')->isEmpty()) {
                foreach ($images as $relativePath) {
                    $fullPath = public_path($relativePath);
                    if (file_exists($fullPath)) {
                        $moment->addMedia($fullPath)
                            ->preservingOriginal()
                            ->toMediaCollection('gallery');
                    }
                }

                $firstMedia = $moment->getFirstMedia('gallery');
                if ($firstMedia && ! $moment->cover_media_id) {
                    $moment->update(['cover_media_id' => $firstMedia->id]);
                }
            }
        }
    }
}
