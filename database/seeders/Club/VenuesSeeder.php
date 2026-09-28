<?php

namespace Database\Seeders\Club;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenuesSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            [
                'name' => 'Frederick Knight Sports Centre',
                'slug' => 'frederick-knight-sports-centre',
                'facility' => 'Tottenham Powerleague',
                'address' => 'Willoughby Lane, Tottenham',
                'locality' => 'London',
                'postal_code' => 'N17 0RT',
                'surface' => 'All-Weather 3G Floodlit Pitches',
                'details' => 'Midweek squad training sessions (Tuesdays & Thursdays) and competitive home match fixtures.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Tottenham Community Sports Centre',
                'slug' => 'tottenham-community-sports-centre',
                'facility' => 'High Road Sports Facility',
                'address' => '701–703 High Road, London',
                'locality' => 'London',
                'postal_code' => 'N17 8AD',
                'surface' => 'Sports Centre Training Facility',
                'details' => 'Wednesday evening squad training sessions for junior age groups.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Mabley Green Park',
                'slug' => 'mabley-green-park',
                'facility' => 'Homerton, Hackney',
                'address' => 'Lee Conservancy Road, London',
                'locality' => 'London',
                'postal_code' => 'E9 5HW',
                'surface' => 'Grass & 3G Matchday Pitches',
                'details' => 'Weekend home league fixtures and squad match play.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Hackney Marshes',
                'slug' => 'hackney-marshes',
                'facility' => 'Homerton Road, Hackney',
                'address' => 'Homerton Road, London',
                'locality' => 'London',
                'postal_code' => 'E9 5PF',
                'surface' => 'Natural Grass Matchday Pitches',
                'details' => 'Saturday youth league matches, tournament fixtures, and inter-club friendlies.',
                'sort_order' => 4,
            ],
        ];

        foreach ($venues as $venue) {
            Venue::firstOrCreate(
                ['slug' => $venue['slug']],
                $venue
            );
        }
    }
}
