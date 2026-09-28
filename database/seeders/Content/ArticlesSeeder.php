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

        // Intentionally empty until official editorial articles are provided by the club.
    }
}
