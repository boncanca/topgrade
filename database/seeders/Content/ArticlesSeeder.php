<?php

namespace Database\Seeders\Content;

use App\Models\Content;
use App\Models\ContentType;
use Illuminate\Database\Seeder;

class ArticlesSeeder extends Seeder
{
    /**
     * Seed baseline club articles.
     * Per architecture rules: Seed only approved article content.
     * Do not invent filler or placeholder editorial articles.
     */
    public function run(): void
    {
        $articleType = ContentType::where('slug', 'article')->first();

        if (! $articleType) {
            return;
        }

        $articles = [
            [
                'title' => 'Training Session Highlights',
                'slug' => 'training-session-highlights',
                'category' => 'Training',
                'image' => '/images/club/training-agility-drills-topgrade-london-fc.webp',
                'published_at' => now()->subDays(2),
                'excerpt' => 'Highlights and tactical takeaways from this week\'s high-intensity squad training session.',
                'content' => <<<'HTML'
<p>TopGrade London FC youth squads completed a rigorous technical and tactical session this week, focusing on swift ball progression, positional discipline, and transitional counter-pressing.</p>
<p>Head coaches commended the players' concentration and relentless work rate across all small-sided games and structured match scenarios.</p>
HTML,
            ],
            [
                'title' => 'Match Preview: Hackney Wick FC',
                'slug' => 'match-preview-hackney-wick-fc',
                'category' => 'Match Preview',
                'image' => '/images/club/player-match-action-topgrade-london-fc.webp',
                'published_at' => now()->subDays(3),
                'excerpt' => 'TopGrade London FC travel to face Hackney Wick FC this Saturday in an important league fixture.',
                'content' => <<<'HTML'
<p>TopGrade London FC travel to face Hackney Wick FC this Saturday in another important league fixture. The team continue their strong run of form and will be looking to secure all three points.</p>
<h3>Key Details</h3>
<ul>
    <li><strong>Date:</strong> Saturday, 26 April 2026</li>
    <li><strong>Kick-off:</strong> 15:00 (BST)</li>
    <li><strong>Venue:</strong> Hackney Marshes, London</li>
    <li><strong>Competition:</strong> Combined Counties Premier Division</li>
</ul>
HTML,
            ],
            [
                'title' => 'U18s secure 3-1 victory',
                'slug' => 'u18s-secure-3-1-victory',
                'category' => 'Match Report',
                'image' => '/images/club/squad-celebration-trophy-topgrade-london-fc.webp',
                'published_at' => now()->subDays(5),
                'excerpt' => 'A clinical second-half display earns TopGrade London FC U18s a well-deserved 3-1 win.',
                'content' => <<<'HTML'
<p>A commanding performance saw TopGrade London FC U18s overcome a resilient opponent in Saturday's youth league fixture. After an evenly contested first half, two sharp finishes in the final twenty minutes sealed the victory.</p>
HTML,
            ],
            [
                'title' => 'New Training Facility Partnership',
                'slug' => 'new-training-facility-partnership',
                'category' => 'Club News',
                'image' => '/images/club/floodlit-training-pitch-topgrade-london-fc.webp',
                'published_at' => now()->subDays(14),
                'excerpt' => 'TopGrade London FC secures extended evening access to state-of-the-art floodlit all-weather training facilities.',
                'content' => <<<'HTML'
<p>We are delighted to confirm an expanded facility agreement providing our youth teams with dedicated access to floodlit 4G pitches for midweek evening development sessions.</p>
HTML,
            ],
            [
                'title' => 'Player Spotlight: Daniel Mensah',
                'slug' => 'player-spotlight-daniel-mensah',
                'category' => 'Player News',
                'image' => '/images/club/coach-mentoring-session-topgrade-london-fc.webp',
                'published_at' => now()->subDays(16),
                'excerpt' => 'Discover Daniel Mensah\'s development journey, leadership on the pitch, and ambitions for the season.',
                'content' => <<<'HTML'
<p>In our latest player feature, we catch up with youth squad midfielder Daniel Mensah to talk about his technical development, matchday preparation, and dedication to the team.</p>
HTML,
            ],
        ];

        foreach ($articles as $articleData) {
            Content::updateOrCreate(
                ['slug' => $articleData['slug']],
                [
                    'content_type_id' => $articleType->id,
                    'title' => $articleData['title'],
                    'excerpt' => $articleData['excerpt'],
                    'content' => $articleData['content'],
                    'status' => 'published',
                    'published_at' => $articleData['published_at'],
                    'metadata_json' => [
                        'category' => $articleData['category'],
                        'featured_image' => $articleData['image'],
                    ],
                ]
            );
        }
    }
}
