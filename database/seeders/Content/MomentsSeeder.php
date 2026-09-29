<?php

namespace Database\Seeders\Content;

use App\Models\Moment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

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
                    'images/club/club-training-london.jpg',
                    'images/club/training-agility-drills.jpg',
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
                    '484977737_1109608517845156_6730033439051003629_n.jpg',
                    'images/club/youth_match_action.jpg',
                    'images/club/squad_celebration.jpg',
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
                    'images/club/tactical_coaching.jpg',
                    '484192970_1108922961247045_3935375872642678849_n.jpg',
                ],
            ],
        ];

        foreach ($momentsData as $data) {
            $images = $data['images'];
            unset($data['images']);

            $moment = Moment::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            foreach ($images as $imgRelPath) {
                $fullPath = public_path($imgRelPath);
                $fileName = basename($imgRelPath);
                $media = $moment->media()
                    ->where('collection_name', 'gallery')
                    ->where('file_name', $fileName)
                    ->first();

                if (! $media) {
                    if (file_exists($fullPath)) {
                        $moment->addMedia($fullPath)
                            ->preservingOriginal()
                            ->toMediaCollection('gallery');
                    }
                } elseif (! file_exists($media->getPath()) && file_exists($fullPath)) {
                    File::ensureDirectoryExists(dirname($media->getPath()));
                    File::copy($fullPath, $media->getPath());
                }
            }
        }
    }
}
