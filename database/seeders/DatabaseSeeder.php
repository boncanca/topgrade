<?php

namespace Database\Seeders;

use Database\Seeders\Booking\BookableItemsSeeder;
use Database\Seeders\Booking\SchedulesSeeder;
use Database\Seeders\Club\StaffSeeder;
use Database\Seeders\Club\TeamsSeeder;
use Database\Seeders\Club\TrainingTimetableSeeder;
use Database\Seeders\Club\VenuesSeeder;
use Database\Seeders\Content\ArticlesSeeder;
use Database\Seeders\Content\MomentsSeeder;
use Database\Seeders\Content\PagesSeeder;
use Database\Seeders\Site\ClubSettingsSeeder;
use Database\Seeders\Site\NavigationSeeder;
use Database\Seeders\Site\SeoSeeder;
use Database\Seeders\Site\SiteSettingsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's foundational production database.
     * Idempotent bootstrap only: No fake transactional bookings or contacts.
     */
    public function run(): void
    {
        // 1. Initial admin bootstrap accounts (Club Admin & Dev Super Admin)
        $this->call(UserSeeder::class);

        // 2. Settings & Canonical Navigation
        $this->call([
            SiteSettingsSeeder::class,
            ClubSettingsSeeder::class,
            NavigationSeeder::class,
        ]);

        // 3. Independent Club Domain Entities
        $this->call([
            VenuesSeeder::class,
            StaffSeeder::class,
            TeamsSeeder::class,
        ]);

        // 4. Content Schemas, Core Pages, Moments & SEO
        $this->call([
            ContentTypeSeeder::class,
            PagesSeeder::class,
            MomentsSeeder::class,
            ArticlesSeeder::class,
            SeoSeeder::class,
        ]);

        // 5. Booking Activities & Recurring Timetable
        $this->call([
            BookableItemsSeeder::class,
            TrainingTimetableSeeder::class,
            SchedulesSeeder::class,
        ]);
    }
}
