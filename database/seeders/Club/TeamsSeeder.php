<?php

namespace Database\Seeders\Club;

use App\Models\Moment;
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
                'image_asset' => 'images/club/u7-u8-squad-photo-topgrade-london-fc.webp',
                'players' => [
                    ['name' => 'Leo Mensah', 'number' => 1, 'position' => 'Goalkeeper', 'role' => 'Player'],
                    ['name' => 'Ethan Walker', 'number' => 4, 'position' => 'Defender', 'role' => 'Player'],
                    ['name' => 'Kai Robinson', 'number' => 7, 'position' => 'Midfielder', 'role' => 'Captain'],
                    ['name' => 'Noah Campbell', 'number' => 9, 'position' => 'Forward', 'role' => 'Player'],
                    ['name' => 'Oliver Wright', 'number' => 10, 'position' => 'Playmaker', 'role' => 'Player'],
                ],
            ],
            [
                'name' => 'U9 – U10',
                'slug' => 'u9-u10',
                'age_group' => 'U9–U10',
                'stage' => 'Skill Acquisition',
                'description' => '1v1 attacking and defending, spatial awareness, passing range, and instinctive game decisions.',
                'visual_variant' => 'acquisition',
                'sort_order' => 2,
                'image_asset' => 'images/club/skill-acquisition-training-topgrade-london-fc.webp',
                'players' => [
                    ['name' => 'Lucas Davies', 'number' => 1, 'position' => 'Goalkeeper', 'role' => 'Player'],
                    ['name' => 'Mason Taylor', 'number' => 3, 'position' => 'Defender', 'role' => 'Player'],
                    ['name' => 'Zack O\'Connor', 'number' => 6, 'position' => 'Midfielder', 'role' => 'Captain'],
                    ['name' => 'Archie Evans', 'number' => 8, 'position' => 'Midfielder', 'role' => 'Player'],
                    ['name' => 'Jayden Brooks', 'number' => 11, 'position' => 'Winger', 'role' => 'Player'],
                ],
            ],
            [
                'name' => 'U11 – U12',
                'slug' => 'u11-u12',
                'age_group' => 'U11–U12',
                'stage' => 'Game Development',
                'description' => 'Tactical positioning, transition play, set-pieces, spatial compactness, and match tempo management.',
                'visual_variant' => 'development',
                'sort_order' => 3,
                'image_asset' => 'images/club/game-development-match-action-topgrade-london-fc.webp',
                'players' => [
                    ['name' => 'Callum Mitchell', 'number' => 1, 'position' => 'Goalkeeper', 'role' => 'Player'],
                    ['name' => 'Harry Phillips', 'number' => 5, 'position' => 'Centre Back', 'role' => 'Vice-Captain'],
                    ['name' => 'Tariq Sterling', 'number' => 8, 'position' => 'Box-to-Box', 'role' => 'Captain'],
                    ['name' => 'Finley Hughes', 'number' => 10, 'position' => 'Attacking Mid', 'role' => 'Player'],
                    ['name' => 'Kobe Mensah', 'number' => 9, 'position' => 'Striker', 'role' => 'Player'],
                ],
            ],
            [
                'name' => 'U13 – U14',
                'slug' => 'u13-u14',
                'age_group' => 'U13–U14',
                'stage' => 'Youth Progression',
                'description' => 'Tactical discipline, physical conditioning, competitive league fixtures, and structured pitch mentoring.',
                'visual_variant' => 'progression',
                'sort_order' => 4,
                'image_asset' => 'images/club/tactical-coaching-briefing-topgrade-london-fc.webp',
                'players' => [
                    ['name' => 'Daniel Ward', 'number' => 1, 'position' => 'Goalkeeper', 'role' => 'Player'],
                    ['name' => 'Aaron Clarke', 'number' => 4, 'position' => 'Centre Back', 'role' => 'Captain'],
                    ['name' => 'Reuben Powell', 'number' => 6, 'position' => 'Defensive Mid', 'role' => 'Player'],
                    ['name' => 'Dominic King', 'number' => 7, 'position' => 'Right Winger', 'role' => 'Player'],
                    ['name' => 'Micah Foster', 'number' => 9, 'position' => 'Centre Forward', 'role' => 'Player'],
                ],
            ],
            [
                'name' => 'U15 – U16',
                'slug' => 'u15-u16',
                'age_group' => 'U15–U16',
                'stage' => 'Youth Competition',
                'description' => 'Competitive match play, high-intensity game management, leadership, and senior club pathway preparation.',
                'visual_variant' => 'competition',
                'sort_order' => 5,
                'image_asset' => 'images/club/squad-celebration-trophy-topgrade-london-fc.webp',
                'players' => [
                    ['name' => 'Jordan Bailey', 'number' => 1, 'position' => 'Goalkeeper', 'role' => 'Player'],
                    ['name' => 'Marcus Adebayo', 'number' => 5, 'position' => 'Centre Back', 'role' => 'Captain'],
                    ['name' => 'Tyler Bennett', 'number' => 8, 'position' => 'Central Mid', 'role' => 'Vice-Captain'],
                    ['name' => 'Cameron Cole', 'number' => 11, 'position' => 'Left Winger', 'role' => 'Player'],
                    ['name' => 'Elijah Vance', 'number' => 9, 'position' => 'Striker', 'role' => 'Player'],
                ],
            ],
        ];

        $moments = Moment::published()->get();

        foreach ($teams as $idx => $item) {
            $imageAsset = $item['image_asset'] ?? null;
            unset($item['image_asset']);

            if ($moments->isNotEmpty() && empty($item['moment_id'])) {
                $item['moment_id'] = $moments[$idx % $moments->count()]->id;
            }

            $team = Team::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );

            // Populate media collection from shipped canonical assets if empty
            if ($imageAsset && $team->getMedia('image')->isEmpty()) {
                $fullPath = public_path($imageAsset);
                if (file_exists($fullPath)) {
                    $team->addMedia($fullPath)
                        ->preservingOriginal()
                        ->toMediaCollection('image');
                }
            }
        }
    }
}
