<?php

namespace Database\Seeders\Club;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamsSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            [
                'name' => 'U7 – U8',
                'slug' => 'u7-u8',
                'age_group' => 'U7–U8',
                'stage' => 'Foundation Phase',
                'description' => 'First touch, ball mastery, agility, balance, and introducing team play in a positive environment.',
                'visual_variant' => 'foundation',
                'sort_order' => 1,
                'image_asset' => 'images/club/youth_match_action.jpg',
            ],
            [
                'name' => 'U9 – U10',
                'slug' => 'u9-u10',
                'age_group' => 'U9–U10',
                'stage' => 'Skill Acquisition',
                'description' => '1v1 attacking and defending, spatial awareness, passing range, and instinctive game decisions.',
                'visual_variant' => 'acquisition',
                'sort_order' => 2,
                'image_asset' => '484192970_1108922961247045_3935375872642678849_n.jpg',
            ],
            [
                'name' => 'U11 – U12',
                'slug' => 'u11-u12',
                'age_group' => 'U11–U12',
                'stage' => 'Game Development',
                'description' => 'Tactical positioning, transition play, set-pieces, spatial compactness, and match tempo management.',
                'visual_variant' => 'development',
                'sort_order' => 3,
                'image_asset' => '485087659_1108923127913695_5031817050217357486_n.jpg',
            ],
            [
                'name' => 'U13 – U14',
                'slug' => 'u13-u14',
                'age_group' => 'U13–U14',
                'stage' => 'Youth Progression',
                'description' => 'Tactical discipline, physical conditioning, competitive league fixtures, and structured pitch mentoring.',
                'visual_variant' => 'progression',
                'sort_order' => 4,
                'image_asset' => 'images/club/tactical_coaching.jpg',
            ],
            [
                'name' => 'U15 – U16',
                'slug' => 'u15-u16',
                'age_group' => 'U15–U16',
                'stage' => 'Youth Competition',
                'description' => 'Competitive match play, high-intensity game management, leadership, and senior club pathway preparation.',
                'visual_variant' => 'competition',
                'sort_order' => 5,
                'image_asset' => 'images/club/squad_celebration.jpg',
            ],
        ];

        foreach ($teams as $item) {
            $imageAsset = $item['image_asset'];
            unset($item['image_asset']);

            $team = Team::firstOrCreate(
                ['slug' => $item['slug']],
                $item
            );

            if ($team->getMedia('image')->isEmpty()) {
                $imagePath = public_path($imageAsset);
                if (file_exists($imagePath)) {
                    $team->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('image');
                }
            }
        }
    }
}
