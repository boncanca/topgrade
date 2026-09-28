<?php

namespace Database\Seeders\Site;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Explicitly clean up legacy conflicting menus
        Menu::whereIn('slug', ['main', 'mobile', 'footer'])->delete();

        // 2. Canonical Main Navigation
        $mainMenu = Menu::updateOrCreate(
            ['slug' => 'main-navigation'],
            [
                'name' => 'Main Navigation',
                'location' => 'header',
            ]
        );

        $mainItems = [
            ['label' => 'About', 'url' => '/#develop', 'sort_order' => 1],
            ['label' => 'Training', 'url' => '/#train', 'sort_order' => 2],
            ['label' => 'Teams', 'url' => '/#teams', 'sort_order' => 3],
            ['label' => 'Moments', 'url' => '/moments', 'sort_order' => 4],
            ['label' => 'Matchday', 'url' => '/#matchday', 'sort_order' => 5],
            ['label' => 'Bookings', 'url' => '/bookings', 'sort_order' => 6],
        ];

        // Seed items if not already present
        if ($mainMenu->allItems()->count() === 0) {
            foreach ($mainItems as $item) {
                $mainMenu->allItems()->create($item);
            }
        }

        // 3. Canonical Footer Navigation
        $footerMenu = Menu::updateOrCreate(
            ['slug' => 'footer-navigation'],
            [
                'name' => 'Footer Navigation',
                'location' => 'footer',
            ]
        );

        $footerItems = [
            // Club Links
            ['label' => 'About the Club', 'url' => '/about', 'sort_order' => 1],
            ['label' => 'Weekly Training', 'url' => '/training', 'sort_order' => 2],
            ['label' => 'Our Teams', 'url' => '/#teams', 'sort_order' => 3],
            ['label' => 'Matchday Grounds', 'url' => '/#matchday', 'sort_order' => 4],
            ['label' => 'Moments & Stories', 'url' => '/moments', 'sort_order' => 5],

            // Information Links
            ['label' => 'Bookings & Trials', 'url' => '/bookings', 'sort_order' => 10],
            ['label' => 'News & Articles', 'url' => '/articles', 'sort_order' => 11],
            ['label' => 'Contact Us', 'url' => '/contact', 'sort_order' => 12],

            // Support & Governance Links
            ['label' => 'Privacy Policy', 'url' => '/privacy-policy', 'sort_order' => 20],
            ['label' => 'Terms & Conditions', 'url' => '/terms-and-conditions', 'sort_order' => 21],
        ];

        if ($footerMenu->allItems()->count() === 0) {
            foreach ($footerItems as $item) {
                $footerMenu->allItems()->create($item);
            }
        }
    }
}
