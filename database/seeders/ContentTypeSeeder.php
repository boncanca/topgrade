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
            ContentType::create([
                ...$type,
                'is_system' => true,
                'is_active' => true,
            ]);
        }

        // Seed menus
        Menu::create(['name' => 'Main Navigation', 'slug' => 'main', 'location' => 'main']);
        Menu::create(['name' => 'Footer Navigation', 'slug' => 'footer', 'location' => 'footer']);
        Menu::create(['name' => 'Mobile Navigation', 'slug' => 'mobile', 'location' => 'mobile']);
    }
}
