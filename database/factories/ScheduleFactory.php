<?php

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        $now = now();
        $startsAt = $now->copy()->addDays(rand(1, 30));
        $endsAt = $startsAt->copy()->addMinutes(60);

        return [
            'bookable_item_id' => null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'capacity' => rand(10, 30),
            'location' => $this->faker->city(),
            'status' => 'active',
            'recurrence_pattern' => null,
        ];
    }
}
