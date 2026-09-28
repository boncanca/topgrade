<?php

namespace Database\Seeders\Booking;

use App\Models\BookableItem;
use Illuminate\Database\Seeder;

class BookableItemsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'slug' => 'mini-kickers-ages-4-6',
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
            ],
            [
                'slug' => 'u8-squad-training',
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
            ],
            [
                'slug' => 'u10-squad-training',
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
            ],
            [
                'slug' => 'u12-youth-development',
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
            ],
            [
                'slug' => 'free-trial-session',
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
            ],
        ];

        foreach ($items as $item) {
            BookableItem::firstOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
