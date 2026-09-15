<?php

namespace Database\Seeders;

use App\Models\BookableItem;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class TGLFCSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Confirmed Football Club Activities
        BookableItem::updateOrCreate(
            ['slug' => 'mini-kickers-ages-4-6'],
            [
                'name' => 'Mini Kickers (Ages 4–6)',
                'description' => 'Introduction to football fundamentals. Focus on coordination, basic ball control, and having fun with teammates.',
                'duration_minutes' => 45,
                'location' => 'London Training Ground',
                'price' => 15.00,
                'currency' => 'GBP',
                'capacity' => 12,
                'booking_label' => 'Book a Trial',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::updateOrCreate(
            ['slug' => 'u8-squad-training'],
            [
                'name' => 'U8 Squad Training',
                'description' => 'Weekly training for under 8 club members. Develops technical skills, decision-making, and team play in a positive environment.',
                'duration_minutes' => 60,
                'location' => 'London Training Ground',
                'price' => 20.00,
                'currency' => 'GBP',
                'capacity' => 16,
                'booking_label' => 'Book a Trial',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::updateOrCreate(
            ['slug' => 'u10-squad-training'],
            [
                'name' => 'U10 Squad Training',
                'description' => 'Structured football training for under 10 club players. Emphasis on technical ball mastery, tactical awareness, and match preparation.',
                'duration_minutes' => 75,
                'location' => 'London Training Ground',
                'price' => 25.00,
                'currency' => 'GBP',
                'capacity' => 18,
                'booking_label' => 'Book a Trial',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::updateOrCreate(
            ['slug' => 'u12-youth-development'],
            [
                'name' => 'U12 Youth Development',
                'description' => 'Focused football development for under 12 players. Technical drills, tactical positioning, and competitive match experience.',
                'duration_minutes' => 90,
                'location' => 'London Training Ground',
                'price' => 30.00,
                'currency' => 'GBP',
                'capacity' => 20,
                'booking_label' => 'Book a Trial',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::updateOrCreate(
            ['slug' => 'free-trial-session'],
            [
                'name' => 'Introductory Trial Session',
                'description' => 'Experience TopGrade London FC first-hand. Join a session to discover our coaching, team environment, and football values.',
                'duration_minutes' => 45,
                'location' => 'London Training Ground',
                'price' => 0.00,
                'currency' => 'GBP',
                'capacity' => 20,
                'booking_label' => 'Book a Trial',
                'is_active' => true,
                'requires_payment' => false,
            ]
        );

        // 2. Ensure Page & Article Content Types exist
        $pageType = ContentType::firstOrCreate(
            ['slug' => 'page'],
            [
                'name' => 'Page',
                'kind' => 'collection',
                'template' => 'default',
                'is_system' => true,
                'is_active' => true,
            ]
        );

        ContentType::firstOrCreate(
            ['slug' => 'article'],
            [
                'name' => 'Article',
                'kind' => 'collection',
                'template' => 'article',
                'is_system' => true,
                'is_active' => true,
            ]
        );

        // 3. Seed 5 Core Club Pages
        $pages = [
            [
                'slug' => 'home',
                'title' => 'TopGrade London FC — Youth Football Club',
                'excerpt' => 'A youth football club in London helping young players develop through training, teamwork and playing experience.',
                'content' => 'TopGrade London FC provides structured youth football training and development for young players.',
            ],
            [
                'slug' => 'about',
                'title' => 'About TopGrade London FC',
                'excerpt' => 'TopGrade London FC is a youth football club dedicated to helping young players develop through training, teamwork and playing experience.',
                'content' => 'We provide structured youth football training, coaching, and match opportunities in London.',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact TopGrade London FC',
                'excerpt' => 'Get in touch with TopGrade London FC for questions regarding club teams, trial bookings, and training sessions.',
                'content' => 'Contact the club via email or our contact message form.',
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'excerpt' => 'Privacy policy and data protection guidelines for TopGrade London FC.',
                'content' => 'TopGrade London FC collects information you provide directly when booking a trial session or submitting a contact enquiry.',
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms & Conditions',
                'excerpt' => 'Terms and conditions for participation, trials, and sessions at TopGrade London FC.',
                'content' => 'By booking a trial session or registering with TopGrade London FC, parents agree to provide accurate participant information.',
            ],
        ];

        foreach ($pages as $p) {
            $content = Content::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'content_type_id' => $pageType->id,
                    'title' => $p['title'],
                    'excerpt' => $p['excerpt'],
                    'content' => $p['content'],
                    'status' => 'published',
                    'published_at' => now(),
                ]
            );

            if ($p['slug'] === 'home') {
                $content->blocks()->delete();
            }
        }

        // Backward compatibility for existing Menu relationships if present
        $headerMenu = Menu::firstOrCreate(
            ['slug' => 'main-navigation'],
            ['name' => 'Main Navigation', 'location' => 'main']
        );
        if ($headerMenu->allItems()->count() === 0) {
            $headerMenu->allItems()->createMany([
                ['label' => 'Home', 'url' => '/', 'sort_order' => 1],
                ['label' => 'About', 'url' => '/about', 'sort_order' => 2],
                ['label' => 'Activities', 'url' => '/bookings', 'sort_order' => 3],
                ['label' => 'Articles', 'url' => '/articles', 'sort_order' => 4],
                ['label' => 'Contact', 'url' => '/contact', 'sort_order' => 5],
            ]);
        }
    }
}
