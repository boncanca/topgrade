<?php

namespace Database\Seeders;

use App\Models\BookableItem;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TGLFCSeeder extends Seeder
{
    public function run(): void
    {
        // Create TGLFC training programs
        BookableItem::firstOrCreate(
            ['slug' => 'mini-kickers-ages-4-6'],
            [
                'name' => 'Mini Kickers (Ages 4-6)',
                'description' => 'Introduction to football fundamentals. Focus on coordination, basic ball control, and having fun with peers. Perfect first experience in organized football.',
                'duration_minutes' => 45,
                'location' => 'Main Ground',
                'price' => 15.00,
                'currency' => 'GBP',
                'capacity' => 12,
                'booking_label' => 'Book Trial',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::firstOrCreate(
            ['slug' => 'u8-academy-training'],
            [
                'name' => 'U8 Academy Training',
                'description' => 'Regular weekly training for under 8 academy members. Develops technical skills, tactical awareness, and team play in a competitive environment.',
                'duration_minutes' => 60,
                'location' => 'Main Ground',
                'price' => 20.00,
                'currency' => 'GBP',
                'capacity' => 16,
                'booking_label' => 'Enroll Now',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::firstOrCreate(
            ['slug' => 'u10-academy-training'],
            [
                'name' => 'U10 Academy Training',
                'description' => 'Intensive training for under 10 competitive academy players. Emphasis on advanced technical skills, tactical positioning, and match preparation.',
                'duration_minutes' => 75,
                'location' => 'Main Ground',
                'price' => 25.00,
                'currency' => 'GBP',
                'capacity' => 18,
                'booking_label' => 'Enroll Now',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::firstOrCreate(
            ['slug' => 'u12-development-programme'],
            [
                'name' => 'U12 Development Programme',
                'description' => 'Elite development programme for under 12 players. Advanced tactical training, strength & conditioning, and pathway to competitive leagues.',
                'duration_minutes' => 90,
                'location' => 'Main Ground',
                'price' => 30.00,
                'currency' => 'GBP',
                'capacity' => 20,
                'booking_label' => 'Enroll Now',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::firstOrCreate(
            ['slug' => 'futsal-training-ages-8-12'],
            [
                'name' => 'Futsal Training (Ages 8-12)',
                'description' => 'Fast-paced futsal training combining skill development with competitive play. Improves ball control, quick decision making, and shooting accuracy.',
                'duration_minutes' => 60,
                'location' => 'Indoor Arena',
                'price' => 18.00,
                'currency' => 'GBP',
                'capacity' => 14,
                'booking_label' => 'Book Now',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::firstOrCreate(
            ['slug' => 'free-trial-session'],
            [
                'name' => 'Free Trial Session',
                'description' => 'Experience TopGrade FC! Join us for a free trial session to see if we\'re the right fit for your child. No commitment required.',
                'duration_minutes' => 45,
                'location' => 'Main Ground',
                'price' => 0.00,
                'currency' => 'GBP',
                'capacity' => 20,
                'booking_label' => 'Book Free Trial',
                'is_active' => true,
                'requires_payment' => false,
            ]
        );

        $homeType = ContentType::where('slug', 'page')->first();

        if ($homeType) {
            $homeContent = Content::firstOrCreate(
                ['slug' => 'home'],
                [
                    'content_type_id' => $homeType->id,
                    'title' => 'Home - Train Like A Champion',
                    'excerpt' => 'Youth football excellence in London. Ages 4-18.',
                    'content' => 'Welcome to TopGrade London FC.',
                    'status' => 'published',
                    'published_at' => now(),
                    'metadata_json' => [
                        'tagline' => 'ALWAYS THE BEST — EST. 2022',
                        'headline' => 'DEVELOP YOUR FOOTBALL FUTURE',
                        'subheadline' => 'Youth football excellence in London. Ages 4–18. Professional coaching, competitive pathways, and a community that builds champions.',
                        'stats' => [
                            ['number' => '200+', 'label' => 'PLAYERS'],
                            ['number' => '15+', 'label' => 'TEAMS'],
                            ['number' => '4–18', 'label' => 'AGE RANGE'],
                        ],
                        'why_choose_title' => 'Why Choose TopGrade?',
                        'why_choose_subtitle' => "We're committed to developing young footballers with professional coaching, modern facilities, and a supportive community.",
                        'features' => [
                            'Professional coaching staff with international experience',
                            'Competitive pathways and opportunities',
                            'State-of-the-art training facilities',
                            'Strong community and player development focus',
                        ],
                    ],
                ]
            );

            if ($homeContent->blocks()->count() === 0) {
                $homeContent->blocks()->create([
                    'uuid' => (string) Str::uuid(),
                    'type' => 'hero',
                    'payload' => [
                        'title' => 'DEVELOP YOUR FOOTBALL FUTURE',
                        'subtitle' => 'Youth football excellence in London. Ages 4–18.',
                        'button_text' => 'Book a Trial',
                        'button_url' => '/activities',
                    ],
                    'settings' => [
                        'theme' => 'dark',
                        'align' => 'center',
                    ],
                    'sort_order' => 1,
                ]);

                $homeContent->blocks()->create([
                    'uuid' => (string) Str::uuid(),
                    'type' => 'cta',
                    'payload' => [
                        'title' => 'Ready to Join TopGrade?',
                        'subtitle' => 'Book your first trial session today and start your journey to football excellence.',
                        'button_text' => 'Book Your Trial Now',
                        'button_url' => '/activities',
                    ],
                    'settings' => [
                        'theme' => 'glass',
                    ],
                    'sort_order' => 2,
                ]);
            }
        }

        // Seed Main Navigation
        $headerMenu = Menu::firstOrCreate(
            ['slug' => 'main-navigation'],
            [
                'name' => 'Main Navigation',
                'location' => 'main',
            ]
        );

        if ($headerMenu->allItems()->count() === 0) {
            $headerMenu->allItems()->createMany([
                ['label' => 'Home', 'url' => '/', 'sort_order' => 1],
                ['label' => 'Training', 'url' => '/training', 'sort_order' => 2],
                ['label' => 'About', 'url' => '/about', 'sort_order' => 3],
                ['label' => 'Contact', 'url' => '/contact', 'sort_order' => 4],
            ]);
        }

        // Seed Footer Navigation
        $footerMenu = Menu::firstOrCreate(
            ['slug' => 'footer-navigation'],
            [
                'name' => 'Footer Quick Links',
                'location' => 'footer',
            ]
        );

        if ($footerMenu->allItems()->count() === 0) {
            $footerMenu->allItems()->createMany([
                ['label' => 'Home', 'url' => '/', 'sort_order' => 1],
                ['label' => 'Training', 'url' => '/training', 'sort_order' => 2],
                ['label' => 'About', 'url' => '/about', 'sort_order' => 3],
                ['label' => 'Contact', 'url' => '/contact', 'sort_order' => 4],
            ]);
        }
    }
}
