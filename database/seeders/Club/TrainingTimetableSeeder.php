<?php

namespace Database\Seeders\Club;

use App\Models\BookableItem;
use App\Models\TrainingSession;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class TrainingTimetableSeeder extends Seeder
{
    public function run(): void
    {
        $fkVenue = Venue::where('slug', 'frederick-knight-sports-centre')->first();
        $tcscVenue = Venue::where('slug', 'tottenham-community-sports-centre')->first();

        if (! $fkVenue || ! $tcscVenue) {
            return;
        }

        $freeTrialItem = BookableItem::where('slug', 'free-trial-session')->first();
        $u8Item = BookableItem::where('slug', 'u8-squad-training')->first();
        $u12Item = BookableItem::where('slug', 'u12-youth-development')->first();

        $sessions = [
            [
                'venue_id' => $fkVenue->id,
                'bookable_item_id' => $u8Item?->id ?? $freeTrialItem?->id,
                'days_label' => 'Tuesdays & Thursdays',
                'badge' => 'Midweek Squad Training',
                'age_group' => 'U7 – U12',
                'time_label' => '5:00 PM – 7:00 PM',
                'sort_order' => 1,
            ],
            [
                'venue_id' => $fkVenue->id,
                'bookable_item_id' => $u12Item?->id ?? $freeTrialItem?->id,
                'days_label' => 'Tuesdays & Thursdays',
                'badge' => 'Midweek Squad Training',
                'age_group' => 'U13 – U16',
                'time_label' => '6:30 PM – 8:00 PM',
                'sort_order' => 2,
            ],
            [
                'venue_id' => $tcscVenue->id,
                'bookable_item_id' => $freeTrialItem?->id,
                'days_label' => 'Wednesdays',
                'badge' => 'Technical Base',
                'age_group' => 'U7 – U12',
                'time_label' => '5:30 PM – 7:00 PM',
                'sort_order' => 3,
            ],
        ];

        foreach ($sessions as $session) {
            TrainingSession::firstOrCreate(
                [
                    'venue_id' => $session['venue_id'],
                    'days_label' => $session['days_label'],
                    'age_group' => $session['age_group'],
                    'time_label' => $session['time_label'],
                ],
                $session
            );
        }
    }
}
