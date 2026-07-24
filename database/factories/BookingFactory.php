<?php

namespace Database\Factories;

use App\Models\BookableItem;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'bookable_item_id' => BookableItem::factory(),
            'reference' => Booking::generateReference(),
            'status' => 'pending',
            'scheduled_at' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'timezone' => 'UTC',
            'participant_name' => $this->faker->name(),
            'participant_email' => $this->faker->email(),
            'participant_phone' => $this->faker->phoneNumber(),
            'notes' => $this->faker->optional()->sentence(),
            'amount' => $this->faker->optional()->randomFloat(2, 10, 500),
            'currency' => 'USD',
            'payment_status' => 'unpaid',
            'metadata' => null,
        ];
    }
}
