<?php

namespace Database\Seeders\Booking;

use Illuminate\Database\Seeder;

class SchedulesSeeder extends Seeder
{
    /**
     * Seed operational booking schedules.
     * Per architecture rules: Only seed explicitly approved live dates.
     * Do NOT fabricate realistic-looking future dates to populate the UI.
     * Left empty in production bootstrap until authoritative schedule slots are provided.
     */
    public function run(): void
    {
        // Intentionally empty until official session dates are configured by club administrators.
    }
}
