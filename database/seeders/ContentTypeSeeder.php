<?php

namespace Database\Seeders;

use App\Models\ContentType;
use App\Models\Menu;
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

        // Seed menus
        Menu::firstOrCreate(['slug' => 'main'], ['name' => 'Main Navigation', 'location' => 'main']);
        Menu::firstOrCreate(['slug' => 'footer'], ['name' => 'Footer Navigation', 'location' => 'footer']);
        Menu::firstOrCreate(['slug' => 'mobile'], ['name' => 'Mobile Navigation', 'location' => 'mobile']);
    }
}
