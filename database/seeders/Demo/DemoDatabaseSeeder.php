<?php

namespace Database\Seeders\Demo;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Seeder;

class DemoDatabaseSeeder extends Seeder
{
    /**
     * Run the demo database seeds.
     * Only for local developer environments needing mock inquiries and bookings.
     */
    public function run(): void
    {
        // 1. Ensure production baseline is established first
        $this->call(DatabaseSeeder::class);

        // 2. Add mock transactional data
        $this->call([
            SampleInquiriesSeeder::class,
            SampleBookingsSeeder::class,
        ]);
    }
}
