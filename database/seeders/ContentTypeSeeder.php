<?php

namespace Database\Seeders;

use App\Models\ContentType;
use Illuminate\Database\Seeder;

class ContentTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Seed content types (simplified)
        $types = [
            ['name' => 'Page', 'slug' => 'page', 'kind' => 'singleton', 'template' => 'page'],
            ['name' => 'Article', 'slug' => 'article', 'kind' => 'collection', 'template' => 'article'],
            ['name' => 'Block', 'slug' => 'block', 'kind' => 'singleton', 'template' => 'block'],
        ];

        foreach ($types as $type) {
            ContentType::firstOrCreate(
                ['slug' => $type['slug']],
                [
                    ...$type,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}
